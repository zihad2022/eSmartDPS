<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case DUE = 'due';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

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
