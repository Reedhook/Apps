<?php

/** Тестирования метода show, контроллера IndexController of File */

namespace Tests\Feature\File;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected $file;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
        $this->file = File::factory()->create();
    }

    /**
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_files_show()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/files/'.$this->file->id);

        $response->assertStatus(200);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/files/2');

        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/files/'.$this->file->id);
        $response->assertStatus(401);
    }
}
