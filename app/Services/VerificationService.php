<?php

namespace App\Services;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerificationService
{
    public function sendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    public function markVerified(EmailVerificationRequest $request): bool
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }
}
