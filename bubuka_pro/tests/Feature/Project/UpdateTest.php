<?php

/** Тестирования метода update, контроллера UpdateController of Project */

namespace Tests\Feature\Project;

use App\Exceptions\SameDataException;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Project|Collection|Model $project; // Объект проекта
    protected User|Collection|Model $user; // Объект пользователя
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->
        create([
            'is_admin' => true
        ]); // Создание администратора
        $this->userToken = JWTAuth::fromUser($this->user); // Генерация токена для пользователя
        $this->project = Project::factory()->create(['admin_id'=>1]); // Создание проекта
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
        ])->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $this->assertDatabaseCount('projects', 1); // Проверка, что в базе данных существует одна запись о проекте

        $response = $response->json();

        // Проверка, что поле 'name' в ответе соответствует отправленным данным
        $this->assertEquals($data['name'], $response['body']['project']['name']);

        // Проверка, что поле 'name' в ответе отличается от исходного значения
        $this->assertNotEquals($response['body']['project']['name'], $this->project->name);

        // Проверка, что поле 'description' в ответе соответствует отправленным данным
        $this->assertEquals($data['description'], $response['body']['project']['description']);

        // Проверка, что поле 'description' в ответе отличается от исходного значения
        $this->assertNotEquals($response['body']['project']['description'], $this->project->description);
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о проекте в UpdateRequest: проверка на пустоту
     * @test
     */
    public function attribute_name_and_description_are_required_for_updating_project()
    {
        $data = [
            'name' => '',
            'description' => ''
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required when description is not present.'],
                // Проверка, что поле 'name' обязательно для заполнения
                'description' => ['The description field is required when name is not present.'],
                // Проверка, что поле 'description' обязательно для заполнения
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о проекте в UpdateRequest: проверка на тип данных
     * @test
     */
    public function attributes_name_and_description_should_be_string_for_updating_project()
    {
        $data = [
            'name' => 12345, // Передача числового значения вместо строки
            'description' => 12345 // Передача числового значения вместо строки
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must be a string.'], // Проверка, что поле 'name' должно быть строкой
                'description' => ['The description field must be a string.'],
                // Проверка, что поле 'description' должно быть строкой
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'name' и 'description' при обновлении записи о проекте в UpdateRequest: проверка на максимальную длину
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
        ])->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'],
                // Проверка, что поле 'name' не должно превышать 255 символов
                'description' => ['The description field must not be greater than 255 characters.'],
                // Проверка, что поле 'description' не должно превышать 255 символов
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлении записи о проекте в UpdateRequest: проверка на уникальность
     * @test
     */
    public function attribute_name_should_be_unique_for_updating_project()
    {
        $project = Project::factory()->create(); // Создание проекта с таким же именем
        $data = [
            'name' => $project['name'], // Передача имени уже существующего проекта
            'description' => 'Для description такого правила нет, но ее надо сюда вписать'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте

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
            'description' => $this->project->description
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch("api/projects/{$this->project['id']}", $data);
        $response->assertStatus(405)
            ->assertJson([
                'message' => "Данные идентичны, обновление не требуется"
            ]);
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
        $response = $this->patch("api/projects/{$this->project->id}",
            $data); // Отправка PATCH-запроса для обновления информации о проекте без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }

    /**
     * Тестирование исключения ModelNotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $data = [
            'name' => fake()->word,
            'description' => fake()->sentence,
        ];
        // Запрос на endpoint. Так как мы создали только 1 запись в фальшивом бд, несмотря на существование данных в реальном бд, id будет начинаться с 1. Поэтому для проверки достаточно искать записи с id=2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/projects/2', $data);

        // Проверка статуса
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Forbidden
     * @test
     */
    public function test_user_is_admin()
    {
        $user = User::factory()->create(); // Создание пользователя без прав администратора
        $userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $data = [
            'name' => fake()->word(), // Генерация случайного слова
            'description' => fake()->sentence() // Генерация случайного предложения
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken, // Передача токена в заголовке запроса
            'Accept' => 'application/json' // Установка заголовка Accept на application/json
        ])->patch("api/projects/{$this->project->id}", $data); // Отправка PATCH-запроса для обновления информации о проекте


        $response->assertStatus(403); // Проверка, что запрос вернул статус 403 (Запрещено)
    }
}
