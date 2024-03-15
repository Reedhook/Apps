<?php

namespace App\Console;

use App\Jobs\CheckEmptyDirectoryInStorageJob;
use App\Jobs\CheckRefreshToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $check = new CheckRefreshToken();
        $checkDirectory = new CheckEmptyDirectoryInStorageJob();
        $schedule->job($check)->dailyAt('6:00');
        $schedule->job($checkDirectory)->dailyAt('6:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
