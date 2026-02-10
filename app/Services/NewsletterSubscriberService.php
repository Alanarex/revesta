<?php

namespace App\Services;

use App\Repositories\NewsletterRepository;
use App\Models\Newsletter;

class NewsletterSubscriberService
{
    public function __construct(
        protected NewsletterRepository $repository
    ) {
    }

    /**
     * Verify a subscriber.
     *
     * @param int $id
     * @return array
     */
    public function verify(int $id): array
    {
        try {
            $subscriber = Newsletter::findOrFail($id);
            $subscriber->update(['verified_at' => now()]);

            return [
                'success' => true,
                'message' => 'Abonné vérifié avec succès.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la vérification de l\'abonné.',
            ];
        }
    }

    /**
     * Delete/unsubscribe a subscriber.
     *
     * @param int $id
     * @return array
     */
    public function delete(int $id): array
    {
        try {
            $subscriber = Newsletter::findOrFail($id);
            $email = $subscriber->email;
            $subscriber->delete();

            return [
                'success' => true,
                'message' => "L'abonné {$email} a été désabonné.",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'abonné.',
            ];
        }
    }
}
