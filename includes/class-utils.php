<?php
/**
 * Utility helper functions for the plugin.
 * You can use this class for reusable static methods across the plugin.
 */
class HPULR_Utils {

    /**
     * Convert a comma-separated string to an array of trimmed values.
     *
     * @param string $input
     * @return array
     */
    public static function csv_to_array($input) {
        return array_map('trim', explode(',', $input));
    }

    /**
     * Sanitize a country code (e.g. for validation or logging).
     *
     * @param string $code
     * @return string
     */
    public static function sanitize_country_code($code) {
        return strtoupper(sanitize_text_field($code));
    }

    /**
     * Check if a value exists in a comma-separated list.
     *
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public static function in_csv($needle, $haystack) {
        $array = self::csv_to_array($haystack);
        return in_array($needle, $array);
    }
}
