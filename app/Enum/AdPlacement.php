<?php

namespace App\Enum;

enum AdPlacement: string
{
    case HEADER = 'header';
    case SIDEBAR = 'sidebar';
    case CATEGORY = 'category';
    case INLINE = 'inline';

    public function label(): string
    {
        return match ($this) {
            self::HEADER => 'Header Banner',
            self::SIDEBAR => 'Sidebar Banner',
            self::CATEGORY => 'Category Page',
            self::INLINE => 'Inline (Within Listings)',
        };
    }
}
