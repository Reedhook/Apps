<?php

namespace App\Services;

use App\DTO\ChangeLogDTO;
use App\Interfaces\DataServiceInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DbService implements DataServiceInterface
{
    /**
     * @throws Exception
     */
    public function create(Model $model, $dto): Model
    {
        $data = [];
        foreach ($dto->toArray() as $field => $value) {
            $data[$field] = $value;
            Log::info($data[$field]);
        }

        return $model::create($data) ?: throw new Exception('Ошибка сервера', 500);
    }

    public function createArr(Model $model, array $data): Model
    {
        return $model::create($data) ?: throw new Exception('Ошибка сервера', 500);
    }

    public function delete(Model $model, int $id): Model
    {
        /** Поиск в базе данных по id. При ответе false кидается исключение */
        $data = $model::findOrFail($id);

        $data->delete(); // Удаление записи

        /** Проверка на успешность операции */
        $data['deleted_at'] != null ?: throw new Exception(' Ошибка сервера ', 500);
        return $data;
    }


    public function update(Model $model, int $id, $dto): array
    {
        $instanceModel = $model::findOrFail($id);

        /** Сохраняем старые данные для логов */
        $oldData = $instanceModel->getAttributes();
        foreach ($dto->toArray() as $field => $value) {
            $instanceModel->$field = $value;
        }
        /** Обновить записи в базе данных данными прошедшими валидацию */
        $instanceModel->save();
        return [
            'oldData' => $oldData,
            'instanceModel' => $instanceModel
        ];
    }

    public function updateArr(Model $model, int $id, array $data): array
    {
        $instanceModel = $model::findOrFail($id);

        /** Сохраняем старые данные для логов */
        $oldData = $instanceModel->getAttributes();
        foreach ($data as $field => $value) {
            $instanceModel[$field] = $value;
        }
        /** Обновить записи в базе данных данными прошедшими валидацию */
        $instanceModel->save();
        return [
            'oldData' => $oldData,
            'instanceModel' => $instanceModel
        ];
    }
}
