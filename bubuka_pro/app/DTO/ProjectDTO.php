<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Project\StoreRequest;
use App\Http\Requests\Project\UpdateRequest;
use Illuminate\Support\Facades\Auth;

class ProjectDTO
{
    public ?string $name;
    public ?string $description;
    public int $admin_id;

    public function __construct(?string $name, ?string $description, int $admin_id)
    {
        $this->name = $name;
        $this->description = $description;
        $this->admin_id = Auth::id();

    }

    public static function fromRequest(CustomFormRequest $request): ProjectDTO
    {
        return new static (
            $request->get('name'),
            $request->get('description'),
            Auth::id(),
        );
    }

    public function toArray(): array
    {
        $data = [];
        if ($this->name != null) {
            $data['name'] = $this->name;
        }
        if ($this->description != null) {
            $data['description'] = $this->description;
        }
        if ($this->admin_id != null) {
            $data['admin_id'] = $this->admin_id;
        }
        return $data;
    }
}
