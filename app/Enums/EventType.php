<?php

namespace App\Enums;

enum EventType: string
{
    case PAGE_VIEW = 'page_view';
    case FORM_SUBMIT = 'form_submit';
    case VIDEO_PLAY = 'video_play';
    case VIDEO_25 = 'video_25';
    case VIDEO_50 = 'video_50';
    case VIDEO_75 = 'video_75';
    case VIDEO_100 = 'video_100';
    case CTA_CLICK = 'cta_click';
    case WHATSAPP_CLICK = 'whatsapp_click';
    case PAYMENT_CLICK = 'payment_click';
    case CONVERSION = 'conversion';

    public function label(): string
    {
        return match ($this) {
            self::PAGE_VIEW => 'Visite de page',
            self::FORM_SUBMIT => 'Soumission formulaire',
            self::VIDEO_PLAY => 'Lecture vidéo',
            self::VIDEO_25 => 'Vidéo vue à 25%',
            self::VIDEO_50 => 'Vidéo vue à 50%',
            self::VIDEO_75 => 'Vidéo vue à 75%',
            self::VIDEO_100 => 'Vidéo complétée',
            self::CTA_CLICK => 'Clic CTA',
            self::WHATSAPP_CLICK => 'Clic WhatsApp',
            self::PAYMENT_CLICK => 'Clic Paiement',
            self::CONVERSION => 'Conversion',
        };
    }

    public function defaultPoints(): int
    {
        return match ($this) {
            self::PAGE_VIEW => 1,
            self::FORM_SUBMIT => 10,
            self::VIDEO_PLAY => 2,
            self::VIDEO_25 => 3,
            self::VIDEO_50 => 5,
            self::VIDEO_75 => 8,
            self::VIDEO_100 => 15,
            self::CTA_CLICK => 5,
            self::WHATSAPP_CLICK => 25,
            self::PAYMENT_CLICK => 30,
            self::CONVERSION => 100,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PAGE_VIEW => 'heroicon-o-eye',
            self::FORM_SUBMIT => 'heroicon-o-clipboard-document-check',
            self::VIDEO_PLAY => 'heroicon-o-play',
            self::VIDEO_25, self::VIDEO_50, self::VIDEO_75, self::VIDEO_100 => 'heroicon-o-play-circle',
            self::CTA_CLICK => 'heroicon-o-cursor-arrow-rays',
            self::WHATSAPP_CLICK => 'heroicon-o-chat-bubble-left-ellipsis',
            self::PAYMENT_CLICK => 'heroicon-o-credit-card',
            self::CONVERSION => 'heroicon-o-check-badge',
        };
    }

    public function isVideoEvent(): bool
    {
        return in_array($this, [
            self::VIDEO_PLAY,
            self::VIDEO_25,
            self::VIDEO_50,
            self::VIDEO_75,
            self::VIDEO_100,
        ]);
    }

    public function isConversionEvent(): bool
    {
        return in_array($this, [
            self::WHATSAPP_CLICK,
            self::PAYMENT_CLICK,
            self::CONVERSION,
        ]);
    }
}
