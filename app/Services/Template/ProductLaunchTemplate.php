<?php

namespace App\Services\Template;

use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Lancement de Produit (2 pages)
 * 
 * Version améliorée avec :
 * - Sticky bar
 * - Icon boxes
 * - Progress bars
 * - Trust badges
 * - Social icons
 */
class ProductLaunchTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'product_launch';
    }

    public function getIcon(): string
    {
        return '🚀';
    }

    public function getName(): string
    {
        return 'Lancement de Produit';
    }

    public function getDescription(): string
    {
        return 'Page de teasing pour lancement produit avec countdown et early-bird signup.';
    }

    public function getTags(): array
    {
        return ['lancement', 'produit', 'teasing', 'countdown', 'early-bird'];
    }

    public function getPrimaryColor(): string
    {
        return '#EC4899';
    }

    public function getSecondaryColor(): string
    {
        return '#BE185D';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => '[NOM DU PRODUIT] - Lancement Imminent',
            'meta_description' => 'Soyez parmi les premiers à découvrir [PRODUIT]. Inscrivez-vous pour l\'accès early bird et -50% !',
        ]);

        $this->buildTeasingPage($funnel);
        $this->buildConfirmationPage($funnel);

        return $funnel;
    }

    private function buildTeasingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Coming Soon',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '⚡ LANCEMENT IMMINENT - Inscrivez-vous pour -50% !', 'REJOINDRE LA LISTE VIP'),

            // Badge
            $this->richText(2, '
                <div class="text-center mb-6">
                    <span class="bg-pink-500/20 text-pink-400 px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                        ⚡ COMING SOON
                    </span>
                </div>
            '),

            $this->heroTitle(3, '[NOM DU PRODUIT] 🚀', [
                'color' => '#EC4899',
            ]),

            $this->subtitle(4, 'La révolution arrive. Soyez parmi les premiers à en profiter.'),

            // Countdown
            $this->countdown(5, '🎯 LANCEMENT DANS :', 336),

            // Features with Icon Boxes
            $this->richText(6, '<h3 class="text-2xl font-bold text-center mb-6">🔥 CE QUI VOUS ATTEND :</h3>'),

            $this->iconBox(7, '🎯', 'Innovation #1', 'Description de la première fonctionnalité majeure'),
            $this->iconBox(8, '⚡', 'Innovation #2', 'Description de la seconde fonctionnalité'),
            $this->iconBox(9, '💎', 'Innovation #3', 'Description de la troisième fonctionnalité'),

            // Early bird offer
            $this->richText(10, '
                <div class="bg-yellow-900/30 p-8 rounded-2xl border border-yellow-500/30 text-center mb-8">
                    <h3 class="text-yellow-400 font-bold text-2xl mb-4">🎁 OFFRE EARLY BIRD</h3>
                    <p class="text-lg mb-4">Les 100 premiers inscrits recevront :</p>
                </div>
            '),

            // Trust badges for early bird benefits
            $this->trustBadges(11, [
                ['icon' => '✅', 'text' => 'Accès prioritaire'],
                ['icon' => '💰', 'text' => '-50% au lancement'],
                ['icon' => '🎁', 'text' => 'Bonus exclusifs'],
            ]),

            // Form
            $this->form(12, 'Rejoignez la liste VIP', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Votre email', 'type' => 'email', 'required' => true],
            ], 'JE VEUX ÊTRE NOTIFIÉ 🔔'),

            // Social proof
            $this->richText(13, '
                <div class="text-center mt-6">
                    <p class="text-gray-400 text-sm">Déjà <strong class="text-pink-400">847 personnes</strong> inscrites sur la liste VIP</p>
                </div>
            '),

            // Exit popup
            $this->popup(14, '🎁 Attendez !', [
                'body' => '<p class="mb-4">Ne manquez pas :</p><ul class="text-left mb-4"><li>✅ L\'accès prioritaire au produit</li><li>✅ -50% au lancement</li><li>✅ Les bonus exclusifs early bird</li></ul>',
                'trigger' => 'exit_intent',
                'button_text' => 'REJOINDRE LA LISTE VIP',
                'show_once' => true,
            ]),

            // Social icons
            $this->socialIcons(15, [
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'tiktok', 'url' => '#'],
            ]),
        ]);
    }

    private function buildConfirmationPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::THANK_YOU,
            'title' => 'Inscription Confirmée',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Inscription VIP confirmée ! 🎉'),

            $this->heroTitle(2, '🎉 VOUS ÊTES SUR LA LISTE VIP !', [
                'color' => '#10B981',
            ]),

            // Success box
            $this->richText(3, '
                <div class="bg-green-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">✅</div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">Inscription confirmée !</h3>
                    <p>Vous serez parmi les premiers à découvrir le produit.</p>
                </div>
            '),

            // What's next
            $this->richText(4, $this->stepsHtml([
                '📧 Email de confirmation envoyé',
                '🔔 Vous serez notifié le jour J',
                '🎁 Accès prioritaire garanti',
                '💰 -50% appliqué automatiquement',
            ], '📋 CE QUI VA SE PASSER :')),

            // Benefits reminder
            $this->richText(5, '<h3 class="text-xl font-bold text-center mb-4">🎁 VOS AVANTAGES EARLY BIRD :</h3>'),

            $this->iconBox(6, '🚀', 'Accès Prioritaire', 'Vous aurez le produit avant tout le monde'),
            $this->iconBox(7, '💰', '-50% Lancement', 'Prix réduit automatiquement appliqué'),
            $this->iconBox(8, '🎁', 'Bonus Exclusifs', 'Des cadeaux réservés aux early birds'),

            // Share
            $this->whatsappButton(9, 'Partager avec mes amis 💬', '', 'Je viens de m\'inscrire en early bird pour [PRODUIT] ! Inscris-toi aussi pour avoir -50% :'),

            // Social icons
            $this->socialIcons(10, [
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'facebook', 'url' => '#'],
            ]),
        ]);
    }
}
