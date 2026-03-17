<?php

namespace App\Mail;

use App\Mail\Concerns\ProvidesEmailLayoutData;
use App\Models\Simulation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SimulationReportMail extends Mailable implements ShouldQueue
{
    use ProvidesEmailLayoutData, Queueable, SerializesModels;

    public function __construct(public Simulation $simulation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: 'REVESTA - Compte-rendu de votre projet immobilier',
        );
    }

    public function content(): Content
    {
        $simulation = $this->simulation->loadMissing(['user', 'ad.images', 'works', 'aids']);

        return new Content(
            view: 'emails.simulation-report',
            with: array_merge(
                $this->emailLayoutData(),
                ['simulation' => $simulation]
            )
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
