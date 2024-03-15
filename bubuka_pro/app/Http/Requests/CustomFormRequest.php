<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class CustomFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {

        $file = $validator->errors()->first();
        $callerClass = get_class($this);

        Log::error('Validation error in file: ' . $file . ' in class: ' . $callerClass, [
            'errors' => $validator->errors(),
            'code' => 422
        ]);

        throw new HttpResponseException(response()->json(['status'=>false, 'errors'=>$validator->errors(), 'code'=>422], 422));
    }
}
