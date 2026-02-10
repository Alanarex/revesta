<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Repositories\NewsletterCampaignRepository;

class NewsletterCampaignService
{
    public function __construct(
        protected NewsletterCampaignRepository $campaignRepository,
    ) {
    }

    /**
     * Get all campaigns.
     */
    public function getAllCampaigns(int $perPage = 20)
    {
        return $this->campaignRepository->getAll($perPage);
    }

    /**
     * Get draft campaigns.
     */
    public function getDrafts(int $perPage = 20)
    {
        return $this->campaignRepository->getDrafts($perPage);
    }

    /**
     * Get sent campaigns.
     */
    public function getSent(int $perPage = 20)
    {
        return $this->campaignRepository->getSent($perPage);
    }

    /**
     * Get scheduled campaigns.
     */
    public function getScheduled()
    {
        return $this->campaignRepository->getScheduled();
    }

    /**
     * Create a new draft campaign.
     *
     * @param string $title
     * @param string $content
     * @return array<string, bool|NewsletterCampaign>
     */
    public function createDraft(string $title, string $content): array
    {
        try {
            $campaign = $this->campaignRepository->create([
                'title' => $title,
                'content' => $content,
                'status' => NewsletterCampaign::DRAFT,
            ]);

            return [
                'success' => true,
                'message' => 'Brouillon créé avec succès.',
                'data' => $campaign,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du brouillon.',
            ];
        }
    }

    /**
     * Update a campaign (only drafts can be updated).
     *
     * @param int $id
     * @param string $title
     * @param string $content
     * @return array<string, bool|string>
     */
    public function updateDraft(int $id, string $title, string $content): array
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campagne non trouvée.'];
        }

        if (!$campaign->isDraft()) {
            return ['success' => false, 'message' => 'Seuls les brouillons peuvent être mis à jour.'];
        }

        try {
            $this->campaignRepository->update($id, [
                'title' => $title,
                'content' => $content,
            ]);

            return ['success' => true, 'message' => 'Campagne mise à jour avec succès.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la campagne.'];
        }
    }

    /**
     * Get campaign by ID.
     *
     * @param int $id
     * @return NewsletterCampaign|null
     */
    public function getCampaign(int $id): ?NewsletterCampaign
    {
        return $this->campaignRepository->find($id);
    }

    /**
     * Delete a campaign (only drafts can be deleted).
     *
     * @param int $id
     * @return array<string, bool|string>
     */
    public function deleteCampaign(int $id): array
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campagne non trouvée.'];
        }

        if (!$campaign->isDraft()) {
            return ['success' => false, 'message' => 'Seuls les brouillons peuvent être supprimés.'];
        }

        try {
            $this->campaignRepository->delete($id);
            return ['success' => true, 'message' => 'Campagne supprimée avec succès.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression de la campagne.'];
        }
    }

    /**
     * Send campaign now.
     *
     * @param int $id
     * @return array<string, bool|string|int>
     */
    public function sendNow(int $id): array
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campagne non trouvée.'];
        }

        try {
            // Get all verified subscribers
            $subscribers = Newsletter::whereNotNull('verified_at')
                ->whereNull('deleted_at')
                ->select('id', 'email')
                ->get();

            if ($subscribers->isEmpty()) {
                return ['success' => false, 'message' => 'Aucun abonné vérifié trouvé.'];
            }

            // TODO: Send emails using mail service
            // For now, just mark as sent
            $this->campaignRepository->markAsSent($id, $subscribers->count());

            return [
                'success' => true,
                'message' => "Campagne envoyée à {$subscribers->count()} abonnés.",
                'sent_count' => $subscribers->count(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi de la campagne.'];
        }
    }

    /**
     * Schedule campaign for later.
     * Allows scheduling both draft and already scheduled campaigns.
     *
     * @param int $id
     * @param \DateTime $scheduledAt
     * @return array<string, bool|string>
     */
    public function schedule(int $id, \DateTime $scheduledAt): array
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campagne non trouvée.'];
        }

        // Allow both draft and scheduled campaigns to be scheduled
        if (!$campaign->isDraft() && !$campaign->isScheduled()) {
            return ['success' => false, 'message' => 'Seuls les brouillons ou les campagnes programmées peuvent être programmés.'];
        }

        try {
            $this->campaignRepository->markAsScheduled($id, $scheduledAt);
            $action = $campaign->isScheduled() ? 'mise à jour' : 'programmée';
            return ['success' => true, 'message' => "Campagne {$action} avec succès."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la programmation de la campagne.'];
        }
    }

    /**
     * Cancel scheduled campaign and revert to draft.
     *
     * @param int $id
     * @return array<string, bool|string>
     */
    public function cancelSchedule(int $id): array
    {
        $campaign = $this->campaignRepository->find($id);

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campagne non trouvée.'];
        }

        if (!$campaign->isScheduled()) {
            return ['success' => false, 'message' => 'Seules les campagnes programmées peuvent être annulées.'];
        }

        try {
            $this->campaignRepository->update($id, [
                'status' => NewsletterCampaign::DRAFT,
                'scheduled_at' => null,
            ]);
            return ['success' => true, 'message' => 'Programmation annulée. La campagne a été rétablie en brouillon.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'annulation de la programmation.'];
        }
    }
    /**
     * Calculate subscribers count for a campaign.
     * Returns 0 if draft, otherwise returns count of verified newsletter subscribers.
     *
     * @param NewsletterCampaign $campaign
     * @return int
     */
    public function getSubscribersCount(NewsletterCampaign $campaign): int
    {
        // Always return 0 for draft campaigns
        if ($campaign->isDraft()) {
            return 0;
        }

        // For scheduled/sent campaigns, count verified subscribers
        return Newsletter::whereNotNull('verified_at')
            ->whereNull('deleted_at')
            ->count();
    }
}