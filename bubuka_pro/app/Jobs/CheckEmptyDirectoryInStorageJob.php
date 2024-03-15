<?php

namespace App\Jobs;

use App\Http\Controllers\File\DeleteController;
use App\Services\File\FileService;
use App\Services\File\ValidationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CheckEmptyDirectoryInStorageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $deletDirectories = new DeleteController(new FileService(), new ValidationService());

        Log::debug('Удаление пустых директории запущено');
        $deletDirectories->deleteEmptyDirectories('/');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
