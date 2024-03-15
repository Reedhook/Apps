<?php

/** Тестирования метода create, контроллера CreateController of Project */

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
    }

    /**
     * Тестирование метода  CreateController@create()
     * @test
     */
    public function created_new_records()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence() // Генерация случайного предложения
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/projects', $data); // Отправка POST-запроса на создание проекта

        $response->assertStatus(201); // Проверка, что запрос вернул статус 201 (Успешное создание)
        $this->assertDatabaseCount('projects', 1); // Проверка, что в базе данных появилась одна запись

        $response = $response->json();
        $this->assertEquals($data['name'], $response['body']['project']['name']); // Проверка, что имя проекта в ответе соответствует отправленным данным
        $this->assertEquals($data['description'], $response['body']['project']['description']); // Проверка, что описание проекта в ответе соответствует отправленным данным
    }

    /**
     * Тестирование валидации полей 'name' и 'description при создании проекта, StoreRequest: на пустоту
     * @test
     */
    public function attribute_name_is_required_for_storing_project()
    {
        $data = [
            'name' => '', // Пустое значение
            'description' => '',
            'user_id' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/projects', $data); // Отправка POST-запроса на создание проекта с пустыми полями

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'name'
                'description' => ['The description field is required.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'description'
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при создании проекта, StoreRequest: на тип данных
     * @test
     */
    public function attributes_name_and_description_should_be_string_for_storing_project()
    {
        $data = [
            'name' => 12345, // Числовое значение
            'description' => 12345,
            'user_id' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/projects', $data); // Отправка POST-запроса на создание проекта с некорректными типами данных

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must be a string.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'name'
                'description' => ['The description field must be a string.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'description'
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при создании проекта, StoreRequest: на максимальную длину
     * @test
     */
    public function attributes_name_and_description_should_not_exceed_max_length()
    {
        $data = [
            'name' => 'a'.str_repeat('b', 255),
            'description' => 'a'.str_repeat('b', 255),
            'user_id' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/projects',
            $data); // Отправка POST-запроса на создание проекта с данными превышающими максимальную длину

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'name'
                'description' => ['The description field must not be greater than 255 characters.'],
                // Проверка, что получен ответ с ошибкой валидации для поля 'description'
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при создании проекта, StoreRequest: на уникальность
     * @test
     */
    public function attribute_name_should_be_unique_for_storing_project()
    {
        $project = Project::factory()->create(); // Создание проекта
        $data = [
            'name' => $project['name'], // Использование имени существующего проекта
            'description' => 'Для description такого правила нет, но ее надо сюда вписать',
            'user_id' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/projects', $data); // Отправка POST-запроса на создание проекта с уже существующим именем
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name has already been taken.']
                ]
        ]); // Проверка, что запрос вернул статус 422 (Непрошедшая валидацию)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence(), // Генерация случайного предложения
            'user_id' => 1
        ];
        $response = $this->post('api/projects', $data); // Отправка POST-запроса на создание проекта без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }

    /**
     * Тестирование исключения Forbidden
     * @test
     */
    public function test_user_is_admin()
    {
        $user = User::factory()->create(); // Создание пользователя
        $userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence(), // Генерация случайного предложения
            'user_id' => 1
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken, // Передача токена в заголовке запроса
            'Accept' => 'application/json' // Установка заголовка Accept на application/json
        ])->post('api/projects', $data); // Отправка POST-запроса на создание проекта

        $response->assertStatus(403); // Проверка, что запрос вернул статус 403 (Запрещено)
    }
}
