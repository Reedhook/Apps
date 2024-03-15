<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Http\UploadedFile;

class ReleaseDTO
{
    public ?UploadedFile $file;
    public ?int $file_id;
    public ?int $project_id;
    public ?int $platform_id;
    public ?int $change_id;
    public ?int $release_type_id;
    public ?int $technical_requirement_id;
    public ?bool $is_ready;
    public ?string $version;
    public ?string $description;
    public ?string $download_url;
    public ?bool $file_status;

    public function __construct(
        ?UploadedFile $file,
        ?int $project_id,
        ?int $platform_id,
        ?int $change_id,
        ?int $release_type_id,
        ?int $technical_requirement_id,
        ?int $file_id,
        ?bool $is_ready,
        ?string $version,
        ?string $description,
        ?string $download_url,
    ) {
        $this->file = $file;
        $this->project_id = $project_id;
        $this->platform_id = $platform_id;
        $this->change_id = $change_id;
        $this->release_type_id = $release_type_id;
        $this->technical_requirement_id = $technical_requirement_id;
        $this->file_id = $file_id;
        $this->is_ready = $is_ready;
        $this->version = $version;
        $this->description = $description;
        $this->download_url = $download_url;
        $this->file_status = false;
    }

    public static function fromRequest(CustomFormRequest $request): ReleaseDTO
    {
        return new static (
            $request->file('file'),
            $request->get('project_id'),
            $request->get('platform_id'),
            $request->get('change_id'),
            $request->get('release_type_id'),
            $request->get('technical_requirement_id'),
            $request->get('file_id'),
            $request->get('is_ready'),
            $request->get('version'),
            $request->get('description'),
            $request->get('download_url'),
        );
    }

    public function toArray(): array
    {
        $data = [];
        if ($this->project_id != null) {
            $data['project_id'] = $this->project_id;
        }
        if ($this->platform_id != null) {
            $data['platform_id'] = $this->platform_id;
        }
        if ($this->change_id != null) {
            $data['change_id'] = $this->change_id;
        }
        if ($this->release_type_id != null) {
            $data['release_type_id'] = $this->release_type_id;
        }
        if ($this->technical_requirement_id != null) {
            $data['technical_requirement_id'] = $this->technical_requirement_id;
        }
        if ($this->file_id != null) {
            $data['file_id'] = $this->file_id;
        }
        if ($this->is_ready != null) {
            $data['is_ready'] = $this->is_ready;
        }
        if ($this->version != null) {
            $data['version'] = $this->version;
        }
        if ($this->description != null) {
            $data['description'] = $this->description;
        }
        if ($this->download_url != null) {
            $data['download_url'] = $this->download_url;
        }
        return $data;
    }
}
