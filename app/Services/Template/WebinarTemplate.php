<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Inscription Webinaire (3 pages)
 * 
 * Version améliorée avec :
 * - Sticky bar
 * - Icon boxes
 * - Progress bars
 * - Trust badges
 * - Social icons
 */
class WebinarTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'webinar';
    }

    public function getIcon(): string
    {
        return '🎙️';
    }

    public function getName(): string
    {
        return 'Inscription Webinaire';
    }

    public function getDescription(): string
    {
        return 'Page d\'inscription complète pour webinaire avec date/heure, speakers, countdown et confirmation automatique.';
    }

    public function getTags(): array
    {
        return ['webinaire', 'live', 'inscription', 'événement', 'masterclass', 'conférence'];
    }

    public function getPrimaryColor(): string
    {
        return '#F97316';
    }

    public function getSecondaryColor(): string
    {
        return '#EA580C';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Webinaire Gratuit - [SUJET] | [DATE]',
            'meta_description' => 'Inscrivez-vous au webinaire gratuit sur [SUJET]. [DATE] à [HEURE]. Places limitées !',
        ]);

        $this->buildRegistrationPage($funnel);
        $this->buildConfirmationPage($funnel);
        $this->buildReplayPage($funnel);

        return $funnel;
    }

    private function buildRegistrationPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Inscription Webinaire',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '🔴 WEBINAIRE GRATUIT - Places limitées !', 'RÉSERVER MA PLACE →'),

            // Live badge
            $this->richText(2, '
                <div class="text-center mb-6">
                    <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                        🔴 WEBINAIRE EN DIRECT - GRATUIT
                    </span>
                </div>
            '),

            $this->heroTitle(3, 'LES 3 SECRETS POUR [RÉSULTAT DÉSIRÉ] EN [TEMPS]', [
                'color' => '#F97316',
            ]),

            $this->subtitle(4, 'Rejoignez-nous en direct pour découvrir la méthode qui a permis à +500 personnes de [BÉNÉFICE]'),

            // Event details with Icon Boxes
            $this->iconBox(5, '📅', 'Samedi 15 Février', '2025'),
            $this->iconBox(6, '⏰', '15h00 (Paris)', 'Durée : 90 min'),
            $this->iconBox(7, '💻', 'En ligne (Zoom)', '+ Replay disponible'),

            // Countdown
            $this->countdown(8, '⏱️ LE WEBINAIRE COMMENCE DANS :', 72),

            // What you'll learn
            $this->features(9, '📚 AU PROGRAMME DE CE WEBINAIRE :', [
                ['title' => 'Secret #1 : [Titre]', 'text' => 'Ce que la majorité des gens ignorent', 'icon' => 'heroicon-o-key'],
                ['title' => 'Secret #2 : [Titre]', 'text' => 'La stratégie contre-intuitive qui accélère les résultats', 'icon' => 'heroicon-o-bolt'],
                ['title' => 'Secret #3 : [Titre]', 'text' => 'Mon plan d\'action personnel en exclusivité', 'icon' => 'heroicon-o-map'],
                ['title' => 'Bonus : Session Q&A', 'text' => 'Posez toutes vos questions en direct', 'icon' => 'heroicon-o-chat-bubble-left-right'],
            ]),

            // Speaker
            $this->richText(10, '
                <div class="bg-gradient-to-r from-orange-900/30 to-amber-900/30 p-8 rounded-2xl border border-orange-500/30 mb-8">
                    <h3 class="text-center text-xl font-bold mb-6">👨‍🏫 VOTRE FORMATEUR</h3>
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <img src="https://i.pravatar.cc/120?u=speaker" class="w-28 h-28 rounded-full border-4 border-orange-500" alt="">
                        <div>
                            <h4 class="text-2xl font-bold">[NOM DU SPEAKER]</h4>
                            <p class="text-orange-400 mb-3">Expert [DOMAINE] • +10 ans d\'expérience</p>
                            <p class="text-gray-400 text-sm">[Bio courte du speaker]</p>
                        </div>
                    </div>
                </div>
            '),

            // Registration form
            $this->form(11, 'Réservez votre place GRATUITE', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ], 'OUI, JE RÉSERVE MA PLACE ! 🎯'),

            // Trust badges
            $this->trustBadges(12, [
                ['icon' => '✅', 'text' => '100% gratuit'],
                ['icon' => '🎥', 'text' => 'Replay inclus'],
                ['icon' => '❓', 'text' => 'Q&A en direct'],
            ]),

            // Exit popup
            $this->popup(13, '⏰ Ne ratez pas le webinaire !', [
                'body' => '<p class="mb-4">Inscrivez-vous maintenant pour :</p><ul class="text-left mb-4"><li>✅ Accéder au webinaire gratuit</li><li>✅ Recevoir le replay 48h</li><li>✅ Poser vos questions en direct</li></ul>',
                'trigger' => 'exit_intent',
                'button_text' => 'RÉSERVER MA PLACE',
                'show_once' => true,
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
            $this->progress(1, 100, 'Place réservée ! 🎉'),

            $this->heroTitle(2, '✅ VOUS ÊTES INSCRIT(E) !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre place au webinaire est réservée'),

            // Confirmation
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-green-400 mb-4">Inscription confirmée !</h3>
                    <p class="mb-4">Un email avec le lien Zoom vous a été envoyé.</p>
                    <div class="bg-white/10 p-4 rounded-xl inline-block">
                        <p class="font-bold text-lg">📅 Samedi 15 Février 2025</p>
                        <p class="text-orange-400">⏰ 15h00 (Paris)</p>
                    </div>
                </div>
            '),

            // Calendar buttons with Icon Boxes
            $this->richText(5, '<h3 class="font-bold text-xl mb-4 text-center">📅 Ajoutez le rappel à votre agenda :</h3>'),

            $this->richText(6, '
                <div class="flex justify-center gap-4 flex-wrap mb-8">
                    <a href="#" class="bg-blue-600 px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">📆 Google Calendar</a>
                    <a href="#" class="bg-gray-600 px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">📆 Outlook</a>
                    <a href="#" class="bg-gray-600 px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">📆 Apple Calendar</a>
                </div>
            '),

            // Important steps
            $this->richText(7, $this->stepsHtml([
                'Vérifiez vos emails (et spams) pour le lien Zoom',
                'Connectez-vous 5-10 min avant le début',
                'Préparez vos questions pour le Q&A',
                'Le replay sera disponible 48h après le live',
            ], '⚠️ IMPORTANT - À LIRE :')),

            // Share
            $this->whatsappButton(8, 'Inviter un ami 💬', '', 'Hey ! Je viens de m\'inscrire à un webinaire gratuit sur [SUJET]. Inscris-toi ici :'),

            // Social icons
            $this->socialIcons(9, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }

    private function buildReplayPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'Replay du Webinaire',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Sticky bar urgency
            $this->stickyBar(1, '⏰ Ce replay sera supprimé dans 48h !', 'REGARDER MAINTENANT'),

            $this->heroTitle(2, '🎬 REPLAY DU WEBINAIRE', [
                'color' => '#F97316',
            ]),

            $this->subtitle(3, 'Vous avez manqué le live ? Pas de problème, regardez le replay'),

            // Countdown
            $this->countdown(4, '⏰ LE REPLAY SERA SUPPRIMÉ DANS :', 48),

            // Video
            $this->video(5),

            // Offer box
            $this->richText(6, '
                <div class="bg-gradient-to-r from-orange-900/30 to-amber-900/30 p-8 rounded-2xl border border-orange-500/30 text-center mt-8">
                    <h3 class="text-orange-400 font-bold text-2xl mb-4">🚀 PRÊT À PASSER À L\'ACTION ?</h3>
                    <p class="mb-6">Profitez de l\'offre spéciale webinaire (valable 48h)</p>
                </div>
            '),

            $this->ctaButton(7, 'DÉCOUVRIR L\'OFFRE SPÉCIALE 🎁', [], [
                'backgroundColor' => '#F97316',
            ]),

            $this->spacer(8, '20px'),

            $this->whatsappButton(9, 'Une question ? Contactez-nous 💬', '+33123456789', 'Bonjour ! J\'ai regardé le replay du webinaire et j\'ai une question.'),
        ]);
    }
}
