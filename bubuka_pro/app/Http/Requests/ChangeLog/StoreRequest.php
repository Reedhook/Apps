<?php

namespace App\Http\Requests\ChangeLog;

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
            'changes' => ['required_without:news','string', 'max:255'],
            'news' => ['required_without:changes','string', 'max:255']
        ];
    }
}
