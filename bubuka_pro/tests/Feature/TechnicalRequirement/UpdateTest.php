<?php

/** Тестирования метода update, контроллера UpdateController of TechnicalRequirement */

namespace Tests\Feature\TechnicalRequirement;

use App\Exceptions\SameDataException;
use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected TechnicalRequirement|Collection|Model $tech; // Объект технических требований
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); // Создание тестового пользователя
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->tech = TechnicalRequirement::factory()->create(); // Создание технических требований
    }

    /**
     * Тестирование метода UpdateController@update()
     * @test
     */
    public function updated_record()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $data = [
            'os_type' => fake()->word(), // Генерация случайного слова
            'specifications' => fake()->sentence, // Генерация случайного предложения
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/techs_reqs/{$this->tech->id}", $data); // Отправка PATCH-запроса для обновления технических требований

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $this->assertDatabaseCount('technicals_requirements', 1); // Проверка количества записей в базе данных

        $response = $response->json(); // Преобразование ответа в формат JSON
        $this->assertEquals($data['os_type'], $response['body']['technical_requirement']['os_type']); // Проверка обновления поля 'os_type'
        $this->assertNotEquals($response['body']['technical_requirement']['os_type'], $this->tech->os_type); // Проверка изменения значения поля 'os_type'

        $this->assertEquals($data['specifications'], $response['body']['technical_requirement']['specifications']); // Проверка обновления поля 'specifications'
        $this->assertNotEquals($response['body']['technical_requirement']['specifications'], $this->tech->specifications); // Проверка изменения значения поля 'specifications'
    }

    /**
     * Тестирование валидации полей 'os_type' и 'specifications' при обновлениях записей о платформе UpdateRequest: на пустоту
     * @test
     */
    public function attributes_os_type_and_specifications_are_required_for_updating_project()
    {
        $data = [
            'os_type' => '', // Пустое значение поля 'os_type'
            'specifications' => '', // Пустое значение поля 'specifications'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/techs_reqs/{$this->tech->id}", $data); // Отправка PATCH-запроса для обновления технических требований

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'os_type' => ['The os type field is required when specifications is not present.'], // Проверка сообщения об ошибке валидации
                'specifications' => ['The specifications field is required when os type is not present.'], // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'os_type' и 'specifications' при обновлениях записей о платформе UpdateRequest: на тип данных
     * @test
     */
    public function attributes_os_type_and_specifications_should_be_string_for_updating_project()
    {
        $data = [
            'os_type' => 12345, // Числовое значение вместо строки
            'specifications' => 12345, // Числовое значение вместо строки
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/techs_reqs/{$this->tech->id}", $data); // Отправка PATCH-запроса для обновления технических требований

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'os_type' => ['The os type field must be a string.'], // Проверка сообщения об ошибке валидации
                'specifications' => ['The specifications field must be a string.'], // Проверка сообщения об ошибке валидации
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
            'specifications' => 'a'.str_repeat('b', 255), // Значение поля 'specifications' с длиной больше 255 символов
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/techs_reqs/{$this->tech->id}", $data); // Отправка PATCH-запроса для обновления технических требований

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'os_type' => ['The os type field must not be greater than 255 characters.'], // Проверка сообщения об ошибке валидации
                'specifications' => ['The specifications field must not be greater than 255 characters.'], // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование исключения BadRequest
     * @test
     */
    public function test_same_data()
    {
        $this->expectException(SameDataException::class);
        $data = [
            'os_type' => $this->tech->os_type, // Генерация случайного слова
            'specifications' => $this->tech->specifications, // Генерация случайного предложения
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch("api/techs_reqs/{$this->tech['id']}", $data);
        $response->assertStatus(405)
            ->assertJson([
                'message' => "Данные идентичны, обновление не требуется"
            ]);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $data =[
            'os_type' => fake()->word(), // Генерация случайного слова
            'specifications' => fake()->sentence, // Генерация случайного предложения
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch('api/techs_reqs/2', $data); // Отправка GET-запроса для получения информации о несуществующем типе релиза

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'os_type' => fake()->word(), // Генерация случайного слова
            'specifications' => fake()->sentence, // Генерация случайного предложения
        ];
        $response = $this->patch("api/techs_reqs/{$this->tech->id}", $data); // Отправка PATCH-запроса без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
