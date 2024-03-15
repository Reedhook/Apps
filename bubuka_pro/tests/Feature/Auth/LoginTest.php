<?php

/** Класс для тестирования метода авторизации login, контроллера AuthController */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginTest extends TestCase
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
    public function user_can_logged()
    {
        $this->withoutExceptionHandling();

        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => Str::random(10),
        ];
        User::factory()
            ->create([
                'email' => $data['email'],
                'password' => $data['password']
            ]);


        $response = $this->post('api/auth/login', $data);

        $response->assertStatus(200);

        $this->assertArrayHasKey('access_token', $response->json());
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на пустоту
     * @test
     */
    public function attribute_email_is_required()
    {
        $data = [
            'email' => ' ',
            'password' => Str::random(10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);
        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на тип данных
     * @test
     */
    public function attribute_email_should_be_string()
    {
        $data = [
            'email' => 12345,
            'password' => Str::random(10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на формат email
     * @test
     */
    public function attribute_email_should_be_valid_email()
    {
        $data = [
            'email' => 'invalid_email',
            'password' => Str::random(10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на максимальную длину
     * @test
     */
    public function attribute_email_should_not_exceed_max_length()
    {
        $data = [
            'email' => 'a'.str_repeat('b', 255),
            'password' => Str::random(10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля 'password' при авторизации, LoginRequest: на пустоту
     * @test
     */
    public function attribute_password_is_required()
    {
        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => ''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);
        $response->assertStatus(422);
        $response->assertInvalid('password');
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на тип данных
     * @test
     */
    public function attribute_password_should_be_string()
    {
        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => 12345
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('password');
    }

    /**
     * Тестирование валидации поля 'email' при авторизации, LoginRequest: на максимальную длину
     * @test
     */
    public function attribute_password_should_not_exceed_max_length()
    {
        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => 'a'.str_repeat('b', 10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('password');
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_to_user()
    {
        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => Str::random(10)
        ];
        $response = $this->post('api/auth/login', $data);

        $response->assertStatus(401);
    }

}
