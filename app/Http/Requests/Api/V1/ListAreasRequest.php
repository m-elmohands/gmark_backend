<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListAreasRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,24'],
            'featured' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:100'],
            'sort' => ['sometimes', 'in:display_order,name,newest'],
        ];
    }

    public function filters(): array
    {
        return [...$this->validated(), 'featured' => $this->boolean('featured')];
    }
}
