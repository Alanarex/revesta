<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public NewsletterCampaign $campaign,
        public Newsletter $subscriber,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->title,
            from: config('mail.from.address') ?? 'noreply@' . config('app.domain', 'example.com'),
            replyTo: [config('mail.reply_to.address') ?? null],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'title' => $this->campaign->title,
                'content' => $this->campaign->content,
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['email' => $this->subscriber->email]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
