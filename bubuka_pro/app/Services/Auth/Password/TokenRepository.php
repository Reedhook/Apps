<?php

namespace App\Services\Auth\Password;

use App\Jobs\ForgotMailJob;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TokenRepository implements TokenRepositoryInterface
{
    use CanResetPassword;

    /** Создание токена для сброса пароля
     * @param  CanResetPasswordContract  $user
     * @return array
     */
    public function create(CanResetPasswordContract $user): array
    {
        // Генерация случайного токена
        $token = Str::random(60);

        //Хеширование с bcrypt
        $encryptedToken = Hash::make($token);
        $data = [
            'email' => $user->email,
            'token' => $encryptedToken
        ];

        // Отправка ссылки на почту
        dispatch(new ForgotMailJob($data['email'], $token));

        // Сохранение токена в базе данных
        $this->saveToken($data);

        //TODO: Удалить выдачи токенов в будущем
        return [
            'status' => true,
            'token' => $token,
        ];
    }

    /** Сохранение токена в базе данных
     * @param  array  $data
     * @return void
     */
    public function saveToken(array $data): void
    {
        // В UpdateOrInsert первым параметром передаем email, для поиска записи, вторым параметром данные для обновления или добавление
        DB::table('password_reset_tokens')->updateOrInsert([
            'email' => $data['email']
        ], $data);
    }


    public function exists(CanResetPasswordContract $user, $token)
    {
        // TODO: Implement exists() method.
    }

    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        // TODO: Implement recentlyCreatedToken() method.
    }

    public function delete(CanResetPasswordContract $user)
    {
        // TODO: Implement delete() method.
    }

    public function deleteExpired()
    {
        // TODO: Implement deleteExpired() method.
    }
}
