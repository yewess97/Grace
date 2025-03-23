<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    use FormRequestHelper;

    protected int $max_product_sizes_count;
    protected array $product_id_validation = [PRODUCT_ID => PRODUCTS_TABLE];

    /**
     * Get the validation rules that apply to the request.
     *
     * @param int $maxProductSizesCount
     * @return array<string, mixed>
     */
    final public function rules(int $maxProductSizesCount = 0): array
    {
        $product_quantity_rules = ['required', 'integer', 'min:1'];

        $this->max_product_sizes_count = $maxProductSizesCount;

        $rules = [...$this->collectionIdValidation($this->product_id_validation)];

        if (empty(array_diff(CART_COMMON_ATTRIBUTES, $this->modelAttributes))) {
            $rules = [...$rules, ...$this->multipleSelectionValidation(PRODUCT_SIZE, $this->max_product_sizes_count)];
            $rules[$this->dataKeyOf(PRODUCT_QUANTITY)] = $product_quantity_rules;
        }

        if (empty(array_diff(QUICK_VIEW_COMMON_ATTRIBUTES, $this->modelAttributes))) {
            $rules = [...$rules, ...$this->multipleSelectionValidation(PRODUCT_SIZE_QUICK_VIEW, $this->max_product_sizes_count)];
            $rules[$this->dataKeyOf(PRODUCT_QUANTITY_QUICK_VIEW)] = $product_quantity_rules;
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
        $product_size_quantity = [
            CART_COMMON_ATTRIBUTES,
            QUICK_VIEW_COMMON_ATTRIBUTES,
        ];

        $messages = [...$this->collectionIdValidation($this->product_id_validation, true)];

        if (empty(array_diff($product_size_quantity[0], $this->modelAttributes))) {
            return $this->productSizeQuantityMessages($product_size_quantity[0]);
        }

        if (empty(array_diff($product_size_quantity[1], $this->modelAttributes))) {
            return $this->productSizeQuantityMessages($product_size_quantity[1]);
        }

        return $messages;
    }

    /**
     * Get the validation messages for the product size & quantity.
     *
     * @param array $attributes
     * @return array<string, string>
     */
    private function productSizeQuantityMessages(array $attributes): array
    {
        $cap_product_size     = capitalizeAll(PRODUCT_SIZE);
        $cap_product_quantity = capitalizeAll(PRODUCT_QUANTITY);

        return [
            ...$this->multipleSelectionValidation($attributes[0], $this->max_product_sizes_count, null, "$cap_product_size(s)", true),
            ...$this->requiredMessage($this->dataKeyOf($attributes[1]), $cap_product_quantity),
            ...$this->integerMessage($this->dataKeyOf($attributes[1]),  $cap_product_quantity),
            "{$this->dataKeyOf($attributes[1])}.min" => "$cap_product_quantity must be at least :min",
        ];
    }
}
