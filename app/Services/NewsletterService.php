<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Repositories\NewsletterRepository;

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
                'message' => 'Email already subscribed.',
            ];
        }

        try {
            $newsletter = $this->newsletterRepository->subscribe($email, $ipAddress);

            return [
                'success' => true,
                'message' => 'Successfully subscribed to newsletter.',
                'data' => $newsletter,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to subscribe to newsletter.',
            ];
        }
    }

    /**
     * Verify subscriber.
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
                'message' => 'Subscriber not found.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Email verified successfully.',
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
                'message' => 'Subscriber not found.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Unsubscribed successfully.',
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
}
