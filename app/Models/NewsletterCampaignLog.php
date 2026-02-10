<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterCampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'sent_at',
        'opened',
        'clicked',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened' => 'boolean',
        'clicked' => 'boolean',
    ];

    /**
     * Get the campaign this log belongs to.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NewsletterCampaign::class, 'campaign_id');
    }

    /**
     * Get the subscriber this log is for.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class, 'subscriber_id');
    }
}
