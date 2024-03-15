<?php

namespace App\Http\Controllers\Project;

use App\Actions\Project\CreateAction;
use App\DTO\ProjectDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreRequest;
use App\Http\Requests\Project\UpdateRequest;
use App\Models\Project;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/projects/",
     *     summary="Создание нового проекта",
     *     security={{"bearer_token":{}}},
     *     tags={"Admin","Projects"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              required={"name","description"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Название проекта",
     *                  example="Мобильная разработка"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="Описание проекта",
     *                  example="Просто разработка"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  description="Id проекта",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  description="Название проекта",
     *                  example="Мобильная разработка"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  description="Краткое описание",
     *                  example="Данный проект нужен, чтобы ..."
     *              ),
     *              @OA\Property(
     *                  property="admin_id",
     *                  type="integer",
     *                  description="Id пользователя создавшего проект",
     *                  example=1
     *              ),
     *              @OA\Property(
     *                  property="created_at",
     *                  format="date-time",
     *                  description="Дата создания",
     *                  type="string",
     *                  example="2023-12-27T09:59:49.000000Z"
     *              ),
     *              @OA\Property(
     *                  property="updated_at",
     *                  format="date-time",
     *                  description="Дата изменения",
     *                  type="string",
     *                  example="2023-12-27T09:59:49.000000Z"
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
     *                          "The name field must be a string",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example={
     *                          "The description field is required.",
     *                          "The description field must be a string",
     *                          "The description field must not be greater than 255 characters."
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
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *  )
     *  Создание новых записей сущности Project после валидации данных
     *
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        $dto = ProjectDTO::fromRequest($request);
        $project = $action->execute($dto);


        return $this->createResponse($project, 'project');
    }
}
