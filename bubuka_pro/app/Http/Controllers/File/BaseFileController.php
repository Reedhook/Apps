<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Services\File\FileService;
use App\Services\File\ValidationService;


class BaseFileController extends Controller
{
    public FileService $file;
    public ValidationService $validation;

    public function __construct(FileService $fileService, ValidationService $validationService)
    {

        $this->file = $fileService;
        $this->validation = $validationService;

    }

}
