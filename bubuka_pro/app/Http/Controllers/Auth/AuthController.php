<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RefreshRequest;
use App\Models\RefreshTokens;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/auth/login/",
     *     summary="Авторизация",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  description="Адрес электронной почты",
     *                  format="email",
     *                  example="user@example.com"
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  description="Ранее полученный пароль при регистрации",
     *                  example="M9H5466ej3"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Auth")
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
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля password",
     *                      example={
     *                          "The password field is required.",
     *                          "The password field must be a string",
     *                          "The password field must not be greater than 10 characters.",
     *                       }
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *          )
     *     )
     * )
     * Авторизация пользователя
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Из request берется email и password
        $credentials = request(['email', 'password']);

        // Попытка авторизации. При удачной авторизации создается токен
        if (!$accessToken = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        /** Каждый раз при авторизации удаляем старый refresh токен, если существует */
        $refreshToken = RefreshTokens::where('user_id', auth()->id())->first();
        !$refreshToken?: $refreshToken->delete();

        // Создаем refresh токен и записываем в бд
        $refreshToken = Str::random(80);
        RefreshTokens::firstOrCreate([
            'user_id' => auth()->id(),
            'token' => $refreshToken
        ]);
        Log::channel('user')
            ->info(
                message: 'Авторизован',
                context: [
                    'email' => request(['email']),
                    'refresh' => $refreshToken
                ]
            );
        return $this->respondWithToken($accessToken, $refreshToken);
    }
    /**
     * @OA\Get(
     *     path="/api/auth/users/",
     *     summary="Информация о пользователях",
     *     security={{"bearer_token":{}}},
     *     tags={"Auth", "Admin"},
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/User" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent( ref="#components/schemas/403")
     *     )
     *  )
     * Получить пользователей.
     *
     * @return JsonResponse
     */
    public function users():JsonResponse
    {
        return $this->OkResponse(User::all(), 'users');
    }

    /**
     * @OA\Get(
     *     path="/api/auth/user/",
     *     summary="Информация о пользователе",
     *     security={{"bearer_token":{}}},
     *     tags={"Auth"},
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/User" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     ),
     *  )
     * Получить авторизованного пользователя.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return $this->OkResponse(auth()->user(), 'user');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout/",
     *     summary="Деавторизация",
     *     security={{"bearer_token":{}}},
     *     tags={"Auth"},
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  description="Статус ответа",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Содержимое ответа",
     *                  example="Successfully logged out"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     * )
     * ДеАвторизация (Токен становится не валидным).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh/",
     *     summary="Refresh",
     *     tags={"Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *           @OA\JsonContent(
     *               required={"token"},
     *               @OA\Property(
     *                   property="token",
     *                   type="string",
     *                   description="Refresh Token",
     *                   example="L6a9f9Q6pP8vEvwfveYPZq6ka8a0TKvh8SdyvAMbRuq6WLPu3ca3Yde9Q4xArWbGDpp2GKo9V33rDeeD"
     *               )
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property( property="access_token", type="string", description="Токен пользователя", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3MDUwMzQ1MTYsImV4cCI6MT" ),
     *       @OA\Property( property="token_type", type="string", description="Тип токена", example="bearer" ),
     *       @OA\Property( property="expires_in", type="integer", description="Время жизни токена в минутах", example=525960 )
     *           )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=false,
     *                  description="Статус ответа"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Refresh Token не действителен",
     *                  description="Сообщение ответа"
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=401,
     *                  description="Код ответа"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *               @OA\Property(
     *                   property="status",
     *                   type="boolean",
     *                   description="Статус ответа",
     *                   example=false
     *               ),
     *               @OA\Property(
     *                   property="errors",
     *                   type="object",
     *                   description="Сообщение об ошибке",
     *                   example={
     *                       "The token field is required.",
     *                       "The token field must be a string",
     *                       "The token field must not be greater than 80 characters."
     *                   }
     *               ),
     *               @OA\Property(
     *                   property="code",
     *                   type="integer",
     *                   description="Код ответа",
     *                   example=422
     *               )
     *           )
     *      )
     *  )
     * Refresh a token.
     *
     * @param  RefreshRequest  $request
     * @return JsonResponse
     */
    public function refresh(RefreshRequest $request): mixed
    {
        // Получаем только те данные, которые прошли валидацию
        $token = $request->validated();
        // Поиск в бд
            $refreshToken = RefreshTokens::where('token', $token)->first();

        // Если присланный токен существует в бд и со времени создания токена прошло меньше 7 дней, то true
        if ($refreshToken && Carbon::parse($refreshToken['created_at'])->addDays(7) >= now()) {
            // Отправляем новый access токен
            return $this->respondWithToken(auth()->refresh());
        } else {
            return response()->json([
                'status'=>false,
                'message'=>'Refresh Token не действителен',
                'code'=>401
            ],401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string       $accessToken
     * @param  string|null  $refreshToken
     * @return JsonResponse
     */
    protected function respondWithToken(string $accessToken, string $refreshToken = null): JsonResponse
    {
        $response = [
            'access_token' => $accessToken,
            'expires_in_access_token' => auth()->factory()->getTTL(),
            'token_type' => 'bearer'
        ];
        if($refreshToken !=null){
            $response_with_message = [
                'refreshToken' => $refreshToken,
                'expires_in_refresh_token' => 10080 // на 7 дней
            ];
            $response = array_merge($response, $response_with_message);
        }
        return response()->json($response);
    }
}
