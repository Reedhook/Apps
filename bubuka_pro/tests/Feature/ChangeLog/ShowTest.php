<?php

/** Тестирования метода show, контроллера IndexController of ChangeLog */

namespace Tests\Feature\ChangeLog;

use App\Models\ChangeLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected ChangeLog|Collection|Model $changelog;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Создание нового пользователя
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);

        // Создание новой записи
        $this->changelog = ChangeLog::factory()->create();
    }

    /**
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_changes_show()
    {
        // Отключение обработки исключении
        $this->withoutExceptionHandling();

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/changes/'.$this->changelog->id);

        // Проверка статуса ответа
        $response->assertStatus(200);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/changes/2');

        // Проверка статус ответа
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        // Запрос на endpoint без JWT токена
        $response = $this->get('api/changes/'.$this->changelog->id);

        // Проверка статуса ответа
        $response->assertStatus(401);
    }
}
