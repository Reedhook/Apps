<?php

/** Класс для тестирования метода авторизации login, контроллера AuthController */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
    }

    /**
     * Тестирование метода  AuthController@login()
     * @test
     */
    public function delete_test()
    {
        $this->withoutExceptionHandling();


        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete('api/auth/user/delete');

        $response->assertStatus(200);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/auth/user/delete');

        $response->assertStatus(401);
    }
}
