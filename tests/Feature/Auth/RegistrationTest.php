<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailVerificationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/auth/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/auth/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);


        // User is created but not authenticated (needs email verification first)
        $this->assertDatabaseHas('users', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        
        // Verify verification email was queued
        Mail::assertQueued(EmailVerificationMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
        
        // Redirects to login page with success message
        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');
    }
}
