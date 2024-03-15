<?php

namespace App\Http\Controllers\ChangeLog;

use App\Actions\ChangeLogs\CreateAction;
use App\DTO\ChangeLogDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeLog\StoreRequest;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/changes/",
     *     summary="Добавление записей об изменениях",
     *     security={{"bearer_token":{}}},
     *     tags={"ChangeLog"},
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
     *                  description="Что нового было добавлено?",
     *                  example="Был добавлен сервис для поиска иностранной музыки"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/ChangeLog" )
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
     *                      property="changes",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля changes",
     *                      example={
     *                          "The changes field is required when news is not present.",
     *                          "The changes field must be a string.",
     *                          "The changes field must not be greater than 255 characters."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="news",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля news",
     *                      example={
     *                          "The news field is required when changes is not present.",
     *                          "The news field must be a string.",
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
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *      )
     *  )
     *
     * Добавление записей об изменениях
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        /** Берем данные, прошедшие валидацию */
        $changelog = ChangeLogDTO::fromRequest($request); // создаем DTO

        $response = $action->execute($changelog);

        return $this->createResponse($response, 'changelog');
    }
}
