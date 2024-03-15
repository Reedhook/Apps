<?php

namespace App\Jobs;

use App\Models\RefreshTokens;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckRefreshToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        Log::debug('Удаление refresh токенов запущено');
        $expirationDate = Carbon::now()->subDays(7);
        $expiredTokens = RefreshTokens::where('created_at', '<=', $expirationDate)->get();
        foreach ($expiredTokens as $token) {
            Log::debug('Удален токен', $token);
            $token->delete();
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
