<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    use FormRequestHelper;

    protected string $requestType;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    final public function rules(?string $requestType = null): array
    {
        $this->requestType = $requestType ?? REGISTER;

         return $this->userValidation($this->requestType);
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        return $this->userValidation($this->requestType, true);
    }
}
