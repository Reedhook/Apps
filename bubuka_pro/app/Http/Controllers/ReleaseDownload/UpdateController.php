<?php
// Контроллер работает, только не понятно нужен или нет
//
//namespace App\Http\Controllers\ReleaseDownload;
//
//use App\Exceptions\SameDataException;
//use App\Http\Controllers\Controller;
//use App\Http\Requests\ReleaseDownload\UpdateRequest;
//use App\Models\ReleaseDownload;
//use Illuminate\Http\JsonResponse;
//use Illuminate\Support\Facades\Log;
//
//class UpdateController extends Controller
//{
//    /**
//     * Обновление записей сущности ReleaseDownload после валидации данных
//     *
//     * @param  UpdateRequest  $request
//     * @param  int            $id
//     * @return JsonResponse
//     */
//    public function update(UpdateRequest $request, int $id): JsonResponse
//    {
//        /** Получение данных прошедших валидацию */
//        $data = $request->validated();
//
//        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */
//        $rd = ReleaseDownload::findOrFail($id);
//
//        /** Сохраняем старые данные для логов */
//        $oldData = $rd->getAttributes();
//
//        /** Менять поле release_id если такой существует */
//        if (isset($data['release_id'])) {
//            $rd['release_id'] = $data['release_id'];
//        }
//
//        /** Менять поле ip если такой существует */
//        if (isset($data['ip'])) {
//            $rd['ip'] = $data['ip'];
//        }
//
//        /** Менять поле user_agent если такой существует */
//        if (isset($data['user_agent'])) {
//            $rd['user_agent'] = $data['user_agent'];
//        }
//
//        /** Менять поле utm если такой существует */
//        if (isset($data['utm'])) {
//            $rd['utm'] = $data['utm'];
//        }
//
//        /** Обновить записи в базе данных */
//        $rd->update($data);
//
//        /** Проверка на изменения данных модели */
//        $newData = $rd->getChanges();
//
//        /** Если данные изменились записываем в логи со старыми данными*/
//        if ($newData) {
//            foreach ($newData as $field => $value) {
//
//                /** Логирование результата */
//                Log::info(
//                    message: ' Запись была изменена ',
//                    context: [
//                        'Releases_Download_id' => $rd['id'],
//                        'field' => $field,
//                        'Старое значение' => $oldData[$field],
//                        'Новое значение' => $value
//                    ]
//                );
//            }
//        } else {
//            throw new SameDataException();
//        }
//        return $this->OkResponse($rd, 'release_download');
//    }
//
//}
