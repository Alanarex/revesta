<?php

namespace App\Mail;

use App\Mail\Concerns\ProvidesEmailLayoutData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, ProvidesEmailLayoutData;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $userName,
        public string $resetUrl,
        public int $expirationMinutes = 15,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: __('Réinitialisation de votre mot de passe - ') . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: array_merge(
                $this->emailLayoutData(),
                [
                    'userName' => $this->userName,
                    'resetUrl' => $this->resetUrl,
                    'expirationMinutes' => $this->expirationMinutes,
                ]
            ),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
