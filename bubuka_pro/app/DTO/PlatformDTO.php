<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;

class PlatformDTO
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;

    }

    public static function fromRequest(CustomFormRequest $request): PlatformDTO
    {
        return new static (
            $request->get('name'),
        );
    }

    public function toArray(): array
    {
        $data['name'] = $this->name;
        return $data;
    }
}
