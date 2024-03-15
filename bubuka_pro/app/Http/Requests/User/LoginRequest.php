<?php

namespace App\Http\Requests\User;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends CustomFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:10']
        ];
    }
}
