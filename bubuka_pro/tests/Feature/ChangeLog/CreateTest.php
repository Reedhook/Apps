<?php

/** Тестирования метода create, контроллера CreateController of ChangeLog */

namespace Tests\Feature\ChangeLog;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected User|Collection|Model $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Каждый раз при запуске теста из этого файла создаем нового пользователя для JWT авторизации
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    /**
     * Тестирование метода  CreateController@create()
     * @test
     */
    public function created_new_records()
    {
        // Отключаем обработку исключении
        $this->withoutExceptionHandling();

        // Создаем ложные данные
        $data = [
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];

        // Отправляем запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/changes', $data);

        // Проверяем статус ответа
        $response->assertStatus(201);

        // Проверяем количество записей в фальшивом бд
        $this->assertDatabaseCount('changes', 1);

        // Конвертируем в json
        $response = $response->json();

        // Проверяем на идентичность отправленных и полученных данных
        $this->assertEquals($data['changes'], $response['body']['changelog']['changes']);
        $this->assertEquals($data['news'], $response['body']['changelog']['news']);
    }

    /**
     * Тестирование валидации полей при создании тугриков, StoreRequest: на пустоту
     * @test
     */
    public function test_validation_required_attributes()
    {
        // Отправляем пустые строки
        $data = [
            'changes' => '',
            'news' => ''
        ];

        // Отправляем запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/changes', $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field is required when news is not present.'],
                'news' => ['The news field is required when changes is not present.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации полей при создании тугриков StoreRequest: на тип данных
     * @test
     */
    public function test_validation_type_data_attributes()
    {
        // Отправка числа
        $data = [
            'changes' => 12345,
            'news' => 12345
        ];

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/changes', $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field must be a string.'],
                'news' => ['The news field must be a string.']
            ],
        ]);
    }

    /**
     * Тестирование валидации полей при создании тугриков StoreRequest: на максимальную длину
     * @test
     */
    public function test_validation_max_length_attributes()
    {
        // Отправка 256 символов
        $data = [
            'changes' => 'a'.str_repeat('b', 255),
            'news' =>  'a'.str_repeat('b', 255)
        ];

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/changes', $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field must not be greater than 255 characters.'],
                'news' => ['The news field must not be greater than 255 characters.']
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_jwt_user()
    {
        $data = [
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];

        // Запрос на endpoint без JWT токена
        $response = $this->post('api/changes', $data);

        // Проверка статуса ответа
        $response->assertStatus(401);
    }
}
