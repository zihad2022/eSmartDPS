<?php

namespace App\Enums\Pakage;

enum DiscountType: int
{
    case PERCENT = 1;
    case FIXED = 2;

    public function label(): string
    {
        return match ($this) {
            self::PERCENT => 'Percent',
            self::FIXED => 'Fixed',
        };
    }
}
