<?php

namespace App\Actions\ChangeLogs;

use App\DTO\ChangeLogDTO;
use App\Exceptions\SameDataException;
use App\Models\ChangeLog;
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
        $this->model = new ChangeLog();
        $this->dbService = $dbService;
    }

    public function execute(ChangeLogDTO $dto, int $id)
    {
        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */

        $changelog = $this->dbService->update($this->model, $id, $dto);
        $oldData = $changelog['oldData'];
        $changelog = $changelog['instanceModel'];

        /** Проверка на изменения данных модели */
        $newData = $changelog->getChanges();

        /** Если данные изменились записываем в логи со старыми данными*/
        if ($newData) {
            foreach ($newData as $field => $value) {

                /** Логирование результата */
                Log::channel('change')
                    ->info(
                        message: 'Запись ChangeLog была изменена',
                        context: [
                            'change_id' => $changelog['id'],
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
        return $changelog;
    }
}
