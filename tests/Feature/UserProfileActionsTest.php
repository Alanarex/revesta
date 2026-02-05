<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_toggle_activate()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('users.toggle-activate', $user))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_toggle_other_user()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->role()->associate($role);
        $admin->save();

        $other = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-activate', $other))
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_owner_can_reset_password()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('users.reset-password', $user), ['password' => 'newpassword123'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_reset_other_password()
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->role()->associate($role);
        $admin->save();

        $other = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $other), ['password' => 'adminsetpass'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_owner_can_delete_account()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('users.destroy', $user))
            ->assertStatus(302);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
