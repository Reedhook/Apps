<?php

namespace App\Http\Requests\TechnicalRequirement;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;


class StoreRequest extends CustomFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'os_type' => ['required', 'string', 'max:255'],
            'specifications' => ['string', 'max:255'],
        ];
    }
}
