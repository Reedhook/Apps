<?php

/** Тестирования метода delete, контроллера DeleteController of Platform */

namespace Tests\Feature\ReleaseDownload;

use App\Models\ReleaseDownload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $rd;
    protected $userToken;


    protected function setUp(): void
    {
        parent::setUp();
        $this->rd = ReleaseDownload::factory()->create();

        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
    }

    /**
     * Тестирование метода DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete("api/rels_dls/{$this->rd->id}");

        // Проверка, что запись была фактически удалена из базы данных
        $response->assertStatus(200);

        $trash = ReleaseDownload::withTrashed()->find($this->rd->id);
        $this->assertEquals($this->rd->ip, $trash->ip);
        $this->assertEquals($this->rd->release_id, $trash->release_id);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete("api/rels_dls/2");
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete("api/rels_dls/{$this->rd->id}");

        $response->assertStatus(401);
    }


}
