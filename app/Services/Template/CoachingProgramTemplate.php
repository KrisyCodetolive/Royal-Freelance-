<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Programme de Coaching (6 pages)
 * 
 * Version améliorée style Systeme.io avec :
 * - Quiz multi-étapes avec progress bars
 * - FAQ accordéon
 * - Pricing table comparatif
 * - Trust badges
 * - Icon boxes
 * - Sticky bar
 * - Exit popup
 * - Social icons
 */
class CoachingProgramTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'coaching';
    }

    public function getIcon(): string
    {
        return '🎯';
    }

    public function getName(): string
    {
        return 'Programme de Coaching';
    }

    public function getDescription(): string
    {
        return 'Tunnel complet pour coach : page d\'attraction, quiz de qualification intelligent, résultats personnalisés, offre de coaching premium et prise de rendez-vous.';
    }

    public function getTags(): array
    {
        return ['coaching', 'quiz', 'consultation', 'accompagnement', 'programme', 'rendez-vous', 'premium'];
    }

    public function getPrimaryColor(): string
    {
        return '#F59E0B';
    }

    public function getSecondaryColor(): string
    {
        return '#D97706';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Programme de Coaching - Transformez votre [DOMAINE]',
            'meta_description' => 'Coaching personnalisé avec accompagnement sur-mesure. Faites le quiz gratuit pour découvrir votre profil et recevoir votre plan d\'action.',
        ]);

        $this->buildAttractionPage($funnel);
        $this->buildQuizPage($funnel);
        $this->buildResultsPage($funnel);
        $this->buildSalesPage($funnel);
        $this->buildBookingPage($funnel);
        $this->buildConfirmationPage($funnel);

        return $funnel;
    }

    // =========================================================================
    // PAGE 1: ATTRACTION
    // =========================================================================
    private function buildAttractionPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Présentation Coaching',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '🎯 Quiz GRATUIT : Découvrez votre profil en 2 min !', 'FAIRE LE QUIZ →'),

            // Badge
            $this->richText(2, '
                <div class="text-center mb-4">
                    <span class="bg-amber-500/20 text-amber-400 px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                        🔥 NOUVEAU PROGRAMME 2025
                    </span>
                </div>
            '),

            $this->heroTitle(3, '🚀 TRANSFORMEZ VOTRE [DOMAINE] EN 90 JOURS', [
                'color' => '#F59E0B',
            ]),

            $this->subtitle(4, 'Le programme de coaching personnalisé qui vous accompagne pas à pas vers vos objectifs — avec garantie de résultats'),

            // Video
            $this->video(5),

            // Results showcase with Icon Boxes
            $this->richText(6, '<div class="grid md:grid-cols-4 gap-6 mb-12">'),
            $this->iconBox(7, '847+', 'Clients accompagnés', ''),
            $this->iconBox(8, '94%', 'Atteignent leurs objectifs', ''),
            $this->iconBox(9, '12+', 'Années d\'expérience', ''),
            $this->iconBox(10, '4.9/5', 'Note moyenne', ''),

            // What you get
            $this->features(11, '🎯 CE QUE MES CLIENTS OBTIENNENT :', [
                ['title' => '📞 Séances de Coaching Hebdomadaires', 'text' => 'Appels individuels 1-à-1 d\'1h chaque semaine pour un suivi personnalisé.', 'icon' => 'heroicon-o-phone'],
                ['title' => '📋 Plan d\'Action Personnalisé', 'text' => 'Une stratégie sur-mesure créée spécifiquement pour votre situation.', 'icon' => 'heroicon-o-clipboard-document-check'],
                ['title' => '💬 Support WhatsApp Illimité', 'text' => 'Accès direct à votre coach 7j/7. Réponse sous 24h garantie.', 'icon' => 'heroicon-o-chat-bubble-left-right'],
                ['title' => '🎯 Suivi et Mesure des Résultats', 'text' => 'Tableaux de bord personnalisés pour visualiser votre progression.', 'icon' => 'heroicon-o-chart-bar-square'],
                ['title' => '📚 Ressources Exclusives', 'text' => 'Accès à ma bibliothèque privée : templates, scripts, outils.', 'icon' => 'heroicon-o-book-open'],
                ['title' => '🤝 Communauté d\'Entraide', 'text' => 'Rejoignez un groupe de personnes motivées partageant les mêmes objectifs.', 'icon' => 'heroicon-o-user-group'],
            ]),

            // Testimonials
            $this->testimonial(12, 'Mathieu R.', 'Avant le coaching, je tournais en rond depuis 2 ans. En 3 mois, j\'ai clarifié ma vision, structuré mon offre et triplé mes revenus. Le meilleur investissement de ma vie.', 'Entrepreneur — CA x3 en 90 jours'),
            $this->testimonial(13, 'Sarah L.', 'J\'avais le syndrome de l\'imposteur et j\'étais paralysée. Le coaching m\'a donné la confiance et la méthode pour lancer mon activité. 6 mois plus tard, j\'ai 15 clients réguliers.', 'Coach bien-être — De 0 à 15 clients'),

            // Urgency
            $this->richText(14, '
                <div class="bg-red-900/30 p-6 rounded-2xl border border-red-500/30 text-center mb-8">
                    <h3 class="text-red-400 font-bold text-xl mb-2">⚠️ PLACES TRÈS LIMITÉES</h3>
                    <p class="mb-2">Je n\'accompagne que <strong>12 clients</strong> en même temps</p>
                    <p class="text-sm text-gray-400">Pour garantir un suivi de qualité maximale</p>
                    <p class="text-amber-400 font-bold mt-4">Actuellement : <span class="text-white">3 places disponibles</span></p>
                </div>
            '),

            // CTA
            $this->ctaButton(15, 'FAIRE LE QUIZ GRATUIT (2 min) 📋', [], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.3rem',
            ]),

            $this->richText(16, '
                <p class="text-center text-gray-400 mt-4">
                    Découvrez votre profil et recevez des conseils personnalisés
                </p>
            '),

            // Trust badges
            $this->trustBadges(17, [
                ['icon' => '✅', 'text' => 'Quiz 100% gratuit'],
                ['icon' => '⏱️', 'text' => '2 minutes seulement'],
                ['icon' => '🎁', 'text' => 'Conseils personnalisés'],
            ]),

            // Exit popup
            $this->popup(18, '🎯 Attendez !', [
                'body' => '<p class="mb-4">Faites le quiz GRATUIT et découvrez :</p><ul class="text-left mb-4"><li>✅ Votre profil personnalisé</li><li>✅ Vos forces et points à travailler</li><li>✅ Un plan d\'action sur-mesure</li></ul>',
                'trigger' => 'exit_intent',
                'button_text' => 'FAIRE LE QUIZ MAINTENANT',
                'show_once' => true,
            ]),

            // Social icons
            $this->socialIcons(19, [
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
                ['platform' => 'linkedin', 'url' => '#'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 2: QUIZ DE QUALIFICATION
    // =========================================================================
    private function buildQuizPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::QUIZ,
            'title' => 'Quiz de Qualification',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress bar (new block type!)
            $this->progress(1, 50, 'Quiz en cours...'),

            $this->heroTitle(2, '📋 QUIZ PERSONNALISÉ', [
                'color' => '#F59E0B',
                'fontSize' => '2.5rem',
            ]),

            $this->subtitle(3, 'Répondez à ces questions pour que je puisse mieux comprendre votre situation'),

            // Benefits with Icon Boxes
            $this->richText(4, '<h3 class="text-blue-400 font-bold text-lg mb-4 text-center">🎁 À LA FIN DU QUIZ, VOUS RECEVREZ :</h3>'),

            $this->iconBox(5, '🎯', 'Votre profil détaillé', 'Découvrez vos forces et points d\'amélioration'),
            $this->iconBox(6, '📋', 'Conseils personnalisés', 'Des recommandations adaptées à votre situation'),
            $this->iconBox(7, '🚀', 'Plan d\'action sur-mesure', 'Les étapes concrètes pour atteindre vos objectifs'),

            // Quiz form
            $this->form(8, '', [
                ['name' => 'first_name', 'label' => 'Votre prénom', 'type' => 'text', 'required' => true, 'placeholder' => 'Prénom'],
                ['name' => 'email', 'label' => 'Votre email', 'type' => 'email', 'required' => true, 'placeholder' => 'email@exemple.com'],
                [
                    'name' => 'situation',
                    'label' => 'Quelle est votre situation actuelle ?',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Salarié(e) en reconversion',
                        'Entrepreneur(e) débutant(e)',
                        'Entrepreneur(e) confirmé(e)',
                        'Freelance/Indépendant(e)',
                        'Autre',
                    ]
                ],
                [
                    'name' => 'goal',
                    'label' => 'Quel est votre objectif principal ?',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Lancer mon activité',
                        'Développer mon CA',
                        'Trouver plus de clients',
                        'Mieux m\'organiser',
                        'Gagner en confiance',
                        'Autre',
                    ]
                ],
                [
                    'name' => 'timeline',
                    'label' => 'Dans quel délai souhaitez-vous atteindre cet objectif ?',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Dès que possible (urgence)',
                        'Dans 1-3 mois',
                        'Dans 3-6 mois',
                        'Dans les 12 prochains mois',
                    ]
                ],
                ['name' => 'challenge', 'label' => 'Quel est votre plus gros défi aujourd\'hui ?', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Décrivez en quelques mots ce qui vous bloque le plus...'],
                [
                    'name' => 'investment',
                    'label' => 'Êtes-vous prêt(e) à investir pour atteindre vos objectifs ?',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Oui, c\'est une priorité',
                        'Oui, si le programme me convient',
                        'Je dois d\'abord voir les résultats potentiels',
                    ]
                ],
            ], 'VOIR MES RÉSULTATS 🎯', [
                'maxWidth' => '600px',
            ]),

            // Trust
            $this->trustBadges(9, [
                ['icon' => '🔒', 'text' => '100% confidentiel'],
                ['icon' => '📧', 'text' => 'Pas de spam'],
                ['icon' => '⏱️', 'text' => '2 min seulement'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 3: RÉSULTATS DU QUIZ
    // =========================================================================
    private function buildResultsPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'Vos Résultats',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Quiz terminé ! 🎉'),

            $this->heroTitle(2, '🎯 VOS RÉSULTATS PERSONNALISÉS', [
                'color' => '#10B981',
            ]),

            // Profile result
            $this->richText(3, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                        🌟
                    </div>
                    <h2 class="text-3xl font-bold text-green-400 mb-4">Profil : Le [PROFIL DYNAMIQUE]</h2>
                    <p class="text-lg max-w-xl mx-auto">
                        Basé sur vos réponses, vous avez un fort potentiel mais quelques blocages spécifiques vous empêchent d\'avancer aussi vite que vous le souhaitez.
                    </p>
                </div>
            '),

            // Strengths with Icon Boxes
            $this->richText(4, '<h3 class="text-2xl font-bold mb-6">💪 VOS FORCES IDENTIFIÉES</h3>'),
            $this->iconBox(5, '🎯', 'Objectifs Clairs', 'Vous savez où vous voulez aller, c\'est essentiel pour réussir.'),
            $this->iconBox(6, '🔥', 'Motivation Forte', 'Votre envie de changer est un moteur puissant.'),
            $this->iconBox(7, '💡', 'Ouverture d\'Esprit', 'Vous êtes prêt(e) à apprendre et à vous remettre en question.'),

            // Challenges with Icon Boxes
            $this->richText(8, '<h3 class="text-2xl font-bold mb-6 mt-8">⚠️ VOS DÉFIS À SURMONTER</h3>'),
            $this->iconBox(9, '🧭', 'Manque de Clarté', 'Trop d\'options possibles créent de la confusion et de l\'inaction.'),
            $this->iconBox(10, '⏰', 'Gestion du Temps', 'Difficultés à prioriser et à rester constant dans l\'effort.'),
            $this->iconBox(11, '😰', 'Syndrome de l\'Imposteur', 'Doutes sur vos capacités qui freinent vos initiatives.'),

            // Recommendations
            $this->richText(12, $this->stepsHtml([
                'Clarifiez votre positionnement — Avant d\'agir, savoir exactement QUI vous aidez et COMMENT.',
                'Structurez vos actions — Un plan clair avec des étapes précises évitera la dispersion.',
                'Faites-vous accompagner — Un coach vous fera aller plus vite et éviter les erreurs coûteuses.',
            ], '💡 MES RECOMMANDATIONS POUR VOUS :')),

            // Solution presentation
            $this->richText(13, '
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold mb-4">🚀 LA SOLUTION ADAPTÉE À VOTRE PROFIL</h3>
                    <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                        Mon programme de coaching de 90 jours a été spécifiquement conçu pour les profils comme le vôtre. Découvrez comment je peux vous aider à atteindre vos objectifs.
                    </p>
                </div>
            '),

            $this->ctaButton(14, 'DÉCOUVRIR LE PROGRAMME 🚀', [], [
                'backgroundColor' => '#F59E0B',
                'fontSize' => '1.3rem',
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 4: PAGE DE VENTE
    // =========================================================================
    private function buildSalesPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::SALES,
            'title' => 'Programme Coaching',
            'sort_order' => 4,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '⏰ Plus que 3 places disponibles !', 'RÉSERVER MON APPEL'),

            $this->heroTitle(2, '🏆 PROGRAMME COACHING INTENSIF', [
                'color' => '#F59E0B',
            ]),

            $this->subtitle(3, 'L\'accompagnement personnalisé qui transforme votre potentiel en résultats concrets'),

            // Countdown
            $this->countdown(4, '⏰ OFFRE DE LANCEMENT - Plus que :', 72),

            // What's included
            $this->features(5, '📦 LE PROGRAMME COMPLET INCLUT :', [
                ['title' => '📞 12 Séances de Coaching Individuel', 'text' => 'Appels hebdomadaires d\'1h en visio pendant 3 mois. Valeur : 1.800€', 'icon' => 'heroicon-o-phone'],
                ['title' => '📋 Plan d\'Action Personnalisé', 'text' => 'Stratégie 100% sur-mesure créée ensemble. Valeur : 500€', 'icon' => 'heroicon-o-clipboard-document-check'],
                ['title' => '💬 Support WhatsApp Illimité', 'text' => 'Accès direct 7j/7, réponse garantie sous 24h. Valeur : 600€', 'icon' => 'heroicon-o-chat-bubble-left-right'],
                ['title' => '📚 Accès à Toutes Mes Formations', 'text' => 'Bibliothèque complète de cours vidéo et outils. Valeur : 997€', 'icon' => 'heroicon-o-academic-cap'],
                ['title' => '🎯 Suivi des Objectifs', 'text' => 'Tableau de bord + réunions de bilan mensuelles. Valeur : 300€', 'icon' => 'heroicon-o-chart-bar'],
                ['title' => '🤝 Communauté Privée', 'text' => 'Groupe d\'entraide avec mes autres clients. Valeur : 500€', 'icon' => 'heroicon-o-user-group'],
            ]),

            // Comparison
            $this->richText(6, $this->comparisonHtml(
                [
                    'Avancer seul(e) à l\'aveugle',
                    'Perdre du temps sur les mauvaises priorités',
                    'Douter et procrastiner',
                    'Répéter les mêmes erreurs',
                    'Stagner pendant des mois',
                ],
                [
                    'Parcours guidé étape par étape',
                    'Focus sur ce qui génère des résultats',
                    'Confiance et momentum constant',
                    'Apprendre de mon expérience',
                    'Résultats mesurables dès le 1er mois',
                ]
            )),

            // Results
            $this->richText(7, '
                <h3 class="text-2xl font-bold text-center mb-8">📊 RÉSULTATS TYPIQUES DE MES CLIENTS</h3>
                <div class="grid md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 text-center">
                        <p class="text-5xl font-bold text-green-400">x2.5</p>
                        <p class="text-gray-400 mt-2">Revenus moyens après 90 jours</p>
                    </div>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 text-center">
                        <p class="text-5xl font-bold text-blue-400">89%</p>
                        <p class="text-gray-400 mt-2">Clients satisfaits</p>
                    </div>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 text-center">
                        <p class="text-5xl font-bold text-purple-400">-50%</p>
                        <p class="text-gray-400 mt-2">Temps de travail gagné</p>
                    </div>
                </div>
            '),

            // PRICING TABLE (nouveau bloc!)
            $this->pricing(8, [
                [
                    'name' => 'Coaching Solo',
                    'price' => '997€',
                    'period' => 'ou 3x 332€',
                    'description' => 'L\'essentiel',
                    'features' => [
                        '6 séances de coaching',
                        'Plan d\'action personnalisé',
                        'Support email',
                        'Accès formations de base',
                    ],
                    'button_text' => 'Choisir',
                    'is_popular' => false,
                ],
                [
                    'name' => 'Coaching Premium',
                    'price' => '1.997€',
                    'period' => 'ou 3x 665€',
                    'old_price' => '4.697€',
                    'description' => 'Le plus populaire ⭐',
                    'features' => [
                        '✅ 12 séances de coaching',
                        '✅ Plan d\'action personnalisé',
                        '✅ Support WhatsApp illimité',
                        '✅ Toutes les formations',
                        '✅ Suivi des objectifs',
                        '✅ Communauté privée',
                        '✅ Garantie résultats 90j',
                    ],
                    'button_text' => 'CHOISIR PREMIUM 🚀',
                    'is_popular' => true,
                ],
            ]),

            // CTA
            $this->ctaButton(9, 'RÉSERVER MON APPEL DÉCOUVERTE 📞', [], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.4rem',
                'padding' => '24px 48px',
            ]),

            $this->richText(10, '
                <p class="text-center text-gray-400 mt-4">
                    Appel gratuit de 30 min pour voir si nous sommes compatibles
                </p>
            '),

            // Trust badges
            $this->trustBadges(11, [
                ['icon' => '✅', 'text' => 'Satisfait ou remboursé 14j'],
                ['icon' => '🔒', 'text' => 'Paiement sécurisé'],
                ['icon' => '💳', 'text' => 'Paiement 3x sans frais'],
            ]),

            $this->spacer(12, '30px'),

            // Guarantee
            $this->richText(13, '
                <div class="bg-blue-900/30 p-8 rounded-2xl border border-blue-500/30 text-center mb-8">
                    <div class="text-5xl mb-4">🛡️</div>
                    <h3 class="text-2xl font-bold text-blue-400 mb-4">MA GARANTIE ZÉRO RISQUE</h3>
                    <p class="max-w-xl mx-auto">
                        Si après 14 jours vous estimez que le programme ne vous convient pas, 
                        je vous rembourse intégralement. Aucune question, aucune complication.
                        <br><br>
                        <strong>Mieux encore :</strong> Si vous ne voyez pas de résultats concrets en 90 jours 
                        en suivant le programme, je vous offre 3 mois de coaching supplémentaires gratuits.
                    </p>
                </div>
            '),

            // FAQ (nouveau bloc!)
            $this->faq(14, '❓ QUESTIONS FRÉQUENTES', [
                ['question' => 'Comment se déroulent les séances ?', 'answer' => 'Les séances se font en visio (Zoom) et durent 1h. Nous définissons ensemble un créneau fixe chaque semaine. Chaque séance est enregistrée pour que vous puissiez la revoir.'],
                ['question' => 'Je n\'ai pas beaucoup de temps, est-ce adapté ?', 'answer' => 'Le programme demande environ 5-7h par semaine. Nous optimisons votre temps pour vous concentrer sur ce qui compte vraiment. L\'objectif est justement de vous faire gagner du temps.'],
                ['question' => 'Et si je ne vois pas de résultats ?', 'answer' => '94% de mes clients atteignent leurs objectifs. Si ce n\'est pas votre cas après avoir appliqué le programme, je vous offre 3 mois de coaching supplémentaires gratuits.'],
                ['question' => 'Puis-je payer en plusieurs fois ?', 'answer' => 'Oui ! Je propose un paiement en 3 fois sans frais pour rendre le programme accessible.'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 5: RÉSERVATION D'APPEL
    // =========================================================================
    private function buildBookingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Réserver un Appel',
            'sort_order' => 5,
        ]);

        $this->createBlocks($page, [
            // Progress
            $this->progress(1, 80, 'Plus qu\'une étape...'),

            $this->heroTitle(2, '📞 RÉSERVEZ VOTRE APPEL DÉCOUVERTE', [
                'color' => '#10B981',
                'fontSize' => '2.5rem',
            ]),

            $this->subtitle(3, 'Appel gratuit de 30 minutes pour voir si nous pouvons travailler ensemble'),

            // What to expect with Icon Boxes
            $this->richText(4, '<h3 class="text-blue-400 font-bold text-xl mb-6 text-center">📋 CE QUI SE PASSE PENDANT L\'APPEL :</h3>'),

            $this->iconBox(5, '1️⃣', 'On fait le point sur votre situation', 'Où en êtes-vous ? Quels sont vos défis ?'),
            $this->iconBox(6, '2️⃣', 'On clarifie vos objectifs', 'Où voulez-vous aller ? Dans quel délai ?'),
            $this->iconBox(7, '3️⃣', 'Je vous donne des conseils', 'Minimum 3 actions concrètes à mettre en place'),
            $this->iconBox(8, '4️⃣', 'On voit si on est compatibles', 'Aucune pression, on discute simplement'),

            // Booking form
            $this->form(9, 'Choisissez votre créneau', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Nom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'Téléphone / WhatsApp', 'type' => 'tel', 'required' => true],
                [
                    'name' => 'preferred_date',
                    'label' => 'Créneau préféré',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        'Cette semaine - Matin',
                        'Cette semaine - Après-midi',
                        'Semaine prochaine - Matin',
                        'Semaine prochaine - Après-midi',
                    ]
                ],
                ['name' => 'message', 'label' => 'Un message à me transmettre avant l\'appel ?', 'type' => 'textarea', 'required' => false, 'placeholder' => 'Facultatif...'],
            ], 'CONFIRMER MON APPEL GRATUIT 📞', [
                'maxWidth' => '600px',
            ]),

            // Trust badges
            $this->trustBadges(10, [
                ['icon' => '✅', 'text' => 'Appel 100% gratuit'],
                ['icon' => '🚫', 'text' => 'Aucun engagement'],
                ['icon' => '🎁', 'text' => 'Conseils offerts'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 6: CONFIRMATION
    // =========================================================================
    private function buildConfirmationPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::THANK_YOU,
            'title' => 'Confirmation',
            'sort_order' => 6,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Rendez-vous confirmé ! 🎉'),

            $this->heroTitle(2, '🎉 RENDEZ-VOUS CONFIRMÉ !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'J\'ai hâte de discuter avec vous'),

            // Success box
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">Votre appel est réservé !</h3>
                    <p class="text-lg mb-2">Je vous contacterai à l\'heure convenue.</p>
                    <p class="text-sm text-gray-400">Un email de confirmation vous a été envoyé avec tous les détails.</p>
                </div>
            '),

            // Next steps
            $this->richText(5, $this->stepsHtml([
                '📧 Vérifiez votre email de confirmation',
                '📅 Ajoutez le RDV à votre calendrier',
                '📋 Préparez vos questions et objectifs',
                '📞 Je vous appelle à l\'heure convenue',
            ], '📋 D\'ICI NOTRE APPEL :')),

            // Prepare for call with Icon Boxes
            $this->richText(6, '<h3 class="text-amber-400 font-bold text-xl mb-4 text-center">💡 POUR PRÉPARER NOTRE APPEL</h3><p class="text-center mb-6">Réfléchissez à ces questions :</p>'),

            $this->iconBox(7, '❓', 'Quel est votre plus grand défi actuellement ?', ''),
            $this->iconBox(8, '🎯', 'Où voulez-vous être dans 90 jours ?', ''),
            $this->iconBox(9, '🚧', 'Qu\'est-ce qui vous a bloqué jusqu\'ici ?', ''),

            // Contact
            $this->richText(10, '
                <div class="text-center mt-8">
                    <h3 class="font-bold text-xl mb-4">💬 Une question avant l\'appel ?</h3>
                    <p class="text-gray-400 mb-6">N\'hésitez pas à me contacter</p>
                </div>
            '),

            $this->whatsappButton(11, 'M\'envoyer un message 📱', '+33123456789', 'Bonjour ! J\'ai réservé un appel découverte et j\'ai une question.'),

            // Social icons
            $this->socialIcons(12, [
                ['platform' => 'youtube', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'linkedin', 'url' => '#'],
            ]),
        ]);
    }
}
