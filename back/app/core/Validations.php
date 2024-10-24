<?php

namespace App\core;

class Validations {
    function validate(array $form, array $settings): bool {
        foreach ($settings as $field => $setting) {

            if (!key_exists($field, $form)) {
                return false;
            }

            foreach ($setting['validations'] as $rule) {
                if (is_array($rule)) {
                    if (method_exists($this, $rule[0])) {
                        if (!call_user_func_array([$this, $rule[0]], [$form[$field], $rule[1]])) {
                            return false;
                        }
                    }
                } else {
                    if (method_exists($this, $rule)) {
                        if (!call_user_func([$this, $rule], $form[$field])) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    function required($value): bool {
        return isset($value) && $value != '';
    }

    function email($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    function lengthMin($value, $min) {
        return strlen($value) >= $min;
    }

    function lengthMax($value, $max) {
        return strlen($value) <= $max;
    }

    function alphaOnly($value) {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }

    function numericOnly($value) {
        return is_numeric($value);
    }

    function url($value) {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    function lowercase($value, $min) {
        preg_match_all('/[a-z]/', $value, $matches);
        return count($matches[0]) >= $min;
    }

    function uppercase($value, $min) {
        preg_match_all('/[A-Z]/', $value, $matches);
        return count($matches[0]) >= $min;
    }

    function special($value, $min) {
        preg_match_all('/[!@#$%^&*(),.?":{}|<>]/', $value, $matches);
        return count($matches[0]) >= $min;
    }

    function date($value) {
        $formats = ['Y-m-d', 'd/m/Y', 'm-d-Y', 'Y/m/d', 'd-m-Y', 'm.d.Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return true;
            }
        }
        return false;
    }
}
