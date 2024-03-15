<?php

namespace App\DTO;

use App\Http\Requests\ChangeLog\StoreRequest;
use App\Http\Requests\ChangeLog\UpdateRequest;
use App\Http\Requests\CustomFormRequest;
use Illuminate\Http\Request;

class ChangeLogDTO
{
    public ?string $changes;
    public ?string $news;
    public function __construct(?string $changes, ?string $news)
    {
        $this->changes = $changes;
        $this->news = $news;

    }

    public static function fromRequest(CustomFormRequest $request): ChangeLogDTO
    {
        return new static (
            $request->get('changes'),
            $request->get('news')
        );
    }
    public function toArray(): array
    {
        $data =[];
        if($this->changes != null){
            $data['changes'] = $this->changes;
        }
        if($this->news != null){
            $data['news'] = $this->news;
        }
        return $data;
    }
}
