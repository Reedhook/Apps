<?php

namespace App\Http\Controllers\TechnicalRequirement;

use App\Actions\TechnicalRequirement\DeleteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    /**
     * @OA\Delete(
     *        path="/api/techs_reqs/{tech_req_id}/",
     *        summary="Удаление записи",
     *        tags={"Technicals_Requirements"},
     *        security={{"bearer_token":{}}},
     *        @OA\Parameter(
     *            name="tech_req_id",
     *            in="path",
     *            description="Id тех. хар-и",
     *            required=true,
     *            @OA\Schema(
     *                type="integer",
     *                format="int64",
     *                example=1
     *            )
     *        ),
     *        @OA\Response(
     *            response=200,
     *            description="OK",
     *            @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус ответа",
     *                  example=true
     *              )
     *            )
     *        ),
     *        @OA\Response(
     *            response=404,
     *            description="Not Found",
     *            @OA\JsonContent( ref="#/components/schemas/404" )
     *        ),
     *        @OA\Response (
     *            response=401,
     *            description="Unauthorized",
     *            @OA\JsonContent ( ref="#/components/schemas/401" )
     *        )
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
