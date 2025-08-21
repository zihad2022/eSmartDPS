<?php

namespace App\Enums;

enum InvoiceStatus: int
{
    // Enum cases (values stored in DB as int)
    case UNPAID = 1;
    case PAID = 2;
    case REFUND_REQUESTED = 3;
    case REFUNDED = 4;
    case CANCELLED = 5;

    /**
     * Get human-readable label for each status
     */
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

    /**
     * Get Tailwind CSS text color class for each status
     */
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

    /**
     * Optional: Add background colors for badges
     */
    public function bgColor(): string
    {
        return match ($this) {
            self::UNPAID => 'bg-red-100',
            self::PAID => 'bg-green-100',
            self::REFUND_REQUESTED => 'bg-orange-100',
            self::REFUNDED => 'bg-yellow-100',
            self::CANCELLED => 'bg-gray-100',
        };
    }
}
