<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Jobs\RegisterMailJob;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery\Exception;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected string $redirectTo = '/';

    /**
     * @OA\Post(
     *     path="/api/auth/registration/",
     *     summary="Регистрация нового пользователя",
     *     tags={"Admin","Auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  description="Адрес электронной почты",
     *                  format="email",
     *                  example="user@example.com"
     *              ),
     *              @OA\Property(
     *                  property="is_admin",
     *                  type="boolean",
     *                  description="Установка статуса. Админ или нет",
     *                  example=false,
     *                  enum={true, false}
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="OK",
     *          @OA\JsonContent ( ref="#/components/schemas/User" )
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
     *                      property="email",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля email",
     *                      example={
     *                          "The email field is required.",
     *                          "The email field must be a string",
     *                          "The email field must be a valid email address.",
     *                          "The email field must not be greater than 255 characters.",
     *                          "The email has already been taken."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="is_admin",
     *                      type="string",
     *                      description="Возможная ошибка валидации поля is_admin" ,
     *                      example="The is_admin field must be true or false"
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
     *      ),
     *      @OA\Response(
     *            response=403,
     *            description="Forbidden",
     *            @OA\JsonContent( ref="#/components/schemas/403" )
     *      ),
     * )
     *
     *  Создание нового пользователя
     *
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */
    protected function create(RegisterRequest $request): JsonResponse
    {
        try {
            /** Используем транзакцию, чтобы не было случаев, когда пароль отправился, а пользователь не был создан  */
            DB::beginTransaction();

            /** Генерация случайного пароля */
            $password = Str::random(10);

            /** Получение данных, прошедших валидацию */
            $data = $request->validated();

            /** Пароль можно не хэшировать, хеширование происходит под капотом по протоколу bcrypt */
            $data['password'] = $password;

            /** Создание нового пользователя и проверка результата*/
            $user = User::create($data) ?: throw new Exception('Не удалось создать пользователя', 400);

            /** Производим поиск по id, чтобы получить все параметры, так как если не будет указан is_admin, то в $user ее не будет */
            $user = User::find($user['id']);
            $user['p_password'] = $password;
            // TODO: нужно потом убрать отправку пароля

            /** Пароль будет отправлен на указанную почту  */
            dispatch(new RegisterMailJob($data, $password));

            DB::commit();

            /** Логирование */
            Log::channel('user')
                ->info(
                    message: 'Был создан пользователь',
                    context: [
                        'user_id' => $user['id'],
                        'email' => $data['email']
                    ]
                );
            return response()->json($user, 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Ошибка при создании пользователя'], 500);
        }
    }
}
