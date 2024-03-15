<?php

/** Тестирования метода update, контроллера UpdateController of File */

namespace Tests\Feature\File;

use App\Http\Controllers\File\CreateController;
use App\Http\Controllers\File\UpdateController;
use App\Models\File;
use App\Models\Release;
use App\Models\User;
use App\Services\File\FileService;
use App\Services\File\ValidationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase;


    protected TestingFile $file;
    protected User $user;
    protected $userToken;
    protected array $data;
    protected UpdateController $UpdateFile;
    protected array $res;

    protected function setUp(): void
    {
        parent::setUp();
        $fileCreate = new CreateController(new FileService(), new ValidationService());
        $this->UpdateFile = new UpdateController(new FileService(), new ValidationService());

        $user = User::factory()->create(['is_admin' => true]);
        $this->userToken = JWTAuth::fromUser($user);

        Storage::fake('local');
        $this->file = TestingFile::create(fake()->word().'.png', fake()->randomNumber());
        $this->data = [
            'file' => $this->file,
            'download_url' => fake()->word().'/'.fake()->word().'/'.fake()->word(),
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];

        $this->res = $fileCreate->save($this->data);
    }

    /**
     * Тестирование метода  UpdateController@update()
     * @test
     */
    public function updated_record_upload_file()
    {
        Storage::fake('local');
        $file = TestingFile::create(fake()->word().'.png', fake()->randomNumber());

        $newFile = TestingFile::create(fake()->word(), fake()->randomNumber());
        $release = Release::factory()->create([
            'file_id' => $this->res['file_id'],
            'download_url' => $this->data['download_url'],
            'version' => $this->res['version']
        ]);

        Storage::putFileAs('public/'.$this->data['download_url'], $file,
            $this->res['version'].'.png');

        $release['file'] = $newFile;
        $response = $this->UpdateFile->update($release);
        $this->assertDatabaseCount('files', 2);
        $this->assertTrue($response['status']);
    }

    /**
     * Тестирование исключения: "Файла по такому пути нет"
     * @test
     */
    public function test_DeleteException()
    {
        try {
            $newFile = TestingFile::create(fake()->word(), fake()->randomNumber());
            $oldFile = File::find($this->res['file_id']);
            Storage::delete($oldFile['path']);

            $release = Release::factory()->create([
                'file_id' => $this->res['file_id'],
                'download_url' => $this->data['download_url'],
                'version' => $this->res['version'],
            ]);
            $release['file'] = $newFile;
            $this->UpdateFile->update($release);

            $this->expectExceptionCode(400);
        } catch (Exception $e) {
            $expectedMessage = 'Файла по такому пути не существует';
            $this->assertEquals($expectedMessage, $e->getMessage());
        }
    }

    /**
     * Тестирование исключения: "Перенос файла в новую директорию не удалось"
     * @test
     */
    public function test_MoveException()
    {
        try {
            $oldFile = File::find($this->res['file_id']);
            Storage::delete($oldFile['path']);

            $release = Release::factory()->create([
                'file_id' => $this->res['file_id'],
                'download_url' => fake()->word().'/'.fake()->word().'/'.fake()->word(),
                'version' => $this->res['version'],
            ]);
            $this->UpdateFile->update($release);

            $this->expectExceptionCode(400);
        } catch (Exception $e) {
            $expectedMessage = 'Файла по такому пути не существует';
            $this->assertEquals($expectedMessage, $e->getMessage());
        }
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        $release = Release::factory()->create();
        $release['file_id'] = 3;
        $this->UpdateFile->update($release);

        $this->expectExceptionCode(404);
    }
}
