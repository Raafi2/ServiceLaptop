<?php

namespace App\Helpers;

use App\Models\Service;

class TrackingNumber
{
    /**
     * Karakter yang digunakan untuk nomor lacak (tanpa 0, O, 1, I, L)
     */
    public const CHARACTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /**
     * Panjang kode nomor lacak (tidak termasuk prefix SL-)
     */
    public const LENGTH = 6;

    /**
     * Buat nomor lacak acak SL-XXXXXX unik
     */
    public static function generate(): string
    {
        $chars = str_split(self::CHARACTERS);
        $charCount = count($chars);

        do {
            $code = '';
            for ($i = 0; $i < self::LENGTH; $i++) {
                $code .= $chars[random_int(0, $charCount - 1)];
            }
            $trackingNo = 'SL-'.$code;
        } while (Service::where('tracking_no', $trackingNo)->exists());

        return $trackingNo;
    }

    /**
     * Normalisasi nomor lacak masukan pengguna (hapus spasi, uppercase)
     */
    public static function normalize(?string $input): string
    {
        if ($input === null) {
            return '';
        }

        return strtoupper(trim(preg_replace('/\s+/', '', $input)));
    }

    /**
     * Validasi format SL-XXXXXX
     */
    public static function isValid(string $trackingNo): bool
    {
        return (bool) preg_match('/^SL-['.self::CHARACTERS.']{'.self::LENGTH.'}$/', $trackingNo);
    }
}
