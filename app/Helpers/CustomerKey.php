<?php

namespace App\Helpers;

class CustomerKey
{
    /**
     * Normalisasi nama untuk name_key: huruf kecil, spasi tunggal, trim
     */
    public static function normalizeName(string $name): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($name));

        return mb_strtolower($clean, 'UTF-8');
    }

    /**
     * Normalisasi no. HP: ubah +62 atau 62 menjadi awalan 08
     * Contoh: +6281234567890 -> 081234567890, 6281234567890 -> 081234567890
     */
    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Validasi format dan panjang digit no HP (8 sampai 15 digit)
     */
    public static function isValidPhone(string $phone): bool
    {
        $digits = self::normalizePhone($phone);
        $len = strlen($digits);

        // Cek minimal ada 1 digit numerik setelah normalize
        if (! preg_match('/^\d+$/', $digits)) {
            return false;
        }

        return $len >= 8 && $len <= 15;
    }
}
