<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    use FormRequestHelper;

    protected array $address_id_validation = [ADDRESS_ID => ADDRESSES_TABLE];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    final public function rules(): array
    {
        $rules = [
            $this->dataKeyOf(STATUS) => ['required', 'in:'.implode(',', array_values(ORDER_STATUS_ENUM))],
        ];

        if ($this->operation === ADD) {
            $rules = [...$rules, ...$this->collectionIdValidation($this->address_id_validation)];
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
        $messages = [
            ...$this->requiredMessage($this->dataKeyOf(STATUS), ucfirst(STATUS)),
            "{$this->dataKeyOf(STATUS)}.in" => ucfirst(STATUS).' must be one of the following: { '.implode(', ', array_keys(ORDER_STATUS_ENUM)).' }',
        ];

        if ($this->operation === ADD) {
            $messages = [...$messages, ...$this->collectionIdValidation($this->address_id_validation, true)];
        }

        return $messages;
    }
}
