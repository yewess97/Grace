<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Subcategory;
use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    use FormRequestHelper;

    protected int $max_categories, $max_subcategories, $max_sizes;

    /**
     * Get the validation rules that apply to the request.
     *
     * @param string|null $id
     * @return array<string, mixed>
     */
    final public function rules(?string $id = null): array
    {
        $this->max_categories    = Category::query()->count([ID]);
        $this->max_subcategories = Subcategory::query()->count([ID]);
        $this->max_sizes         = count(PRODUCT_SIZE_ENUM);

        if ($this->operation === FILTER) {
            return [
                ...$this->multipleSelectionValidation(CATEGORIES_TABLE, $this->max_categories, CATEGORIES_TABLE),
                ...$this->multipleSelectionValidation(SUBCATEGORIES_TABLE, $this->max_subcategories, SUBCATEGORIES_TABLE),
                ...$this->multipleSelectionValidation(SIZES, $this->max_sizes, PRODUCT_SIZES_TABLE),
                ...$this->numberValidation(MIN_PRICE, 'numeric'),
                ...$this->numberValidation(MAX_PRICE, 'numeric'),
                $this->dataKeyOf(SORT) => ['nullable', 'string', 'in:'.implode(',', array_values(SORT_PRODUCTS_ENUM))],
            ];
        }

        $rules = [
            ...$this->nameDescriptionRules($id, NAME, 3, 100),
            ...$this->nameDescriptionRules(null, SHORT_DESCRIPTION, 5, 1000),
            ...$this->nameDescriptionRules(null, LONG_DESCRIPTION, 10, 5000),
            ...$this->imageValidation(MAIN_IMAGE),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, $this->max_categories, CATEGORIES_TABLE),
            ...$this->multipleSelectionValidation(RELATED_SUBCATEGORIES, $this->max_subcategories, SUBCATEGORIES_TABLE),
            ...$this->multipleSelectionValidation(SIZES, $this->max_sizes, PRODUCT_SIZES_TABLE),
            ...$this->numberValidation(OLD_PRICE, 'numeric'),
            ...$this->numberValidation(NEW_PRICE, 'numeric'),
            ...$this->numberValidation(QUANTITY, 'integer'),
            $this->dataKeyOf(STATUS) => ['required', 'in:0,1'],
        ];

        if (Arr::has($this->modelAttributes, THUMB_IMAGE)) {
            $rules[$this->dataKeyOf(THUMB_IMAGE).'.*'] = ['image', 'mimes:png,jpg,jpeg', 'max:2048'];
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        $cap_categories    = ucfirst(CATEGORIES_TABLE);
        $cap_subcategories = ucfirst(SUBCATEGORIES_TABLE);
        $cap_sizes         = ucfirst(pluralize(SIZE));
        $cap_status        = ucfirst(pluralize(STATUS));
        $cap_sort          = ucfirst(PRODUCTS_TABLE.' '.SORT);

        if ($this->operation === FILTER) {
            return [
                ...$this->multipleSelectionValidation(CATEGORIES_TABLE, $this->max_categories, null, $cap_categories, true),
                ...$this->multipleSelectionValidation(SUBCATEGORIES_TABLE, $this->max_subcategories, null, $cap_subcategories, true),
                ...$this->multipleSelectionValidation(SIZES, $this->max_sizes, null, $cap_sizes, true),
                ...$this->numberValidation(MIN_PRICE, 'numeric', true),
                ...$this->numberValidation(MAX_PRICE, 'numeric', true),
                "{$this->dataKeyOf(SORT)}.string" => "$cap_sort must be a text",
                "{$this->dataKeyOf(SORT)}.in"     => "$cap_sort must be one of the following: ".implode(', ', array_values(SORT_PRODUCTS_ENUM)),
            ];
        }

        return [
            ...$this->validationMessages(NAME, 2, 50, 'characters, numbers, and some symbols', PRODUCT_MODEL),
            ...$this->validationMessages(SHORT_DESCRIPTION, 5, 1000),
            ...$this->validationMessages(LONG_DESCRIPTION, 10, 5000),
            ...$this->imageValidation(MAIN_IMAGE, true),
            ...$this->multipleSelectionValidation(RELATED_CATEGORIES, $this->max_categories, null, $cap_categories, true),
            ...$this->multipleSelectionValidation(RELATED_SUBCATEGORIES, $this->max_subcategories, null, $cap_subcategories, true),
            ...$this->multipleSelectionValidation(SIZES, $this->max_sizes, null, $cap_sizes, true),
            ...$this->numberValidation(OLD_PRICE, 'numeric', true),
            ...$this->numberValidation(NEW_PRICE, 'numeric', true),
            ...$this->numberValidation(QUANTITY, 'integer', true),
            ...$this->requiredMessage($this->dataKeyOf(STATUS), $cap_status),
            "{$this->dataKeyOf(STATUS)}.in"  => "$cap_status must be Available or Not Available",
        ];
    }

    /**
     * Set the validation rules for the name & descriptions.
     *
     * @param string|null $id
     * @param string $attribute
     * @param int $min
     * @param int $max
     * @return array<string, string>
     */
    private function nameDescriptionRules(string|null $id, string $attribute, int $min, int $max): array
    {
        $unique_product = Rule::unique(PRODUCTS_TABLE, SLUG);

        $name_regex = $attribute === NAME
            ? "regex:/^[a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{}\'\"\\|:,.<>\/]*$/"
            : null;

        return [
            $this->dataKeyOf($attribute) => [
                "required", $name_regex, "min:$min", "max:$max",
                ($this->operation === UPDATE && isset($id)) ? $unique_product->ignore($id) : $unique_product
            ],
        ];
    }

    /**
     * Price & Quantity validation rules & messages.
     *
     * @param string $attribute
     * @param string $numericType
     * @param bool $isMessage
     * @return array<string, string>
     */
    private function numberValidation(string $attribute, string $numericType, bool $isMessage = false): array
    {
        $cap_attribute = capitalizeAll($attribute);
        $numeric_rules = ['numeric', 'regex:/^\d+(\.\d{1,2})?$/', 'max:9999.99'];
        $integer_rules = ['integer', 'regex:/^[1-9]\d*$/'];

        if ($isMessage) {
            return [
                ...$this->requiredMessage($this->dataKeyOf($attribute), $cap_attribute),
                "{$this->dataKeyOf($attribute)}.$numericType" => "$cap_attribute must be a number",
                "{$this->dataKeyOf($attribute)}.regex" => "$cap_attribute must be a number ".$numericType === 'numeric' ? "2 decimal places" : "",
                "{$this->dataKeyOf($attribute)}.max" => "$cap_attribute must be less than :max",
                "{$this->dataKeyOf($attribute)}.min" => "$cap_attribute must be at least :min",
            ];
        }

        return [
            $this->dataKeyOf($attribute) => [
                'required',
                $numericType === 'numeric' ? $numeric_rules : $integer_rules,
                'min:1'
            ],
        ];
    }
}
