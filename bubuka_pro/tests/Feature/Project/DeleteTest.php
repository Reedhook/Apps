<?php

/** Тестирования метода delete, контроллера DeleteController of Project */

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected Project|Collection|Model $project; // Проект
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->project = Project::factory()->create(); // Создание проекта
    }

    /**
     * Тестирование метода  DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete("api/projects/{$this->project->id}"); // Отправка DELETE-запроса для удаления проекта

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $trash = Project::withTrashed()->find($this->project->id); // Поиск удаленного проекта

        $this->assertEquals($this->project->name, $trash->name); // Проверка, что имя проекта соответствует удаленной записи
        $this->assertEquals($this->project->description, $trash->description); // Проверка, что описание проекта соответствует удаленной записи
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete('api/projects/2'); // Отправка DELETE-запроса для удаления несуществующего проекта

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/projects/'.$this->project->id); // Отправка DELETE-запроса для удаления проекта без JWT токена

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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$userToken, // Передача токена в заголовке запроса
            'Accept' => 'application/json' // Установка заголовка Accept на application/json
        ])->delete('api/projects/'.$this->project->id); // Отправка DELETE-запроса для удаления проекта

        $response->assertStatus(403); // Проверка, что запрос вернул статус 403 (Запрещено)
    }
}
