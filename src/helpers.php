<?php

// ── Global Helper Functions ─────────────────────────────────────────────

if (!function_exists('formatPrice')) {
    /**
     * Format a price value dynamically:
     * - Whole numbers return without decimal places (e.g. 25.00 -> 25)
     * - Decimals return with 2 decimal places (e.g. 12.50 -> 12.50)
     *
     * @param float|int|string|null $price
     * @return string
     */
    function formatPrice($price) {
        $val = floatval($price ?? 0);
        if (floor($val) == $val) {
            return number_format($val, 0);
        }
        return number_format($val, 2);
    }
}

if (!function_exists('__')) {
    /**
     * Global language translation helper
     *
     * @param string $key
     * @param array $replacements
     * @return string
     */
    function __($key, $replacements = []) {
        return \Core\Lang::get($key, $replacements);
    }
}
