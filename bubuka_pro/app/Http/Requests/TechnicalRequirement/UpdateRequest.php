<?php

namespace App\Http\Requests\TechnicalRequirement;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateRequest extends CustomFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'os_type' => ['required_without:specifications','string', 'max:255'],
            'specifications' => ['required_without:os_type','string', 'max:255'],
        ];
    }


}
