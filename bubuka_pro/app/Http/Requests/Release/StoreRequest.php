<?php

namespace App\Http\Requests\Release;

use App\Http\Requests\CustomFormRequest;
use App\Models\Project;
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
            'file' => ['required', 'file'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'platform_id' => ['required', 'integer','exists:platforms,id'],
            'change_id' => ['required', 'integer', 'exists:changes,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'release_type_id' => ['required', 'integer', 'exists:releases_types,id'],
            'is_ready' => ['boolean'],
            'technical_requirement_id' => ['required', 'integer', 'exists:technicals_requirements,id'],
            'version' => ['required', 'string', 'max:10'],
        ];
    }
}
