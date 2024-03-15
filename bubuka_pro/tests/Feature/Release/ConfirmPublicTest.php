<?php

/** Тестирования метода confirm public, контроллера UpdateController of Release */

namespace Tests\Feature\Release;

use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ConfirmPublicTest extends TestCase
{
    use RefreshDatabase;

    protected $release;
    protected $user;
    protected $userToken;
    protected $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin'=>true]);
        $this->userToken = JWTAuth::fromUser($this->user);
        $this->release = Release::factory()->create();
    }

    /**
     * Тестирование метода UpdateController@confirm_public()
     * @test
     */
    public function confirm_public()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->patch("api/releases/confirm_public/{$this->release->id}");
        $response->assertStatus(200);
        $response = $response->json();
        $this->assertDatabaseCount('releases', 1);
        $this->assertTrue($response['data']['is_public']);
    }

    /**
     * Тестирование метода UpdateController@confirm_public()
     * @test
     */
    public function not_confirm_public()
    {
        $this->withoutExceptionHandling();

        for($i=0; $i<2; $i++){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$this->userToken,
            ])->patch("api/releases/confirm_public/{$this->release->id}");
        }
        $response = $response->json();
        $this->assertDatabaseCount('releases', 1);
        $this->assertFalse($response['data']['is_public']);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {

        $response = $this->patch("api/releases/confirm_public/{$this->release->id}");

        $response->assertStatus(401);

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
}
