<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Formation en Ligne Complète (6 pages)
 * 
 * Version améliorée style Systeme.io avec :
 * - Nouveaux types de blocs (FAQ, Pricing, Progress, Trust Badges, etc.)
 * - Structure Section > Columns pour mise en page
 * - Order Bump sur page checkout
 * - Sticky Bar pour conversion
 * - Popup exit-intent
 */
class OnlineTrainingTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'formation';
    }

    public function getIcon(): string
    {
        return '🎓';
    }

    public function getName(): string
    {
        return 'Formation en Ligne Complète';
    }

    public function getDescription(): string
    {
        return 'Tunnel complet pour formations en ligne avec landing, capture, contenu, upsell et confirmation. Optimisé pour la conversion avec plus de 6 pages professionnelles.';
    }

    public function getTags(): array
    {
        return ['formation', 'en-ligne', 'éducation', 'cours', 'upsell', 'tunnel-complet', 'certification'];
    }

    public function getPrimaryColor(): string
    {
        return '#6366F1';
    }

    public function getSecondaryColor(): string
    {
        return '#4F46E5';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => 'Formation Complète - Maîtrisez [COMPÉTENCE] en 30 jours',
            'meta_description' => 'Formation pratique avec modules progressifs, exercices et certificat. Accès immédiat + Bonus exclusifs.',
        ]);

        $this->buildLandingPage($funnel);
        $this->buildCapturePage($funnel);
        $this->buildTrainingPage($funnel);
        $this->buildUpsellPage($funnel);
        $this->buildCheckoutPage($funnel);
        $this->buildConfirmationPage($funnel);

        return $funnel;
    }

    // =========================================================================
    // PAGE 1: LANDING / ATTRACTION
    // =========================================================================
    private function buildLandingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Découvrez la Formation',
            'sort_order' => 1,
            'meta_title' => 'Formation [COMPÉTENCE] - Devenez Expert en 30 Jours',
            'meta_description' => 'Découvrez notre méthode unique pour maîtriser [COMPÉTENCE]. Formation complète avec exercices pratiques.',
        ]);

        $this->createBlocks($page, [
            // Sticky Bar (conversion boost)
            $this->stickyBar(1, '🔥 Offre limitée : -50% sur la Formation PRO !', 'EN PROFITER →'),

            // Hero Section
            $this->heroTitle(2, 'MAÎTRISEZ [COMPÉTENCE] EN 30 JOURS', [
                'color' => '#FBBF24',
            ]),

            $this->subtitle(3, 'La formation pratique qui transforme les débutants complets en experts reconnus — même sans expérience préalable'),

            // Social Proof - Icon Boxes
            $this->richText(4, '
                <div class="grid grid-cols-3 gap-6 max-w-2xl mx-auto mb-8">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-yellow-400">15,847+</p>
                        <p class="text-sm text-gray-400">Étudiants formés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-400">4.9/5</p>
                        <p class="text-sm text-gray-400">Note moyenne</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-400">98%</p>
                        <p class="text-sm text-gray-400">Taux de réussite</p>
                    </div>
                </div>
            '),

            // Video
            $this->video(5),

            // Icon Boxes - What you learn
            $this->iconBox(6, '📚', 'Module 1 : Les Fondamentaux', 'Comprenez les bases essentielles avec des explications claires et des exemples concrets. Durée : 2h30'),
            $this->iconBox(7, '🔧', 'Module 2 : Pratique Guidée', 'Appliquez immédiatement avec 15 exercices pratiques et études de cas réelles. Durée : 4h'),
            $this->iconBox(8, '🚀', 'Module 3 : Techniques Avancées', 'Découvrez les secrets d\'expert que 95% des professionnels ignorent. Durée : 3h'),
            $this->iconBox(9, '💰', 'Module 4 : Monétisation', 'Apprenez à transformer vos compétences en source de revenus récurrents. Durée : 2h'),

            // Testimonials section
            $this->testimonial(10, 'Marie D.', 'En seulement 3 semaines, j\'ai pu décrocher mon premier client grâce aux compétences acquises. La formation est claire et très pratique !', 'Freelance depuis 2 mois'),
            $this->testimonial(11, 'Thomas L.', 'J\'ai testé beaucoup de formations, celle-ci est de loin la plus complète. Le support est incroyable et les exercices vraiment utiles.', 'Reconversion réussie'),
            $this->testimonial(12, 'Sophie M.', 'Reconversion réussie ! Je suis passée de secrétaire à freelance en 2 mois. La méthode est progressive et accessible à tous.', 'CDI à 38K€'),

            // FAQ Section
            $this->faq(13, '❓ QUESTIONS FRÉQUENTES', [
                ['question' => 'À qui s\'adresse cette formation ?', 'answer' => 'Cette formation est conçue pour les débutants complets comme pour ceux qui ont déjà des bases. Chaque module est progressif et s\'adapte à votre niveau.'],
                ['question' => 'Combien de temps faut-il pour la terminer ?', 'answer' => 'En y consacrant 1h par jour, vous pouvez terminer la formation en 30 jours. Mais vous avez un accès à vie, donc prenez votre temps !'],
                ['question' => 'Y a-t-il un certificat ?', 'answer' => 'Oui ! À la fin du parcours, après avoir validé tous les exercices, vous recevez un certificat professionnel reconnu.'],
                ['question' => 'Puis-je me faire rembourser ?', 'answer' => 'Absolument. Nous offrons une garantie satisfait ou remboursé de 30 jours. Si vous n\'êtes pas satisfait, contactez-nous pour un remboursement intégral.'],
            ]),

            // Urgency Box
            $this->countdown(14, '⚡ PLACES LIMITÉES - Fermeture des inscriptions dans :', 48),

            // CTA Button
            $this->ctaButton(15, 'COMMENCER LA FORMATION GRATUITE ➜', [], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.3rem',
                'padding' => '24px 48px',
            ]),

            // Trust badges (new block type)
            $this->trustBadges(16, [
                ['icon' => '🔒', 'text' => 'Paiement sécurisé SSL'],
                ['icon' => '✅', 'text' => 'Garantie 30 jours'],
                ['icon' => '🎓', 'text' => 'Certificat inclus'],
                ['icon' => '💬', 'text' => 'Support 7j/7'],
            ]),

            // Exit-intent Popup
            $this->popup(17, '🎁 ATTENDEZ !', [
                'body' => '<p class="mb-4">Ne partez pas sans votre <strong>guide gratuit</strong> :</p><p class="text-xl font-bold text-yellow-400 mb-4">"Les 7 Erreurs à Éviter Quand on Débute"</p>',
                'trigger' => 'exit_intent',
                'button_text' => 'RECEVOIR MON GUIDE GRATUIT',
                'show_once' => true,
            ]),

            // Social Icons
            $this->socialIcons(18, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 2: CAPTURE D'EMAIL
    // =========================================================================
    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Inscription Gratuite',
            'sort_order' => 2,
            'meta_title' => 'Inscription Gratuite - Formation [COMPÉTENCE]',
        ]);

        $this->createBlocks($page, [
            $this->heroTitle(1, '🎁 ACCÈS GRATUIT IMMÉDIAT', [
                'color' => '#10B981',
            ]),

            $this->subtitle(2, 'Rejoignez 15,847+ étudiants qui ont déjà transformé leurs compétences'),

            // Progress indicator (new block)
            $this->progress(3, 33, 'Étape 1 sur 3 : Inscription'),

            // Bonus Box with Icon Boxes
            $this->richText(4, '
                <h3 class="text-indigo-400 font-bold text-xl mb-4 text-center">🎁 BONUS EXCLUSIFS INCLUS :</h3>
            '),

            $this->iconBox(5, '📚', 'Guide PDF 50 pages', 'Valeur : 47€ → GRATUIT'),
            $this->iconBox(6, '🎥', '3 vidéos bonus', 'Valeur : 97€ → GRATUIT'),
            $this->iconBox(7, '📋', 'Templates & Checklists', 'Valeur : 37€ → GRATUIT'),
            $this->iconBox(8, '💬', 'Groupe WhatsApp privé', 'Accès exclusif'),

            $this->richText(9, '<p class="text-center mt-4 text-yellow-400 font-bold text-xl">Valeur totale : 181€ → GRATUIT aujourd\'hui !</p>'),

            // Form
            $this->form(10, 'Inscrivez-vous maintenant', [
                ['name' => 'first_name', 'label' => 'Votre prénom', 'type' => 'text', 'required' => true, 'placeholder' => 'Ex: Marie'],
                ['name' => 'email', 'label' => 'Votre meilleur email', 'type' => 'email', 'required' => true, 'placeholder' => 'votre@email.com'],
                ['name' => 'phone', 'label' => 'WhatsApp (optionnel)', 'type' => 'tel', 'required' => false, 'placeholder' => '+225 XX XX XX XX'],
            ], 'ACCÉDER À MA FORMATION GRATUITE 🚀'),

            // Trust items
            $this->trustBadges(11, [
                ['icon' => '✅', 'text' => 'Accès immédiat'],
                ['icon' => '🆓', 'text' => '100% gratuit'],
                ['icon' => '🚫', 'text' => 'Pas de spam'],
                ['icon' => '↩️', 'text' => 'Désinscription facile'],
            ]),

            $this->spacer(12, '30px'),

            // FOMO element
            $this->richText(13, '
                <div class="bg-yellow-900/30 p-4 rounded-lg border border-yellow-600 text-center animate-pulse">
                    <p class="text-yellow-400 font-bold">🔥 127 personnes inscrites dans les dernières 24h</p>
                </div>
            '),
        ]);
    }

    // =========================================================================
    // PAGE 3: ESPACE FORMATION
    // =========================================================================
    private function buildTrainingPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CONTENT,
            'title' => 'Espace Formation',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Welcome message
            $this->richText(1, '
                <div class="bg-gradient-to-r from-green-900/50 to-emerald-900/50 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <h1 class="text-3xl font-bold text-green-400 mb-4">🎉 Bienvenue dans votre Formation !</h1>
                    <p class="text-lg mb-2">Votre parcours d\'apprentissage commence maintenant.</p>
                    <p class="text-gray-400">Suivez les modules dans l\'ordre pour une progression optimale.</p>
                </div>
            '),

            // Progress bar (new block type!)
            $this->progress(2, 0, 'Votre progression : 0/6 modules'),

            // Modules with Icon Boxes
            $this->richText(3, '<h2 class="text-2xl font-bold mb-6">📚 Modules de Formation</h2>'),

            $this->iconBox(4, '🎬', 'Module 1 : Les Fondamentaux', '8 leçons • 2h30 • Quiz inclus — <span class="text-green-400">✓ Débloqué</span>'),
            $this->iconBox(5, '🔧', 'Module 2 : Pratique Guidée', '12 leçons • 4h • 15 exercices — <span class="text-gray-400">🔒 Verrouillé</span>'),
            $this->iconBox(6, '🚀', 'Module 3 : Techniques Avancées', '10 leçons • 3h • Projets — <span class="text-gray-400">🔒 Verrouillé</span>'),
            $this->iconBox(7, '💰', 'Module 4 : Monétisation', '8 leçons • 2h • Business plan — <span class="text-gray-400">🔒 Verrouillé</span>'),

            // Start button
            $this->ctaButton(8, 'COMMENCER LE MODULE 1 ▶️', [], [
                'backgroundColor' => '#6366F1',
            ]),

            $this->spacer(9, '40px'),

            // Upsell promotion
            $this->richText(10, '
                <div class="bg-gradient-to-r from-amber-900/30 to-orange-900/30 p-8 rounded-2xl border border-amber-500/30 text-center">
                    <h3 class="text-amber-400 font-bold text-2xl mb-4">🚀 Envie d\'aller ENCORE plus loin ?</h3>
                    <p class="text-lg mb-4">Découvrez notre <strong>Formation PRO</strong> avec :</p>
                </div>
            '),

            $this->iconBox(11, '👨‍🏫', 'Coaching individuel', '12 séances avec un expert'),
            $this->iconBox(12, '📜', 'Certificat officiel', 'Reconnu par l\'industrie'),
            $this->iconBox(13, '💼', 'Stage garanti', 'Placement professionnel'),

            $this->countdown(14, '🔥 Offre spéciale étudiants : -50% — Expire dans :', 24),

            $this->ctaButton(15, 'DÉCOUVRIR LA FORMATION PRO 🎯', [], [
                'backgroundColor' => '#F59E0B',
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 4: UPSELL - FORMATION PRO
    // =========================================================================
    private function buildUpsellPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::SALES,
            'title' => 'Formation PRO',
            'sort_order' => 4,
        ]);

        $this->createBlocks($page, [
            // Sticky bar for urgency
            $this->stickyBar(1, '⏰ Offre -50% expire bientôt !', 'PROFITER DE L\'OFFRE'),

            $this->heroTitle(2, '🏆 PASSEZ AU NIVEAU SUPÉRIEUR', [
                'color' => '#F59E0B',
            ]),

            $this->subtitle(3, 'La Formation PRO : Coaching Personnel + Certificat + Garantie Emploi'),

            // Countdown
            $this->countdown(4, '⚡ OFFRE SPÉCIALE ÉTUDIANT - Expire dans :', 24),

            // Video
            $this->video(5),

            // Features with Icon Boxes
            $this->richText(6, '<h3 class="text-2xl font-bold text-center mb-6">🎁 TOUT CE QUE VOUS OBTENEZ :</h3>'),

            $this->iconBox(7, '👨‍🏫', '12 Séances de Coaching', '1h par semaine pendant 3 mois avec un expert certifié'),
            $this->iconBox(8, '📜', 'Certificat Professionnel', 'Reconnu par l\'industrie à ajouter sur votre CV et LinkedIn'),
            $this->iconBox(9, '💼', 'Garantie Stage/Emploi', 'Mise en relation avec nos entreprises partenaires'),
            $this->iconBox(10, '📚', 'Ressources Premium', '50+ templates, 30+ outils pro, bibliothèque exclusive'),
            $this->iconBox(11, '💬', 'Communauté VIP', 'Accès au groupe privé des alumni PRO'),
            $this->iconBox(12, '🔄', 'Mises à jour à vie', 'Tous les nouveaux contenus inclus sans supplément'),

            // Comparison
            $this->richText(13, $this->comparisonHtml(
                [
                    'Formation basique uniquement',
                    'Pas de suivi personnalisé',
                    'Pas de certificat reconnu',
                    'Réseau limité',
                    'Placement professionnel difficile',
                ],
                [
                    'Formation complète + modules exclusifs',
                    'Coaching hebdomadaire personnalisé',
                    'Certificat professionnel reconnu',
                    'Communauté d\'experts et alumni',
                    'Garantie stage/emploi en 6 mois',
                ]
            )),

            // Testimonials PRO
            $this->testimonial(14, 'Julien R.', 'Le coaching a fait toute la différence. En 2 mois j\'avais un CDI à 42K€. L\'investissement est amorti en 1 mois de salaire.', 'CDI décroché en 2 mois'),
            $this->testimonial(15, 'Amandine K.', 'Grâce au certificat et au réseau, j\'ai pu me lancer en freelance. Je facture maintenant 4K€/mois après 4 mois.', 'Freelance à 4K€/mois'),

            // PRICING TABLE (nouveau bloc !)
            $this->pricing(16, [
                [
                    'name' => 'Formation Gratuite',
                    'price' => '0€',
                    'period' => '',
                    'description' => 'Pour découvrir',
                    'features' => ['Accès aux 2 premiers modules', 'Quiz de base', 'Support email'],
                    'button_text' => 'Continuer Gratuit',
                    'is_popular' => false,
                ],
                [
                    'name' => 'Formation PRO',
                    'price' => '997€',
                    'period' => 'ou 3x 332€',
                    'old_price' => '1.997€',
                    'description' => 'Le plus populaire',
                    'features' => [
                        '✅ Tous les modules (6+)',
                        '✅ 12 séances coaching',
                        '✅ Certificat professionnel',
                        '✅ Garantie emploi 6 mois',
                        '✅ Communauté VIP',
                        '✅ Mises à jour à vie',
                    ],
                    'button_text' => 'CHOISIR PRO 🚀',
                    'is_popular' => true,
                ],
            ]),

            // CTA
            $this->ctaButton(17, 'OUI, JE PASSE À LA FORMATION PRO ! 🚀', [
                'action_type' => 'page',
                'full_width' => true,
            ], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.4rem',
                'padding' => '24px 48px',
            ]),

            // Trust badges
            $this->trustBadges(18, [
                ['icon' => '✅', 'text' => 'Satisfait ou remboursé 30j'],
                ['icon' => '🔒', 'text' => 'Paiement 100% sécurisé'],
                ['icon' => '💳', 'text' => 'Paiement en 3x sans frais'],
            ]),

            $this->spacer(19, '20px'),

            // FAQ
            $this->faq(20, '❓ QUESTIONS SUR LA FORMATION PRO', [
                ['question' => 'Puis-je payer en plusieurs fois ?', 'answer' => 'Oui ! Nous proposons un paiement en 3 fois sans frais : 332€ par mois pendant 3 mois.'],
                ['question' => 'Comment fonctionne la garantie emploi ?', 'answer' => 'Nous vous mettons en relation avec nos entreprises partenaires. Si vous n\'avez pas d\'opportunité en 6 mois après la certification, nous vous remboursons.'],
                ['question' => 'Combien de temps dure le coaching ?', 'answer' => '12 séances d\'1 heure, soit 1 séance par semaine pendant 3 mois. Les horaires sont flexibles selon vos disponibilités.'],
            ]),

            // No thanks link
            $this->richText(21, '
                <p class="text-center text-sm text-gray-500 mt-8">
                    <a href="#" class="underline hover:text-gray-400">Non merci, je préfère continuer avec la version gratuite →</a>
                </p>
            '),
        ]);
    }

    // =========================================================================
    // PAGE 5: CHECKOUT
    // =========================================================================
    private function buildCheckoutPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::PAYMENT,
            'title' => 'Finaliser l\'inscription',
            'sort_order' => 5,
        ]);

        $this->createBlocks($page, [
            // Progress (step 2/3)
            $this->progress(1, 66, 'Étape 2 sur 3 : Paiement'),

            $this->heroTitle(2, '🔐 FINALISEZ VOTRE INSCRIPTION', [
                'color' => '#10B981',
                'fontSize' => '2.5rem',
            ]),

            // Order summary
            $this->richText(3, '
                <div class="max-w-lg mx-auto">
                    <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden mb-8">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4">
                            <h3 class="text-lg font-bold text-center">📋 RÉCAPITULATIF DE COMMANDE</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-center py-3 border-b border-white/10">
                                <span>Formation PRO Complète</span>
                                <span class="line-through text-gray-500">1.997€</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-white/10">
                                <span class="text-green-400">Réduction Étudiant (-50%)</span>
                                <span class="text-green-400">-1.000€</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-white/10">
                                <span>12 séances de coaching</span>
                                <span class="text-gray-400">Inclus</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-white/10">
                                <span>Certificat professionnel</span>
                                <span class="text-gray-400">Inclus</span>
                            </div>
                            <div class="flex justify-between items-center py-4 text-xl font-bold">
                                <span>TOTAL</span>
                                <span class="text-green-400">997€</span>
                            </div>
                            <p class="text-center text-sm text-gray-400">ou 3x 332€ sans frais</p>
                        </div>
                    </div>
                </div>
            '),

            // ORDER BUMP (nouveau bloc !)
            $this->orderBump(
                4,
                '🎁 OFFRE SPÉCIALE : Pack Templates Premium',
                'Ajoutez 50 templates professionnels prêts à l\'emploi pour gagner du temps immédiatement. Valeur : 197€',
                '47€'
            ),

            // Payment form
            $this->form(5, 'Informations de paiement', [
                ['name' => 'full_name', 'label' => 'Nom complet', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'Téléphone', 'type' => 'tel', 'required' => true],
                ['name' => 'payment_method', 'label' => 'Mode de paiement', 'type' => 'select', 'required' => true, 'options' => ['Paiement unique (997€)', 'Paiement en 3x (332€/mois)']],
            ], 'VALIDER MON INSCRIPTION SÉCURISÉE 🔒', [
                'maxWidth' => '500px',
            ]),

            // Trust badges (new block type)
            $this->trustBadges(6, [
                ['icon' => '🔒', 'text' => 'SSL 256-bit'],
                ['icon' => '💳', 'text' => 'Stripe Secure'],
                ['icon' => '✅', 'text' => 'Garantie 30j'],
                ['icon' => '🔄', 'text' => '3x sans frais'],
            ]),

            // Guarantee
            $this->richText(7, $this->guaranteeBadge('30')),
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
            // Progress complete!
            $this->progress(1, 100, 'Inscription terminée ! 🎉'),

            $this->heroTitle(2, '🎉 FÉLICITATIONS !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre inscription à la Formation PRO est confirmée'),

            // Success box
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">Paiement reçu avec succès !</h3>
                    <p class="text-lg mb-2">Vérifiez votre email pour les instructions d\'accès.</p>
                    <p class="text-sm text-gray-400">Un email de confirmation a été envoyé à votre adresse.</p>
                </div>
            '),

            // Next steps
            $this->richText(5, $this->stepsHtml([
                '✅ Email de confirmation envoyé avec vos accès',
                '📚 Accès complet à tous les modules débloqué',
                '👨‍🏫 Votre coach vous contactera sous 24h',
                '📅 Première séance programmée cette semaine',
                '💬 Invitation au groupe WhatsApp VIP envoyée',
                '📜 Certificat remis à la fin du parcours',
            ], '🚀 VOS PROCHAINES ÉTAPES :')),

            // Access button
            $this->ctaButton(6, 'ACCÉDER À MON ESPACE FORMATION 🎓', [
                'action_type' => 'url',
                'url' => '#formation',
            ], [
                'backgroundColor' => '#6366F1',
            ]),

            $this->spacer(7, '30px'),

            // Support
            $this->richText(8, '
                <div class="text-center">
                    <h3 class="font-bold text-xl mb-4">🤝 Besoin d\'aide ?</h3>
                    <p class="text-gray-400 mb-6">Notre équipe est disponible pour vous accompagner</p>
                </div>
            '),

            $this->whatsappButton(9, 'Contacter le Support 💬', '+33123456789', 'Bonjour ! Je viens de m\'inscrire à la Formation PRO et j\'ai une question.'),

            $this->spacer(10, '30px'),

            // Social Icons (new block type)
            $this->richText(11, '<h4 class="font-bold mb-4 text-center">📣 Suivez-nous sur les réseaux !</h4>'),
            $this->socialIcons(12, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
                ['platform' => 'linkedin', 'url' => '#'],
            ]),
        ]);
    }
}
