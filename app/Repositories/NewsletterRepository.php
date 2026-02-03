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
}
