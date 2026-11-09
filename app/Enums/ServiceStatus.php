<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case Diterima = 'diterima';
    case Diagnosa = 'diagnosa';
    case Dikerjakan = 'dikerjakan';
    case Selesai = 'selesai';
    case Serahkan = 'serahkan';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Diterima => 'Diterima',
            self::Diagnosa => 'Diagnosa',
            self::Dikerjakan => 'Dikerjakan',
            self::Selesai => 'Selesai',
            self::Serahkan => 'Diserahkan',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Diterima => 'check',
            self::Diagnosa => 'clock',
            self::Dikerjakan => 'clock',
            self::Selesai => 'checkc',
            self::Serahkan => 'arrow',
            self::Dibatalkan => 'xc',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Diterima => in_array($target, [self::Diagnosa, self::Dibatalkan], true),
            self::Diagnosa => in_array($target, [self::Dikerjakan, self::Dibatalkan], true),
            self::Dikerjakan => in_array($target, [self::Selesai, self::Dibatalkan], true),
            self::Selesai => in_array($target, [self::Serahkan, self::Dibatalkan], true),
            self::Serahkan => false,
            self::Dibatalkan => false,
        };
    }
}
