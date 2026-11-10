<?php

namespace App\Helpers;

class CustomerKey
{
    public static function normalizeName(string $name): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($name));

        return mb_strtolower($clean, 'UTF-8');
    }

    /**
     * Normalisasi no. HP: ubah awalan +62/62 menjadi 08
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public static function isValidPhone(string $phone): bool
    {
        $digits = self::normalizePhone($phone);
        $len = strlen($digits);

        if (! preg_match('/^\d+$/', $digits)) {
            return false;
        }

        return $len >= 8 && $len <= 15;
    }
}
