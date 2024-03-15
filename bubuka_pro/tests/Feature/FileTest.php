<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use App\Http\Controllers\Auth\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
   /* public function test_a_file_can_be_saved_in_local_storage()
    {
        $this->withoutExceptionHandling();
        $this->withHeaders([
            'Authorization' => 'Bearer '.'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3MDYyNDIwMjcsImV4cCI6MTcwNjc2Nzk4NywibmJmIjoxNzA2MjQyMDI3LCJqdGkiOiJPMUdqOE16N0E5TlJoNjFWIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.IzB4VJs1wB9iLuzaWhvdp_Pj8KkrvFVscOpyiUt8ynQ',
        ]);

        Storage::fake('local');

        $file = File::create('my_image.jpg');

        $data = [
            'email' => 'user@example.com',
            'password' => $file,
        ];

        $response = $this->post('api/auth/registration', $data);

        $response->assertStatus(201);
        $this->assertDatabaseCount('users', 1);

    }*/
}
