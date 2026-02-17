<?php

namespace Tests\Feature\Controllers\Users;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * User Controller Show & Update Routes Tests
 *
 * Tests user profile display and user profile updates
 */
class UserControllerShowUpdateTest extends TestCase
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

    // ============================================================================
    // EDIT ROUTE TESTS
    // ============================================================================

    /**
     * Test edit redirects to show
     */
    public function test_edit_redirects_to_show()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $this->regularUser));

        $response->assertRedirect(route('admin.users.show', $this->regularUser));
    }

    // ============================================================================
    // SHOW ROUTE TESTS
    // ============================================================================

    /**
     * Test show page loads
     */
    public function test_show_page_loads()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
    }

    /**
     * Test show requires authentication
     */
    public function test_show_requires_authentication()
    {
        $this->get(route('admin.users.show', $this->regularUser))
            ->assertRedirect(route('login'));
    }

    /**
     * Test show allows admin to view any user
     */
    public function test_show_allows_admin_to_view_any_user()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->regularUser));

        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    /**
     * Test show allows user to view own profile
     */
    public function test_show_allows_user_to_view_own_profile()
    {
        $user = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($user)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200);
    }

    /**
     * Test show prevents user from viewing others
     */
    public function test_show_prevents_user_from_viewing_others()
    {
        $user1 = User::factory()->create(['role_id' => $this->userRole->id]);
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)
            ->get(route('admin.users.show', $user2));

        $response->assertStatus(403);
    }

    /**
     * Test show passes user data
     */
    public function test_show_passes_user_data()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->regularUser));

        $response->assertViewHas('user');
        $this->assertEquals($this->regularUser->id, $response->viewData('user')->id);
    }

    /**
     * Test show passes permission flags
     */
    public function test_show_passes_permission_flags()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->regularUser));

        $response->assertViewHas('isAdmin', true);
        $response->assertViewHas('canEdit', true);
        $response->assertViewHas('isViewingOwn', false);
    }

    /**
     * Test show includes breadcrumbs
     */
    public function test_show_includes_breadcrumbs()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->regularUser));

        $breadcrumbs = $response->viewData('breadcrumbs');
        $this->assertCount(3, $breadcrumbs);
        $this->assertEquals($this->regularUser->full_name, $breadcrumbs[2]['label']);
    }

    // ============================================================================
    // UPDATE ROUTE TESTS
    // ============================================================================

    /**
     * Test update requires authentication
     */
    public function test_update_requires_authentication()
    {
        $this->put(route('admin.users.update', $this->regularUser), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $this->regularUser->email,
            'role_id' => $this->regularUser->role_id,
        ])->assertRedirect(route('login'));
    }

    /**
     * Test update allows admin to update any user
     */
    public function test_update_allows_admin_to_update_any_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->putCsrf(route('admin.users.update', $user), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $user->email,
            'role_id' => $user->role_id,
            'bio' => 'Updated bio',
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
            'bio' => 'Updated bio',
        ]);
    }

    /**
     * Test update allows user to update own profile
     */
    public function test_update_allows_user_to_update_own_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putCsrf(route('admin.users.update', $user), [
            'first_name' => 'Updated',
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
        ]);
    }

    /**
     * Test update prevents user from updating others
     */
    public function test_update_prevents_user_from_updating_others()
    {
        $user1 = $this->regularUser;
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->putCsrf(route('admin.users.update', $user2), [
            'first_name' => 'Hacked',
            'last_name' => $user2->last_name,
            'email' => $user2->email,
            'role_id' => $user2->role_id,
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test update prevents privilege escalation
     */
    public function test_update_prevents_privilege_escalation()
    {
        $user = User::factory()->create();
        $originalRole = $user->role_id;

        $response = $this->actingAs($user)->putCsrf(route('admin.users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.show', $user));

        $user->refresh();
        $this->assertEquals($originalRole, $user->role_id);
    }

    /**
     * Test update validates unique email
     */
    public function test_update_validates_unique_email()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($this->admin)->putCsrf(route('admin.users.update', $user1), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $user2->email,
            'role_id' => $user1->role_id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test update allows same email on update
     */
    public function test_update_allows_same_email_on_update()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->putCsrf(route('admin.users.update', $user), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('admin.users.show', $user));
    }
}
