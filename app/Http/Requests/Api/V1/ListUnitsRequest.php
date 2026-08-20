<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListUnitsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,24'],
            'search' => ['sometimes', 'string', 'max:100'],
            'unit_type' => ['sometimes', 'string', 'max:100'],
            'bedrooms' => ['sometimes', 'numeric', 'min:0'],
            'availability' => ['sometimes', 'in:available,reserved,sold,coming_soon,on_request'],
            'sort' => ['sometimes', 'in:newest,title,indoor_area_asc,indoor_area_desc'],
        ];
    }
}
