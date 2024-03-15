<?php

/** Тестирования метода create, контроллера CreateController of Project */

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddUserTest extends TestCase
{
    use RefreshDatabase;

    // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected $userToken;
    protected Project|Collection|Model $project;
    protected User|Collection|Model $user;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]); // Создание платформы
        $this->userToken = JWTAuth::fromUser($this->user); // Генерация токена для пользователя
        $this->project = Project::factory()->create(['admin_id' => $this->user->id]); // Создание проекта
        $this->data = [
            'user_id' => $this->user->id, // Идентификатор платформы
            'project_id' => $this->project->id // Идентификатор проекта
        ];
    }

    /**
     * Тестирование метода  CreateController@attach()
     * @test
     */
    public function added_user_to_project()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch(
            "api/projects/{$this->project->id}/users/{$this->user->id}");
        // Отправка patch-запроса для добавления платформы к проекту

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'К проекту добавлен пользователь',
        ]);
        $this->assertDatabaseHas('projects_users',
            [ // Проверка, что в базе данных есть запись о связи проекта с платформой
                'project_id' => $this->project->id,
                'user_id' => $this->user->id
            ]);
    }

    /**
     * Тестирование метода  CreateController@detach()
     * @test
     */
    public function delete_user_from_project()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $this->project->users()->attach($this->user); // Привязка платформы к проекту

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete("api/projects/{$this->project->id}/users/{$this->user->id}");
        // Отправка delete-запроса для удаления платформы из проекта

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'Из проекта удален пользователь',
        ]);
        $this->assertDatabaseMissing('projects_users',
            [ // Проверка, что в базе данных нет записи о связи проекта с платформой
                'project_id' => $this->project->id,
                'user_id' => $this->user->id
            ]);
    }

    /**
     * Тестирование исключения Пользователь уже добавлен()
     * @test
     */
    public function test_exception()
    {
        $this->expectException(Exception::class);
        $this->project->users()->attach($this->user); // Привязка платформы к проекту

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/projects/{$this->project->id}/users/{$this->user->id}",
            $this->data); // Отправка patch-запроса для удаления платформы из проекта

        $response->assertStatus(500)->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'Данный пользователь уже добавлен к проекту',
        ]); // Проверка, что запрос вернул статус 500
    }

    /**
     * Тестирование исключения Пользователя нет в проекте
     * @test
     */
    public function test_exception2()
    {
        $this->expectException(Exception::class);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete("api/projects/{$this->project->id}/users/{$this->user->id}",
            $this->data); // Отправка delete-запроса для удаления платформы из проекта

        $response->assertStatus(500)->assertJson([
            'message' => 'Данного пользователя нет в проекте',
        ]); // Проверка, что запрос вернул статус 500
    }
}
