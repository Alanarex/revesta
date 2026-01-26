<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthServiceTest extends TestCase
{
    public function test_authenticate_logs_in_user_on_success(): void
    {
        $password = 'mypassword';
        $user = User::factory()->create(['password' => bcrypt($password)]);

        $service = app(AuthService::class);

        $service->authenticate(['email' => $user->email, 'password' => $password], false);

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_authenticate_throws_on_invalid_credentials(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create(['password' => bcrypt('password')]);

        $service = app(AuthService::class);

        $service->authenticate(['email' => $user->email, 'password' => 'bad'], false);
    }
}
