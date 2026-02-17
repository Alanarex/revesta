<?php

namespace App\Mail;

use App\Mail\Concerns\ProvidesEmailLayoutData;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, ProvidesEmailLayoutData;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user)
    {
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
            to: [new Address($this->user->email)],
            subject: __('Confirmations de changement de mot de passe - ') . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: array_merge(
                $this->emailLayoutData(),
                [
                    'userName' => $this->user->first_name,
                    'userEmail' => $this->user->email,
                ]
            ),
        );
    }
}
