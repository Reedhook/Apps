<?php

namespace App\Http\Controllers\ChangeLog;

use App\Actions\ChangeLogs\UpdateAction;
use App\DTO\ChangeLogDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeLog\UpdateRequest;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/api/changes/{change_id}/",
     *     summary="Изменение записей тугрика ",
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
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="changes",
     *                  type="string",
     *                  description="Что было изменено?",
     *                  example="1. Было переделано логирование; 2. Был переделан интрефейс"
     *              ),
     *              @OA\Property(
     *                  property="news",
     *                  type="string",
     *                  example="Был добавлен сервис для поиска иностранной музыки",
     *                  description="Что нового было добавлено?"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/ChangeLog" )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус кода",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Сообщение ошибки",
     *                  @OA\Property(
     *                      property="changes",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля changes",
     *                      example={
     *                          "The changes field is required when news is not present.",
     *                          "The changed field must be a string",
     *                          "The changed field must not be greater than 255 characters.",
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="news",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля news",
     *                      example={
     *                          "The news field is required when changes is not present.",
     *                          "The news field must be a string",
     *                          "The news field must not be greater than 255 characters."
     *                      }
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *           )
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad Request",
     *           @OA\JsonContent( ref="#/components/schemas/400-Update" )
     *       ),
     *       @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent ( ref="#/components/schemas/401" )
     *       )
     *  )
     *  Обновление записей сущности ChangeLog после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        $dto = ChangeLogDTO::fromRequest($request);
        $changelog = $action->execute($dto, $id);

        return $this->OkResponse($changelog, 'changelog');
    }

}
