<?php

namespace App\Http\Requests\Release;

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
            'file' => ['file'],
            'platform_id' => ['integer', 'exists:platforms,id',],
            'change_id' => ['integer', 'exists:changes,id'],
            'description' => ['string', 'max:255'],
            'release_type_id' => ['integer', 'exists:releases_types,id'],
            'is_ready' => ['boolean'],
            'technical_requirement_id' => ['integer', 'exists:technicals_requirements,id'],
            'version' => ['string', 'max:10'],
        ];
    }


}
