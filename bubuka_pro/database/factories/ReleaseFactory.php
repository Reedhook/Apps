<?php

namespace Database\Factories;

use App\Models\ChangeLog;
use App\Models\File;
use App\Models\Platform;
use App\Models\Project;
use App\Models\ReleaseType;
use App\Models\TechnicalRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReleaseFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tech = TechnicalRequirement::factory()->create();
        $change = ChangeLog::factory()->create();

        $project = Project::factory()->create();
        $platform = Platform::factory()->create();
        $project->platforms()->attach($platform);
        $rt = ReleaseType::factory()->create();
        $file = File::factory()->create();

        return [
            'file_id' => $file->id,
            'project_id' => $project->id,
            'platform_id' => $platform->id,
            'change_id' => $change->id,
            'description' => fake()->sentence(),
            'release_type_id' => $rt->id,
            'is_ready' => false,
            'is_public' => false,
            'technical_requirement_id' => $tech->id,
            'version' => $file['name'],
            'download_url' => url("download/$project->name/$platform->name/$rt->name/$file->name"),
        ];
    }
}
