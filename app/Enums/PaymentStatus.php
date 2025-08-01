<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PENDING = 1;
    case DUE = 2;
    case PAID = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::DUE => 'Due',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
        };
    }
}
