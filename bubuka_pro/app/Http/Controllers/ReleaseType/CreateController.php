<?php

namespace App\Http\Controllers\ReleaseType;

use App\Actions\ReleaseType\CreateAction;
use App\DTO\ReleaseTypeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseType\StoreRequest;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/releases_types/",
     *      summary="Добавление записи о типе релиза",
     *      tags={"Releases_Types"},
     *      security={{"bearer_token":{}}},
     *      @OA\RequestBody (
     *          @OA\JsonContent (
     *              required={"name"},
     *              @OA\Property (
     *                  property="name",
     *                  type="string",
     *                  example="Major Release",
     *                  description="Название типа релиза"
     *              ),
     *              @OA\Property (
     *                  property="description",
     *                  type="string",
     *                  example="Крупное обновление программного продукта с большим количеством новых функций и изменений.",
     *                  description="Описание типа релиза"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Release_Type" )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property (
     *                      property="name",
     *                      type="string",
     *                      example={
     *                          "The name field is required.",
     *                          "The name field must be a string.",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken."
     *                      }
     *                  ),
     *                  @OA\Property (
     *                      property="description",
     *                      type="string",
     *                      example={
     *                          "The description field must be a string",
     *                          "The description field must not be greater than 255 characters."
     *                      }
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *  )
     *  Создание новых записей сущности Release_type после валидации данных
     *
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        $dto = ReleaseTypeDTO::fromRequest($request);

        $rt = $action->execute($dto);

        return $this->createResponse($rt, 'release_type');

    }
}
