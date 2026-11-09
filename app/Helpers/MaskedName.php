<?php

namespace App\Helpers;

class MaskedName
{
    /**
     * Samarkan nama untuk tampilan publik:
     * "Budi" -> "Budi"
     * "Budi Santoso" -> "Budi S."
     * "Muhammad Budi Santoso" -> "Muhammad B."
     */
    public static function mask(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return '';
        }

        $masked = array_map(function ($part) {
            $first = mb_substr($part, 0, 1, 'UTF-8');
            $len = mb_strlen($part, 'UTF-8');

            return $len > 1 ? $first.str_repeat('*', $len - 1) : $first;
        }, $parts);

        return implode(' ', $masked);
    }
}
