<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\CustomFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'max:255',  Rule::unique('projects')->ignore($this->project, 'id')->whereNull('deleted_at')],
            'description' => ['required', 'string', 'max:255']
        ];
    }
}
