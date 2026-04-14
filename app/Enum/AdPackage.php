<?php

namespace App\Enum;

enum AdPackage: string
{
    case BASIC    = 'basic';
    case STANDARD = 'standard';
    case PREMIUM  = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::BASIC    => 'Basic',
            self::STANDARD => 'Standard',
            self::PREMIUM  => 'Premium',
        };
    }

    /** Price in USD */
    public function priceUsd(): int
    {
        return match ($this) {
            self::BASIC    => 45,
            self::STANDARD => 85,
            self::PREMIUM  => 125,
        };
    }

    /** Ad duration in days */
    public function durationDays(): int
    {
        return match ($this) {
            self::BASIC    => 14,
            self::STANDARD => 30,
            self::PREMIUM  => 60,
        };
    }

    /** All packages use the banner placement (670 × 76 px) */
    public function allowedPlacements(): array
    {
        return match ($this) {
            self::BASIC    => [AdPlacement::HEADER],
            self::STANDARD => [AdPlacement::HEADER, AdPlacement::SIDEBAR],
            self::PREMIUM  => [AdPlacement::HEADER, AdPlacement::SIDEBAR, AdPlacement::CATEGORY],
        };
    }

    /** Feature list for display on the advertise page */
    public function features(): array
    {
        return match ($this) {
            self::BASIC => [
                '14-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Header placement',
                'Click & impression tracking',
            ],
            self::STANDARD => [
                '30-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Header + Sidebar placement',
                'Click & impression tracking',
                'Priority queue position',
            ],
            self::PREMIUM => [
                '60-day banner campaign',
                '670 × 76 px banner (auto-compressed)',
                'Header, Sidebar & Category placement',
                'Click & impression tracking',
                'Top priority — above all other ads',
                'Monthly analytics report',
            ],
        };
    }

    /** Accent color for the pricing card */
    public function badgeColor(): string
    {
        return match ($this) {
            self::BASIC    => '#3b82f6',   // blue
            self::STANDARD => '#10b981',   // green
            self::PREMIUM  => '#f59e0b',   // amber / gold
        };
    }

    /** Highlighted / recommended tier */
    public function isPopular(): bool
    {
        return $this === self::STANDARD;
    }
}
