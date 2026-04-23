<?php

namespace App\Enums;

enum OfferType: string
{
    case FORMATION = 'formation';
    case LIVRE = 'livre';
    case PROGRAMME = 'programme';
    case MLM = 'mlm';

    public function label(): string
    {
        return match ($this) {
            self::FORMATION => 'Formation',
            self::LIVRE => 'Livre',
            self::PROGRAMME => 'Programme',
            self::MLM => 'MLM',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FORMATION => 'heroicon-o-academic-cap',
            self::LIVRE => 'heroicon-o-book-open',
            self::PROGRAMME => 'heroicon-o-user-group',
            self::MLM => 'heroicon-o-share',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FORMATION => 'primary',
            self::LIVRE => 'info',
            self::PROGRAMME => 'success',
            self::MLM => 'warning',
        };
    }
}
