<?php

/** Тестирования метода create, контроллера CreateController of TechnicalRequirement */

namespace Tests\Feature\TechnicalRequirement;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    // Используется для обновления базы данных перед каждым тестом

    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp(); // Вызов метода setUp() родительского класса
        $user = User::factory()->create(); // Создание тестового пользователя
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
            'os_type' => fake()->word(), // Генерация случайного слова
            'specifications' => fake()->sentence() // Генерация случайного предложения
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->post('api/techs_reqs', $data); // Отправка POST-запроса на указанный URL с данными

        $response->assertStatus(201); // Проверка статуса ответа

        $this->assertDatabaseCount('technicals_requirements', 1); // Проверка количества записей в базе данных

        $response = $response->json(); // Преобразование ответа в формат JSON

        // Проверка соответствия значения поля 'os_type'
        $this->assertEquals($data['os_type'], $response['body']['technical_requirement']['os_type']);

        // Проверка соответствия значения поля 'specifications'
        $this->assertEquals($data['specifications'], $response['body']['technical_requirement']['specifications']);
    }

    /**
     * Тестирование валидации поля 'os_type' при создании проекта, StoreRequest: на пустоту
     * @test
     */
    public function attribute_os_type_is_required_for_storing_technical_requirement()
    {
        $data = [
            'os_type' => '', // Пустое значение поля 'os_type'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/techs_reqs', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'os_type' => ['The os type field is required.'], // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'os_type' и 'specifications' при создании проекта, StoreRequest: на тип данных
     * @test
     */
    public function attributes_os_type_and_specifications_should_be_string_for_storing_technical_requirement()
    {
        $data = [
            'os_type' => 12345, // Числовое значение вместо строки
            'specifications' => 12345, // Числовое значение вместо строки
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/techs_reqs', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'os_type' => ['The os type field must be a string.'], // Проверка сообщения об ошибке валидации
                'specifications' => ['The specifications field must be a string.'],
                // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'os_type' и 'specifications' при создании проекта, StoreRequest: на максимальную длину
     * @test
     */
    public function attributes_os_type_and_specifications_should_not_exceed_max_length()
    {
        $data = [
            'os_type' => 'a'.str_repeat('b', 255), // Значение поля 'os_type' с длиной больше 255 символов
            'specifications' => 'a'.str_repeat('b', 255) // Значение поля 'specifications' с длиной больше 255 символов
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/techs_reqs', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                // Проверка сообщения об ошибке валидации
                'os_type' => ['The os type field must not be greater than 255 characters.'],
                'specifications' => ['The specifications field must not be greater than 255 characters.'],

            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'os_type' => fake()->word(), // Генерация случайного слова
        ];
        $response = $this->post('api/techs_reqs', $data); // Отправка POST-запроса на указанный URL с данными

        $response->assertStatus(401); // Проверка статуса ответа
    }

}
