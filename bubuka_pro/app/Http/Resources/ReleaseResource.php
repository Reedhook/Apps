<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\Platform;
use App\Models\Project;
use App\Models\Release_type;
use App\Models\Technical_requirement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReleaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = $request->all();

        /** Поиск записей по id */
        $project = Project::find($data['project_id']);
        $platform = Platform::find($data['platform_id']);
        $release_type = Release_type::find($data['release_type_id']);
        $technical_requirement = Technical_requirement::find($data['technical_requirement_id']);
        $file = File::find($data['file_id']);

        /** Меняем вид $tech_require */
        unset($technical_requirement['deleted_at'],$technical_requirement['created_at'], $technical_requirement['updated_at']);

        return [
            'id'=>$this->id,
            'project' => $project['name'],
            'platform' => $platform['name'],
            'changes' => $this->changes,
            'release_type' => $release_type['name'],
            'is_ready' => $this->is_ready,
            'is_public' => $this->is_public,
            'technical_requirement' => $technical_requirement,
            'download_url' => $download_url,
            'version' =>$this->version,
            'file' =>$file['name'],
            'created_at'=>$this->created_at
        ];
    }
}
