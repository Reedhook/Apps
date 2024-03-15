<?php

/** Тестирования метода update, контроллера UpdateController of Platform */

namespace Tests\Feature\Platform;

use App\Exceptions\SameDataException;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Platform|Collection|Model $platform;
    protected User|Collection|Model $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);
        $this->platform = Platform::factory()->create();
    }

    /**
     * Тестирование метода UpdateController@update()
     * @test
     */
    public function updated_record()
    {
        $this->withoutExceptionHandling();

        // Подготовка данных для обновления записи
        $data = [
            'name' => fake()->word()
        ];

        // Отправка PATCH-запроса для обновления записи
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/' . $this->platform->id, $data);

        // Проверка успешного обновления
        $response->assertStatus(200);
        $this->assertDatabaseCount('platforms', 1);

        // Проверка соответствия обновленного имени
        $response = $response->json();
        $this->assertEquals($data['name'], $response['body']['platform']['name']);
        $this->assertNotEquals($response['body']['platform']['name'], $this->platform->name);
    }

    /**
     * Тестирование валидации поля 'name' при обновлениях записей о платформе UpdateRequest: на пустоту
     * @test
     */
    public function attribute_changes_and_news_are_required_for_updating_platforms()
    {
        // Подготовка данных с пустым именем
        $data = [
            'name' => ''
        ];

        // Отправка PATCH-запроса для обновления записи с пустым именем
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/'.$this->platform->id, $data);

        // Проверка наличия ошибки 422 (Unprocessable Entity) и сообщения об ошибке
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field is required.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлениях записей о платформе UpdateRequest: на тип данных
     * @test
     */
    public function attribute_name_should_be_string_for_updating_platform()
    {
        // Подготовка данных с числовым именем
        $data = [
            'name' => 12345
        ];

        // Отправка PATCH-запроса для обновления записи с числовым именем
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/'.$this->platform->id, $data);

        // Проверка наличия ошибки 422 (Unprocessable Entity) и сообщения об ошибке
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must be a string.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлениях записей о платформе UpdateRequest: на максимальную длину
     * @test
     */
    public function attribute_name_should_not_exceed_max_length()
    {
        // Подготовка данных с именем, превышающим максимальную длину
        $data = [
            'name' => 'a'.str_repeat('b', 255)
        ];

        // Отправка PATCH-запроса для обновления записи с длинным именем
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/'.$this->platform->id, $data);

        // Проверка наличия ошибки 422 (Unprocessable Entity) и сообщения об ошибке
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'name' => ['The name field must not be greater than 255 characters.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'name' при обновлениях записей о платформе UpdateRequest: на уникальность
     * @test
     */
    public function attribute_name_should_be_unique_for_updating_platform()
    {
        // Создание другой записи о платформе
        $platform = Platform::factory()->create();
        $data = [
            'name' => $platform['name']
        ];

        // Отправка PATCH-запроса для обновления записи с уже существующим именем
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/'.$this->platform->id, $data);

        // Проверка наличия ошибки 422 (Unprocessable Entity)
        $response->assertStatus(422);
    }

    /**
     * Тестирование исключения ModelNotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $data = [
            'name' => fake()->sentence,
        ];
        // Запрос на endpoint. Так как мы создали только 1 запись в фальшивом бд, несмотря на существование данных в реальном бд, id будет начинаться с 1. Поэтому для проверки достаточно искать записи с id=2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/platforms/2', $data);

        // Проверка статуса
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        // Подготовка данных для обновления записи
        $data = [
            'name' => fake()->sentence
        ];

        // Отправка PATCH-запроса для обновления записи без JWT токена
        $response = $this->patch('api/platforms/'. $this->platform->id, $data);

        // Проверка наличия ошибки 401 (Unauthorized)
        $response->assertStatus(401);
    }
}
