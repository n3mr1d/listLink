<?php

namespace App\Enum;

enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::ACTIVE => 'Active',
            self::REJECTED => 'Rejected',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::PENDING => 'status-pending',
            self::ACTIVE => 'status-active',
            self::REJECTED => 'status-rejected',
        };
    }
}
