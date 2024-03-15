<?php

/** Тестирования метода show, контроллера IndexController of Project */

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
{
    // Использование RefreshDatabase для очистки базы данных после каждого теста
    use RefreshDatabase;

    protected Project|Collection|Model $project;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()
            ->create([
                'is_admin' => true
            ]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->project = Project::factory()->create(); // Создание проекта
    }

    /**
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_projects_show()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/projects/'.$this->project->id); // Отправка GET-запроса для получения информации о проекте

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/projects/2'); // Отправка GET-запроса для получения информации о несуществующем проекте

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/projects/'.$this->project->id); // Отправка GET-запроса для получения информации о проекте без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
