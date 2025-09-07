<?php

namespace App\Enums;

enum PaymentMethod: int
{
    case BANK_TRANSFER = 1;
    case CASH_DEPOSIT = 2;
    case MOBILE_MONEY = 3;
    case CHECK = 4;
    case ONLINE = 5;

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CASH_DEPOSIT => 'Cash Deposit',
            self::MOBILE_MONEY => 'Mobile Money',
            self::CHECK => 'Check',
            self::ONLINE => 'Online Payment',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'bg-blue-100',
            self::CASH_DEPOSIT => 'bg-green-100',
            self::MOBILE_MONEY => 'bg-yellow-100',
            self::CHECK => 'bg-red-100',
            self::ONLINE => 'bg-gray-100',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'text-blue-600',
            self::CASH_DEPOSIT => 'text-green-600',
            self::MOBILE_MONEY => 'text-yellow-600',
            self::CHECK => 'text-red-600',
            self::ONLINE => 'text-gray-600',
        };
    }
}
