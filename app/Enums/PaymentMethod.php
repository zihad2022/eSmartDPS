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
}
