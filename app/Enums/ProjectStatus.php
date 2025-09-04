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

    public function bgColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-green-100',
            self::COMPLETED => 'bg-gray-100',
            self::CANCELLED => 'bg-red-100',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'text-green-600',
            self::COMPLETED => 'text-gray-600',
            self::CANCELLED => 'text-red-600',
        };
    }
}
