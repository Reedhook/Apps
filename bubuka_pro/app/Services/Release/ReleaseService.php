<?php

namespace App\Services\Release;

use App\DTO\ReleaseDTO;
use App\Exceptions\SameDataException;
use App\Models\Platform;
use App\Models\Project;
use App\Models\ReleaseType;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReleaseService
{
    /**
     * Сервис для сбора пути для скачивания
     * @param  ReleaseDTO  $dto
     */
    public function download_url(ReleaseDTO $dto): void
    {
        /** Поиск записей по id  */

        $data['project'] = Project::findOrFail($dto->project_id);
        $data['platform'] = Platform::findOrFail($dto->platform_id);
        $data['release_type'] = ReleaseType::findOrFail($dto->release_type_id);

        /** Создание url для скачивания */
        $data['download_url'] = $data['project']['name'].'/'.$data['platform']['name'].'/'.$data['release_type']['name'];

        /** Заменяем все пробелы на нижнее подчеркивание  */
        $dto->download_url = str_replace(' ', '_', $data['download_url']);
    }

    /**
     * Метод для проверки связи Платформы и проекта
     * @param  Model  $release
     * @param  array  $data
     * @return void
     * @throws Exception
     */
    public function checking_platform_connection(Model $release, array $data): void
    {
        /** Делаем проверку на существование связи между платформой и проектом  */
        $project = Project::findOrFail($release['project_id']);

        $project->platforms()->where('platform_id',
            $data['platform_id'])->exists() ?: throw new Exception('Данная платформа не прикреплена к проекту',
            400);
    }

    /**
     * Метод для обновления записей в бд
     * @param  Model  $release
     * @param  array  $oldData
     * @return Model
     */
    public function update(Model $release, array $oldData): Model
    {
        $status = $release['status'];
        $file = $release['file'];

        unset($release['status']);
        unset($release['file']);

        $release->save();
        /** Проверка на изменения данных модели */
        $newData = $release->getChanges();
        /** Если данные изменились записываем в логи со старыми данными*/
        if ($newData) {
            if ($status) {
                $release['file'] = $file;
            }
            foreach ($newData as $field => $value) {

                /** Логирование результата */
                Log::channel('release')
                    ->info(
                        message: ' Запись была изменена ',
                        context: [
                            'release_id' => $release['id'],
                            'field' => $field,
                            'Старое значение' => $oldData[$field],
                            'Новое значение' => $value
                        ]
                    );
            }
            return $release;
        } elseif ($status) {
            $release['file'] = $file;
            return $release;
        } else {
            throw new SameDataException();
        }
    }

    /**
     * Метод для запрета/разрешения публикации релиза
     * @param  Model  $release
     * @param         $is_public
     * @return JsonResponse
     */
    public function confirm_public(Model $release, $is_public): JsonResponse
    {
        // Проверяем значение в бд и меняем на противоположное
        if ($is_public) {
            $message = 'Админ подтвердил публикацию';
            $release['is_public'] = true;
        } else {
            $message = 'Админ запретил публикацию';
            $release['is_public'] = false;
        }
        Log::channel('release')
            ->info(
                message: $message,
                context: [$release]
            );
        $release->save();
        return response()->
        json([
            'status' => true,
            'message' => $message,
            'data' => $release,
            'code' => 200
        ]);
    }

}
