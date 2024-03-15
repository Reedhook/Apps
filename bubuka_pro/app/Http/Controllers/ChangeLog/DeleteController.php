<?php

namespace App\Http\Controllers\ChangeLog;

use App\Actions\ChangeLogs\DeleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/changes/{change_id}/",
     *     summary="Удаление тугрика",
     *     tags={"ChangeLog"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="change_id",
     *          in="path",
     *          description="Id тугрика",
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
     *                  description="Статус ответа",
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
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *     )
     *   )
     *  Метод для удаления записи
     * @param  int           $id
     * @param  DeleteAction  $action
     * @return JsonResponse
     */
    public function delete(int $id, DeleteAction $action): JsonResponse
    {
        $action->execute($id);

        return $this->deleteResponse(); // отправка json ответа

    }

}
