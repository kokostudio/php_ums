<?php

namespace App\Core;

class Validator
{
    /**
     * Validate data against a set of rules.
     *
     * @param array $data The data to validate (e.g. $_POST)
     * @param array $rules The validation rules (e.g. ['email' => 'required|email'])
     * @return array An array of error messages. Empty if validation passes.
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                // If it's not required and empty, skip other rules
                if ($rule !== 'required' && empty($value) && $value !== '0' && $value !== 0) {
                    continue;
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters.";
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "The {$field} may not be greater than {$max} characters.";
                    }
                } else {
                    switch ($rule) {
                        case 'required':
                            if (empty($value) && $value !== '0' && $value !== 0) {
                                $errors[$field][] = "The {$field} field is required.";
                            }
                            break;
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $errors[$field][] = "The {$field} must be a valid email address.";
                            }
                            break;
                        case 'numeric':
                            if (!is_numeric($value)) {
                                $errors[$field][] = "The {$field} must be a number.";
                            }
                            break;
                        case 'alpha_dash':
                            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                                $errors[$field][] = "The {$field} may only contain letters, numbers, dashes and underscores.";
                            }
                            break;
                        case 'alpha_num':
                            if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                                $errors[$field][] = "The {$field} may only contain letters and numbers.";
                            }
                            break;
                    }
                }
            }
        }

        return $errors;
    }
}
