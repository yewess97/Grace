<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    use FormRequestHelper;

    /**
     * Get the validation rules that apply to the request.
     *
     * @param string|null $id
     * @return array<string, mixed>
     */
    final public function rules(string $id = null): array
    {
        return [
            ...$this->categorySubcategoryNameValidation($id, CATEGORIES_TABLE),
            ...$this->imageValidation(MAIN_IMAGE),
            ...$this->imageValidation(BANNER_IMAGE),
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        return [
            ...$this->categorySubcategoryNameValidation(null, CATEGORIES_TABLE, CATEGORY_MODEL, true),
            ...$this->imageValidation(MAIN_IMAGE, true),
            ...$this->imageValidation(BANNER_IMAGE, true),
        ];
    }
}
