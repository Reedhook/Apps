<?php

/** Тестирования метода show, контроллера IndexController of TechnicalRequirement */

namespace Tests\Feature\TechnicalRequirement;

use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
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
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_techs_reqs_show()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/techs_reqs/{$this->tech->id}"); // Отправка GET-запроса для получения конкретного технического требования

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
        ])->get('api/techs_reqs/2'); // Попытка получения несуществующего технического требования

        $response->assertStatus(404); // Проверка, что запрос вернул статус 404 (Не найдено)
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/techs_reqs/'.$this->tech->id); // Попытка получения технического требования без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}
