<?php

namespace App\Http\Controllers\Platform;

use App\Actions\Platform\UpdateAction;
use App\DTO\PlatformDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\UpdateRequest;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/api/platforms/{platform_id}/",
     *     summary="Изменение записей о платформе ",
     *     tags={"Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="platform_id",
     *          in="path",
     *          description="Id проекта",
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
     *                  property="name",
     *                  type="string",
     *                  description="Название платформы",
     *                  example="Windows 12"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent( ref="#/components/schemas/Platform" )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
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
     *                      property="name",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля name",
     *                      example={
     *                          "The name field is required.",
     *                          "The name field must be a string",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken.",
     *                      }
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     *  )
     *  Обновление записей сущности Platform после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        $dto = PlatformDTO::fromRequest($request);
        $platform = $action->execute($dto, $id);
        return $this->OkResponse($platform, 'platform');
    }

}
