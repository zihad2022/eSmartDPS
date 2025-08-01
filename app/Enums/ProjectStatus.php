<?php

namespace App\Enums;

enum ProjectStatus: int
{
    case ACTIVE = 1;
    case COMPLETED = 2;
    case CANCELLED = 3;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }
}
