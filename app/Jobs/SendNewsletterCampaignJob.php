<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignLog;
use App\Repositories\NewsletterCampaignRepository;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $campaignId;

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
        $this->onQueue('emails');
    }

    public function handle(NewsletterCampaignRepository $repo)
    {
        $campaign = NewsletterCampaign::find($this->campaignId);
        if (!$campaign) {
            return;
        }

        $sent = 0;

        Newsletter::whereNotNull('verified_at')
            ->whereNull('deleted_at')
            ->select(['id', 'email'])
            ->chunkById(500, function ($subscribers) use (&$sent, $campaign, $repo) {
                foreach ($subscribers as $subscriber) {
                    try {
                        Mail::to($subscriber->email)->queue(new NewsletterCampaignMail($campaign->title, $campaign->content));

                        NewsletterCampaignLog::create([
                            'campaign_id' => $campaign->id,
                            'subscriber_id' => $subscriber->id,
                            'sent_at' => now(),
                        ]);

                        $sent++;
                    } catch (\Throwable $e) {
                        // Log and continue
                        // Optionally: record failures for retry/inspection
                    }
                }
            });

        // Mark campaign as sent
        $repo->markAsSent($campaign->id, $sent);
    }
}
