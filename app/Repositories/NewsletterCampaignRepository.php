<?php

namespace App\Repositories;

use App\Models\NewsletterCampaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsletterCampaignRepository
{
    /**
     * Get all campaigns with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 20): LengthAwarePaginator
    {
        return NewsletterCampaign::select(['id', 'title', 'status', 'sent_count', 'created_at', 'scheduled_at', 'sent_at'])
            ->withCount('logs')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all drafts.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getDrafts(int $perPage = 20): LengthAwarePaginator
    {
        return NewsletterCampaign::where('status', NewsletterCampaign::DRAFT)
            ->select(['id', 'title', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all sent campaigns.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSent(int $perPage = 20): LengthAwarePaginator
    {
        return NewsletterCampaign::where('status', NewsletterCampaign::SENT)
            ->select(['id', 'title', 'sent_count', 'sent_at', 'created_at'])
            ->withCount('logs')
            ->orderBy('sent_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all scheduled campaigns.
     *
     * @return Collection
     */
    public function getScheduled(): Collection
    {
        return NewsletterCampaign::where('status', NewsletterCampaign::SCHEDULED)
            ->select(['id', 'title', 'scheduled_at', 'created_at'])
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    /**
     * Create a new campaign.
     *
     * @param array<string, mixed> $data
     * @return NewsletterCampaign
     */
    public function create(array $data): NewsletterCampaign
    {
        return NewsletterCampaign::create($data);
    }

    /**
     * Update a campaign.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return NewsletterCampaign::findOrFail($id)->update($data);
    }

    /**
     * Find a campaign by ID.
     *
     * @param int $id
     * @return NewsletterCampaign|null
     */
    public function find(int $id): ?NewsletterCampaign
    {
        return NewsletterCampaign::with('logs')->find($id);
    }

    /**
     * Delete a campaign (soft delete).
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $campaign = NewsletterCampaign::findOrFail($id);
        return $campaign->delete();
    }

    /**
     * Mark campaign as sent.
     *
     * @param int $id
     * @param int $sentCount
     * @return bool
     */
    public function markAsSent(int $id, int $sentCount = 0): bool
    {
        return NewsletterCampaign::findOrFail($id)->update([
            'status' => NewsletterCampaign::SENT,
            'sent_at' => now(),
            'sent_count' => $sentCount,
        ]);
    }

    /**
     * Mark campaign as scheduled.
     *
     * @param int $id
     * @param \DateTime $scheduledAt
     * @return bool
     */
    public function markAsScheduled(int $id, \DateTime $scheduledAt): bool
    {
        return NewsletterCampaign::findOrFail($id)->update([
            'status' => NewsletterCampaign::SCHEDULED,
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Get total count of all campaigns.
     *
     * @return int
     */
    public function count(): int
    {
        return NewsletterCampaign::count();
    }

    /**
     * Search campaigns with filtering and sorting.
     *
     * @param string $search Search term for title
     * @param string $sort Column to sort by
     * @param string $direction Sort direction (asc/desc)
     * @param int $perPage Results per page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $search = '', string $sort = 'created_at', string $direction = 'desc', int $perPage = 15)
    {
        $query = NewsletterCampaign::query();

        // Filter by search term
        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Sort with validation
        $validColumns = ['id', 'title', 'status', 'created_at', 'scheduled_at', 'sent_at', 'subscribers_count'];
        $sort = in_array($sort, $validColumns) ? $sort : 'created_at';
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction)->paginate($perPage);
    }
}
