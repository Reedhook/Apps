<?php

/** Тестирования метода update, контроллера UpdateController of ChangeLog */

namespace Tests\Feature\ChangeLog;

use App\Exceptions\SameDataException;
use App\Models\ChangeLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase;


    protected ChangeLog|Collection|Model $changelog;
    protected User|Collection|Model $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Создание нового пользователя
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);

        // Создание новой записи
        $this->changelog = ChangeLog::factory()->create();
    }

    /**
     * Тестирование метода  UpdateController@update()
     * @test
     */
    public function updated_record()
    {
        // Отключение обработки исключении
        $this->withoutExceptionHandling();

        $data =[
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/changes/' . $this->changelog->id, $data);

        // Проверка статус ответа
        $response->assertStatus(200);

        // Проверяем что в базе данных все еще 1 запись
        $this->assertDatabaseCount('changes', 1);

        $response= $response->json();
        // Проверяем на идентичность отправленных для изменения и полученных данных
        $this->assertEquals($data['changes'], $response['body']['changelog']['changes']);

        //Проверяем на не идентичность изначальных и полученных данных
        $this->assertNotEquals($response['body']['changelog']['changes'], $this->changelog->changes);

        $this->assertEquals($data['news'], $response['body']['changelog']['news']);
        $this->assertNotEquals($response['body']['changelog']['news'], $this->changelog->news);
    }

    /**
     * Тестирование валидации поля 'changes' и 'news' при обновлениях записей тугриков UpdateRequest: на пустоту
     * @test
     */
    public function attribute_changes_and_news_are_required_for_updating_changelogs()
    {
        // Отправляем пустые строки
        $data = [
            'changes' => '',
            'news' => ''
        ];

        // Отправляем запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/changes/'. $this->changelog->id, $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field is required when news is not present.'],
                'news' => ['The news field is required when changes is not present.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации полей при обновлениях записей тугриков UpdateRequest: на тип данных
     * @test
     */
    public function attribute_changes_should_be_string_for_updating_changelogs()
    {
        // Отправляем числа вместо строк
        $data = [
            'changes' => 12345,
            'news' => 12345
        ];

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/changes/'. $this->changelog->id, $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field must be a string.'],
                'news' => ['The news field must be a string.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля 'changes' при обновлениях записей тугриков UpdateRequest: на максимальную длину
     * @test
     */
    public function attribute_changes_should_not_exceed_max_length()
    {
        // Отправка 256 символов
        $data = [
            'changes' => 'a'.str_repeat('b', 255),
            'news' => 'a'.str_repeat('b', 255)
        ];

        // Запрос в бд
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/changes/'. $this->changelog->id, $data);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'changes' => ['The changes field must not be greater than 255 characters.'],
                'news' => ['The news field must not be greater than 255 characters.']
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
            'changes' => $this->changelog->changes,
            'news' => $this->changelog->news
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch("api/changes/{$this->changelog['id']}", $data);
        $response->assertStatus(405)
            ->assertJson([
                'message' => "Данные идентичны, обновление не требуется"
            ]);
    }

    /**
     * Тестирование исключения ModelNotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $data = [
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];
        // Запрос на endpoint. Так как мы создали только 1 запись в фальшивом бд, несмотря на существование данных в реальном бд, id будет начинаться с 1. Поэтому для проверки достаточно искать записи с id=2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch('api/changes/2', $data);

        // Проверка статуса
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];

        // Запрос на endpoint без JWT
        $response = $this->patch('api/changes/'. $this->changelog->id, $data);

        $response->assertStatus(401);
    }
}
