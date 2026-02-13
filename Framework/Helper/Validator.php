<?php
namespace Framework\Helper;

class Validator
{
    private $errors = [];
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function make($data, $rules)
    {
        $validator = new self($data);
        return $validator->validate($rules);
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $ruleList) {
            $rulesArray = explode('|', $ruleList);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return $this;
    }

    private function applyRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "$field is required");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email");
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "$field must be a number");
                }
                break;

            case 'integer':
                if (!is_numeric($value) || !ctype_digit((string)$value)) {
                    $this->addError($field, "$field must be an integer");
                }
                break;

            case 'alpha':
                if (!ctype_alpha(str_replace(' ', '', $value))) {
                    $this->addError($field, "$field must contain only letters");
                }
                break;

            case 'alpha_num':
                if (!ctype_alnum(str_replace(' ', '', $value))) {
                    $this->addError($field, "$field must contain only letters and numbers");
                }
                break;

            case 'alpha_dash':
                if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $value)) {
                    $this->addError($field, "$field may only contain letters, numbers, dashes and underscores");
                }
                break;

            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, "$field must be a valid URL");
                }
                break;

            case 'ip':
                if (!filter_var($value, FILTER_VALIDATE_IP)) {
                    $this->addError($field, "$field must be a valid IP address");
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!isset($this->data[$confirmField]) || $value !== $this->data[$confirmField]) {
                    $this->addError($field, "$field confirmation does not match");
                }
                break;

            case 'same':
                // This would need a parameter, so we'll handle it differently
                break;

            default:
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $this->addError($field, "$field must be at least $min characters");
                    }
                } elseif (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $this->addError($field, "$field must not exceed $max characters");
                    }
                } elseif (strpos($rule, 'between:') === 0) {
                    $parts = explode(':', $rule);
                    if (isset($parts[1], $parts[2])) {
                        $min = (int) $parts[1];
                        $max = (int) $parts[2];
                        $len = strlen($value);
                        if ($len < $min || $len > $max) {
                            $this->addError($field, "$field must be between $min and $max characters");
                        }
                    }
                } elseif (strpos($rule, 'in:') === 0) {
                    $allowedValues = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowedValues)) {
                        $this->addError($field, "$field must be one of: " . implode(', ', $allowedValues));
                    }
                } elseif (strpos($rule, 'not_in:') === 0) {
                    $disallowedValues = explode(',', substr($rule, 7));
                    if (in_array($value, $disallowedValues)) {
                        $this->addError($field, "$field must not be one of: " . implode(', ', $disallowedValues));
                    }
                } elseif (strpos($rule, 'regex:') === 0) {
                    $pattern = substr($rule, 6);
                    if (!preg_match($pattern, $value)) {
                        $this->addError($field, "$field format is invalid");
                    }
                }
                break;
        }
    }

    private function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function first($field)
    {
        return $this->errors[$field][0] ?? null;
    }
}