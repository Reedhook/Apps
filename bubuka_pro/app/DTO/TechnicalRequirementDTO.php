<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;

class TechnicalRequirementDTO
{
    public ?string $os_type;
    public ?string $specifications;

    public function __construct(?string $os_type, ?string $specifications)
    {
        $this->os_type = $os_type;
        $this->specifications = $specifications;

    }

    public static function fromRequest(CustomFormRequest $request): TechnicalRequirementDTO
    {
        return new static (
            $request->get('os_type'),
            $request->get('specifications')
        );
    }

    public function toArray(): array
    {
        if($this->os_type != null){
            $data['os_type'] = $this->os_type;
        }
        if($this->specifications != null){
            $data['specifications'] = $this->specifications;

        }
        return $data;
    }
}
