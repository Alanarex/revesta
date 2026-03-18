<?php

namespace App\Mail\Concerns;

trait ProvidesEmailLayoutData
{
    protected function emailLayoutData(): array
    {
        return [
            'contactEmail' => config('mail.contact_email', env('CONTACT_EMAIL', 'contact@revesta.com')),
            'appName' => config('app.name'),
            'appUrl' => config('app.url'),
        ];
    }
}
