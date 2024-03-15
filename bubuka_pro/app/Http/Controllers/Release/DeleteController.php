<?php

namespace App\Http\Controllers\Release;

use App\Actions\Release\DeleteAction;
use App\Models\Release;
use App\Services\Release\ReleaseService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\File\DeleteController as FileController;
class DeleteController extends BaseReleaseController
{
    protected FileController $file;

    public function __construct(ReleaseService $releaseService, FileController $file)
    {
        parent::__construct($releaseService);
        $this->file = $file;
    }

    /**
     * @OA\Delete(
     *     path="/api/releases/{release_id}/",
     *     summary="Удаление записи",
     *     tags={"Releases"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="release_id",
     *          in="path",
     *          description="Id релиза",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *                example=1
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
     * Метод для удаления записи
     * @param  int           $id
     * @param  DeleteAction  $action
     * @return JsonResponse
     */
    public function delete(int $id, DeleteAction $action): JsonResponse
    {
        $action->execute($id);

        /** Возвращаем ответ */
        return $this->deleteResponse();
    }
}
