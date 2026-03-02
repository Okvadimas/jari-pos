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

/**
 * Format tanggal ke Bahasa Indonesia
 * 
 * @param mixed $date - Tanggal (string, Carbon, atau DateTime)
 * @param string $format - Format output (default: 'd F Y')
 * @param bool $short - Gunakan nama bulan singkat (default: false)
 * @return string
 * 
 * Contoh penggunaan:
 *   formatTanggal('2026-03-01')              => '01 Maret 2026'
 *   formatTanggal('2026-03-01', 'd M Y', true) => '01 Mar 2026'
 *   formatTanggal('2026-03-01', 'd F Y, H:i') => '01 Maret 2026, 17:34'
 */
if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd F Y', $short = false)
    {
        if (empty($date)) {
            return '-';
        }

        $bulanPanjang = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $bulanSingkat = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
            4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep',
            10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $carbon = \Carbon\Carbon::parse($date);
        $bulan = $short ? $bulanSingkat[$carbon->month] : $bulanPanjang[$carbon->month];

        // Replace F (full month) and M (short month) with Indonesian name
        $result = $carbon->format($format);
        $result = str_replace($carbon->format('F'), $bulan, $result);
        $result = str_replace($carbon->format('M'), $short ? $bulan : $bulanSingkat[$carbon->month], $result);

        return $result;
    }
}
