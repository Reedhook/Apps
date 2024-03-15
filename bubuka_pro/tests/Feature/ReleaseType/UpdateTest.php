<?php

/** Тестирования метода update, контроллера UpdateController of ReleaseType */

namespace Tests\Feature\ReleaseType;

use App\Exceptions\SameDataException;
use App\Models\ReleaseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected ReleaseType|Collection|Model $rt; // Объект типа релиза
    protected User|Collection|Model $user; // Объект пользователя
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($this->user); // Генерация токена для пользователя
        $this->rt = ReleaseType::factory()->create(); // Создание типа релиза
    }

    /**
     * Тестирование метода UpdateController@update()
     * @test
     */
    public function updated_record()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence // Генерация случайного предложения
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $this->assertDatabaseCount('releases_types', 1); // Проверка, что в базе данных существует одна запись о типе релиза

        $this->assertEquals($data['name'], $response['body']['release_type']['name']); // Проверка, что поле 'name' в ответе соответствует отправленным данным
        $this->assertNotEquals($response['body']['release_type']['name'], $this->rt->name); // Проверка, что поле 'name' в ответе отличается от исходного значения

        $this->assertEquals($data['description'], $response['body']['release_type']['description']); // Проверка, что поле 'description' в ответе соответствует отправленным данным
        $this->assertNotEquals($response['body']['release_type']['description'], $this->rt->description); // Проверка, что поле 'description' в ответе отличается от исходного значения
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о типе релиза в UpdateRequest: проверка на пустоту
     * @test
     */
    public function attribute_name_and_description_are_required_for_updating_release_type()
    {
        $data = [
            'name' => '', // Пустое значение поля 'name'
            'description' => '' // Пустое значение поля 'description'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required when description is not present.'], // Проверка, что поле 'name' обязательно для заполнения
                'description' => ['The description field is required when name is not present.'], // Проверка, что поле 'description' обязательно для заполнения
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о типе релиза в UpdateRequest: проверка на тип данных
     * @test
     */
    public function attributes_name_and_description_should_be_string_for_updating_release_type()
    {
        $data = [
            'name' => 12345, // Передача числового значения вместо строки
            'description' => 12345 // Передача числового значения вместо строки
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must be a string.'], // Проверка, что поле 'name' должно быть строкой
                'description' => ['The description field must be a string.'], // Проверка, что поле 'description' должно быть строкой
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при обновлении записи о типе релиза в UpdateRequest: проверка на максимальную длину
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
        ])->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'], // Проверка, что поле 'name' не должно превышать 255 символов
                'description' => ['The description field must not be greater than 255 characters.'], // Проверка, что поле 'description' не должно превышать 255 символов
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о типе релиза в UpdateRequest: проверка на уникальность
     * @test
     */
    public function attribute_name_should_be_unique_for_updating_release_type()
    {
        $project = ReleaseType::factory()->create(); // Создание типа релиза с таким же именем
        $data = [
            'name' => $project['name'], // Передача имени уже существующего типа релиза
            'description' => 'Для description такого правила нет, но ее надо сюда вписать'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза

        $response->assertStatus(422); // Проверка, что запрос вернул статус 422 (Неверный запрос)
    }

    /**
     * Тестирование исключения BadRequest
     * @test
     */
    public function test_same_data()
    {
        $this->expectException(SameDataException::class);
        $data = [
            'description' => $this->rt->description
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch("api/releases_types/{$this->rt['id']}", $data);
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
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence() // Генерация случайного предложения
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch('api/releases_types/2', $data); // Отправка GET-запроса для получения информации о несуществующем типе релиза

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
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
        $response = $this->patch("api/releases_types/{$this->rt->id}", $data); // Отправка PATCH-запроса для обновления информации о типе релиза без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
