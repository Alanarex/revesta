<?php

namespace Tests\Feature\Controllers\Users;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

/**
 * User Controller Create & Store Routes Tests
 *
 * Tests user creation form display and user creation submission
 */
class UserControllerCreateStoreTest extends TestCase
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
        Role::firstOrCreate(
            ['name' => 'editor'],
            ['display_name' => 'Editor']
        );

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
}
