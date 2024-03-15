<?php

/** Тестирование контроллера RegisterController, метода create, для создания новых пользователей */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    /**
     * Тестирование метода  RegisterController@create()
     * @test
     */
    public function test_create_new_user_by_admin()
    {
        $this->withoutExceptionHandling();

        $data = [
            'email' => fake()->unique()->safeEmail(),
            'is_admin' => false
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);
        $response->assertStatus(201);

        $this->assertDatabaseCount('users', 2);

        $user = User::find(2);

        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['is_admin'], $user->is_admin);
    }

    /**
     * Тестирование валидации поля 'email' при создании новых пользователей RegisterRequest: на пустоту
     * @test
     */
    public function attribute_email_is_required_for_storing_user()
    {
        $data = [
            'email' => ' '
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);
        $response->assertStatus(422);
    }

    /**
     * Тестирование валидации поля 'email' при создании новых пользователей RegisterRequest: на тип данных
     * @test
     */
    public function attribute_email_should_be_string_for_storing_user()
    {
        $data = [
            'email' => 12345
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);

        $response->assertStatus(422);
    }

    /**
     * Тестирование валидации поля 'email' при создании новых пользователей RegisterRequest: на формат email
     * @test
     */
    public function attribute_email_should_be_valid_email_for_storing_user()
    {
        $data = [
            'email' => 'invalid_email'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);

        $response->assertStatus(422);
    }

    /**
     * Тестирование валидации поля 'email' при создании новых пользователей RegisterRequest: на максимальную длину
     * @test
     */
    public function attribute_email_should_not_exceed_max_length_for_storing_user()
    {
        $data = [
            'email' => 'a'.str_repeat('b', 255)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);

        $response->assertStatus(422);
    }

    /**
     * Тестирование валидации поля 'email' при создании новых пользователей RegisterRequest: на уникальность
     * @test
     */
    public function attribute_email_should_be_unique_for_storing_user()
    {
        $data = [
            'email' => $this->user->email
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);

        $response->assertStatus(422);
    }

    /**
     * Тестирование валидации поля 'is_admin' при создании новых пользователей RegisterRequest: на тип данных
     * @test
     */
    public function attribute_is_admin_should_be_boolean_for_storing_user()
    {
        $data = [
            'is_admin' => 'not_a_boolean'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/auth/registration', $data);

        $response->assertStatus(422);
    }

    /**
     * Тестирование исключения Forbidden при создании новых пользователей
     * @test
     */
    public function test_user_to_role()
    {
        $user = User::factory()->create();
        $userToken = JWTAuth::fromUser($user);

        $data = [
            'email' => fake()->unique()->safeEmail(),
            'is_admin' => false
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken,
            'Accept'=>'application/json'
        ])->post('api/auth/registration', $data);

        $response->assertStatus(403);
    }

    /**
     * Тестирование исключения Unauthorized при создании новых пользователей(для случаев попыток запроса без токена, либо с просроченным токеном)
     * @test
     */
    public function test_user_to_user()
    {
        $response = $this->post('/api/auth/registration');
        $response->assertStatus(401);
    }

}
