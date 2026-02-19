<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'status',
        'scheduled_at',
        'sent_at',
        'sent_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Status constants
    public const DRAFT = 'draft';

    public const SENT = 'sent';

    public const SCHEDULED = 'scheduled';

    /**
     * Get all send logs for this campaign.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterCampaignLog::class, 'campaign_id');
    }

    /**
     * Check if campaign is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    /**
     * Check if campaign is sent.
     */
    public function isSent(): bool
    {
        return $this->status === self::SENT;
    }

    /**
     * Check if campaign is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === self::SCHEDULED;
    }
}
