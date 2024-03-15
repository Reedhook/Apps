<?php

/** Класс для тестирования метода авторизации login, контроллера AuthController */

namespace Tests\Feature\Auth;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);

    }

    /**
     * Тестирование метода  ResetPasswordController@reset()
     * @test
     */
    public function user_can_reset_password()
    {
        $this->withoutExceptionHandling();

        $token = Str::random(60);
        $scryptToken = Hash::make($token);
        PasswordResetToken::factory()->create(['email'=>$this->user['email'],'token' => $scryptToken]);
        $password = fake()->password(8);
        $data = [
            'email' => $this->user['email'],
            'password' => $password,
            'password_confirmation' => $password,
            'token' => $token
        ];
        $response = $this->post('api/auth/reset', $data);

        $response->assertStatus(302);
    }

    /**
     * Тестирование валидации полей: на существование в запросе
     * @test
     */
    public function test_validation_required_attributes()
    {
        $data = [
            'email' => '',
            'password' => '',
            'token' => '',
        ];
        $response = $this->post('api/auth/reset', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The email field is required.'],
                'token' => ['The token field is required.'],
                'password' => ['The password field is required.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации полей email и password: на тип данных
     * @test
     */
    public function test_validation_string_email_password()
    {
        $data = [
            'email' => 123456,
            'password' => 12345789,
            'password_confirmation' =>12345789
        ];
        $response = $this->post('api/auth/reset', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The email field must be a string.'],
                'password' => ['The password field must be a string.',]
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
            'email' => 'invalid email',
        ];
        $response = $this->post('api/auth/reset', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The email field must be a valid email address.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля email: на существование в базе данных
     * @test
     */
    public function test_validation_Invalid_email()
    {
        $data = [
            'email' => fake()->safeEmail()
        ];
        $response = $this->post('api/auth/reset', $data);
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'email' => ['The selected email is invalid.'],
            ],
        ]);
        $response->assertInvalid('email');
    }

    /**
     * Тестирование валидации поля password: на подтверждение пароля
     * @test
     */
    public function test_validation_confirmed_password()
    {
        $data = [
            'token' => str::random(60),
            'email' => fake()->safeEmail(),
            'password' => fake()->password,
            'password_confirmation' => fake()->password, // Неправильное подтверждение пароля
        ];

        $response = $this->post('api/auth/reset', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'password' => ['The password field confirmation does not match.'],
            ],
        ]);
    }
}
