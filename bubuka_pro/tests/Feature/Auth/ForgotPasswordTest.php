<?php

/** Класс для тестирования метода авторизации login, контроллера AuthController */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;
    protected User|Collection|Model $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    /**
     * Тестирование метода  AuthController@login()
     * @test
     */
    public function user_can_get_link()
    {
        $this->withoutExceptionHandling();

        $data = [
            'email' => $this->user['email']
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
            'Accept' => 'application/json',
        ])->post('api/auth/forgot', $data);
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response);
    }

    /**
     * Тестирование валидации поля email: на существование в базе данных
     * @test
     */
    public function test_validation_Invalid_email()
    {
        $email = fake()->safeEmail();
        $data = [
            'email' => $email
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
            'Accept' => 'application/json',
        ])->post('api/auth/forgot', $data);
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The selected email is invalid.'],
            ],
        ]);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля email: на тип данных
     * @test
     */
    public function test_validation_string_email()
    {
        $email = 12345;
        $data = [
            'email' => $email
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
            'Accept' => 'application/json',
        ])->post('api/auth/forgot', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The email field must be a string.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля email: на существование в запросе
     * @test
     */
    public function test_validation_required_email()
    {
        $email = '';
        $data = [
            'email' => $email
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
            'Accept' => 'application/json',
        ])->post('api/auth/forgot', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The email field is required.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля email: на формат email
     * @test
     */
    public function test_validation_email_email()
    {
        $data = [
            'email' => 'invalid_email'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
            'Accept' => 'application/json',
        ])->post('api/auth/forgot', $data);

        $response->assertStatus(422);
    }
}
