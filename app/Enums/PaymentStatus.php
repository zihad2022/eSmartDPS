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

    public function bgColor(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100',
            self::DUE => 'bg-blue-100',
            self::PAID => 'bg-green-100',
            self::CANCELLED => 'bg-red-100',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'text-yellow-600',
            self::DUE => 'text-blue-600',
            self::PAID => 'text-green-600',
            self::CANCELLED => 'text-red-600',
        };
    }
}
