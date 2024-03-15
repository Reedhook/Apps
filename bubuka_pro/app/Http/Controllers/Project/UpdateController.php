<?php

namespace App\Http\Controllers\Project;

use App\Actions\Project\PlatformAction;
use App\Actions\Project\UpdateAction;
use App\Actions\Project\UserAction;
use App\DTO\ProjectDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\UpdateRequest;
use App\Repositories\IndexRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    /**
     * @OA\Patch(
     *       path="/api/projects/{project_id}/",
     *       summary="Изменение записей о проекте",
     *       tags={"Admin","Projects"},
     *       security={{"bearer_token":{}}},
     *       @OA\Parameter(
     *           name="project_id",
     *           in="path",
     *           description="Id проекта",
     *           required=true,
     *           @OA\Schema(
     *               type="integer",
     *               format="int64",
     *               example=1
     *           )
     *       ),
     *       @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Мобильная разработка",
     *                  description="Название проекта"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="Просто разработка",
     *                  description="Описание проекта"
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Project" )
     *       ),
     *       @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус кода",
     *                  example=false ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Сообщение ошибки",
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля name",
     *                      example={
     *                          "The name field is required when description is not present.",
     *                          "The name field must be a string.",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example={
     *                          "The description field is required when name is not present.",
     *                          "The description field must be a string.",
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
     *           )
     *       ),
     *       @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent(ref="#/components/schemas/404")
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad Request",
     *           @OA\JsonContent(ref="#/components/schemas/400-Update")
     *       ),
     *       @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent( ref="#/components/schemas/401" )
     *       )
     *  )
     *  Обновление записей сущности Project после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        // Создаем DTO
        $dto = ProjectDTO::fromRequest($request);

        //Отправляем в actions
        $project = $action->execute($dto, $id);

        // Возвращаем ответ
        return $this->OkResponse($project, 'project');
    }

    /**
     * @OA\Patch(
     *     path="/api/projects/{project_id}/users/{user_id}",
     *     summary="Добавление пользователя к проекту",
     *     tags={"Admin","Projects"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="project_id",
     *          in="path",
     *          description="Id проекта",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="user_id",
     *          in="path",
     *          description="Id пользователя",
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
     *                  description="Статус кода",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example="К проекту добавлен пользователь"
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  type="object",
     *                  ref="#/components/schemas/Project"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=200
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *      ),
     *      @OA\Response(
     *            response=401,
     *            description="Unauthorized",
     *            @OA\JsonContent( ref="#/components/schemas/401" )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="BadRequest",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус кода",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example="Данный пользователь уже добавлен к проекту"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=400
     *              )
     *          )
     *      )
     * )
     * Метод для добавления пользователя к проекту
     * @param  Request     $request
     * @param  int         $project_id
     * @param  int         $user_id
     * @param  UserAction  $action
     * @return JsonResponse
     * @throws Exception
     */
    public function addDeleteUser(
        Request $request,
        int $project_id,
        int $user_id,
        UserAction $action,
    ): JsonResponse {
        // Получаем метод
        $method = $request->method();

        // отправляем в action для дальнейшей обработки
        $response = $action->execute($project_id, $user_id, $method);

        /** Возвращаем успешный JSON-ответ */
        return $this->OkResponse($response['response'], 'project', $response['message']);
    }

    /**
     * @OA\Delete(
     *     path="/api/projects/{project_id}/users/{user_id}",
     *     summary="Удаление пользователя из проекта",
     *     tags={"Admin","Projects"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="project_id",
     *          in="path",
     *          description="Id проекта",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="user_id",
     *          in="path",
     *          description="Id пользователя",
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
     *                  description="Статус кода",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example="К проекту добавлен пользователь"
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  type="object",
     *                  ref="#/components/schemas/Project"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=200
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *      ),
     *      @OA\Response(
     *            response=401,
     *            description="Unauthorized",
     *            @OA\JsonContent( ref="#/components/schemas/401" )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="BadRequest",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус кода",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example="Данного пользователя нет в проекте"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=400
     *              )
     *          )
     *      )
     * )
     * Метод для удаления пользователя к проекту
     */
    public function deleteUser()
    {
        // Метод просто нужен, чтобы swagger работал
    }

    /**
     * @OA\Patch(
     *     path="/api/projects/{project_id}/platforms/{platform_id}",
     *     summary="Добавление платформы к проекту",
     *     tags={"Admin","Projects", "Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="project_id",
     *          in="path",
     *          description="Id проекта",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="platform_id",
     *          in="path",
     *          description="Id платформы",
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
     *                  description="Статус кода",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example={
     *                      "Платформа добавлена к проекту",
     *                      "Платформа удалена из проекта"
     *                  }
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  type="object",
     *                  ref="#/components/schemas/Project"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=200
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *      ),
     *      @OA\Response(
     *            response=401,
     *            description="Unauthorized",
     *            @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *)
     *  Добавление платформы к проекту
     * @param  Request          $request
     * @param  int              $project_id
     * @param  int              $platform_id
     * @param  PlatformAction   $action
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function attachDetach(
        Request $request,
        int $project_id,
        int $platform_id,
        PlatformAction $action,
        IndexRepository $indexRepository
    ): JsonResponse {
        // Получаем метод
        $method = $request->method();

        // отправляем в action для дальнейшей обработки
        $response = $action->execute($project_id, $platform_id, $method);

        /** Возвращаем успешный JSON-ответ */
        return $this->OkResponse($response['response'], 'project', $response['message']);
    }

    /**
     * @OA\Delete(
     *     path="/api/projects/{project_id}/platforms/{platform_id}",
     *     summary="Добавление платформы к проекту",
     *     tags={"Admin","Projects", "Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="project_id",
     *          in="path",
     *          description="Id проекта",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="platform_id",
     *          in="path",
     *          description="Id платформы",
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
     *                  description="Статус кода",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение овтета",
     *                  example= "Платформа удалена из проекта"
     *              ),
     *              @OA\Property(
     *                  property="project",
     *                  type="object",
     *                  ref="#/components/schemas/Project"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=200
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *      ),
     *      @OA\Response(
     *            response=401,
     *            description="Unauthorized",
     *            @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *)
     *  Метод для удаления платформы из проекта
     */
    public function detach()
    {
        // Метод просто нужен, чтобы swagger работал
    }
}
