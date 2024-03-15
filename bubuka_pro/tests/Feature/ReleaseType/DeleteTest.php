<?php

/** Тестирования метода delete, контроллера DeleteController of ReleaseType */

namespace Tests\Feature\ReleaseType;

use App\Models\ReleaseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected ReleaseType|Collection|Model $rt; // Объект типа релиза
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]); // Создание пользователя с правами администратора
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->rt = ReleaseType::factory()->create(); // Создание типа релиза
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
        ])->delete("api/releases_types/{$this->rt->id}"); // Отправка DELETE-запроса для удаления типа релиза

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $trash = ReleaseType::withTrashed()->find($this->rt->id); // Поиск удаленной записи в базе данных

        $this->assertEquals($this->rt->name, $trash->name); // Проверка, что имя удаленной записи соответствует исходной
        $this->assertEquals($this->rt->description, $trash->description); // Проверка, что описание удаленной записи соответствует исходному
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->delete('api/releases_types/2'); // Попытка удаления несуществующего типа релиза

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/releases_types/'.$this->rt->id); // Попытка удаления типа релиза без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
