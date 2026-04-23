<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: MLM & Communauté (5 pages)
 * 
 * Version améliorée avec les nouveaux blocs Systeme.io :
 * - Sticky bar
 * - Icon boxes
 * - FAQ accordéon
 * - Progress bars
 * - Trust badges
 * - Social icons
 * - Pricing table
 */
class MLMCommunityTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'mlm_community';
    }

    public function getIcon(): string
    {
        return '🤝';
    }

    public function getName(): string
    {
        return 'MLM & Communauté';
    }

    public function getDescription(): string
    {
        return 'Tunnel spécialisé MLM et communauté : page d\'attraction, capture de contacts, présentation d\'opportunité, inscription à l\'équipe et onboarding complet.';
    }

    public function getTags(): array
    {
        return ['mlm', 'communauté', 'recrutement', 'opportunité', 'équipe', 'réseau', 'marketing'];
    }

    public function getPrimaryColor(): string
    {
        return '#10B981';
    }

    public function getSecondaryColor(): string
    {
        return '#059669';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Opportunité Unique - Rejoignez Notre Équipe de Succès',
            'meta_description' => 'Découvrez comment des milliers de personnes génèrent des revenus complémentaires depuis chez elles. Formation et accompagnement inclus.',
        ]);

        $this->buildAttractionPage($funnel);
        $this->buildCapturePage($funnel);
        $this->buildOpportunityPage($funnel);
        $this->buildRegistrationPage($funnel);
        $this->buildOnboardingPage($funnel);

        return $funnel;
    }

    // =========================================================================
    // PAGE 1: ATTRACTION
    // =========================================================================
    private function buildAttractionPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Découvrez l\'Opportunité',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '🔥 PLACES LIMITÉES : Seulement 7 places ce mois !', 'VOIR L\'OPPORTUNITÉ →'),

            // Badge
            $this->richText(2, '
                <div class="text-center mb-6">
                    <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold animate-pulse">
                        🔴 PRÉSENTATION EN DIRECT
                    </span>
                </div>
            '),

            $this->heroTitle(3, '💰 COMMENT GÉNÉRER UN REVENU COMPLÉMENTAIRE DEPUIS CHEZ VOUS', [
                'color' => '#10B981',
            ]),

            $this->subtitle(4, 'La méthode simple que des milliers de personnes utilisent pour créer une source de revenus stable — sans quitter leur emploi'),

            // Stats with Icon Boxes
            $this->iconBox(5, '12,500+', 'Membres actifs', 'Dans le monde entier'),
            $this->iconBox(6, '47', 'Pays représentés', 'Communauté internationale'),
            $this->iconBox(7, '850K€', 'Commissions versées', 'Ce mois-ci'),
            $this->iconBox(8, '4.8/5', 'Satisfaction', 'Note moyenne'),

            // Video
            $this->video(9),

            // Testimonials
            $this->testimonial(10, 'Marie L.', 'En 3 mois, j\'ai remplacé mon ancien salaire. Aujourd\'hui je gagne 2.450€/mois en travaillant 2h par jour de chez moi.', 'Membre depuis 8 mois'),
            $this->testimonial(11, 'Thomas R.', 'J\'étais sceptique au début. Mais avec le système et le support de l\'équipe, j\'ai atteint 5.800€/mois. Ma vie a changé.', 'Membre depuis 14 mois'),

            // Benefits with Features
            $this->features(12, '🎯 POURQUOI ÇA FONCTIONNE AUSSI BIEN :', [
                ['title' => '📱 100% en Ligne', 'text' => 'Travaillez de n\'importe où avec simplement votre téléphone.', 'icon' => 'heroicon-o-device-phone-mobile'],
                ['title' => '⏰ Horaires Flexibles', 'text' => 'Vous décidez quand travailler. 2h par jour suffisent.', 'icon' => 'heroicon-o-clock'],
                ['title' => '🎓 Formation Complète', 'text' => 'Pas besoin d\'expérience, on vous apprend tout.', 'icon' => 'heroicon-o-academic-cap'],
                ['title' => '👥 Équipe Solidaire', 'text' => 'Groupe privé, coaching hebdo, et parrain dédié.', 'icon' => 'heroicon-o-user-group'],
            ]),

            // Urgency
            $this->richText(13, '
                <div class="bg-gradient-to-r from-red-900/40 to-orange-900/40 p-6 rounded-2xl border border-red-500/30 text-center mb-8">
                    <h3 class="text-red-400 font-bold text-xl mb-2">⚡ PLACES LIMITÉES CE MOIS-CI</h3>
                    <p class="mb-2">Nous recrutons seulement <strong>25 nouvelles personnes</strong> par mois</p>
                    <p class="text-amber-400 font-bold mt-4">Actuellement : <span class="text-white">7 places restantes</span></p>
                </div>
            '),

            // CTA
            $this->ctaButton(14, 'DÉCOUVRIR L\'OPPORTUNITÉ 🚀', [], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.3rem',
            ]),

            // Trust badges
            $this->trustBadges(15, [
                ['icon' => '✅', 'text' => 'Présentation gratuite'],
                ['icon' => '🚫', 'text' => 'Sans engagement'],
                ['icon' => '⏱️', 'text' => '15 minutes'],
            ]),

            // Social icons
            $this->socialIcons(16, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 2: CAPTURE
    // =========================================================================
    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Accéder à la Présentation',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress bar
            $this->progress(1, 33, 'Étape 1 sur 3 : Inscription'),

            $this->heroTitle(2, '🎬 PRÉSENTATION EXCLUSIVE', [
                'color' => '#F59E0B',
            ]),

            $this->subtitle(3, 'Découvrez en détail notre système et voyez si cette opportunité est faite pour vous'),

            // What they'll discover with Icon Boxes
            $this->richText(4, '<h3 class="text-green-400 font-bold text-xl mb-6 text-center">📋 CE QUE VOUS ALLEZ DÉCOUVRIR :</h3>'),

            $this->iconBox(5, '1️⃣', 'Le système complet', 'Comment tout fonctionne étape par étape'),
            $this->iconBox(6, '2️⃣', 'Les revenus réels', 'Témoignages avec preuves de paiements'),
            $this->iconBox(7, '3️⃣', 'Le plan de démarrage', 'Votre feuille de route pour les 30 premiers jours'),
            $this->iconBox(8, '4️⃣', 'Les outils fournis', 'Tout ce que vous recevez gratuitement'),
            $this->iconBox(9, '5️⃣', 'L\'accompagnement', 'Comment nous vous aidons à réussir'),

            // Form
            $this->form(10, 'Entrez vos informations pour accéder', [
                ['name' => 'first_name', 'label' => 'Prénom', 'type' => 'text', 'required' => true],
                ['name' => 'last_name', 'label' => 'Nom', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'WhatsApp', 'type' => 'tel', 'required' => true],
                [
                    'name' => 'situation',
                    'label' => 'Votre situation actuelle',
                    'type' => 'select',
                    'required' => true,
                    'options' => ['Salarié(e)', 'Indépendant(e)', 'Sans emploi', 'Étudiant(e)', 'Retraité(e)', 'Autre']
                ],
            ], 'VOIR LA PRÉSENTATION MAINTENANT 🎬'),

            // Trust badges
            $this->trustBadges(11, [
                ['icon' => '🔒', 'text' => 'Informations confidentielles'],
                ['icon' => '❌', 'text' => 'Pas de spam'],
                ['icon' => '✅', 'text' => '100% gratuit'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 3: PRÉSENTATION OPPORTUNITÉ
    // =========================================================================
    private function buildOpportunityPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'L\'Opportunité en Détail',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '⏰ L\'offre de lancement expire bientôt !', 'REJOINDRE MAINTENANT'),

            $this->heroTitle(2, '🚀 VOICI NOTRE SYSTÈME', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Le business model que nous utilisons pour générer des revenus stables'),

            // Main video
            $this->video(4),

            // How it works
            $this->richText(5, '<h3 class="text-2xl font-bold text-center mb-8">🎯 COMMENT ÇA FONCTIONNE</h3>'),

            $this->iconBox(6, '1️⃣', 'Vous vous formez', 'Formation gratuite de 7 jours pour maîtriser le système.'),
            $this->iconBox(7, '2️⃣', 'Vous recommandez', 'Partagez simplement les produits que vous utilisez.'),
            $this->iconBox(8, '3️⃣', 'Vous gagnez', 'Commissions sur vos ventes + bonus sur votre équipe.'),

            // Pricing Table - Income Levels
            $this->pricing(9, [
                [
                    'name' => 'Débutant',
                    'price' => '300-800€',
                    'period' => '/mois',
                    'description' => '1-2h/jour',
                    'features' => ['1-5 personnes dans l\'équipe', 'Formation de base', 'Support groupe'],
                    'button_text' => '→',
                    'is_popular' => false,
                ],
                [
                    'name' => 'Confirmé',
                    'price' => '1.500-3.000€',
                    'period' => '/mois',
                    'description' => '2-3h/jour',
                    'features' => ['10-30 personnes', 'Coaching avancé', 'Outils premium'],
                    'button_text' => '→',
                    'is_popular' => true,
                ],
                [
                    'name' => 'Leader',
                    'price' => '5.000€+',
                    'period' => '/mois',
                    'description' => '3-4h/jour',
                    'features' => ['50+ personnes', 'Mentorat VIP', 'Revenus passifs'],
                    'button_text' => '→',
                    'is_popular' => false,
                ],
            ]),

            // What you get
            $this->features(10, '🎁 CE QUE VOUS RECEVEZ EN REJOIGNANT :', [
                ['title' => '🎓 Formation Complète Gratuite', 'text' => '7 jours de formation vidéo pour maîtriser le système', 'icon' => 'heroicon-o-academic-cap'],
                ['title' => '🛠️ Kit de Démarrage', 'text' => 'Scripts, templates, visuels, pages de capture.', 'icon' => 'heroicon-o-wrench-screwdriver'],
                ['title' => '👨‍🏫 Parrain Dédié', 'text' => 'Accompagnement personnalisé par un membre expérimenté.', 'icon' => 'heroicon-o-user-plus'],
                ['title' => '💬 Groupe Privé', 'text' => 'Accès 24/7 à notre communauté active.', 'icon' => 'heroicon-o-user-group'],
            ]),

            // Countdown
            $this->countdown(11, '⏰ L\'OFFRE DE LANCEMENT EXPIRE DANS :', 24),

            // CTA
            $this->ctaButton(12, 'REJOINDRE L\'ÉQUIPE MAINTENANT 🚀', [], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.4rem',
            ]),

            $this->richText(13, $this->guaranteeBadge('14')),

            // FAQ
            $this->faq(14, '❓ QUESTIONS FRÉQUENTES', [
                ['question' => 'Est-ce du MLM pyramidal ?', 'answer' => 'Non. Notre système est 100% légal et basé sur la vente de produits réels. Vous ne gagnez jamais sur le recrutement seul.'],
                ['question' => 'Je n\'ai aucune expérience, est-ce possible ?', 'answer' => 'Absolument ! Notre formation est conçue pour les débutants complets. 80% de nos top performers n\'avaient aucune expérience avant.'],
                ['question' => 'Combien de temps pour voir des résultats ?', 'answer' => 'La plupart font leur première vente dans les 2 premières semaines. Les premiers revenus significatifs arrivent entre 1 et 3 mois.'],
                ['question' => 'Et si ça ne fonctionne pas pour moi ?', 'answer' => 'Vous avez 14 jours pour tester. Si vous n\'êtes pas satisfait, remboursement intégral sans question.'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 4: INSCRIPTION
    // =========================================================================
    private function buildRegistrationPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::PAYMENT,
            'title' => 'Finaliser Inscription',
            'sort_order' => 4,
        ]);

        $this->createBlocks($page, [
            // Progress
            $this->progress(1, 66, 'Étape 2 sur 3 : Inscription'),

            $this->heroTitle(2, '🎯 REJOIGNEZ L\'ÉQUIPE', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Plus que quelques étapes pour commencer votre nouvelle aventure'),

            // Order summary
            $this->richText(4, '
                <div class="max-w-lg mx-auto mb-8">
                    <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden">
                        <div class="bg-green-600 p-4">
                            <h3 class="text-lg font-bold text-center">📦 VOTRE PACK DE DÉMARRAGE</h3>
                        </div>
                        <div class="p-6 space-y-3 text-sm">
                            <div class="flex justify-between"><span>🎓 Formation complète (7 jours)</span><span class="text-gray-400 line-through">147€</span></div>
                            <div class="flex justify-between"><span>🛠️ Kit outils & ressources</span><span class="text-gray-400 line-through">97€</span></div>
                            <div class="flex justify-between"><span>👨‍🏫 Parrain dédié (3 mois)</span><span class="text-gray-400 line-through">297€</span></div>
                            <div class="flex justify-between"><span>💬 Accès groupe privé</span><span class="text-gray-400">Inclus</span></div>
                            <div class="border-t border-white/10 pt-3 mt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>TOTAL</span>
                                    <div>
                                        <span class="text-gray-400 line-through text-sm mr-2">541€</span>
                                        <span class="text-green-400">97€</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            '),

            // Order Bump
            $this->orderBump(
                5,
                'Accès VIP 6 mois',
                'Accès au groupe VIP avec le fondateur + 2 sessions coaching privées. Valeur : 497€',
                '97€'
            ),

            // Registration form
            $this->form(6, 'Vos informations d\'inscription', [
                ['name' => 'full_name', 'label' => 'Nom complet', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'WhatsApp', 'type' => 'tel', 'required' => true],
            ], 'VALIDER MON INSCRIPTION - 97€ 🔐'),

            // Trust badges
            $this->trustBadges(7, [
                ['icon' => '🔒', 'text' => 'Paiement sécurisé'],
                ['icon' => '✅', 'text' => 'Garantie 14 jours'],
                ['icon' => '📧', 'text' => 'Accès immédiat'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 5: ONBOARDING
    // =========================================================================
    private function buildOnboardingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::THANK_YOU,
            'title' => 'Bienvenue dans l\'Équipe',
            'sort_order' => 5,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Inscription terminée ! 🎉'),

            $this->heroTitle(2, '🎉 BIENVENUE DANS L\'ÉQUIPE !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre inscription est confirmée. Votre aventure commence maintenant !'),

            // Success
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">✅</div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">Paiement reçu avec succès !</h3>
                    <p class="text-lg mb-2">Votre parrain va vous contacter dans les prochaines heures.</p>
                </div>
            '),

            // Next steps
            $this->richText(5, $this->stepsHtml([
                '📧 Vérifiez votre email pour recevoir vos accès',
                '💬 Rejoignez le groupe WhatsApp de l\'équipe',
                '👨‍🏫 Votre parrain vous contactera sous 24h',
                '🎓 Commencez le Jour 1 de la formation',
                '🚀 Lancez le Challenge 30 Jours',
            ], '🚀 VOS 5 PROCHAINES ÉTAPES :')),

            // Quick actions
            $this->richText(6, '
                <h3 class="text-2xl font-bold text-center mb-6">⚡ ACTIONS IMMÉDIATES</h3>
                <div class="grid md:grid-cols-2 gap-4 max-w-2xl mx-auto mb-8">
                    <a href="#" class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 rounded-xl text-center hover:scale-105 transition-transform">
                        <span class="text-3xl block mb-2">💬</span>
                        <p class="font-bold">Groupe WhatsApp</p>
                    </a>
                    <a href="#" class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 rounded-xl text-center hover:scale-105 transition-transform">
                        <span class="text-3xl block mb-2">🎓</span>
                        <p class="font-bold">Accéder à la Formation</p>
                    </a>
                </div>
            '),

            $this->whatsappButton(7, 'Contacter Mon Parrain 📱', '+33123456789', 'Salut ! Je viens de rejoindre l\'équipe !'),

            // Social icons
            $this->socialIcons(8, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }
}
