<?php

/** Тестирования метода show, контроллера IndexController of Release */

namespace Tests\Feature\Release;

use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected $release;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]);
        $this->userToken = JWTAuth::fromUser($user);
        $this->release = Release::factory()->create();
    }

    /**
     * Тестирование метода IndexController@show()
     * @test
     */
    public function response_for_route_releases_show()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/releases/'.$this->release->id);

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
        ])->get('api/releases/2');

        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/releases/'.$this->release->id);
        $response->assertStatus(401);
    }
}
