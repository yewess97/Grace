<?php

namespace App\Http\Requests;

use App\Traits\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UserRequest extends FormRequest
{
    use FormRequestHelper;

    protected array $filter_users_attribute;
    protected bool $is_single_attribute, $is_subset;

// اعمل حتى الـ progress bar في beforeSend في ajax

// وشوف حركة تغيير شكل الماوس

// خلي ملفات الـ jquery تبدأ بـ IIFE (function () {

//});







/**
// Example 2: Modify Missing Model Behavior with Custom Exception
// Manage user and throw a custom exception if user not found
Route::resource('users', UserController::class)
->missing(function (Request $request) {
throw new NotFoundHttpException('User not found.');
});
 */

/**
// Display a user and return a JSON response with error message if not found
Route::get('/users/{user}', [UserController::class, 'show'])
->name('users.show')
->missing (function (Request $request) {
return response()->json(['error' 'User not found. '], 484);
});
 */


// ابقى اعمل فولدر اسمه errors واعمل فيه فايلات للـ 404 عشان الـ not found والـ 503 عشان الـ server error أو الـ maintenance mode







    /**
     * Get the validation rules that apply to the request.
     *
     * @param string|null $id
     * @return array<string, mixed>
     */
    final public function rules(string $id = null): array
    {
        $this->filter_users_attribute = array(Arr::last(USER_ATTRIBUTES));

        // Check if $this->modelAttributes has exactly one element
        $this->is_single_attribute = count($this->modelAttributes) === 1;

        // Check if $filter_users_attribute is a subset of $this->modelAttributes
        $this->is_subset = empty(array_diff($this->filter_users_attribute, $this->modelAttributes));

        if ($this->is_single_attribute && $this->is_subset) {
            return [...$this->booleanValidation(ROLE)];
        }

        return $this->userValidation(USER_MODEL, false, $id);
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    final public function messages(): array
    {
        if ($this->is_single_attribute && $this->is_subset) {
            return [...$this->booleanValidation(ROLE, true, "Customer or Admin")];
        }

        return $this->userValidation(USER_MODEL, true);
    }
}
