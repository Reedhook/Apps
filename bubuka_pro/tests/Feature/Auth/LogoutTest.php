<?php

/** Тестирование контроллера AuthController, метода logout, для деАвторизации */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тестирование метода  AuthController@logout()
     * @test
     */
    public function test_logout_from_system()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $userToken = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken,
        ])->post('api/auth/logout');

        $response->assertStatus(200);

        $this->assertTrue($response->json(['status']));
        $this->assertArrayHasKey('message', $response->json());
    }

    /**
     * Тестирование исключения Unauthorized при деАвторизации
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->post('api/auth/logout');

        $response->assertStatus(401);
    }
}
