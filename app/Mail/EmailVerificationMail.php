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
use Illuminate\Support\Facades\URL;

class EmailVerificationMail extends Mailable implements ShouldQueue
{
    use ProvidesEmailLayoutData, Queueable, SerializesModels;

    public function __construct(protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            to: [new Address($this->user->email)],
            subject: 'Vérifiez votre adresse email et définissez votre mot de passe',
        );
    }

    public function content(): Content
    {
        $verificationUrl = URL::temporarySignedRoute(
            'password.set',
            now()->addHours(24),
            ['user' => $this->user->id]
        );

        return new Content(
            view: 'emails.verify-email',
            with: array_merge(
                $this->emailLayoutData(),
                [
                    'userName' => $this->user->first_name,
                    'verificationUrl' => $verificationUrl,
                ]
            ),
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
