<?php

namespace App\Http\Requests\File;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class IndexRequest extends CustomFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'limit' => ['required_with_all:offset','integer'],
            'offset' => ['integer']
        ];
    }
}
