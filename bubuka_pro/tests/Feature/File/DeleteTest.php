<?php

/** Тестирования метода delete, контроллера DeleteController of File */

namespace Tests\Feature\File;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected File|Collection|Model $file;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]);
        $this->userToken = JWTAuth::fromUser($user);
        $this->file = File::factory()->create();
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
        ])->delete("api/files/{$this->file->id}");

        $response->assertStatus(200);

        $trash = File::withTrashed()->find($this->file->id);

        $this->assertEquals($this->file->name, $trash->name);
        $this->assertEquals($this->file->description, $trash->description);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/files/2');

        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $response = $this->delete('api/files/'.$this->file->id);

        $response->assertStatus(401);
    }
}
