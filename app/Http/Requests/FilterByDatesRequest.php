<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class FilterByDatesRequest extends FormRequest
{
    use FormRequestHelper;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    final public function rules(): array
    {
        return [
            $this->dataKeyOf(START_DATE) => ['required', 'date', 'after_or_equal:2022-06-24'],
            $this->dataKeyOf(END_DATE)   => ['required', 'date', 'before_or_equal:'.now()->toDateString()],
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
            ...$this->requiredMessage($this->dataKeyOf(START_DATE), capitalizeAll(START_DATE)),
            "{$this->dataKeyOf(START_DATE)}.date" => 'This field must be a date',
            "{$this->dataKeyOf(START_DATE)}.after_or_equal" => 'Start date must begin from 2022-06-25 or later',
            ...$this->requiredMessage($this->dataKeyOf(END_DATE), capitalizeAll(END_DATE)),
            "{$this->dataKeyOf(END_DATE)}.date" => 'This field must be a date',
            "{$this->dataKeyOf(END_DATE)}.before_or_equal" => 'End date must be from '.now()->toDateString().' or earlier',
        ];
    }
}
