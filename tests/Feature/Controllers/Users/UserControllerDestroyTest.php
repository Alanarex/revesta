<?php

namespace Tests\Feature\Controllers\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * User Controller Destroy Route Tests
 *
 * Tests user deletion functionality
 */
class UserControllerDestroyTest extends TestCase
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
     * Test destroy requires authentication
     */
    public function test_destroy_requires_authentication()
    {
        $user = User::factory()->create();

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('login'));
    }

    /**
     * Test destroy allows admin to delete any user
     */
    public function test_destroy_allows_admin_to_delete_any_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteCsrf(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * Test destroy allows user to delete own account
     */
    public function test_destroy_allows_user_to_delete_own_account()
    {
        $user = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($user)
            ->deleteCsrf(route('admin.users.destroy', $user));

        $response->assertStatus(302);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * Test destroy prevents user from deleting others
     */
    public function test_destroy_prevents_user_from_deleting_others()
    {
        $user1 = User::factory()->create(['role_id' => $this->userRole->id]);
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)
            ->deleteCsrf(route('admin.users.destroy', $user2));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }

    /**
     * Test destroy soft deletes the user
     */
    public function test_destroy_soft_deletes_the_user()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $this->actingAs($this->admin)
            ->deleteCsrf(route('admin.users.destroy', $user));

        $this->assertNull(User::find($userId));
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    /**
     * Test destroy returns JSON for AJAX requests
     */
    public function test_destroy_returns_json_for_ajax_requests()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteCsrf(route('admin.users.destroy', $user), [], [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
