<?php

namespace App\Http\Controllers\ReleaseDownload;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\ReleaseDownload;
use Exception;

class CreateController extends Controller
{
    /**
     * Создание новых записей сущности ReleaseDownload
     *
     * @param  string  $ip
     * @param  string  $user_agent
     * @param  array   $utm
     * @param  int     $release_id
     * @return void
     * @throws Exception
     */
    public function store(string $ip, string $user_agent, array $utm, int $release_id):void
    {
        Release::findOrFail($release_id);
        $utm = json_encode($utm);
        /** Создание новой записи */
        /** Проверка на успешность операции */
        ReleaseDownload::create([
            'release_id' => $release_id,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'utm' => $utm,
        ]) ?: throw new Exception(' Ошибка сервера ', code: 500);;
    }
}
