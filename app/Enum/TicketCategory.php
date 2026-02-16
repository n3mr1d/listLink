<?php

namespace App\Enum;

enum TicketCategory: string
{
    case GENERAL = 'general';
    case ABUSE = 'abuse';
    case LEGAL = 'legal';
    case BUG = 'bug';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'General Inquiry',
            self::ABUSE => 'Report Abuse',
            self::LEGAL => 'Legal / Removal Request',
            self::BUG => 'Bug Report',
        };
    }
}
