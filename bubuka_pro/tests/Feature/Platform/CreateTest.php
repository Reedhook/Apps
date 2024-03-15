<?php

/** Тестирования метода create, контроллера CreateController of Platform */

namespace Tests\Feature\Platform;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
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
     * Тестирование метода CreateController@create()
     * @test
     */
    public function created_new_records()
    {
        $this->withoutExceptionHandling();

        // Подготовка данных для создания новой записи
        $data = [
            'name' => fake()->word(),
        ];

        // Отправка POST-запроса для создания новой записи
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/platforms', $data);

        // Проверка статуса ответа и наличия новой записи в базе данных
        $response->assertStatus(201);
        $this->assertDatabaseCount('platforms', 1);

        $response = $response->json();
        // Проверка соответствия имени возвращенной записи и отправленных данных
        $this->assertEquals($data['name'], $response['body']['platform']['name']);
    }

    /**
     * Тестирование валидации поля 'name' при добавлении платформ, StoreRequest: на пустоту
     * @test
     */
    public function attribute_name_is_required_for_storing_platform()
    {
        // Подготовка данных с пустым именем
        $data = [
            'name' => '',
        ];

        // Отправка POST-запроса для создания новой записи с пустым именем
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/platforms', $data);

        // Проверка статуса ответа и наличия ошибки валидации
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        // Подготовка данных для создания новой записи
        $data = [
            'name' => fake()->word(),
        ];

        // Отправка POST-запроса для создания новой записи без JWT токена
        $response = $this->post('api/platforms', $data);

        // Проверка статуса ответа на отсутствие аутентификации
        $response->assertStatus(401);
    }
}
