<?php

namespace App\Enums;

enum AlertType: string
{
    case HOT_LEAD = 'hot_lead';
    case ENGAGED = 'engaged';
    case WHATSAPP_CLICK = 'whatsapp_click';
    case INACTIVE_7_DAYS = 'inactive_7_days';
    case INACTIVE_14_DAYS = 'inactive_14_days';
    case NEW_REGISTRATION = 'new_registration';

    public function label(): string
    {
        return match ($this) {
            self::HOT_LEAD => 'Prospect chaud',
            self::ENGAGED => 'Prospect engagé',
            self::WHATSAPP_CLICK => 'Clic WhatsApp',
            self::INACTIVE_7_DAYS => 'Inactif 7 jours',
            self::INACTIVE_14_DAYS => 'Inactif 14 jours',
            self::NEW_REGISTRATION => 'Nouvelle inscription',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HOT_LEAD => 'Le prospect est devenu chaud, contactez-le rapidement',
            self::ENGAGED => '3 vidéos vues en 24h, prospect très engagé',
            self::WHATSAPP_CLICK => 'Le prospect a cliqué sur WhatsApp',
            self::INACTIVE_7_DAYS => 'Relance recommandée après 7 jours d\'inactivité',
            self::INACTIVE_14_DAYS => 'Relance urgente après 14 jours d\'inactivité',
            self::NEW_REGISTRATION => 'Un nouveau prospect s\'est inscrit',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HOT_LEAD => 'danger',
            self::ENGAGED => 'warning',
            self::WHATSAPP_CLICK => 'success',
            self::INACTIVE_7_DAYS => 'warning',
            self::INACTIVE_14_DAYS => 'danger',
            self::NEW_REGISTRATION => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::HOT_LEAD => 'heroicon-o-fire',
            self::ENGAGED => 'heroicon-o-bolt',
            self::WHATSAPP_CLICK => 'heroicon-o-chat-bubble-left-ellipsis',
            self::INACTIVE_7_DAYS => 'heroicon-o-clock',
            self::INACTIVE_14_DAYS => 'heroicon-o-exclamation-triangle',
            self::NEW_REGISTRATION => 'heroicon-o-user-plus',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::HOT_LEAD => 5,
            self::WHATSAPP_CLICK => 5,
            self::INACTIVE_14_DAYS => 4,
            self::ENGAGED => 3,
            self::INACTIVE_7_DAYS => 2,
            self::NEW_REGISTRATION => 1,
        };
    }
}
