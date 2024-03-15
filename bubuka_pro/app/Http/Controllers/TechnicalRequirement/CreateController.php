<?php

namespace App\Http\Controllers\TechnicalRequirement;

use App\Actions\TechnicalRequirement\CreateAction;
use App\DTO\TechnicalRequirementDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\TechnicalRequirement\StoreRequest;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/techs_reqs/",
     *     summary="Добавление новых технических характеристик",
     *     tags={"Technicals_Requirements"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="os_type",
     *                  type="string",
     *                  description="Операционная система",
     *                  example="Unix"
     *              ),
     *              @OA\Property(
     *                  property="specifications",
     *                  type="string",
     *                  example="ОЗУ 4 ГБ",
     *                  description="Прочие характеристики"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Technical_Requirement" )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @OA\Property( property="errors", type="object",
     *                  @OA\Property(
     *                      property="os_type",
     *                      type="string",
     *                      example={
     *                          "The os_type field is required.",
     *                          "The os_type field must be a string.",
     *                          "The os_type field must not be greater than 255 characters.",
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="specifications",
     *                      type="string",
     *                      example={
     *                          "The specifications field must be a string",
     *                          "The specifications field must not be greater than 255 characters.",
     *                      }
     *                  )
     *             ),
     *             @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус ответа",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Содержимое ответа",
     *                  example="Запись не создана"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=400
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     *  )
     *  Создание новых записей сущности TechnicalRequirement после валидации данных
     *
     * @param  StoreRequest  $request
     * @param  CreateAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, CreateAction $action): JsonResponse
    {
        $dto = TechnicalRequirementDTO::fromRequest($request);
        $tech = $action->execute($dto);
        return $this->createResponse($tech, 'technical_requirement');
    }
}
