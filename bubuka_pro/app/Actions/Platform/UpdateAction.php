<?php

namespace App\Actions\Platform;

use App\DTO\PlatformDTO;
use App\Exceptions\SameDataException;
use App\Models\Platform;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UpdateAction
{
    protected Model $model;
    protected IndexRepository $indexRepository;
    protected DbService $dbService;

    public function __construct(IndexRepository $indexRepository, DbService $dbService)
    {
        $this->indexRepository = $indexRepository;
        $this->model = new Platform();
        $this->dbService = $dbService;
    }

    public function execute(PlatformDTO $dto, int $id)
    {
        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */

        $platform = $this->dbService->update($this->model, $id, $dto);
        $oldData = $platform['oldData'];
        $platform = $platform['instanceModel'];

        /** Проверка на изменения данных модели */
        $newData = $platform->getChanges();

        /** Если данные изменились записываем в логи со старыми данными*/
        if ($newData) {
            foreach ($newData as $field => $value) {

                /** Логирование результата */
                Log::channel('platform')
                    ->info(
                        message: ' Запись была изменена ',
                        context: [
                            'platform_id' => $platform['id'],
                            'field' => $field,
                            'Старое значение' => $oldData[$field],
                            'Новое значение' => $value
                        ]
                    );
            }
        } else {
            /** Если данные не изменились выкидываем исключение */
            throw new SameDataException();
        }
        return $platform;
    }
}
