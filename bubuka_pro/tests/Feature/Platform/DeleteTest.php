<?php

/** Тестирования метода delete, контроллера DeleteController of Platform */

namespace Tests\Feature\Platform;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
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
     * Тестирование метода DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        $this->withoutExceptionHandling();

        // Удаление записи с использованием метода DELETE
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/platforms/'.$this->platform->id);

        // Проверка успешного удаления
        $response->assertStatus(200);

        // Проверка, что запись была фактически удалена из базы данных
        $trash = Platform::withTrashed()->find($this->platform->id);
        $this->assertEquals($this->platform->name, $trash->name);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        // Попытка удаления несуществующей записи
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/platforms/2');

        // Проверка наличия ошибки 404 (Not Found)
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        // Попытка удаления записи без JWT токена
        $response = $this->delete('api/platforms/'.$this->platform->id);

        // Проверка наличия ошибки 401 (Unauthorized)
        $response->assertStatus(401);
    }
}
