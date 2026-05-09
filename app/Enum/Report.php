<?php

namespace App\Enum;

enum Report: string
{
    case MISSING_INFORMATION = 'missing_information';
    case WRONG_CATEGORY = 'wrong_category';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MISSING_INFORMATION => 'Missing Information',
            self::WRONG_CATEGORY => 'Wrong Category',
            self::OTHER => 'Other',
        };
    }


}
