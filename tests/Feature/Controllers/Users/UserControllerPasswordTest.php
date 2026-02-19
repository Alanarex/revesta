<?php

namespace Tests\Feature\Controllers\Users;

use App\Mail\PasswordResetMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * User Controller Password Reset Route Tests
 *
 * Tests password reset functionality for admin reset and user self-update
 */
class UserControllerPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    private Role $adminRole;

    private Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator']
        );

        $this->userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'User']
        );

        $this->admin = $this->createAdminUser();
        $this->regularUser = User::factory()->create(['role_id' => $this->userRole->id]);
    }

    /**
     * Test reset password requires authentication
     */
    public function test_reset_password_requires_authentication()
    {
        $this->post(route('admin.users.reset-password', $this->regularUser), [])
            ->assertRedirect(route('login'));
    }

    /**
     * Test reset password allows admin
     */
    public function test_reset_password_allows_admin()
    {
        Mail::fake();

        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $this->assertNotNull($user->email_verified_at);

        $response = $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $user), []);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Check email was queued
        Mail::assertQueued(PasswordResetMail::class);

        // Check user email was unverified
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test reset password sends reset email
     */
    public function test_reset_password_sends_reset_email()
    {
        Mail::fake();

        $user = User::factory()->create(['role_id' => $this->userRole->id]);

        $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $user), []);

        Mail::assertQueued(PasswordResetMail::class, function ($mail) use ($user) {
            return $mail->recipientEmail === $user->email;
        });
    }

    /**
     * Test reset password prevents user from resetting others
     */
    public function test_reset_password_prevents_user_from_resetting_others()
    {
        $user1 = User::factory()->create(['role_id' => $this->userRole->id]);
        $user2 = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($user1)
            ->postCsrf(route('admin.users.reset-password', $user2), []);

        $response->assertStatus(403);
    }

    /**
     * Test reset password prevents admin from resetting own password
     */
    public function test_reset_password_prevents_admin_from_resetting_own()
    {
        $response = $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $this->admin), []);

        $response->assertStatus(403);
    }

    /**
     * Test reset password prevents user from resetting own password
     */
    public function test_reset_password_prevents_user_from_resetting_own()
    {
        $user = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($user)
            ->postCsrf(route('admin.users.reset-password', $user), []);

        $response->assertStatus(403);
    }

    /**
     * Test reset password unverifies email
     */
    public function test_reset_password_unverifies_email()
    {
        Mail::fake();

        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $this->assertTrue($user->hasVerifiedEmail());

        $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $user), []);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }
}
