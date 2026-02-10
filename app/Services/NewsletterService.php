<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Repositories\NewsletterRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsletterService
{
    public function __construct(
        protected NewsletterRepository $newsletterRepository
    ) {
    }

    /**
     * Subscribe email to newsletter.
     *
     * @param string $email
     * @param string|null $ipAddress
     * @return array<string, bool|Newsletter>
     */
    public function subscribe(string $email, ?string $ipAddress = null): array
    {
        // Check if already subscribed
        if ($this->newsletterRepository->exists($email)) {
            return [
                'success' => false,
                'message' => 'Cet email est déjà abonné.',
            ];
        }

        try {
            $newsletter = $this->newsletterRepository->subscribe($email, $ipAddress);

            return [
                'success' => true,
                'message' => 'Abonnement à la lettre d\'information réussi.',
                'data' => $newsletter,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'abonnement à la lettre d\'information.',
            ];
        }
    }

    /**
     * Verify subscriber by email.
     *
     * @param string $email
     * @return array<string, bool|string>
     */
    public function verify(string $email): array
    {
        $result = $this->newsletterRepository->verify($email);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Abonné non trouvé.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Email vérifié avec succès.',
        ];
    }

    /**
     * Unsubscribe email from newsletter.
     *
     * @param string $email
     * @return array<string, bool|string>
     */
    public function unsubscribe(string $email): array
    {
        $result = $this->newsletterRepository->unsubscribe($email);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Abonné non trouvé.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Désabonné avec succès.',
        ];
    }

    /**
     * Get verified subscribers count.
     *
     * @return int
     */
    public function getVerifiedCount(): int
    {
        return $this->newsletterRepository->getVerifiedCount();
    }

    /**
     * Get all verified subscribers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVerifiedSubscribers()
    {
        return $this->newsletterRepository->getVerifiedSubscribers();
    }

    /**
     * Get newsletter statistics.
     *
     * @return array<string, int|float>
     */
    public function getStats(): array
    {
        $total = Newsletter::whereNull('deleted_at')->count();
        $verified = Newsletter::whereNotNull('verified_at')->whereNull('deleted_at')->count();
        $unverified = Newsletter::whereNull('verified_at')->whereNull('deleted_at')->count();
        $recentlySubscribed = Newsletter::whereNull('deleted_at')
            ->where('subscribed_at', '>=', now()->subDays(7))
            ->count();

        return [
            'total' => $total,
            'verified' => $verified,
            'unverified' => $unverified,
            'recently_subscribed' => $recentlySubscribed,
            'verification_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get paginated subscribers list.
     *
     * @param int $perPage
     * @param string $sort
     * @param bool $verified
     * @return LengthAwarePaginator
     */
    public function getSubscribers(int $perPage = 50, string $sort = 'latest', bool $verified = true): LengthAwarePaginator
    {
        $query = Newsletter::select(['id', 'email', 'ip_address', 'subscribed_at', 'verified_at', 'created_at'])
            ->whereNull('deleted_at');

        if ($verified) {
            $query->whereNotNull('verified_at');
        }

        return match ($sort) {
            'oldest' => $query->orderBy('subscribed_at', 'asc')->paginate($perPage),
            'alpha' => $query->orderBy('email', 'asc')->paginate($perPage),
            default => $query->orderBy('subscribed_at', 'desc')->paginate($perPage),
        };
    }

    /**
     * Export subscribers to CSV format.
     *
     * @param bool $verified
     * @return string
     */
    public function exportToCSV(bool $verified = true): string
    {
        $query = Newsletter::select(['email', 'subscribed_at', 'verified_at'])
            ->whereNull('deleted_at');

        if ($verified) {
            $query->whereNotNull('verified_at');
        }

        $subscribers = $query->get();

        $csv = "Email,Subscribed At,Verified At\n";

        foreach ($subscribers as $subscriber) {
            $csv .= sprintf(
                '"%s","%s","%s"' . "\n",
                $subscriber->email,
                $subscriber->subscribed_at?->format('Y-m-d H:i:s') ?? '',
                $subscriber->verified_at?->format('Y-m-d H:i:s') ?? ''
            );
        }

        return $csv;
    }

    /**
     * Search subscribers by email.
     *
     * @param string $searchTerm
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchSubscribers(string $searchTerm, int $perPage = 50): LengthAwarePaginator
    {
        return Newsletter::where('email', 'like', '%' . $searchTerm . '%')
            ->whereNull('deleted_at')
            ->orderBy('subscribed_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Bulk verify subscribers.
     *
     * @param array<int> $subscriberIds
     * @return int
     */
    public function bulkVerify(array $subscriberIds): int
    {
        return Newsletter::whereIn('id', $subscriberIds)
            ->whereNull('deleted_at')
            ->update(['verified_at' => now()]);
    }

    /**
     * Bulk unsubscribe (soft delete).
     *
     * @param array<int> $subscriberIds
     * @return int
     */
    public function bulkUnsubscribe(array $subscriberIds): int
    {
        return Newsletter::whereIn('id', $subscriberIds)
            ->whereNull('deleted_at')
            ->delete();
    }

    /**
     * Cleanup duplicate emails (keep oldest, delete newer duplicates).
     *
     * @return int
     */
    public function cleanupDuplicates(): int
    {
        $duplicates = Newsletter::select('email')
            ->whereNull('deleted_at')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        $deleted = 0;

        foreach ($duplicates as $email) {
            // Get all subscribers with this email - returns Newsletter models
            $subscribers = Newsletter::where('email', $email)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'asc')
                ->get();

            // Keep the first one, delete the rest
            if ($subscribers->count() > 1) {
                foreach ($subscribers->slice(1) as $subscriber) {
                    /** @var Newsletter $subscriber */
                    $subscriber->delete();
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Get subscribers by IP address.
     *
     * @param string $ipAddress
     * @return Collection
     */
    public function getSubscribersByIp(string $ipAddress): Collection
    {
        return Newsletter::where('ip_address', $ipAddress)
            ->whereNull('deleted_at')
            ->select(['email', 'subscribed_at', 'verified_at'])
            ->get();
    }

    /**
     * Get recently subscribed (last N days).
     *
     * @param int $days
     * @param int $limit
     * @return Collection
     */
    public function getRecentlySubscribed(int $days = 7, int $limit = 100): Collection
    {
        return Newsletter::where('subscribed_at', '>=', now()->subDays($days))
            ->whereNull('deleted_at')
            ->orderBy('subscribed_at', 'desc')
            ->limit($limit)
            ->select(['email', 'subscribed_at', 'verified_at'])
            ->get();
    }

    /**
     * Check if email is subscribed.
     *
     * @param string $email
     * @return bool
     */
    public function isSubscribed(string $email): bool
    {
        return Newsletter::where('email', strtolower(trim($email)))
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Get unverified subscribers count.
     *
     * @return int
     */
    public function getUnverifiedCount(): int
    {
        return Newsletter::whereNull('verified_at')
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Send verification email to subscriber.
     * (Implementation depends on your email service)
     *
     * @param string $email
     * @return bool
     */
    public function sendVerificationEmail(string $email): bool
    {
        // Implementation would go here
        // Dispatch a job or call a notification service
        return true;
    }

    /**
     * Restore soft-deleted subscriber.
     *
     * @param string $email
     * @return bool
     */
    public function restore(string $email): bool
    {
        $restored = Newsletter::where('email', strtolower(trim($email)))
            ->onlyTrashed()
            ->restore();

        return $restored > 0;
    }

    /**
     * Permanently delete subscriber.
     *
     * @param string $email
     * @return bool
     */
    public function forceDelete(string $email): bool
    {
        $deleted = Newsletter::where('email', strtolower(trim($email)))
            ->forceDelete();

        return $deleted > 0;
    }
}
