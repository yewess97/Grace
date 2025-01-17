<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    use FormRequestHelper;

    protected array $user_id_validation = [USER_ID => USERS_TABLE];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    final public function rules(): array
    {
        $address_lines_rules = ['regex:/^[a-zA-Z0-9\s\-]+$/i', 'min:3', 'max:80'];
        $city_state_rules    = ['regex:/^[a-zA-Z\s]+$/i', 'min:2', 'max:50'];

        return [
            $this->dataKeyOf(ADDRESS1)    => ['required',  ...$address_lines_rules],
            $this->dataKeyOf(ADDRESS2)    => ['nullable',  ...$address_lines_rules],
            $this->dataKeyOf(CITY)        => ['required',  ...$city_state_rules],
            $this->dataKeyOf(STATE)       => ['nullable',  ...$city_state_rules],
            $this->dataKeyOf(COUNTRY)     => ['required'],
            $this->dataKeyOf(POSTAL_CODE) => ['required', 'integer', 'digits_between:5,6'],
            ...$this->collectionIdValidation($this->user_id_validation),
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        $cap_country     = ucfirst(COUNTRY);
        $cap_postal_code = capitalizeFirst(POSTAL_CODE);

        return [
            ...$this->validationMessages(ADDRESS1, 3, 80, "letters, numbers, and hyphen(-)"),
            ...$this->validationMessages(ADDRESS2, 3, 80, "letters, numbers, and hyphen(-)"),
            ...$this->validationMessages(CITY, 3, 50, "characters"),
            ...$this->validationMessages(STATE, 3, 50, "characters"),
            ...$this->requiredMessage($this->dataKeyOf(COUNTRY), $cap_country),
            ...$this->requiredMessage($this->dataKeyOf(POSTAL_CODE), $cap_postal_code),
            ...$this->integerMessage($this->dataKeyOf(POSTAL_CODE), $cap_postal_code),
            "{$this->dataKeyOf(POSTAL_CODE)}.digits_between" => "$cap_postal_code must be five digits",
            ...$this->collectionIdValidation($this->user_id_validation, true),
        ];
    }
}
