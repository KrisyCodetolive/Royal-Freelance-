<?php

namespace App\Enums;

enum EmailSequenceStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::ACTIVE => 'Active',
            self::PAUSED => 'En pause',
            self::ARCHIVED => 'Archivée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'success',
            self::PAUSED => 'warning',
            self::ARCHIVED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document',
            self::ACTIVE => 'heroicon-o-play',
            self::PAUSED => 'heroicon-o-pause',
            self::ARCHIVED => 'heroicon-o-archive-box',
        };
    }
}
