<?php

namespace App\Enums\Pakage;

enum BillingCycle: int
{
    case MONTHLY = 1;
    case YEARLY = 2;

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }
}
