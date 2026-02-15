<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/auth/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Mail::fake();
        
        $user = User::factory()->create();

        $response = $this->post('/auth/forgot-password', ['email' => $user->email]);

        $response->assertStatus(302);
        
        // Verify password reset mail was queued
        Mail::assertQueued(PasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
        
        // Verify a reset token was created in the database
        $this->assertNotNull(
            DB::table('password_reset_tokens')->where('email', $user->email)->first()
        );
    }

    public function test_reset_password_link_not_created_for_nonexistent_email(): void
    {
        $response = $this->post('/auth/forgot-password', ['email' => 'nonexistent@example.com']);

        $response->assertStatus(302);
        // Verify no token was created for this email
        $this->assertNull(
            DB::table('password_reset_tokens')->where('email', 'nonexistent@example.com')->first()
        );
    }
}
