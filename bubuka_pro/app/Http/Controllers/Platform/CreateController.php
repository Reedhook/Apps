<?php

namespace App\Http\Controllers\Platform;

use App\Actions\Platform\CreateAction;
use App\DTO\PlatformDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreRequest;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/platforms/",
     *     summary="Добавление платформы",
     *     tags={"Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Windows 11",
     *                  description="Название платформы"
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Название платформы",
     *                  example="Ubuntu 20.04"
     *              ),
     *              @OA\Property(
     *                  property="updated_at",
     *                  format="date-time",
     *                  type="string",
     *                  description="Дата изменения",
     *                  example="2023-12-27T09:59:49.000000Z"
     *              ),
     *              @OA\Property(
     *                  property="created_at",
     *                  format="date-time",
     *                  type="string",
     *                  description="Дата создания",
     *                  example="2023-12-27T09:59:49.000000Z"
     *              ),
     *              @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  description="Id платформы",
     *                  example=1
     *              )
     *          )
     *      ),
     *      @OA\Response(
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
     *                      property="name",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля name",
     *                      example={
     *                          "The name field is required.",
     *                          "The name field must be a string.",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken."
     *                      }
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *      )
     *  )
     *  Создание новых записей сущности Platform после валидации данных
     *
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        $dto = PlatformDTO::fromRequest($request);

        $platform = $action->execute($dto);

        return $this->createResponse($platform, 'platform');
    }
}
