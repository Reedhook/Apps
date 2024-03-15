<?php

namespace App\Actions\Project;

use App\DTO\ProjectDTO;
use App\Exceptions\SameDataException;
use App\Models\Project;
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
        $this->model = new Project();
        $this->dbService = $dbService;
    }

    public function execute(ProjectDTO $dto, int $id)
    {
        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */
        $project = $this->dbService->update($this->model, $id, $dto);
        $oldData = $project['oldData'];
        $project = $project['instanceModel'];

        /** Проверка на изменения данных модели */
        $newData = $project->getChanges();

        /** Если данные изменились записываем в логи со старыми данными*/
        if ($newData) {
            foreach ($newData as $field => $value) {

                /** Логирование результата */
                Log::channel('change')
                    ->info(
                        message: 'Запись Project была изменена',
                        context: [
                            'change_id' => $project['id'],
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
        return $project;
    }
}
