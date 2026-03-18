<?php

namespace App\Mail;

use App\Mail\Concerns\ProvidesEmailLayoutData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use ProvidesEmailLayoutData, Queueable, SerializesModels;

    public string $title;

    public string $content;

    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.newsletter-campaign')
            ->with(array_merge(
                $this->emailLayoutData(),
                [
                    'title' => $this->title,
                    'content' => $this->content,
                ]
            ));
    }
}
