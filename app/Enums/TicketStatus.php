<?php

namespace App\Enums;

enum TicketStatus: int
{
    case OPEN = 1;
    case IN_PROGRESS = 2;
    case RESOLVED = 3;
    case CLOSED = 4;

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::IN_PROGRESS => 'In Progress',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
        };
    }
}
