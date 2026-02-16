<?php

namespace App\Enum;

enum UptimeStatus: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case TIMEOUT = 'timeout';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
            self::TIMEOUT => 'Timeout',
            self::UNKNOWN => 'Unknown',
        };
    }

    public function cssClass(): string
    {
        return match ($this) {
            self::ONLINE => 'uptime-online',
            self::OFFLINE => 'uptime-offline',
            self::TIMEOUT => 'uptime-timeout',
            self::UNKNOWN => 'uptime-unknown',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ONLINE => '●',
            self::OFFLINE => '○',
            self::TIMEOUT => '◌',
            self::UNKNOWN => '?',
        };
    }
}
