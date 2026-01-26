<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\RegistrationService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegistrationServiceTest extends TestCase
{
    public function test_register_and_login_creates_user_and_logs_in(): void
    {
        $data = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'reg@example.com',
            'password' => 'secret',
        ];

        $service = app(RegistrationService::class);

        $user = $service->registerAndLogin($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'reg@example.com']);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }
}
