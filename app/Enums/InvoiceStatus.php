<?php

namespace App\Enums;

enum InvoiceStatus: int
{
    case UNPAID = 1;
    case PAID = 2;
    case REFUND_REQUESTED = 3;
    case REFUNDED = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::REFUND_REQUESTED => 'Refund Requested',
            self::REFUNDED => 'Refunded',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'text-red-600',
            self::PAID => 'text-green-600',
            self::REFUND_REQUESTED => 'text-orange-600',
            self::REFUNDED => 'text-yellow-600',
            self::CANCELLED => 'text-gray-600',
        };
    }
}
