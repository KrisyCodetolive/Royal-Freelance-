<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Formation Vidéo Gratuite (3 pages)
 * 
 * Version améliorée avec :
 * - Progress bars
 * - Icon boxes
 * - Trust badges
 * - Social icons
 */
class FreeTrainingTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'free_training';
    }

    public function getIcon(): string
    {
        return '🎥';
    }

    public function getName(): string
    {
        return 'Formation Vidéo Gratuite';
    }

    public function getDescription(): string
    {
        return 'Page de capture avec vidéo de présentation, parfaite pour présenter une formation ou un produit avant la capture d\'email.';
    }

    public function getTags(): array
    {
        return ['vidéo', 'formation', 'gratuit', 'youtube', 'présentation', 'masterclass'];
    }

    public function getPrimaryColor(): string
    {
        return '#8B5CF6';
    }

    public function getSecondaryColor(): string
    {
        return '#6D28D9';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Formation Gratuite - [SUJET]',
            'meta_description' => 'Accédez gratuitement à notre formation vidéo complète sur [SUJET]. Durée : [X] minutes.',
        ]);

        $this->buildVideoPage($funnel);
        $this->buildCapturePage($funnel);
        $this->buildAccessPage($funnel);

        return $funnel;
    }

    private function buildVideoPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Présentation',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Badge
            $this->richText(1, '
                <div class="text-center mb-6">
                    <span class="bg-purple-500/20 text-purple-400 px-4 py-2 rounded-full text-sm font-bold">
                        🎬 FORMATION GRATUITE - REGARDEZ MAINTENANT
                    </span>
                </div>
            '),

            $this->heroTitle(2, 'REGARDEZ CETTE VIDÉO AVANT DE VOUS INSCRIRE', [
                'color' => '#A78BFA',
            ]),

            $this->subtitle(3, 'Découvrez comment [BÉNÉFICE] en [TEMPS] — même si [OBJECTION COMMUNE]'),

            // Main video
            $this->video(4),

            // Key points with Icon Boxes
            $this->iconBox(5, '⏱️', 'Durée : 45 min', 'Formation complète et condensée'),
            $this->iconBox(6, '🎯', '100% Actionnable', 'Exercices pratiques inclus'),
            $this->iconBox(7, '🆓', 'Gratuit', 'Aucun paiement requis'),

            // What you'll learn
            $this->features(8, '📚 DANS CETTE FORMATION, VOUS APPRENDREZ :', [
                ['title' => 'Le secret #1', 'text' => 'La première chose que 90% des gens ignorent', 'icon' => 'heroicon-o-light-bulb'],
                ['title' => 'La méthode en 3 étapes', 'text' => 'Un système simple et reproductible', 'icon' => 'heroicon-o-list-bullet'],
                ['title' => 'Les erreurs à éviter', 'text' => 'Ce qui fait échouer la plupart des débutants', 'icon' => 'heroicon-o-exclamation-triangle'],
                ['title' => 'Le plan d\'action', 'text' => 'Une feuille de route pour les 30 prochains jours', 'icon' => 'heroicon-o-map'],
            ]),

            // CTA
            $this->ctaButton(9, 'ACCÉDER À LA FORMATION GRATUITE 🎓', [], [
                'backgroundColor' => '#8B5CF6',
                'fontSize' => '1.3rem',
            ]),

            // Trust badges
            $this->trustBadges(10, [
                ['icon' => '✅', 'text' => 'Accès immédiat'],
                ['icon' => '🔒', 'text' => 'Données protégées'],
                ['icon' => '🚫', 'text' => 'Pas de spam'],
            ]),
        ]);
    }

    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Inscription',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress
            $this->progress(1, 50, 'Étape 1 sur 2'),

            $this->heroTitle(2, '🎓 ACCÉDEZ À LA FORMATION', [
                'color' => '#A78BFA',
            ]),

            $this->subtitle(3, 'Entrez vos informations pour recevoir l\'accès instantané'),

            // What they get with Icon Boxes
            $this->richText(4, '<h3 class="text-purple-400 font-bold text-center mb-4">🎁 CE QUE VOUS ALLEZ RECEVOIR :</h3>'),

            $this->iconBox(5, '🎥', 'Formation vidéo complète', '45 minutes de contenu premium'),
            $this->iconBox(6, '📄', 'PDF récapitulatif', 'Téléchargeable immédiatement'),
            $this->iconBox(7, '📋', 'Exercices pratiques', 'Pour passer à l\'action'),
            $this->iconBox(8, '💬', 'Accès au groupe privé', 'Échangez avec la communauté'),

            // Form
            $this->form(9, '', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'WhatsApp', 'type' => 'tel', 'required' => false],
            ], 'ACCÉDER MAINTENANT 🚀'),

            // Trust
            $this->trustBadges(10, [
                ['icon' => '🔒', 'text' => 'Vos données sont protégées'],
                ['icon' => '⚡', 'text' => 'Accès instantané'],
            ]),
        ]);
    }

    private function buildAccessPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'Votre Formation',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Accès débloqué ! 🎉'),

            $this->heroTitle(2, '🎉 BIENVENUE !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre formation est prête — Commencez maintenant'),

            // Video player
            $this->video(4),

            // Resources with Icon Boxes
            $this->richText(5, '<h3 class="text-xl font-bold text-center mb-6">📥 RESSOURCES À TÉLÉCHARGER</h3>'),

            $this->iconBox(6, '📄', 'PDF Récapitulatif', '<a href="#" class="text-purple-400">Télécharger →</a>'),
            $this->iconBox(7, '📋', 'Checklist d\'action', '<a href="#" class="text-purple-400">Télécharger →</a>'),

            // Next step
            $this->richText(8, '
                <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 p-8 rounded-2xl border border-purple-500/30 text-center mb-8">
                    <h3 class="text-purple-400 font-bold text-xl mb-4">🚀 PRÊT À ALLER PLUS LOIN ?</h3>
                    <p class="mb-6">Découvrez notre programme complet avec coaching personnalisé</p>
                </div>
            '),

            $this->ctaButton(9, 'DÉCOUVRIR LE PROGRAMME COMPLET 🎯', [], [
                'backgroundColor' => '#8B5CF6',
            ]),

            $this->spacer(10, '20px'),

            $this->whatsappButton(11, 'Poser une question 💬', '+33123456789', 'Bonjour ! J\'ai une question sur la formation.'),

            // Social icons
            $this->socialIcons(12, [
                ['platform' => 'youtube', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
            ]),
        ]);
    }
}
