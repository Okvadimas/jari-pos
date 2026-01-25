<?php

/**
 * Format number to currency with thousand separator
 * 
 * @param mixed $value - The number to format
 * @param string $separator - The thousand separator (default: '.')
 * @return string
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency($value, $separator = '.')
    {
        // Remove non-numeric characters
        $value = preg_replace('/[^0-9]/', '', $value);
        
        // Return empty if no value
        if (empty($value)) {
            return '';
        }
        
        // Format with thousand separator
        return number_format((float) $value, 0, ',', $separator);
    }
}

/**
 * Parse formatted currency back to number
 * 
 * @param string $value - The formatted currency string
 * @return int
 */
if (!function_exists('parseCurrency')) {
    function parseCurrency($value)
    {
        // Remove all non-numeric characters
        return (int) preg_replace('/[^0-9]/', '', $value);
    }
}
