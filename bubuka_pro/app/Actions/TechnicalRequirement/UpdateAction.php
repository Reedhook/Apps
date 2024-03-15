<?php

namespace App\Actions\TechnicalRequirement;

use App\DTO\ChangeLogDTO;
use App\DTO\TechnicalRequirementDTO;
use App\Exceptions\SameDataException;
use App\Models\ChangeLog;
use App\Models\TechnicalRequirement;
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
        $this->model = new TechnicalRequirement();
        $this->dbService = $dbService;
    }

    public function execute(TechnicalRequirementDTO $dto, int $id)
    {
        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */

        $tech = $this->dbService->update($this->model, $id, $dto);
        $oldData = $tech['oldData'];
        $tech = $tech['instanceModel'];

        /** Проверка на изменения данных модели */
        $newData = $tech->getChanges();

        /** Если данные изменились записываем в логи со старыми данными*/
        if ($newData) {
            foreach ($newData as $field => $value) {

                /** Логирование результата */
                Log::channel('tech_req')
                    ->info(
                        message: 'Запись была изменена',
                        context: [
                            'change_id' => $tech['id'],
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
        return $tech;
    }
}
