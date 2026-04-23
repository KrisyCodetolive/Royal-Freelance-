<?php

namespace App\Services\Template;

use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Quiz Interactif (5 pages)
 * 
 * Version améliorée avec :
 * - Progress bars natifs
 * - Icon boxes
 * - Trust badges
 * - FAQ
 * - Social icons
 */
class QuizTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'quiz';
    }

    public function getIcon(): string
    {
        return '❓';
    }

    public function getName(): string
    {
        return 'Quiz Interactif';
    }

    public function getDescription(): string
    {
        return 'Quiz multi-étapes pour qualifier les prospects et proposer des solutions personnalisées.';
    }

    public function getTags(): array
    {
        return ['quiz', 'qualification', 'interactif', 'personnalisé'];
    }

    public function getPrimaryColor(): string
    {
        return '#14B8A6';
    }

    public function getSecondaryColor(): string
    {
        return '#0D9488';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Quiz Gratuit - Découvrez Votre [PROFIL]',
            'meta_description' => 'Répondez à 5 questions simples et recevez votre diagnostic personnalisé gratuitement.',
        ]);

        $this->buildIntroPage($funnel);
        $this->buildQuestion1($funnel);
        $this->buildQuestion2($funnel);
        $this->buildCapturePage($funnel);
        $this->buildResultsPage($funnel);

        return $funnel;
    }

    private function buildIntroPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Intro Quiz',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Badge
            $this->richText(1, '
                <div class="text-center mb-6">
                    <span class="bg-teal-500/20 text-teal-400 px-4 py-2 rounded-full text-sm font-bold">
                        🎯 QUIZ GRATUIT • 2 MIN
                    </span>
                </div>
            '),

            $this->heroTitle(2, 'DÉCOUVREZ VOTRE [PROFIL/NIVEAU]', [
                'color' => '#14B8A6',
            ]),

            $this->subtitle(3, 'Répondez à 5 questions simples pour obtenir votre diagnostic personnalisé'),

            // What you get with Icon Boxes
            $this->richText(4, '<h3 class="text-teal-400 font-bold text-xl mb-6 text-center">🎁 À LA FIN DU QUIZ :</h3>'),

            $this->iconBox(5, '📊', 'Votre profil détaillé', 'Découvrez vos forces et points à améliorer'),
            $this->iconBox(6, '💡', 'Conseils personnalisés', 'Des recommandations adaptées à votre situation'),
            $this->iconBox(7, '🎯', 'Plan d\'action sur-mesure', 'Les étapes concrètes pour atteindre vos objectifs'),

            // CTA
            $this->ctaButton(8, 'COMMENCER LE QUIZ ➜', [], [
                'backgroundColor' => '#14B8A6',
                'fontSize' => '1.3rem',
            ]),

            // Trust badges
            $this->trustBadges(9, [
                ['icon' => '⏱️', 'text' => '2 minutes'],
                ['icon' => '❓', 'text' => '5 questions'],
                ['icon' => '✅', 'text' => '100% gratuit'],
            ]),

            // Social proof
            $this->richText(10, '
                <div class="text-center mt-6">
                    <p class="text-gray-400 text-sm">Déjà <strong class="text-teal-400">3,847 personnes</strong> ont fait le quiz</p>
                </div>
            '),
        ]);
    }

    private function buildQuestion1(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::QUIZ,
            'title' => 'Question 1',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress bar (native)
            $this->progress(1, 20, 'Question 1 sur 5'),

            $this->heroTitle(2, 'Quel est votre objectif principal ?', [
                'fontSize' => '2rem',
                'color' => '#FFFFFF',
            ]),

            $this->form(3, '', [
                [
                    'name' => 'objective',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        '🚀 Lancer mon activité',
                        '📈 Développer mes revenus',
                        '⏰ Gagner du temps',
                        '🎯 Me clarifier sur ma direction',
                        '💪 Gagner en confiance',
                    ]
                ],
            ], 'SUIVANT ➜'),
        ]);
    }

    private function buildQuestion2(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::QUIZ,
            'title' => 'Question 2',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Progress bar
            $this->progress(1, 40, 'Question 2 sur 5'),

            $this->heroTitle(2, 'Quel est votre plus grand défi ?', [
                'fontSize' => '2rem',
                'color' => '#FFFFFF',
            ]),

            $this->form(3, '', [
                [
                    'name' => 'challenge',
                    'type' => 'radio',
                    'required' => true,
                    'options' => [
                        '😰 Le manque de temps',
                        '💸 Le manque de budget',
                        '🤷 Le manque de clarté',
                        '😟 La peur d\'échouer',
                        '📚 Le manque de connaissances',
                    ]
                ],
            ], 'SUIVANT ➜'),
        ]);
    }

    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Vos Résultats',
            'sort_order' => 4,
        ]);

        $this->createBlocks($page, [
            // Progress bar complete
            $this->progress(1, 100, '🎉 Quiz terminé !'),

            $this->heroTitle(2, '🎉 QUIZ TERMINÉ !', [
                'color' => '#14B8A6',
            ]),

            $this->subtitle(3, 'Entrez vos informations pour recevoir vos résultats personnalisés'),

            $this->form(4, '', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ], 'VOIR MES RÉSULTATS 🎯'),

            // Trust badges
            $this->trustBadges(5, [
                ['icon' => '🔒', 'text' => 'Données protégées'],
                ['icon' => '📧', 'text' => 'Pas de spam'],
            ]),
        ]);
    }

    private function buildResultsPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'Résultats',
            'sort_order' => 5,
        ]);

        $this->createBlocks($page, [
            $this->heroTitle(1, '🏆 VOTRE PROFIL : [DYNAMIQUE]', [
                'color' => '#14B8A6',
            ]),

            // Profile result
            $this->richText(2, '
                <div class="bg-teal-900/40 p-8 rounded-2xl border border-teal-500/30 text-center mb-8">
                    <div class="w-24 h-24 bg-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">🌟</div>
                    <p class="text-lg">Basé sur vos réponses, voici votre diagnostic personnalisé et les actions recommandées.</p>
                </div>
            '),

            // Strengths with Icon Boxes
            $this->richText(3, '<h3 class="text-green-400 font-bold text-xl mb-4">💪 VOS FORCES</h3>'),

            $this->iconBox(4, '✅', 'Force identifiée #1', 'Description de cette force'),
            $this->iconBox(5, '✅', 'Force identifiée #2', 'Description de cette force'),
            $this->iconBox(6, '✅', 'Force identifiée #3', 'Description de cette force'),

            // Areas to improve
            $this->richText(7, '<h3 class="text-amber-400 font-bold text-xl mb-4 mt-8">⚠️ À AMÉLIORER</h3>'),

            $this->iconBox(8, '🔧', 'Point à travailler #1', 'Description de ce point'),
            $this->iconBox(9, '🔧', 'Point à travailler #2', 'Description de ce point'),

            // Solution
            $this->richText(10, '
                <div class="bg-gradient-to-r from-teal-900/30 to-cyan-900/30 p-8 rounded-2xl border border-teal-500/30 text-center mt-8 mb-8">
                    <h3 class="text-teal-400 font-bold text-xl mb-4">🚀 SOLUTION RECOMMANDÉE</h3>
                    <p class="mb-6">Basé sur votre profil, voici ce qui vous aiderait le plus...</p>
                </div>
            '),

            $this->ctaButton(11, 'DÉCOUVRIR LA SOLUTION 🎯', [], [
                'backgroundColor' => '#14B8A6',
            ]),

            $this->spacer(12, '20px'),

            // Social icons
            $this->socialIcons(13, [
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'facebook', 'url' => '#'],
            ]),
        ]);
    }
}
