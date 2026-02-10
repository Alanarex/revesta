<?php

namespace App\Repositories;

use App\Models\Newsletter;

class NewsletterRepository
{
    /**
     * Subscribe email to newsletter.
     *
     * @param string $email
     * @param string|null $ipAddress
     * @return Newsletter
     */
    public function subscribe(string $email, ?string $ipAddress = null): Newsletter
    {
        return Newsletter::create([
            'email' => strtolower(trim($email)),
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Check if email exists in newsletter.
     *
     * @param string $email
     * @return bool
     */
    public function exists(string $email): bool
    {
        return Newsletter::where('email', strtolower(trim($email)))
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Find newsletter by email.
     *
     * @param string $email
     * @return Newsletter|null
     */
    public function findByEmail(string $email): ?Newsletter
    {
        return Newsletter::where('email', strtolower(trim($email)))
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Get all verified subscribers count.
     *
     * @return int
     */
    public function getVerifiedCount(): int
    {
        return Newsletter::whereNotNull('verified_at')
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Get all verified subscribers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVerifiedSubscribers()
    {
        return Newsletter::whereNotNull('verified_at')
            ->whereNull('deleted_at')
            ->select('email')
            ->get();
    }

    /**
     * Verify subscriber by email.
     *
     * @param string $email
     * @return bool
     */
    public function verify(string $email): bool
    {
        $newsletter = $this->findByEmail($email);

        if (!$newsletter) {
            return false;
        }

        return $newsletter->update(['verified_at' => now()]);
    }

    /**
     * Unsubscribe email from newsletter.
     *
     * @param string $email
     * @return bool
     */
    public function unsubscribe(string $email): bool
    {
        $newsletter = $this->findByEmail($email);

        if (!$newsletter) {
            return false;
        }

        return $newsletter->delete();
    }

    /**
     * Get total count of all subscribers.
     *
     * @return int
     */
    public function count(): int
    {
        return Newsletter::count();
    }

    /**
     * Search subscribers with filtering and sorting.
     *
     * @param string $search Search term for email
     * @param string $sort Column to sort by
     * @param string $direction Sort direction (asc/desc)
     * @param string $status Filter by status (verified/unverified)
     * @param int $perPage Results per page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search = '', string $sort = 'created_at', string $direction = 'desc', string $status = '', int $perPage = 15)
    {
        $query = Newsletter::query();

        // Filter by search term
        if (!empty($search)) {
            $query->where('email', 'like', "%{$search}%");
        }

        // Filter by status
        if (!empty($status)) {
            if ($status === 'verified') {
                $query->whereNotNull('verified_at');
            } elseif ($status === 'unverified') {
                $query->whereNull('verified_at');
            }
        }

        // Sort with validation
        $validColumns = ['id', 'email', 'created_at', 'verified_at'];
        $sort = in_array($sort, $validColumns) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction)->paginate($perPage);
    }
}
