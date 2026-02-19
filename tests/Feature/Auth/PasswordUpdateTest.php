<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('password.update'))
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('password.update'));

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_password_update_sends_notification_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->from(route('password.update'))
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        Mail::assertQueued(PasswordChangedMail::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id;
        });
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('password.update'))
            ->put(route('password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect(route('password.update'));
    }
}
