<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait FormRequestHelper
{
    /**
     * Form Request Constructor.
     *
     * @param  string  $operation
     * @param  string  $model
     * @param  array  $modelAttributes
     * @return void
     */
    final public function __construct(
        private readonly string $operation,
        private readonly string $model,
        private readonly array $modelAttributes
    ){}

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    final public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation data from the request.
     *
     * @return array<string, string>
     */
    final public function data(): array
    {
        return request()?->only(array_map(fn(string $key) =>
            "{$this->operation}_{$this->model}_$key", $this->modelAttributes)
        );
    }

    /**
     * Dictionary of data attributes.
     *
     * @param  array  $array
     * @return array<string, string>
     */
    private function dictionaryOf(array $array): array
    {
        return collect($array)
            ->mapWithKeys(fn(mixed $attributeValue, string $attribute) => [$this->modelAttributes[$attribute] => $attributeValue])
            ->toArray();
    }

    /**
     * Data attributes for validation.
     *
     * @param  string|null  $type
     * @return array<string, string>
     */
    private function dataAttributes(?string $type = null): array
    {
        [$dataKeys, $dataValues] = Arr::divide($this->data());

        return $type === 'values'
            ? $this->dictionaryOf($dataValues)
            : $this->dictionaryOf($dataKeys);
    }

    /**
     * Data keys for validation.
     *
     * @param  string  $attribute
     * @return string
     */
    final public function dataKeyOf(string $attribute): string
    {
        return $this->dataAttributes()[$attribute];
    }

    /**
     * Data values for validation.
     *
     * @return array<string, string>
     */
    final public function dataValues(): array
    {
        return array_values($this->dataAttributes('values'));
    }

    /**
     * Show the required message for the given attribute.
     *
     * @param string $attribute
     * @param string $capAttribute
     * @return array<string, string>
     */
    final protected function requiredMessage(string $attribute, string $capAttribute): array
    {
        return ["$attribute.required" => "$capAttribute is required"];
    }

    /**
     * Show number (integer) message for the given attribute.
     *
     * @param string $attribute
     * @param string $capAttribute
     * @return array<string, string>
     */
    final protected function integerMessage(string $attribute, string $capAttribute): array
    {
        return ["$attribute.integer" => "$capAttribute must be a number"];
    }

    /**
     * Show not an existing message for the given attribute.
     *
     * @param string $attribute
     * @param string $collection
     * @return array<string, string>
     */
    final protected function existsMessage(string $attribute, string $collection): array
    {
        return ["$attribute.exists" => "This $collection does not exist"];
    }

    /**
     * Validation messages.
     *
     * @param string $attribute
     * @param int $min
     * @param int $max
     * @param string|null $regexRules
     * @param string|null $uniqueCollection
     * @return array<string, string>
     */
    final protected function validationMessages(string $attribute, int $min, int $max, ?string $regexRules = null, ?string $uniqueCollection = null): array
    {
        $cap_attribute = capitalizeAll($attribute);

        return [
            ...$this->requiredMessage($this->dataKeyOf($attribute), $cap_attribute),
            "{$this->dataKeyOf($attribute)}.regex"  => "$cap_attribute must contain only $regexRules",
            "{$this->dataKeyOf($attribute)}.min"    => "$cap_attribute must be at least $min characters",
            "{$this->dataKeyOf($attribute)}.max"    => "$cap_attribute must be less than $max characters",
            "{$this->dataKeyOf($attribute)}.unique" => "This $uniqueCollection already exists",
        ];
    }

    /**
     * Collection id validation rules & messages.
     *
     * @param array $attribute
     * @param bool $isMessage
     * @return array<string, string>
     */
    final protected function collectionIdValidation(array $attribute, bool $isMessage = false): array
    {
        $collection_id     = array_keys($attribute)[0];
        $collection_table  = array_values($attribute)[0];
        $collection_model  = singularize(array_values($attribute)[0]);
        $cap_collection_id = capitalizeAll(str_replace('_'.ID, '', array_keys($attribute)[0]));

        if ($isMessage) {
            return [
                ...$this->requiredMessage($this->dataKeyOf($collection_id), $cap_collection_id),
                ...$this->integerMessage($this->dataKeyOf($collection_id), $cap_collection_id),
                ...$this->existsMessage($this->dataKeyOf($collection_id), $collection_model),
            ];
        }

        return [
            $this->dataKeyOf($collection_id) => ["required", "integer", "exists:$collection_table,".ID],
        ];
    }

    /**
     * Name validation rules & messages for the category & subcategory.
     *
     * @param string|null $id
     * @param string $tableName
     * @param string|null $uniqueCollectionForMessage
     * @param bool $isMessage
     * @return array<string, string>
     */
    final protected function categorySubcategoryNameValidation(string|null $id, string $tableName, ?string $uniqueCollectionForMessage = null, bool $isMessage = false): array
    {
        $unique_collection = Rule::unique($tableName, SLUG);

        if ($isMessage) {
            return $this->validationMessages(NAME, 2, 50, 'characters', $uniqueCollectionForMessage);
        }

        return [
            $this->dataKeyOf(NAME) => [
                'required', 'string', 'regex:/^[a-zA-Z&\s]+$/', 'min:2', 'max:50',
                $this->operation === UPDATE
                    ? $unique_collection->ignore($id)
                    : $unique_collection,
            ],
        ];
    }

    /**
     * User validation rules & messages.
     *
     * @param string $requestType
     * @param bool $isMessage
     * @param string|null $id
     * @return array<string, string>
     */
    final protected function userValidation(string $requestType, bool $isMessage = false, ?string $id = null): array
    {
        $cap_token           = ucfirst(TOKEN);
        $cap_email           = ucfirst(EMAIL);
        $cap_password        = ucfirst(PASSWORD);
        $name_rules          = ['required', 'string', 'regex:/^[a-zA-Z\s]+$/', 'min:2', 'max:50'];
        $unique_email        = Rule::unique(USERS_TABLE, EMAIL);
        $addition_email_rule = null;

        if ($requestType === FORGOT_PASSWORD) {
            $unique_email = null;
        }

        if (isset($id) && $this->operation === UPDATE && $requestType === USER_MODEL) {
            $addition_email_rule = $unique_email->ignore((int) $id);
        }

        if (in_array($requestType, [LOGIN, RESET_PASSWORD], true)) {
            $addition_email_rule = 'exists:'.USERS_TABLE.','.EMAIL;
        }

        $email_rules = [
            $this->dataKeyOf(EMAIL) => [
                'required', EMAIL.':rfc,spoof,filter', $addition_email_rule ?? $unique_email,
            ],
        ];

        $email_messages = [
            ...$this->requiredMessage($this->dataKeyOf(EMAIL), $cap_email),
            "{$this->dataKeyOf(EMAIL)}.".EMAIL => "$cap_email must be valid ".EMAIL." address",
            "{$this->dataKeyOf(EMAIL)}.unique" => "This $cap_email already exists",
            ...$this->existsMessage($this->dataKeyOf(EMAIL), $cap_email)
        ];

        if ($requestType === FORGOT_PASSWORD) {
            return $isMessage
                ? $email_messages
                : $email_rules;
        }

        $password_rules = [
            $this->dataKeyOf(PASSWORD) => [
                'required',
                $requestType === LOGIN
                    ? Password::min(8)
                    : Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ];

        $password_messages = [
            ...$this->requiredMessage($this->dataKeyOf(PASSWORD), $cap_password),
            "{$this->dataKeyOf(PASSWORD)}.min" => "The $cap_password must be at least 8 characters.",
        ];

        if ($requestType === LOGIN) {
            return $isMessage
                ? [
                    ...$email_messages,
                    ...$password_messages,
                ]
                : [
                ...$email_rules,
                ...$password_rules,
                ];
        }

        if ($requestType === RESET_PASSWORD) {
            if ($isMessage) {
                $password_messages = [
                    ...$password_messages,
                    ...$this->requiredMessage($this->dataKeyOf(PASSWORD_CONFIRMATION), capitalizeFirst(PASSWORD_CONFIRMATION)),
                    "{$this->dataKeyOf(PASSWORD_CONFIRMATION)}.same" => pluralize($cap_password)." don't match",
                ];

                return [
                    ...$email_messages,
                    ...$this->requiredMessage($this->dataKeyOf(TOKEN), $cap_token),
                    "{$this->dataKeyOf(TOKEN)}.string" => $cap_token." is invalid",
                    ...$this->existsMessage($this->dataKeyOf(TOKEN), $cap_token),
                    ...$password_messages,
                ];
            }

            $password_rules = [
                ...$password_rules,
                $this->dataKeyOf(PASSWORD_CONFIRMATION) => ['required', 'same:'.RESET_PASSWORD.'_'.USER_MODEL.'_'.PASSWORD],
            ];

            return [
                ...$email_rules,
                $this->dataKeyOf(TOKEN) => ['required', 'string', 'exists:'.PASSWORD_RESETS_TABLE.','.TOKEN],
                ...$password_rules,
            ];
        }

        $names_rules = [
            $this->dataKeyOf(FIRST_NAME) => $name_rules,
            $this->dataKeyOf(LAST_NAME) => $name_rules,
        ];

        $names_messages = [
            ...$this->validationMessages(FIRST_NAME, 2, 50, 'characters', true),
            ...$this->validationMessages(LAST_NAME, 2, 50, 'characters', true),
        ];

        $register_user_rules = [
            ...$names_rules,
            ...$email_rules,
            ...$password_rules,
        ];

        $register_user_messages = [
            ...$names_messages,
            ...$email_messages,
            ...$password_messages,
        ];

        if ($requestType === USER_MODEL) {
            return $isMessage
                ? [...$register_user_messages, ...$this->booleanValidation(ROLE, true, "Customer or Admin")]
                : [...$register_user_rules, ...$this->booleanValidation(ROLE)];
        }

        return $requestType === REGISTER && $isMessage
            ? $register_user_messages
            : $register_user_rules;
    }

    /**
     * Main image validation rules & messages.
     *
     * @param string $image
     * @param bool $isMessage
     * @return array<string, string>
     */
    final protected function imageValidation(string $image, bool $isMessage = false): array
    {
        $cap_main_image = capitalizeAll($image);

        if ($isMessage) {
            return [
                ...$this->requiredMessage($this->dataKeyOf($image), $cap_main_image),
                 "{$this->dataKeyOf($image)}.image" => "$cap_main_image must be a valid image",
                 "{$this->dataKeyOf($image)}.mimes" => "Allowed ".strtolower($cap_main_image)." formats are png, jpg, jpeg",
                 "{$this->dataKeyOf($image)}.max"   => "$cap_main_image must be less than 2MB",
            ];
        }

        return [
            $this->dataKeyOf($image) => [
                Rule::requiredIf($this->operation === ADD),
                Rule::when($this->operation === UPDATE, 'nullable'),
                'image', 'mimes:png,jpg,jpeg', 'max:2048',
            ],
        ];
    }

    /**
     * Multiple selection validation rules & messages.
     *
     * @param string $attribute
     * @param int|string $max
     * @param string|null $tableName
     * @param string|null $capAttribute
     * @param bool $isMessage
     * @return array<string, string>
     */
    final protected function multipleSelectionValidation(string $attribute, int|string $max, ?string $tableName = null, ?string $capAttribute = null, bool $isMessage = false): array
    {
        $multiple_selection_rules = [
            $this->dataKeyOf($attribute) => ["array", "min:1", "max:$max"],
        ];

        $multiple_selection_rules["{$this->dataKeyOf($attribute)}.*"] = [$this->operation === FILTER ? "nullable" : "required"];

        if (isset($tableName)) {
            $additional_rules = $tableName === PRODUCT_SIZES_TABLE
                ? ["in:".implode(',', array_values(PRODUCT_SIZE_ENUM))]
                : ["not_in:0", "exists:$tableName,".ID];

            $multiple_selection_rules["{$this->dataKeyOf($attribute)}.*"] = [
                ...$multiple_selection_rules["{$this->dataKeyOf($attribute)}.*"],
                ...$additional_rules
            ];
        }

        if ($isMessage) {
            $messages = [
                 "{$this->dataKeyOf($attribute)}.*.required" => "$capAttribute is(are) required",
                 "{$this->dataKeyOf($attribute)}.*.not_in"   => "$capAttribute ids must be greater than :not_in",
                 "{$this->dataKeyOf($attribute)}.*.exists"   => "Selected $capAttribute don't exist",

                 "{$this->dataKeyOf($attribute)}.array" => "$capAttribute must be an array of some items",
                 "{$this->dataKeyOf($attribute)}.min"   => "$capAttribute must be at least :min item(s)",
                 "{$this->dataKeyOf($attribute)}.max"   => "$capAttribute must be less than ".$max." item(s)",
            ];

            if ($attribute === SIZES) {
                $messages = [...$messages, ...["{$this->dataKeyOf($attribute)}.*.in" => "$capAttribute must be one or more of the following: { ".implode(', ', array_keys(PRODUCT_SIZE_ENUM))." }"]];
            }

            return $messages;
        }

        return $multiple_selection_rules;
    }

    /**
     * Boolean Attribute validation.
     *
     * @param string $attribute
     * @param bool $isMessage
     * @param string|null $inRules
     * @return array<string, string>
     */
    final protected function booleanValidation(string $attribute, bool $isMessage = false, ?string $inRules = null): array
    {
        $cap_attribute = ucfirst($attribute);

        if ($isMessage) {
            return [
                ...$this->requiredMessage($this->dataKeyOf($attribute), $cap_attribute),
                 "{$this->dataKeyOf($attribute)}.in"  => "$cap_attribute must be $inRules",
            ];
        }

        return [
            $this->dataKeyOf($attribute) => ['required', 'in:0,1'],
        ];
    }


//    /**
//     * Selection validation rules & messages.
//     *
//     * @param string|null $tableName
//     * @param string $attribute
//     * @param string|null $capAttribute
//     * @param bool $messages
//     * @return array<string, string>
//     */
//    protected function selectionValidation(string|null $tableName, string $attribute, string $capAttribute = null, bool $messages = false): array
//    {
//        if ($messages) {
//            return [
//                ...$this->isRequiredMsg($this->dataKeyOf($attribute), $capAttribute),
//                "{$this->dataKeyOf($attribute)}.integer" => "$capAttribute id must be an integer",
//                "{$this->dataKeyOf($attribute)}.not_in" => "$capAttribute id must be greater than :not_in",
//                "{$this->dataKeyOf($attribute)}.exists" => "Chosen ".strtolower($capAttribute)." does not exist",
//            ];
//        }
//
//        return [
//            $this->dataKeyOf($attribute) => ["required", "integer", "not_in:0", "exists:$tableName,".ID],
//        ];
//    }
}
