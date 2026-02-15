<?php

namespace Tests\Feature\Controllers;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

/**
 * User Controller Feature Tests
 *
 * Tests the HTTP controller layer for user management including:
 * - CRUD operations (create, read, update, delete)
 * - Index and list (JSON) endpoints
 * - Form submission and validation
 * - Authorization checks
 * - Flash messages and redirects
 * - View rendering
 * - Password reset functionality
 *
 * SAFE TO RUN: RefreshDatabase uses in-memory SQLite database (:memory:)
 * configured in phpunit.xml. This does NOT affect your local database.
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator']
        );

        $this->admin = $this->createAdminUser();
        $this->regularUser = User::factory()->create();
    }

    // ============================================================================
    // INDEX ROUTE TESTS
    // ============================================================================

    /**
     * Test index page loads with admin user
     */
    public function test_index_page_loads()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    /**
     * Test index requires authentication
     */
    public function test_index_requires_authentication()
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    /**
     * Test index requires admin role
     */
    public function test_index_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /**
     * Test index displays correct title
     */
    public function test_index_displays_correct_title()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertViewHas('title', 'Gestion des utilisateurs');
    }

    /**
     * Test index displays total count
     */
    public function test_index_displays_total_count()
    {
        User::factory(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertViewHas('totalCount');
        $this->assertGreaterThanOrEqual(5, $response->viewData('totalCount'));
    }

    /**
     * Test index includes breadcrumbs
     */
    public function test_index_includes_breadcrumbs()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertViewHas('breadcrumbs');
        $breadcrumbs = $response->viewData('breadcrumbs');
        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals('Utilisateurs', $breadcrumbs[1]['label']);
    }

    // ============================================================================
    // LIST API ROUTE TESTS
    // ============================================================================

    /**
     * Test list endpoint returns JSON
     */
    public function test_list_returns_json_response()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.list'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'total', 'per_page', 'current_page']);
    }

    /**
     * Test list requires admin role
     */
    public function test_list_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.users.list'));

        $response->assertStatus(403);
    }

    /**
     * Test list returns users with correct structure
     */
    public function test_list_returns_users_with_correct_structure()
    {
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0612345678',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.list'));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('full_name', $data[0]);
        $this->assertArrayHasKey('email', $data[0]);
        $this->assertArrayHasKey('actions', $data[0]);
    }

    /**
     * Test list search functionality
     */
    public function test_list_searches_by_name_or_email()
    {
        User::factory()->create(['email' => 'john@example.com', 'first_name' => 'John']);
        User::factory()->create(['email' => 'jane@example.com', 'first_name' => 'Jane']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.list') . '?search=jane');

        $data = $response->json('data');
        $this->assertLessThanOrEqual(2, count($data));
    }

    /**
     * Test list pagination
     */
    public function test_list_respects_pagination()
    {
        User::factory(15)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.list') . '?per_page=10&page=1');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals(10, $json['per_page']);
    }

    /**
     * Test list returns correct admin actions
     */
    public function test_list_admin_sees_edit_and_delete_actions()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.list'));

        $data = $response->json('data');
        $actions = collect($data)->pluck('actions')->first();
        $actionTypes = collect($actions)->pluck('type')->all();

        $this->assertContains('edit', $actionTypes);
        $this->assertContains('delete', $actionTypes);
    }

    // ============================================================================
    // CREATE ROUTE TESTS
    // ============================================================================

    /**
     * Test create page loads
     */
    public function test_create_page_loads()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
    }

    /**
     * Test create requires authentication
     */
    public function test_create_requires_authentication()
    {
        $this->get(route('admin.users.create'))
            ->assertRedirect(route('login'));
    }

    /**
     * Test create requires admin role
     */
    public function test_create_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.users.create'));

        $response->assertStatus(403);
    }

    /**
     * Test create displays correct title
     */
    public function test_create_displays_correct_title()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertViewHas('title', 'Créer un utilisateur');
    }

    /**
     * Test create passes roles options
     */
    public function test_create_passes_roles_options()
    {
        Role::factory()->create(['name' => 'editor']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertViewHas('rolesOptions');
        $this->assertNotEmpty($response->viewData('rolesOptions'));
    }

    /**
     * Test create passes status options
     */
    public function test_create_passes_status_options()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertViewHas('civilStatuses');
        $response->assertViewHas('familyStatuses');
    }

    /**
     * Test create includes breadcrumbs
     */
    public function test_create_includes_breadcrumbs()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $breadcrumbs = $response->viewData('breadcrumbs');
        $this->assertCount(3, $breadcrumbs);
        $this->assertEquals('Créer', $breadcrumbs[2]['label']);
    }

    // ============================================================================
    // STORE ROUTE TESTS
    // ============================================================================

    /**
     * Test store creates user with valid data
     */
    public function test_store_creates_user_with_valid_data()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '0612345678',
            'role_id' => $this->adminRole->id,
            'civil_status' => 'married',
            'family_status' => '2_children',
        ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '0612345678',
        ]);
    }

    /**
     * Test store redirects to index on success
     */
    public function test_store_redirects_to_index_on_success()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test store sends verification email
     */
    public function test_store_sends_verification_email()
    {
        Mail::fake();

        $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $this->adminRole->id,
        ]);

        Mail::assertQueued(\App\Mail\EmailVerificationMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    /**
     * Test store requires unique email
     */
    public function test_store_requires_unique_email()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $this->regularUser->email,
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test store requires valid email
     */
    public function test_store_requires_valid_email()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'not-an-email',
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test store requires required fields
     */
    public function test_store_requires_required_fields()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'role_id']);
    }

    /**
     * Test store requires existing role
     */
    public function test_store_requires_existing_role()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => 99999,
        ]);

        $response->assertSessionHasErrors('role_id');
    }

    /**
     * Test store validates civil status enum
     */
    public function test_store_validates_civil_status()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $this->adminRole->id,
            'civil_status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('civil_status');
    }

    /**
     * Test store validates family status enum
     */
    public function test_store_validates_family_status()
    {
        $response = $this->actingAs($this->admin)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $this->adminRole->id,
            'family_status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('family_status');
    }

    /**
     * Test store requires admin role
     */
    public function test_store_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser)->postCsrf(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertStatus(403);
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
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200);
    }

    /**
     * Test show prevents user from viewing others
     */
    public function test_show_prevents_user_from_viewing_others()
    {
        $user1 = User::factory()->create();
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

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

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

        $response->assertStatus(200);
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
        $user1 = User::factory()->create();
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

        $response = $this->actingAs($user)->putCsrf(route('admin.users.update', $user), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role_id' => $this->adminRole->id,
        ]);

        $response->assertStatus(403);
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

        $response->assertStatus(200);
    }

    // ============================================================================
    // DESTROY ROUTE TESTS
    // ============================================================================

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
        $user = User::factory()->create();

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
        $user1 = User::factory()->create();
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

    // ============================================================================
    // RESET PASSWORD ROUTE TESTS
    // ============================================================================

    /**
     * Test reset password requires authentication
     */
    public function test_reset_password_requires_authentication()
    {
        $this->post(route('admin.users.reset-password', $this->regularUser), [
            'password' => 'newpassword123',
        ])->assertRedirect(route('login'));
    }

    /**
     * Test reset password allows admin
     */
    public function test_reset_password_allows_admin()
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $response = $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $user), [
                'password' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
    }

    /**
     * Test reset password allows user to reset own
     */
    public function test_reset_password_allows_user_to_reset_own()
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $response = $this->actingAs($user)
            ->postCsrf(route('admin.users.reset-password', $user), [
                'password' => 'newpassword123',
            ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
    }

    /**
     * Test reset password prevents user from resetting others
     */
    public function test_reset_password_prevents_user_from_resetting_others()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $oldPassword = $user2->password;

        $response = $this->actingAs($user1)
            ->postCsrf(route('admin.users.reset-password', $user2), [
                'password' => 'newpassword123',
            ]);

        $response->assertStatus(403);
        $user2->refresh();
        $this->assertEquals($oldPassword, $user2->password);
    }

    /**
     * Test reset password unverifies email
     */
    public function test_reset_password_unverifies_email()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->assertTrue($user->hasVerifiedEmail());

        $this->actingAs($this->admin)
            ->postCsrf(route('admin.users.reset-password', $user), [
                'password' => 'newpassword123',
            ]);

        $user->refresh();
        $this->assertFalse($user->hasVerifiedEmail());
    }
}
