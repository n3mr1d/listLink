<?php

namespace App\Enum;

enum AdPackage: string
{
    case STARTER = 'starter';
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';
    case PRO = 'pro';
    case ELITE = 'elite';

    public function label(): string
    {
        return match ($this) {
            self::STARTER => 'Starter',
            self::BASIC => 'Basic',
            self::STANDARD => 'Standard',
            self::PREMIUM => 'Premium',
            self::PRO => 'Pro',
            self::ELITE => 'Elite',
        };
    }

    /** Price in USD (whole dollars) */
    public function priceUsd(): int
    {
        return match ($this) {
            self::STARTER => 30,
            self::BASIC => 50,
            self::STANDARD => 75,
            self::PREMIUM => 100,
            self::PRO => 120,
            self::ELITE => 150,
        };
    }

    /** Ad duration in days */
    public function durationDays(): int
    {
        return match ($this) {
            self::STARTER => 7,
            self::BASIC => 14,
            self::STANDARD => 30,
            self::PREMIUM => 45,
            self::PRO => 60,
            self::ELITE => 90,
        };
    }

    /** Allowed ad placement types */
    public function allowedPlacements(): array
    {
        return match ($this) {
            self::STARTER => [AdPlacement::INLINE],
            self::BASIC => [AdPlacement::INLINE, AdPlacement::CATEGORY],
            self::STANDARD => [AdPlacement::INLINE, AdPlacement::CATEGORY, AdPlacement::SIDEBAR],
            self::PREMIUM => [AdPlacement::INLINE, AdPlacement::CATEGORY, AdPlacement::SIDEBAR],
            self::PRO => [AdPlacement::INLINE, AdPlacement::CATEGORY, AdPlacement::SIDEBAR, AdPlacement::HEADER],
            self::ELITE => [AdPlacement::INLINE, AdPlacement::CATEGORY, AdPlacement::SIDEBAR, AdPlacement::HEADER],
        };
    }

    /** Feature list for display */
    public function features(): array
    {
        return match ($this) {
            self::STARTER => [
                '7-day campaign',
                'Inline listing placement',
                '"Sponsored" label',
                'Link + title only',
            ],
            self::BASIC => [
                '14-day campaign',
                'Inline + category placement',
                '"Sponsored" label',
                'Link + title + description',
            ],
            self::STANDARD => [
                '30-day campaign',
                'Inline, category & sidebar',
                '"Sponsored" label',
                'Link + title + description',
                'Custom banner image (300×250)',
            ],
            self::PREMIUM => [
                '45-day campaign',
                'All Standard placements',
                'Priority queue position',
                'Custom 300×250 banner',
                'Click analytics report',
            ],
            self::PRO => [
                '60-day campaign',
                'All placements incl. header',
                'Priority over Premium',
                'Custom 728×90 header banner',
                'Weekly analytics report',
                'Sponsored category highlight',
            ],
            self::ELITE => [
                '90-day campaign',
                'All placements — top priority',
                'Exclusive header slot',
                'All banner sizes supported',
                'Daily analytics dashboard',
                'Dedicated account manager',
                'Featured homepage spot',
            ],
        };
    }

    /** Badge color class (for UI) */
    public function badgeColor(): string
    {
        return match ($this) {
            self::STARTER => '#6b7280',   // gray
            self::BASIC => '#3b82f6',   // blue
            self::STANDARD => '#10b981',   // green
            self::PREMIUM => '#f59e0b',   // amber
            self::PRO => '#8b5cf6',   // violet
            self::ELITE => '#ef4444',   // red / gold
        };
    }

    /** Whether this is a highlighted / recommended tier */
    public function isPopular(): bool
    {
        return $this === self::STANDARD || $this === self::PRO;
    }
}
