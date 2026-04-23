<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Capture de Leads Simple (2 pages)
 * 
 * Version améliorée avec :
 * - Trust badges
 * - Icon boxes
 * - Progress bars
 * - Exit popup
 */
class LeadCaptureTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'lead_capture';
    }

    public function getIcon(): string
    {
        return '📧';
    }

    public function getName(): string
    {
        return 'Capture de Leads';
    }

    public function getDescription(): string
    {
        return 'Page de capture efficace avec titre accrocheur, liste de bénéfices, formulaire email et urgence. Idéal pour générer des leads qualifiés.';
    }

    public function getTags(): array
    {
        return ['capture', 'leads', 'email', 'formulaire', 'simple', 'rapide'];
    }

    public function getPrimaryColor(): string
    {
        return '#3B82F6';
    }

    public function getSecondaryColor(): string
    {
        return '#1E40AF';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Recevez [OFFRE] Gratuitement',
            'meta_description' => 'Inscrivez-vous pour recevoir [OFFRE] directement dans votre boîte mail. C\'est 100% gratuit !',
        ]);

        $this->buildCapturePage($funnel);
        $this->buildThankYouPage($funnel);

        return $funnel;
    }

    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Page de Capture',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Badge
            $this->richText(1, '
                <div class="text-center mb-6">
                    <span class="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-full text-sm font-bold">
                        🎁 100% GRATUIT
                    </span>
                </div>
            '),

            $this->heroTitle(2, 'RECEVEZ [VOTRE OFFRE] GRATUITEMENT', [
                'color' => '#FBBF24',
            ]),

            $this->subtitle(3, 'Découvrez comment [BÉNÉFICE PRINCIPAL] en [TEMPS] grâce à notre [RESSOURCE]'),

            // Benefits with Icon Boxes
            $this->richText(4, '<h3 class="text-xl font-bold mb-6 text-center">📋 Ce que vous allez découvrir :</h3>'),

            $this->iconBox(5, '✅', 'Bénéfice #1', 'Description claire et concise de ce premier avantage'),
            $this->iconBox(6, '✅', 'Bénéfice #2', 'Deuxième avantage qui résonne avec les douleurs'),
            $this->iconBox(7, '✅', 'Bénéfice #3', 'Troisième avantage qui différencie votre offre'),

            // Urgency countdown
            $this->countdown(8, '⏰ Cette offre est disponible pendant :', 72),

            // Form
            $this->form(9, 'Où souhaitez-vous recevoir votre [RESSOURCE] ?', [
                ['name' => 'first_name', 'label' => 'Votre prénom', 'type' => 'text', 'required' => false, 'placeholder' => 'Prénom'],
                ['name' => 'email', 'label' => 'Votre meilleur email', 'type' => 'email', 'required' => true, 'placeholder' => 'email@exemple.com'],
                ['name' => 'phone', 'label' => 'WhatsApp (optionnel)', 'type' => 'tel', 'required' => false],
            ], 'RECEVOIR MA [RESSOURCE] GRATUITE 🚀'),

            // Trust badges
            $this->trustBadges(10, [
                ['icon' => '🔒', 'text' => 'Données protégées'],
                ['icon' => '❌', 'text' => 'Pas de spam'],
                ['icon' => '✅', 'text' => 'Désinscription facile'],
            ]),

            // Urgency notice
            $this->richText(11, '
                <p class="text-center text-red-400 text-sm mt-4">
                    ⚠️ Attention : Places limitées pour garantir un accompagnement de qualité
                </p>
            '),

            // Exit popup
            $this->popup(12, '🎁 Attendez !', [
                'body' => '<p class="mb-4">Ne partez pas sans votre <strong>cadeau gratuit</strong> !</p><p>Inscrivez-vous maintenant et recevez votre [RESSOURCE] immédiatement.</p>',
                'trigger' => 'exit_intent',
                'button_text' => 'OUI, JE VEUX MON CADEAU',
                'show_once' => true,
            ]),
        ]);
    }

    private function buildThankYouPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::THANK_YOU,
            'title' => 'Merci',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Inscription terminée ! 🎉'),

            $this->heroTitle(2, '🎉 FÉLICITATIONS !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre inscription a bien été enregistrée'),

            // Success message
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-green-400 mb-4">Vérifiez votre boîte email !</h3>
                    <p class="mb-2">Un email contenant votre [RESSOURCE] vous a été envoyé.</p>
                    <p class="text-sm text-gray-400">Pensez à vérifier vos spams si vous ne le trouvez pas.</p>
                </div>
            '),

            // Next step
            $this->iconBox(5, '📱', 'Étape suivante recommandée', 'Rejoignez notre groupe WhatsApp pour échanger avec la communauté'),

            $this->whatsappButton(6, 'Rejoindre le Groupe WhatsApp 💬', '+33123456789', 'Bonjour ! Je viens de m\'inscrire et je rejoins le groupe !'),

            // Social icons
            $this->socialIcons(7, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
            ]),
        ]);
    }
}
