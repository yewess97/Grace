<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
{
    use FormRequestHelper;

    protected int $max_categories;

    /**
     * Get the validation rules that apply to the request.
     *
     * @param string|null $id
     * @return array<string, mixed>
     */
    final public function rules(?string $id = null): array
    {
        $this->max_categories = (count((array) $this->dataValues()[2]) === 5) ? 5 : 4;

        return [
            ...$this->categorySubcategoryNameValidation($id, SUBCATEGORIES_TABLE),
            ...$this->imageValidation(MAIN_IMAGE),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, $this->max_categories, CATEGORIES_TABLE),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        $cap_category    = ucfirst(CATEGORY_MODEL);
        $cap_subcategory = ucfirst(SUBCATEGORY_MODEL);

        return [
            ...$this->categorySubcategoryNameValidation(null, SUBCATEGORIES_TABLE, SUBCATEGORY_MODEL, true),
            ...$this->imageValidation(MAIN_IMAGE, true),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, $this->max_categories, null, pluralize($cap_category), true),
        ];
    }
}
