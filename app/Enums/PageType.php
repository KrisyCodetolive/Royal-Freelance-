<?php

namespace App\Enums;

enum PageType: string
{
    case CAPTURE = 'capture';
    case THANK_YOU = 'thank_you';
    case PRESENTATION = 'presentation';
    case MODALITIES = 'modalities';
    case FAQ = 'faq';
    case TESTIMONIALS = 'testimonials';
    case PAYMENT = 'payment';
    case QUIZ = 'quiz';
    case LANDING = 'landing';
    case CONTENT = 'content';
    case SALES = 'sales';

    public function label(): string
    {
        return match ($this) {
            self::CAPTURE => 'Page Capture',
            self::THANK_YOU => 'Page Merci',
            self::PRESENTATION => 'Page Présentation',
            self::MODALITIES => 'Page Modalités',
            self::FAQ => 'Page FAQ',
            self::TESTIMONIALS => 'Page Témoignages',
            self::PAYMENT => 'Page Paiement',
            self::QUIZ => 'Page Quiz',
            self::LANDING => 'Page Landing',
            self::CONTENT => 'Page Content',
            self::SALES => 'Page Sales',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CAPTURE => 'Formulaire de capture de leads',
            self::THANK_YOU => 'Page de remerciement avec vidéo 0',
            self::PRESENTATION => 'Playlist de vidéos de présentation',
            self::MODALITIES => 'Vidéo + boutons CTA WhatsApp',
            self::FAQ => 'Questions fréquentes',
            self::TESTIMONIALS => 'Avis et témoignages clients',
            self::PAYMENT => 'Page de paiement intégrée',
            self::QUIZ => 'Page quiz avec choix multiples',
            self::LANDING => 'Page Landing',
            self::CONTENT => 'Page Content',
            self::SALES => 'Page Sales',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CAPTURE => 'heroicon-o-clipboard-document-list',
            self::THANK_YOU => 'heroicon-o-heart',
            self::PRESENTATION => 'heroicon-o-play-circle',
            self::MODALITIES => 'heroicon-o-chat-bubble-left-right',
            self::FAQ => 'heroicon-o-question-mark-circle',
            self::TESTIMONIALS => 'heroicon-o-star',
            self::PAYMENT => 'heroicon-o-credit-card',
            self::QUIZ => 'heroicon-o-list-bullet',
            self::LANDING => 'heroicon-o-clipboard-document-list',
            self::CONTENT => 'heroicon-o-clipboard-document-list',
            self::SALES => 'heroicon-o-clipboard-document-list',
        };
    }

    public function requiresForm(): bool
    {
        return in_array($this, [self::CAPTURE, self::QUIZ]);
    }
}

