<?php

namespace App\Enum;

enum AdPackage: string
{
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';

    // New packages requested by user
    case SPONSORED_14 = 'sponsored_14';
    case SPONSORED_30 = 'sponsored_30';
    case SIDEBAR_14 = 'sidebar_14';
    case SIDEBAR_30 = 'sidebar_30';

    public function label(): string
    {
        return match ($this) {
            self::BASIC => 'Basic',
            self::STANDARD => 'Standard',
            self::PREMIUM => 'Premium',
            self::SPONSORED_14 => 'Sponsored Link (14 Days)',
            self::SPONSORED_30 => 'Sponsored Link (30 Days)',
            self::SIDEBAR_14 => 'Sidebar (14 Days)',
            self::SIDEBAR_30 => 'Sidebar (30 Days)',
        };
    }

    /** Price in USD */
    public function priceUsd(): int
    {
        return match ($this) {
            self::BASIC => 10,
            self::STANDARD => 20,
            self::PREMIUM => 35,
            self::SPONSORED_14 => 14,
            self::SPONSORED_30 => 24,
            self::SIDEBAR_14 => 20,
            self::SIDEBAR_30 => 34,
        };
    }

    /** Ad duration in days */
    public function durationDays(): int
    {
        return match ($this) {
            self::BASIC => 14,
            self::STANDARD => 30,
            self::PREMIUM => 60,
            self::SPONSORED_14 => 14,
            self::SPONSORED_30 => 30,
            self::SIDEBAR_14 => 14,
            self::SIDEBAR_30 => 30,
        };
    }

    /** All packages use the banner placement (670 × 76 px) */
    public function allowedPlacements(): array
    {
        return match ($this) {
            self::BASIC => [AdPlacement::HEADER],
            self::STANDARD => [AdPlacement::HEADER, AdPlacement::SIDEBAR],
            self::PREMIUM => [AdPlacement::HEADER, AdPlacement::SIDEBAR, AdPlacement::CATEGORY],
            self::SPONSORED_14, self::SPONSORED_30 => [AdPlacement::HEADER],
            self::SIDEBAR_14, self::SIDEBAR_30 => [AdPlacement::SIDEBAR],
        };
    }

    /** Feature list for display on the advertise page */
    public function features(): array
    {
        return match ($this) {
            self::BASIC => [
                '14-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Click & impression tracking',
            ],
            self::STANDARD => [
                '30-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Click & impression tracking',
                'Priority queue position',
            ],
            self::PREMIUM => [
                '60-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Click & impression tracking',
                'Top priority — above all other ads',
                'Monthly analytics report',
            ],
            self::SPONSORED_14 => [
                '14-day sponsored campaign',
                'Header banner placement',
                'Click tracking',
            ],
            self::SPONSORED_30 => [
                '30-day sponsored campaign',
                'Header banner placement',
                'Click tracking',
            ],
            self::SIDEBAR_14 => [
                '14-day sidebar campaign',
                'Sidebar banner placement',
                'Click tracking',
            ],
            self::SIDEBAR_30 => [
                '30-day sidebar campaign',
                'Sidebar banner placement',
                'Click tracking',
            ],
        };
    }

    /** Accent color for the pricing card */
    public function badgeColor(): string
    {
        return match ($this) {
            self::BASIC => '#3b82f6',   // blue
            self::STANDARD => '#10b981',   // green
            self::PREMIUM => '#f59e0b',   // amber / gold
            self::SPONSORED_14, self::SPONSORED_30 => '#c084fc', // purple
            self::SIDEBAR_14, self::SIDEBAR_30 => '#60a5fa', // lighter blue
        };
    }

    /** Highlighted / recommended tier */
    public function isPopular(): bool
    {
        return $this === self::STANDARD;
    }
}
