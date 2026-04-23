<?php

namespace App\Enums;

enum LeadStatus: string
{
    case COLD = 'cold';
    case WARM = 'warm';
    case HOT = 'hot';
    case ULTRA_HOT = 'ultra_hot';
    case CLIENT = 'client';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::COLD => 'Froid',
            self::WARM => 'Tiède',
            self::HOT => 'Chaud',
            self::ULTRA_HOT => 'Ultra Chaud',
            self::CLIENT => 'Client',
            self::MEMBER => 'Membre',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::COLD => 'info',
            self::WARM => 'warning',
            self::HOT => 'danger',
            self::ULTRA_HOT => 'danger',
            self::CLIENT => 'success',
            self::MEMBER => 'primary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::COLD => 'heroicon-o-snowflake',
            self::WARM => 'heroicon-o-sun',
            self::HOT => 'heroicon-o-fire',
            self::ULTRA_HOT => 'heroicon-o-fire',
            self::CLIENT => 'heroicon-o-check-badge',
            self::MEMBER => 'heroicon-o-user-circle',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::COLD => 1,
            self::WARM => 2,
            self::HOT => 3,
            self::ULTRA_HOT => 4,
            self::CLIENT => 5,
            self::MEMBER => 6,
        };
    }

    public static function fromScore(int $score, array $thresholds = []): self
    {
        $thresholds = array_merge([
            'cold' => 0,
            'warm' => 11,
            'hot' => 31,
            'ultra_hot' => 61,
        ], $thresholds);

        return match (true) {
            $score >= $thresholds['ultra_hot'] => self::ULTRA_HOT,
            $score >= $thresholds['hot'] => self::HOT,
            $score >= $thresholds['warm'] => self::WARM,
            default => self::COLD,
        };
    }
}
