<?php

/** Тестирование контроллера AuthController, метода me, для получения информации о пользователе */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeTest extends TestCase
{
    use RefreshDatabase;

    protected User|Collection|Model $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    /**
     * Тестирование метода  AuthController@me()
     * @test
     */
    public function test_me()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/auth/user');
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertEquals($this->user->email, $responseData['body']['user']['email']);
        $this->assertEquals($this->user->is_admin, $responseData['body']['user']['is_admin']);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_to_user()
    {
        $response = $this->get('api/auth/user');

        $response->assertStatus(401);
    }
}
