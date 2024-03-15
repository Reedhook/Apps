<?php

/** Тестирования метода show, контроллера IndexController of ReleaseType */

namespace Tests\Feature\ReleaseType;

use App\Models\ReleaseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
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
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_releases_types_show()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/releases_types/'.$this->rt->id); // Отправка GET-запроса для получения информации о типе релиза

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
        ])->get('api/releases_types/2'); // Отправка GET-запроса для получения информации о несуществующем типе релиза

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/releases_types/'.$this->rt->id); // Отправка GET-запроса для получения информации о типе релиза без JWT токена
        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
