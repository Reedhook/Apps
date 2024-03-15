<?php

namespace App\Interfaces;

use App\DTO\IndexDTO;
use Illuminate\Database\Eloquent\Model;

interface IndexRepositoryInterface
{
    public function all(Model $model, IndexDTO $dto);

    public function getByObject(Model $model, int $id);
}
