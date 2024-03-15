<?php

namespace App\Http\Controllers\Release;

use App\Http\Controllers\Controller;
use App\Services\File\FileService;
use App\Services\File\ValidationService;
use App\Services\Release\ReleaseService;


class BaseReleaseController extends Controller
{
    public ReleaseService $releaseService;

    public function __construct(ReleaseService $releaseService)
    {
        $this->releaseService = $releaseService;
    }

}
