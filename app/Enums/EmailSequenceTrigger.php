<?php

namespace App\Enums;

enum EmailSequenceTrigger: string
{
    case FORM_SUBMIT = 'form_submit';
    case SCORE_THRESHOLD = 'score_threshold';
    case PAGE_VIEW = 'page_view';
    case TAG_ASSIGNED = 'tag_assigned';
    case INACTIVITY = 'inactivity';
    case MANUAL = 'manual';
    case STATUS_CHANGED = 'status_changed';

    public function label(): string
    {
        return match ($this) {
            self::FORM_SUBMIT => 'Soumission formulaire',
            self::SCORE_THRESHOLD => 'Seuil de score atteint',
            self::PAGE_VIEW => 'Vue de page spécifique',
            self::TAG_ASSIGNED => 'Tag assigné',
            self::INACTIVITY => 'Inactivité prolongée',
            self::MANUAL => 'Déclenchement manuel',
            self::STATUS_CHANGED => 'Changement de statut',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FORM_SUBMIT => 'Se déclenche quand un prospect soumet un formulaire',
            self::SCORE_THRESHOLD => 'Se déclenche quand le score du prospect atteint un seuil',
            self::PAGE_VIEW => 'Se déclenche quand une page spécifique est visitée',
            self::TAG_ASSIGNED => 'Se déclenche quand un tag spécifique est assigné',
            self::INACTIVITY => 'Se déclenche après une période d\'inactivité',
            self::MANUAL => 'Déclenchement manuel par un commercial',
            self::STATUS_CHANGED => 'Se déclenche quand le lead devient Chaud, Client, etc.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FORM_SUBMIT => 'heroicon-o-clipboard-document-check',
            self::SCORE_THRESHOLD => 'heroicon-o-chart-bar-square',
            self::PAGE_VIEW => 'heroicon-o-eye',
            self::TAG_ASSIGNED => 'heroicon-o-tag',
            self::INACTIVITY => 'heroicon-o-clock',
            self::MANUAL => 'heroicon-o-hand-raised',
            self::STATUS_CHANGED => 'heroicon-o-arrow-path',
        };
    }

    public function requiredConditions(): array
    {
        return match ($this) {
            self::FORM_SUBMIT => [],
            self::SCORE_THRESHOLD => ['min_score'],
            self::PAGE_VIEW => ['page_id'],
            self::TAG_ASSIGNED => ['tag_id'],
            self::INACTIVITY => ['days'],
            self::MANUAL => [],
            self::STATUS_CHANGED => ['status'],
        };
    }
}
