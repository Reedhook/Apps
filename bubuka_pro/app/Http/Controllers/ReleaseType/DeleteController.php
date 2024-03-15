<?php

namespace App\Http\Controllers\ReleaseType;

use App\Actions\ReleaseType\DeleteAction;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/releases_types/{release_type_id}/",
     *     summary="Удаление записи",
     *     tags={"Releases_Types"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="release_type_id",
     *          in="path",
     *          description="Id типа релиза",
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
     *                  description="Статус ответа",
     * *                example=true
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
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     *   )
     *  Метод для удаления записи о типах релизов
     * @param  int           $id
     * @param  DeleteAction  $action
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(int $id, DeleteAction $action): JsonResponse
    {
        $action->execute($id);

        return $this->deleteResponse(); // отправка json ответа
    }
}
