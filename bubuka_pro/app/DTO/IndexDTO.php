<?php

namespace App\DTO;

use App\Http\Requests\CustomFormRequest;

class IndexDTO
{
    public ?string $offset;
    public ?string $limit;

    public function __construct(?string $offset, ?string $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public static function fromRequest(CustomFormRequest $request): IndexDTO
    {
        $limit = $request->has('limit') ? $request->get('limit') : null;
        $offset = $request->has('offset') ? $request->get('offset') : null;

        return new static($offset, $limit);
    }
}
