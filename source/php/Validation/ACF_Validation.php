<?php

namespace ModularityServiceInfo\Validation;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Validation
 *
 * Generic ACF validation helper. Register new validators by adding entries
 * to the $rules array. Each rule is an array with 'pattern' and 'message'.
 */
class ACF_Validation
{
    /**
     * Field rules keyed by ACF field key.
     * pattern: PCRE (for server-side)
     * html_pattern: pattern attribute (no delimiters) for client-side
     * message: validation error message
     *
     * @var array
     */
    protected $rules = [];

    public function __construct()
    {
        // Define rules here. Add more entries to extend validation.
        $this->rules = [
            // Slug: lowercase letters, dashes and slashes; cannot start or end with dash or slash
            'field_694a9d267cd58' => [
                'pattern' => '/^[a-z](?:[a-z\/-]*[a-z])?$/',
                'html_pattern' => '^[a-z](?:[a-z\/-]*[a-z])?$',
                'message' => __('Lowercase letters, dashes and slashes only. Cannot start or end with dash or slash.', 'modularity-service-info'),
            ],
        ];

        foreach ($this->rules as $field_key => $rule) {
            add_filter('acf/validate_value/key=' . $field_key, [$this, 'validate_value'], 10, 4);
        }
    }

    /**
     * Server-side validation using PCRE.
     */
    public function validate_value($valid, $value, $field, $input)
    {
        if ($valid !== true) {
            return $valid;
        }

        if ($value === '' || $value === null) {
            return $valid;
        }

        $field_key = $field['key'] ?? null;
        if (! $field_key || ! isset($this->rules[$field_key])) {
            return $valid;
        }

        $rule = $this->rules[$field_key];
        $pattern = $rule['pattern'] ?? null;

        if (! $pattern || ! preg_match($pattern, $value)) {
            return $rule['message'] ?? __('Invalid value.', 'modularity-service-info');
        }

        return $valid;
    }
}
