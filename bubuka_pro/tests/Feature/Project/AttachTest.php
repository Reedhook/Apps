<?php

/** Тестирования метода create, контроллера CreateController of Project */

namespace Tests\Feature\Project;

use App\Models\Platform;
use App\Models\Project;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttachTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected $userToken;
    protected Project|Collection|Model $project;
    protected Platform|Collection|Model $platform;
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->project = Project::factory()->create(); // Создание проекта
        $this->platform = Platform::factory()->create(); // Создание платформы
        $this->data = [
            'platform_id' => $this->platform->id, // Идентификатор платформы
            'project_id' => $this->project->id // Идентификатор проекта
        ];
    }

    /**
     * Тестирование метода  CreateController@attach()
     * @test
     */
    public function added_platform_to_project()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch(
            "api/projects/{$this->project->id}/platforms/{$this->platform->id}",
        ); // Отправка POST-запроса для добавления платформы к проекту

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'Платформа была добавлена к проекту',
        ]);
        $this->assertDatabaseHas('projects_platforms', [ // Проверка, что в базе данных есть запись о связи проекта с платформой
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id
        ]);
    }

    /**
     * Тестирование метода  CreateController@detach()
     * @test
     */
    public function delete_platform_from_project()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $this->project->platforms()->attach($this->platform); // Привязка платформы к проекту

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete("api/projects/{$this->project->id}/platforms/{$this->platform->id}"); // Отправка POST-запроса для удаления платформы из проекта

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'Платформа удалена из проекта',
        ]);
        $this->assertDatabaseMissing('projects_platforms', [ // Проверка, что в базе данных нет записи о связи проекта с платформой
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id
        ]);
    }

    /**
     * Тестирование исключения Пользователь уже добавлен()
     * @test
     */
    public function test_exception()
    {
        $this->expectException(Exception::class);
        $this->project->platforms()->attach($this->platform); // Привязка платформы к проекту

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->patch("api/projects/{$this->project->id}/platforms/{$this->platform->id}",
            $this->data); // Отправка patch-запроса для удаления платформы из проекта

        $response->assertStatus(500)->assertJson([ // Проверка, что ответ содержит указанное сообщение
            'message' => 'Данная платформа уже добавлена к проекту',
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
        ])->delete("api/projects/{$this->project->id}/platforms/{$this->platform->id}",
            $this->data); // Отправка delete-запроса для удаления платформы из проекта

        $response->assertStatus(500)->assertJson([
            'message' => 'Данной платформы уже нет в проекте',
        ]); // Проверка, что запрос вернул статус 500
    }
}
