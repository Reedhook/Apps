<?php

/** Тестирования метода delete, контроллера DeleteController of \App\Models\TechnicalRequirement */

namespace Tests\Feature\TechnicalRequirement;

use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
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
     * Тестирование метода DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete("api/techs_reqs/{$this->tech->id}"); // Отправка DELETE-запроса для удаления технических требований

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $trash = TechnicalRequirement::withTrashed()->find($this->tech->id); // Поиск удаленной записи в базе данных

        $this->assertEquals($this->tech->name, $trash->name); // Проверка, что имя удаленной записи соответствует исходному
        $this->assertEquals($this->tech->description, $trash->description); // Проверка, что описание удаленной записи соответствует исходному
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete('api/techs_reqs/2'); // Попытка удаления несуществующих технических требований

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/techs_reqs/'.$this->tech->id); // Попытка удаления технических требований без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
