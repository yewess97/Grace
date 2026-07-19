<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    use FormRequestHelper;

    protected array $product_id_validation = [PRODUCT_ID => PRODUCTS_TABLE];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    final public function rules(): array
    {
        return [
            $this->dataKeyOf(RATING)    => ['required', 'in:'.implode(',', array_values(REVIEW_RATING_ENUM))],
            $this->dataKeyOf(TITLE)     => ['required', 'regex:/^[A-Za-z\s!@#$%^&*()_+\-=\[\]{}\'\"\\|:,.\/]+$/', 'min:2', 'max:50'],
            $this->dataKeyOf(BODY_TEXT) => ['required', 'regex:/^[\w\W]+$/', 'min:2', 'max:1500'],
            ...$this->collectionIdValidation($this->product_id_validation),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        $cap_rating = ucfirst(RATING);

        return [
            ...$this->requiredMessage($this->dataKeyOf(RATING), $cap_rating),
             "{$this->dataKeyOf(RATING)}.in"  => "$cap_rating must be from one star to five stars",
            ...$this->validationMessages(TITLE, 2, 50, 'characters'),
            ...$this->validationMessages(BODY_TEXT, 2, 1500),
            ...$this->collectionIdValidation($this->product_id_validation, true),
        ];
    }
}
