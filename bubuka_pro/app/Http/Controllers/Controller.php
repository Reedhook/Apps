<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info( title="OpenApi Documentation", version="1.0.0", description="Документация для микросервиса" )
 *
 * @OA\Tag( name="Admin", description="Методы только для администратора" )
 * @OA\Tag( name="Auth", description="Регистрация и авторизация" )
 * @OA\Tag( name="ChangeLog", description="Тугрики" )
 * @OA\Tag( name="Files", description="Файлы" )
 * @OA\Tag( name="Password", description="Действия с паролем" )
 * @OA\Tag( name="Platforms", description="Платформы" )
 * @OA\Tag( name="Projects", description="Проекты" )
 * @OA\Tag( name="Releases", description="Релизы" )
 * @OA\Tag( name="Releases_Downloads", description="Логирование скаченных релизов" )
 * @OA\Tag( name="Releases_Types", description="Типы релизов" )
 * @OA\Tag( name="Technicals_Requirements", description="Технические параметры" )
 *
 * @OA\Schema(
 *      schema="Auth",
 *      @OA\Property( property="access_token", type="string", description="Токен пользователя", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3MDUwMzQ1MTYsImV4cCI6MT" ),
 *      @OA\Property( property="token_type", type="string", description="Тип токена", example="bearer" ),
 *      @OA\Property( property="expires_in_access_token", type="integer", description="Время жизни токена в минутах", example=120 ),
 *      @OA\Property( property="refresh_token", type="string", description="Токен пользователя", example="7Kc1tqMYFVZYWH9O1wbJGFR9MgT27f2SbQgAoDrdK3VnHGKNILqVjRV1vPGGt00snr3Qr0FBo3pSrHwG" ),
 *      @OA\Property( property="expires_in_refresh_token", type="integer", description="Время жизни токена в минутах", example=10080 ),
 *  )
 *
 * @OA\Schema(
 *      schema="User",
 *      @OA\Property( property="status", type="boolean", example=true ),
 *      @OA\Property( property="body", type="object",
 *           @OA\Property( property="users/user", type="object",
 *               @OA\Property( property="id", type="integer", description="Id пользователя", example=1 ),
 *               @OA\Property( property="email", type="string", description="Почта пользователя", example="example@bubuka.info" ),
 *               @OA\Property( property="is_admin", type="integer", description="Установка статуса. Админ или нет", example=0 ),
 *               @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *               @OA\Property( property="created_at", format="date-time", type="string", description="Дата создания", example="2023-12-27T09:59:49.000000Z" ),
 *               @OA\Property( property="updated_at", format="date-time", type="string", description="Дата изменения", example="2023-12-27T09:59:49.000000Z" )
 *           )
 *       )
 * )
 *
 * @OA\Schema(
 *     schema="ChangeLog",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="changelogs/changelog", type="object",
 *              @OA\Property( property="id", type="integer", description="Id changelog", example=1 ),
 *              @OA\Property( property="changes", type="string", description="Изменения в релизе", example="1. Было переделано логирование. 2. Был переделан интрефейс" ),
 *              @OA\Property( property="news", type="string", description="Нововведения",example="Был добавлен сервис для поиска иностранной музыки" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="created_at", type="string", description="Дата создания", example="2023-12-27T09:59:49.000000Z" ),
 *              @OA\Property( property="updated_at", type="string", description="Дата изменения",example="2023-12-27T09:59:49.000000Z" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="File",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="file", type="object",
 *              @OA\Property( property="name", type="string", example="Readme", description="Название файла" ),
 *              @OA\Property( property="data", type="string", example="Тут должен быть текст, который находится в файле", description="Содержимое файла" ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="id", type="integer", example=1, description="Id файла" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Project",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="projects/project", type="object",
 *              @OA\Property( property="id", type="integer", example="1", description="Id проекта" ),
 *              @OA\Property( property="name", type="string", example="Мобильная разработка", description="Название проекта" ),
 *              @OA\Property( property="description", type="string", example="Данный проект нужен, чтобы ...", description="Краткое описание" ),
 *              @OA\Property( property="user_id", type="integer", example="1", description="Id пользователя создавшего проект" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" ),
 *              @OA\Property( property="platforms", type="array", @OA\Items( ref="#/components/schemas/Platform" ), description="Добавленные платформы" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Platform",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="paltforms/platform", type="object",
 *              @OA\Property( property="id", type="integer", example="1", description="Id платформы" ),
 *              @OA\Property( property="name", type="string", example="Ubuntu 20.04", description="Название платформы" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Technical_Requirement",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="technicals_requirements/technical_requirement", type="object",
 *              @OA\Property( property="id", type="integer", example="1", description="Id записи" ),
 *              @OA\Property( property="os_type", type="string", example="Unix", description="Тип операционной системы" ),
 *              @OA\Property( property="specifications", type="string", example="ОЗУ 16 Гб", description="Другие характеристики" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Release_Type",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="releases_types/release_type", type="object",
 *              @OA\Property( property="id", type="integer", example="1", description="Id записи" ),
 *              @OA\Property( property="name", type="string", example="Major Release", description="Название типа релиза" ),
 *              @OA\Property( property="description", type="string", example="Крупное обновление программного продукта с большим количеством новых функций и изменений.", description="Описания типа релиза"  ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" )
 *          )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Release_Download",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *          @OA\Property( property="releases_downloads/release_download", type="object",
 *              @OA\Property( property="id", type="integer", example=1, description="Id записи" ),
 *              @OA\Property( property="release_id", type="integer", example=2, description="Id релиза" ),
 *              @OA\Property( property="ip", type="string", example="127.0.0.1", description="Ip адрес" ),
 *              @OA\Property( property="user_agent", type="string", example="Mozilla/5.0(X11; Ubuntu; Linux x86_64; rv:122.0) Gecko/20100101 Firefox/122.0", description="User-agent" ),
 *              @OA\Property( property="utm", type="string", example="{\'Source\':null,\'Medium\':null,\'Campaign\':null}", description="Спеу метки" ),
 *              @OA\Property( property="created_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата создания" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", example="2023-12-27T09:59:49.000000Z", description="Дата изменения" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null )
 *         )
 *      )
 * )
 *
 * @OA\Schema(
 *     schema="Release",
 *     @OA\Property( property="status", type="boolean", example=true ),
 *     @OA\Property( property="body", type="object",
 *        @OA\Property( property="releases/release", type="object",
 *              @OA\Property( property="id", type="integer", example="1", description="Id записи" ),
 *              @OA\Property( property="project_id", type="integer", description="Id проекта", example=1 ),
 *              @OA\Property( property="platform_id", type="integer", description="Id платформы", example=1 ),
 *              @OA\Property( property="file_id", type="integer", description="Id файла", example=1 ),
 *              @OA\Property( property="release_type_id", type="integer", description="Id типа релиза", example=1 ),
 *              @OA\Property( property="technical_requirement_id", type="integer", description="Id технических характеристик", example=1 ),
 *              @OA\Property( property="change_id", type="integer", description="Id изменении", example=1 ),
 *              @OA\Property( property="description", type="string", description="Доп описание", example="Пока ничего" ),
 *              @OA\Property( property="is_ready", type="integer", description="Готов ли релиз к отображению на сайте и к скачивания", example=0 ),
 *              @OA\Property( property="is_public", type="integer", description="Подтвердил ли релиз супер-админ", example=0 ),
 *              @OA\Property( property="download_url", type="string", description="Ссылка для скачивания", example="http://127.0.0.1:8000/download/Мобильная_разработка/Windows_11/Major_Release/1.0.0" ),
 *              @OA\Property( property="version", type="string", description="Версия релиза", example="1.0.0" ),
 *              @OA\Property( property="created_at", format="date-time", type="string", description="Дата создания", example="2023-12-27T09:59:49.000000Z" ),
 *              @OA\Property( property="updated_at", format="date-time", type="string", description="Дата изменения", example="2023-12-27T09:59:49.000000Z" ),
 *              @OA\Property( property="deleted_at", nullable=true, format="date-time", type="string", description="Дата удаления", example=null )
 *          )
 *      )
 *  )
 *
 * @OA\Schema(
 *         schema="400-Update",
 *         @OA\Property( property="status", type="boolean", example="false", description="Статус ответа" ),
 *         @OA\Property( property="message", type="string", example="Данные идентичны, обновление не требуется", description="Сообщение ответа" ),
 *         @OA\Property( property="code", type="integer", example="405", description="Код ответа", description="Код ответа" )
 *       )
 *
 * @OA\Schema(
 *         schema="404",
 *         @OA\Property( property="status", type="boolean", example="false", description="Статус ответа" ),
 *         @OA\Property( property="message", type="string", example="Запись не найдена",description="Сообщение ответа" ),
 *         @OA\Property( property="code", type="integer", example="404", description="Код ответа" )
 *        )
 *
 * @OA\Schema(
 *          schema="500",
 *          @OA\Property( property="status", type="boolean", example="false", description="Статус ответа" ),
 *          @OA\Property( property="message", type="string", example="Ошибка сервера", description="Сообщение ответа" ),
 *          @OA\Property( property="code", type="integer", example="500", description="Код ответа" )
 *         )
 * @OA\Schema(
 *           schema="401",
 *           @OA\Property( property="status", type="boolean", example="false", description="Статус ответа" ),
 *           @OA\Property( property="message", type="string", example={"Token not provided","The token has been blacklisted"}, description="Сообщение ответа"  ),
 *           @OA\Property( property="code", type="integer", example="401", description="Код ответа" )
 *          )
 * @OA\Schema(
 *            schema="403",
 *            @OA\Property( property="status", type="boolean", example=false, description="Статус ответа" ),
 *            @OA\Property( property="message", type="string", example="Forbidden", description="Сообщение ответа"  ),
 *            @OA\Property( property="code", type="integer", example=401, description="Код ответа" )
 *           )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Ответ при удалении
    public function deleteResponse(): JsonResponse
    {
        return response()->json([
            'status' => true
        ]);
    }

    // Ответ при создании

    /**
     * @param  Model   $data
     * @param  string  $alias
     * @return JsonResponse
     */
    public function createResponse(Model $data, string $alias = 'model'): JsonResponse
    {
        return response()->json([
            'status' => true,
            'body' => [
                $alias => $data
            ]
        ], 201);
    }

    /**
     * @param               $data
     * @param  string       $alias
     * @param  string|null  $message
     * @return JsonResponse
     */
    public function OkResponse($data, string $alias = 'data', string $message = null): JsonResponse
    {
        $response = [
            'status' => true,
            'body' => [
                $alias => $data
            ]
        ];
        if($message !=null){
            $response_with_message = [
                'message' => $message
            ];
            $response = array_merge($response, $response_with_message);
        }
        return response()->json($response);
    }
}
