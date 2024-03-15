<?php

namespace App\Http\Requests\ReleaseDownload;

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
            'ip' => ['string', 'max:16'],
            'user_agent' => ['string'],
            'utm' => ['string'],
        ];
    }


}
