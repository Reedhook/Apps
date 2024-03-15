<?php

/** Тестирование контроллера AuthController, метода refresh, для получения информации о пользователе */

namespace Tests\Feature\Auth;

use App\Models\RefreshTokens;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тестирование метода  AuthController@refresh()
     * @test
     */
    public function test_refresh_token()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $userToken = JWTAuth::fromUser($user);

        $refreshToken = RefreshTokens::factory()->create(['user_id' => $user['id']]);
        $data =[
          'token' => $refreshToken['token']
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken,
        ])->post('api/auth/refresh', $data);

        $response->assertStatus(200);

        $this->assertArrayHasKey('access_token', $response->json());
    }

    /**
     * Тестирование исключения Refresh token не действителен
     * @test
     */
    public function test_is_correct_refresh_token()
    {
        $data = [
            'token' => Str::random(80)
        ];
        $response = $this->post('api/auth/refresh',$data);

        $response->assertStatus(401);
    }
}
