<?php

namespace App\Http\Requests\ReleaseType;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

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
            'name' => ['required_without:description','string', 'max:255', Rule::unique('releases_types')->ignore($this->release_type, 'id')->whereNull('deleted_at')],
            'description' => ['required_without:name','string', 'max:255'],
        ];
    }


}
