<?php

namespace App\Http\Controllers\Release;

use App\Actions\Release\CreateAction;
use App\DTO\ReleaseDTO;
use App\Http\Controllers\File\CreateController as FileController;
use App\Http\Requests\Release\StoreRequest;
use App\Services\Release\ReleaseService;
use App\Services\Release\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;

class CreateController extends BaseReleaseController
{
    protected FileController $file;
    protected ValidationService $validate;

    public function __construct(ReleaseService $releaseService, FileController $file, ValidationService $validate)
    {
        parent::__construct($releaseService);
        $this->file = $file;
        $this->validate = $validate;
    }

    /**
     * @OA\Post(
     *     path="/api/releases/",
     *     summary="Создание релиза",
     *     tags={"Releases"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      description="Файл для загрузки",
     *                      type="file"
     *                  ),
     *                  @OA\Property(
     *                      property="project_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id проекта",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="platform_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id платформы",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="change_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="id тугрика",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Краткое описание",
     *                      example="Исправлено : ... , ..."
     *                  ),
     *                  @OA\Property(
     *                      property="release_type_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id типа релиза",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="is_ready",
     *                      type="boolean",
     *                      description="Готов ли релиз к отображению на сайте и к скачивания",
     *                      example=false
     *                  ),
     *                  @OA\Property(
     *                      property="technical_requirement_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id технических характеристик",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="version",
     *                      type="string",
     *                      description="Версия релиза",
     *                      example="1.0.0"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Release" )
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
     *                      property="project_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля project_id",
     *                      example={
     *                          "The project_id field is required.",
     *                          "The project_id field must be a integer"
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля file",
     *                      example={
     *                          "The file field is required.",
     *                          "The file field must be a file."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="platform_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля platform_id",
     *                      example={
     *                          "The platform_id field is required.",
     *                          "The platform_id field must be a integer"
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example={
     *                          "The description field must be a string",
     *                          "The description field must not be greater than 255 characters."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="release_type_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля release_type_id",
     *                      example={
     *                          "The release_type_id field is required.",
     *                          "The release_type_id field must be a integer",
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="is_ready",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля is_ready",
     *                      example="The is_ready field is boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="technical_requirement_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля technical_requirement_id",
     *                      example={
     *                          "The technical_requirement_id field is required.",
     *                          "The technical_requirement_id field must be a integer",
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="version",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля version",
     *                      example={
     *                          "The version field is required.",
     *                          "The version field must be a string",
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
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent( ref="#/components/schemas/500" )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *  )
     * Создание новых записей сущности Release после валидации данных
     *
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        $dto = ReleaseDTO::fromRequest($request);

        $release = $action->execute($dto);



        return $this->createResponse($release, 'release');
    }

}
