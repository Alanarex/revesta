<?php

namespace Tests\Feature\Controllers\Users;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * User Controller Index & List Routes Tests
 *
 * Tests the index page listing and JSON API list endpoint
 */
class UserControllerIndexTest extends TestCase
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
}
