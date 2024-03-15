<?php

/** Тестирования метода create, контроллера CreateController of ReleaseType */

namespace Tests\Feature\ReleaseType;

use App\Models\ReleaseType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); // Создание пользователя
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
    }

    /**
     * Тестирование метода CreateController@create()
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
        ])->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза

        $response->assertStatus(201); // Проверка, что запрос вернул статус 201 (Успешное создание)
        $this->assertDatabaseCount('releases_types', 1); // Проверка, что в базе данных существует одна запись о типе релиза

        $response = $response->json(); // Преобразование ответа в JSON формат
        $this->assertEquals($data['name'], $response['body']['release_type']['name']); // Проверка, что поле 'name' в ответе соответствует отправленным данным
        $this->assertEquals($data['description'], $response['body']['release_type']['description']); // Проверка, что поле 'description' в ответе соответствует отправленным данным
    }

    /**
     * Тестирование валидации поля 'name' при добавлении типа релиза в StoreRequest: проверка на пустоту
     * @test
     */
    public function attribute_name_is_required_for_storing_releases_types()
    {
        $data = [
            'name' => '', // Пустое значение поля 'name'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'], // Проверка, что поле 'name' обязательно для заполнения
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при добавлении типа релиза в StoreRequest: проверка на тип данных
     * @test
     */
    public function attributes_name_and_description_should_be_string_for_storing_releases_types()
    {
        $data = [
            'name' => 12345, // Передача числового значения вместо строки
            'description' => 12345 // Передача числового значения вместо строки
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must be a string.'], // Проверка, что поле 'name' должно быть строкой
                'description' => ['The description field must be a string.'], // Проверка, что поле 'description' должно быть строкой
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при добавлении типа релиза в StoreRequest: проверка на максимальную длину
     * @test
     */
    public function attributes_name_and_description_should_not_exceed_max_length()
    {
        $data = [
            'name' => 'a'.str_repeat('b', 255), // Передача значения, превышающего максимальную длину
            'description' => 'a'.str_repeat('b', 255) // Передача значения, превышающего максимальную длину
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'], // Проверка, что поле 'name' не должно превышать 255 символов
                'description' => ['The description field must not be greater than 255 characters.'], // Проверка, что поле 'description' не должно превышать 255 символов
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при добавлении типа релиза в StoreRequest: проверка на уникальность
     * @test
     */
    public function attribute_name_should_be_unique_for_storing_releases_types()
    {
        $project = ReleaseType::factory()->create(); // Создание типа релиза
        $data = [
            'name' => $project['name'], // Передача имени уже существующего типа релиза
            'description' => 'Для description такого правила нет, но ее надо сюда вписать'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза

        $response->assertStatus(422); // Проверка, что запрос вернул статус 422 (Неверный запрос)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence() // Генерация случайного предложения
        ];
        $response = $this->post('api/releases_types', $data); // Отправка POST-запроса для создания нового типа релиза без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
