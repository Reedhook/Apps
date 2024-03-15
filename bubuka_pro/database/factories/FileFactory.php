<?php

namespace Database\Factories;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory<ChangeLog>
 */
class FileFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        Storage::fake('local');
        $this->file = TestingFile::create(fake()->word(), fake()->randomNumber());

        $data = [
            'file' => $this->file,
            'download_url' => fake()->word().'/'.fake()->word().'/'.fake()->word(),
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
            'extension' => $this->file->getClientOriginalExtension(),

        ];

        Storage::putFileAs('public/'.$data['download_url'], $data['file'],
            $data['version'].'.'.$data['extension']);
        return [
            'name' => $data['version'],
            'path' => '/public/'.$data['download_url'].'/'.$data['version'].'.'.$data['extension'],
            'extension' => $data['extension'],
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->sizeToReport
        ];
    }
}
