<?php

/** Тестирования метода show, контроллера IndexController of Platform */

namespace Tests\Feature\Platform;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected Platform|Collection|Model $platform;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
        $this->platform = Platform::factory()->create();
    }

    /**
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_platforms_show()
    {
        $this->withoutExceptionHandling();

        // Получение информации о конкретной записи с использованием метода GET
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms/'.$this->platform->id);

        // Проверка успешного получения информации
        $response->assertStatus(200);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        // Попытка получения информации о несуществующей записи
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms/2');

        // Проверка наличия ошибки 404 (Not Found)
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        // Попытка получения информации о записи без JWT токена
        $response = $this->get('api/platforms/'.$this->platform->id);
        $response->assertStatus(401);
    }
}
