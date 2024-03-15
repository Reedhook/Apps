<?php

namespace App\Http\Controllers\ReleaseDownload;

use App\Http\Controllers\Controller;
use App\Models\ReleaseDownload;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DeleteController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/rels_dls/{rel_dl_id}/",
     *     summary="Удаление записи",
     *     tags={"Releases_Downloads"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="rel_dl_id",
     *          in="path",
     *          description="Id записи скаченного релиза",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404" )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent( ref="#/components/schemas/500" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *     )
     * )
     *
     * Метод для удаления записи
     * @param  int  $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(int $id): JsonResponse
    {
        /** Поиск записи по id, в Случае ошибки выкинет исключение 404 */
        $rd = ReleaseDownload::findOrFail($id);
        $rd->delete(); // удаление

        /** Проверка на успешность операции */
        ($rd['deleted_at'] != null) ?: throw new Exception(' Ошибка сервера ', 500);

        /** Логирование результата */
        Log::info(
            message: ' Запись удалена ',
            context: [$rd]
        );
        return $this->deleteResponse();

    }

}
