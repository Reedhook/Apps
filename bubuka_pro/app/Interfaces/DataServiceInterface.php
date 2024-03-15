<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface DataServiceInterface
{
    public function create(Model $model, $dto);
    public function delete(Model $model, int $id);
    public function update(Model $model, int $id, $dto);
}
