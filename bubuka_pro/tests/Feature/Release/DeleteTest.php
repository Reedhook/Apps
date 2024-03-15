<?php

/** Тестирования метода delete, контроллера DeleteController of Release */

namespace Tests\Feature\Release;

use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected Release|Collection|Model $release;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
        $this->release = Release::factory()->create();
    }

    /**
     * Тестирование метода  DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete("api/releases/{$this->release->id}");


        $response->assertStatus(200);

        $trash = Release::withTrashed()->find($this->release->id);

        $this->assertEquals($this->release->name, $trash->name);
        $this->assertEquals($this->release->description, $trash->description);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/releases/2');

        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/releases/'.$this->release->id);

        $response->assertStatus(401);
    }
}
