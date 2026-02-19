<?php

namespace Tests\Unit\Services;

use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    public function test_register_and_login_creates_user_and_logs_in(): void
    {
        Mail::fake();

        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'reg@example.com',
            'password' => 'secret',
        ];

        $service = app(RegistrationService::class);

        $user = $service->registerAndLogin($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'reg@example.com']);

        // Verify verification email was queued
        Mail::assertQueued(EmailVerificationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
