<?php

namespace App\Enum;

enum AdType: string
{
    case BANNER = 'banner';
    case SPONSORED = 'sponsored';
    case FEATURED = 'featured';
    case BOOST = 'boost';

    public function label(): string
    {
        return match ($this) {
            self::BANNER => 'Banner Ad',
            self::SPONSORED => 'Sponsored Link',
            self::FEATURED => 'Featured Placement',
            self::BOOST => 'Category Boost',
        };
    }
}
