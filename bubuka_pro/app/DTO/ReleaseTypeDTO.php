<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;

class ReleaseTypeDTO
{
    public ?string $name;
    public ?string $description;

    public function __construct(?string $name, ?string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function fromRequest(CustomFormRequest $request): ReleaseTypeDTO
    {
        return new static (
            $request->get('name'),
            $request->get('description'),
        );
    }

    public function toArray(): array
    {
        if ($this->name != null) {
            $data['name'] = $this->name;
        }
        if ($this->description != null) {
            $data['description'] = $this->description;
        }
        return $data;
    }
}
