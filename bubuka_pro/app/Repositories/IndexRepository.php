<?php

namespace App\Repositories;

use App\DTO\IndexDTO;
use App\Interfaces\IndexRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class IndexRepository implements IndexRepositoryInterface
{

    public function all(Model $model, IndexDTO $dto): Collection
    {
        /** Раздельно добавляем условия */
        $query = $model::query();

        /** Проверка на существования условия "ограничение по количеству записей"*/
        if ($dto->limit != null) {
            $query->limit($dto->limit);

            //Offset нельзя использовать без limit
            if ($dto->offset != null) {
                $query->offset($dto->offset); // сколько записей нужно пропустить
            }
        }
        return $query->get();
    }

    public function getByObject(Model $model, int $id): Model
    {
        return $model::findOrFail($id);
    }

    public function getByObjectWith(Model $model, int $id, string|array $anotherModel): Model|Collection|Builder|array|null
    {
        return $model::with($anotherModel)->findOrFail($id);
    }

    public function allWith(Model $model, IndexDTO $dto, string|array $anotherModel): Collection|array
    {
        /** Раздельно добавляем условия */
        $query = $model::query();

        /** Проверка на существования условия "ограничение по количеству записей"*/
        if ($dto->limit != null) {
            $query->limit($dto->limit);

            //Offset нельзя использовать без limit
            if ($dto->offset != null) {
                $query->offset($dto->offset); // сколько записей нужно пропустить
            }
        }

        return $query->with($anotherModel)->get();
    }
}
