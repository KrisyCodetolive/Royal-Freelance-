<?php

namespace App\Services\Template;

use App\Enums\BlockType;
use App\Enums\PageType;
use App\Models\Funnel;

/**
 * Template: Livre & Produit Numérique (5 pages)
 * 
 * Version améliorée style Systeme.io avec :
 * - FAQ accordéon
 * - Trust badges
 * - Order bump
 * - Exit popup
 * - Pricing table
 * - Progress bars
 * - Icon boxes
 */
class DigitalBookTemplate extends AbstractFunnelTemplate
{
    public function getCategory(): string
    {
        return 'digital_product';
    }

    public function getIcon(): string
    {
        return '📚';
    }

    public function getName(): string
    {
        return 'Livre & Produit Numérique';
    }

    public function getDescription(): string
    {
        return 'Tunnel optimisé pour la vente de livres numériques : présentation du livre, capture d\'emails avec aperçu gratuit, page de vente persuasive et téléchargement sécurisé.';
    }

    public function getTags(): array
    {
        return ['livre', 'ebook', 'produit-numérique', 'vente', 'téléchargement', 'auteur', 'infoproduit'];
    }

    public function getPrimaryColor(): string
    {
        return '#8B5CF6';
    }

    public function getSecondaryColor(): string
    {
        return '#7C3AED';
    }

    public function build(): Funnel
    {
        $funnel = $this->createFunnel([
            'meta_title' => '[TITRE DU LIVRE] - Le Guide Complet par [AUTEUR]',
            'meta_description' => 'Découvrez les secrets de [SUJET] dans ce guide pratique de 256 pages. Téléchargement immédiat après achat. Bonus exclusifs inclus.',
        ]);

        $this->buildPresentationPage($funnel);
        $this->buildCapturePage($funnel);
        $this->buildSalesPage($funnel);
        $this->buildCheckoutPage($funnel);
        $this->buildDownloadPage($funnel);

        return $funnel;
    }

    // =========================================================================
    // PAGE 1: PRÉSENTATION DU LIVRE
    // =========================================================================
    private function buildPresentationPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::LANDING,
            'title' => 'Découvrez le Livre',
            'sort_order' => 1,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '📖 NOUVEAU : Obtenez les 3 premiers chapitres GRATUITS !', 'TÉLÉCHARGER →'),

            // Badge
            $this->richText(2, '
                <div class="text-center mb-4">
                    <span class="bg-yellow-500/20 text-yellow-400 px-4 py-2 rounded-full text-sm font-bold">
                        📖 NOUVEAU LIVRE DISPONIBLE
                    </span>
                </div>
            '),

            $this->heroTitle(3, '"[TITRE DU LIVRE]"', [
                'color' => '#8B5CF6',
            ]),

            $this->subtitle(4, 'Le guide complet pour [RÉSOUDRE PROBLÈME/ATTEINDRE OBJECTIF] — même si vous partez de zéro'),

            // Book mockup + Author
            $this->richText(5, '
                <div class="flex flex-col md:flex-row items-center justify-center gap-12 mb-12">
                    <div class="relative">
                        <img src="https://picsum.photos/350/500" alt="Couverture du livre" class="rounded-lg shadow-2xl transform hover:scale-105 transition-transform" style="box-shadow: 0 25px 50px -12px rgba(139, 92, 246, 0.5);">
                        <div class="absolute -top-4 -right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold text-sm transform rotate-12">
                            BEST-SELLER
                        </div>
                    </div>
                    <div class="text-center md:text-left max-w-md">
                        <p class="text-purple-400 font-bold mb-2">Écrit par</p>
                        <h3 class="text-2xl font-bold mb-2">[NOM DE L\'AUTEUR]</h3>
                        <p class="text-gray-400 mb-4">Expert reconnu • 10+ ans d\'expérience • Formateur certifié</p>
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-4">
                            <span class="text-yellow-400">⭐⭐⭐⭐⭐</span>
                            <span class="text-sm text-gray-400">4.9/5 (2,847 avis)</span>
                        </div>
                    </div>
                </div>
            '),

            // Icon Boxes - Key benefits
            $this->richText(6, '<h3 class="text-2xl font-bold text-center mb-6">📖 DANS CE LIVRE, VOUS DÉCOUVRIREZ :</h3>'),

            $this->iconBox(7, '✅', 'La méthode en 7 étapes', 'Pour [obtenir résultat] sans [douleur habituelle]'),
            $this->iconBox(8, '✅', 'Les 5 erreurs fatales', 'Que 90% des débutants font (et comment les éviter)'),
            $this->iconBox(9, '✅', '15 études de cas réelles', 'Analysées en détail avec les leçons à retenir'),
            $this->iconBox(10, '✅', 'Le plan d\'action de 30 jours', 'Étape par étape pour des résultats concrets'),
            $this->iconBox(11, '✅', 'Les scripts et templates', 'Prêts à l\'emploi que vous pouvez copier'),
            $this->iconBox(12, '✅', 'Les ressources secrètes', 'Outils et sources que l\'auteur utilise personnellement'),

            // Table of contents with Features
            $this->features(13, '📑 SOMMAIRE COMPLET :', [
                ['title' => 'Introduction : Pourquoi ce livre changera tout', 'text' => 'Comprenez la vision et le parcours qui vous attend', 'icon' => 'heroicon-o-book-open'],
                ['title' => 'Partie 1 : Les Fondations (Chapitres 1-4)', 'text' => 'Maîtrisez les bases essentielles souvent négligées', 'icon' => 'heroicon-o-cube'],
                ['title' => 'Partie 2 : La Méthode (Chapitres 5-9)', 'text' => 'Le système complet étape par étape', 'icon' => 'heroicon-o-cog-6-tooth'],
                ['title' => 'Partie 3 : Passage à l\'Action (Chapitres 10-12)', 'text' => 'Implémentez et obtenez des résultats', 'icon' => 'heroicon-o-rocket-launch'],
                ['title' => 'Bonus : Ressources & Templates', 'text' => '25+ outils, scripts et checklists téléchargeables', 'icon' => 'heroicon-o-gift'],
            ]),

            // Testimonials
            $this->testimonial(14, 'François D.', 'Ce livre a littéralement transformé ma façon de voir les choses. J\'ai appliqué les conseils du chapitre 7 et obtenu des résultats en 2 semaines !', 'Acheteur vérifié'),
            $this->testimonial(15, 'Caroline M.', 'Enfin un livre qui va droit au but ! Pas de blabla, juste des méthodes concrètes. Je l\'ai lu 3 fois et j\'y reviens régulièrement.', 'Acheteur vérifié'),
            $this->testimonial(16, 'Marc T.', 'Les templates inclus valent le prix du livre à eux seuls. J\'ai économisé des heures de travail grâce à eux. Investissement rentabilisé !', 'Acheteur vérifié'),

            // CTA
            $this->ctaButton(17, 'LIRE UN APERÇU GRATUIT 📖', [], [
                'backgroundColor' => '#8B5CF6',
                'fontSize' => '1.3rem',
            ]),

            $this->richText(18, '
                <p class="text-center text-gray-400 mt-4">
                    Recevez les 3 premiers chapitres gratuitement par email
                </p>
            '),

            // Trust badges
            $this->trustBadges(19, [
                ['icon' => '📄', 'text' => '256 pages'],
                ['icon' => '📱', 'text' => 'PDF, EPUB, MOBI'],
                ['icon' => '⚡', 'text' => 'Téléchargement immédiat'],
                ['icon' => '✅', 'text' => 'Garantie 30 jours'],
            ]),

            // Exit popup
            $this->popup(20, '🎁 Avant de partir...', [
                'body' => '<p class="mb-4">Téléchargez gratuitement les <strong>3 premiers chapitres</strong> :</p><p class="text-xl font-bold text-purple-400 mb-4">52 pages de contenu exclusif !</p>',
                'trigger' => 'exit_intent',
                'button_text' => 'RECEVOIR MON APERÇU GRATUIT',
                'show_once' => true,
            ]),

            // Social icons
            $this->socialIcons(21, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 2: CAPTURE (APERÇU GRATUIT)
    // =========================================================================
    private function buildCapturePage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::CAPTURE,
            'title' => 'Aperçu Gratuit',
            'sort_order' => 2,
        ]);

        $this->createBlocks($page, [
            // Progress bar
            $this->progress(1, 33, 'Étape 1 sur 3 : Inscription'),

            $this->heroTitle(2, '🎁 APERÇU GRATUIT', [
                'color' => '#F59E0B',
            ]),

            $this->subtitle(3, 'Lisez les 3 premiers chapitres gratuitement avant de décider'),

            // What's included with Icon Boxes
            $this->richText(4, '<h3 class="text-purple-400 font-bold text-xl mb-4 text-center">📚 CE QUE CONTIENT L\'APERÇU :</h3>'),

            $this->iconBox(5, '1️⃣', 'Introduction complète (12 pages)', 'La vision et le cadre de réflexion'),
            $this->iconBox(6, '2️⃣', 'Chapitre 1 : Les erreurs à éviter (18 pages)', 'Les 5 pièges qui font échouer 90% des gens'),
            $this->iconBox(7, '3️⃣', 'Chapitre 2 : La méthode pas-à-pas (22 pages)', 'Les premières étapes du système complet'),
            $this->iconBox(8, '🎁', 'BONUS : Checklist de démarrage', 'Votre feuille de route personnelle (PDF)'),

            $this->richText(9, '<p class="text-center mt-4 text-lg"><strong>52 pages de contenu</strong> 100% gratuit</p>'),

            // Form
            $this->form(10, 'Recevez votre aperçu par email', [
                ['name' => 'first_name', 'label' => 'Votre prénom', 'type' => 'text', 'required' => true, 'placeholder' => 'Marie'],
                ['name' => 'email', 'label' => 'Votre meilleur email', 'type' => 'email', 'required' => true, 'placeholder' => 'marie@example.com'],
            ], 'TÉLÉCHARGER L\'APERÇU GRATUIT 📥'),

            // Trust badges
            $this->trustBadges(11, [
                ['icon' => '📧', 'text' => 'Pas de spam'],
                ['icon' => '🔒', 'text' => 'Données protégées'],
                ['icon' => '❌', 'text' => 'Désabonnement 1 clic'],
            ]),

            // Social proof
            $this->richText(12, '
                <div class="text-center bg-white/5 p-6 rounded-xl mt-8">
                    <p class="text-2xl font-bold text-purple-400 mb-2">8,432+</p>
                    <p class="text-gray-400">personnes ont déjà téléchargé l\'aperçu</p>
                </div>
            '),
        ]);
    }

    // =========================================================================
    // PAGE 3: PAGE DE VENTE
    // =========================================================================
    private function buildSalesPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::SALES,
            'title' => 'Acheter le Livre',
            'sort_order' => 3,
        ]);

        $this->createBlocks($page, [
            // Sticky bar
            $this->stickyBar(1, '🔥 OFFRE LIMITÉE : -40% sur le pack complet !', 'J\'EN PROFITE →'),

            // Hook
            $this->richText(2, '
                <div class="text-center mb-6">
                    <span class="bg-green-500/20 text-green-400 px-4 py-2 rounded-full text-sm font-bold">
                        ✅ Vous avez aimé l\'aperçu ?
                    </span>
                </div>
            '),

            $this->heroTitle(3, '📚 OBTENEZ LE LIVRE COMPLET', [
                'color' => '#8B5CF6',
            ]),

            $this->subtitle(4, 'Accédez immédiatement aux 256 pages + tous les bonus exclusifs'),

            // Countdown
            $this->countdown(5, '🔥 OFFRE SPÉCIALE LANCEMENT - Expire dans :', 48),

            // Book + What's included
            $this->richText(6, '<h3 class="text-2xl font-bold mb-6 text-center">📦 LE PACK COMPLET INCLUT :</h3>'),

            $this->iconBox(7, '📖', 'Le livre complet (256 pages)', '12 chapitres approfondis avec exercices — Valeur : 47€'),
            $this->iconBox(8, '📱', '3 formats inclus', 'PDF, EPUB et MOBI pour tous appareils — Inclus'),
            $this->iconBox(9, '🎁', 'BONUS #1 : Pack Templates', '25 templates et checklists prêts à l\'emploi — Valeur : 97€'),
            $this->iconBox(10, '🎁', 'BONUS #2 : Vidéos Explicatives', '3h de vidéo pour approfondir les concepts — Valeur : 147€'),
            $this->iconBox(11, '🎁', 'BONUS #3 : Accès Communauté', 'Groupe privé avec l\'auteur et les lecteurs — Valeur : 97€'),
            $this->iconBox(12, '🎁', 'BONUS #4 : Mises à jour', 'Nouvelles éditions incluses à vie — Inestimable'),

            // Pricing Table
            $this->pricing(13, [
                [
                    'name' => 'Livre Seul',
                    'price' => '27€',
                    'period' => '',
                    'description' => 'L\'essentiel',
                    'features' => ['Livre complet (256 pages)', '3 formats (PDF, EPUB, MOBI)', 'Mises à jour incluses'],
                    'button_text' => 'Choisir',
                    'is_popular' => false,
                ],
                [
                    'name' => 'Pack Complet',
                    'price' => '27€',
                    'period' => 'au lieu de 388€',
                    'old_price' => '388€',
                    'description' => 'Meilleure offre !',
                    'features' => [
                        '✅ Livre complet (256 pages)',
                        '✅ 3 formats inclus',
                        '✅ Pack 25 Templates',
                        '✅ 3h de Vidéos',
                        '✅ Accès Communauté',
                        '✅ Mises à jour à vie',
                    ],
                    'button_text' => 'CHOISIR LE PACK 🎁',
                    'is_popular' => true,
                ],
            ]),

            // CTA
            $this->ctaButton(14, 'ACHETER MAINTENANT - 27€ 💳', [
                'action_type' => 'page',
                'full_width' => true,
            ], [
                'backgroundColor' => '#10B981',
                'fontSize' => '1.4rem',
                'padding' => '24px',
            ]),

            // Trust badges
            $this->trustBadges(15, [
                ['icon' => '⚡', 'text' => 'Téléchargement immédiat'],
                ['icon' => '✅', 'text' => 'Garantie 30 jours'],
                ['icon' => '🔒', 'text' => 'Paiement sécurisé'],
            ]),

            $this->spacer(16, '30px'),

            // Guarantee box
            $this->richText(17, '
                <div class="bg-blue-900/30 p-8 rounded-2xl border border-blue-500/30 text-center mb-8">
                    <div class="text-5xl mb-4">🛡️</div>
                    <h3 class="text-2xl font-bold text-blue-400 mb-4">GARANTIE 100% SATISFAIT</h3>
                    <p class="max-w-lg mx-auto">
                        Si dans les 30 jours vous n\'êtes pas entièrement satisfait du livre, 
                        envoyez-moi simplement un email et je vous rembourse intégralement. 
                        Sans question. Vous gardez même les bonus.
                    </p>
                    <p class="mt-4 text-sm text-gray-400">— [NOM AUTEUR]</p>
                </div>
            '),

            // FAQ (new block type!)
            $this->faq(18, '❓ QUESTIONS FRÉQUENTES', [
                ['question' => 'Dans quel format est le livre ?', 'answer' => 'Le livre est disponible en 3 formats : PDF (pour ordinateur), EPUB (pour tablettes et liseuses), et MOBI (pour Kindle). Vous recevez les 3 formats immédiatement après l\'achat.'],
                ['question' => 'Comment accéder au livre après l\'achat ?', 'answer' => 'Immédiatement après le paiement, vous recevez un email avec vos liens de téléchargement. Vous pouvez aussi accéder à votre espace membre à tout moment.'],
                ['question' => 'Ce livre convient-il aux débutants ?', 'answer' => 'Absolument ! Le livre a été conçu pour être accessible même si vous partez de zéro. Les concepts sont expliqués progressivement avec des exemples concrets.'],
                ['question' => 'Comment fonctionne la garantie ?', 'answer' => 'Simple : si vous n\'êtes pas satisfait dans les 30 jours, envoyez un email et vous êtes remboursé. Pas de questions, pas de complications.'],
            ]),

            // Final CTA
            $this->ctaButton(19, 'OUI, JE VEUX LE LIVRE COMPLET ! 📚', [
                'action_type' => 'page',
            ], [
                'backgroundColor' => '#8B5CF6',
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 4: CHECKOUT
    // =========================================================================
    private function buildCheckoutPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::PAYMENT,
            'title' => 'Finaliser l\'achat',
            'sort_order' => 4,
        ]);

        $this->createBlocks($page, [
            // Progress
            $this->progress(1, 66, 'Étape 2 sur 3 : Paiement'),

            $this->heroTitle(2, '🔐 COMMANDE SÉCURISÉE', [
                'color' => '#10B981',
                'fontSize' => '2.5rem',
            ]),

            // Order summary
            $this->richText(3, '
                <div class="max-w-lg mx-auto">
                    <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden mb-8">
                        <div class="bg-purple-600 p-4">
                            <h3 class="text-lg font-bold text-center">📦 VOTRE COMMANDE</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                                <img src="https://picsum.photos/80/100" alt="Livre" class="rounded-lg">
                                <div class="flex-1">
                                    <p class="font-bold">[Titre du Livre]</p>
                                    <p class="text-sm text-gray-400">Livre numérique + 4 bonus</p>
                                </div>
                                <p class="font-bold text-green-400">27€</p>
                            </div>
                            <div class="py-4 space-y-2 text-sm text-gray-400">
                                <div class="flex justify-between"><span>📖 Livre complet (256 pages)</span><span>✅</span></div>
                                <div class="flex justify-between"><span>📱 3 formats (PDF, EPUB, MOBI)</span><span>✅</span></div>
                                <div class="flex justify-between"><span>🎁 Pack Templates (25 fichiers)</span><span>✅</span></div>
                                <div class="flex justify-between"><span>🎥 Vidéos explicatives (3h)</span><span>✅</span></div>
                                <div class="flex justify-between"><span>💬 Accès communauté</span><span>✅</span></div>
                            </div>
                            <div class="pt-4 border-t border-white/10">
                                <div class="flex justify-between text-xl font-bold">
                                    <span>TOTAL</span>
                                    <span class="text-green-400">27€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            '),

            // ORDER BUMP (new block type!)
            $this->orderBump(
                4,
                '🎧 AJOUTEZ : Version Audio du Livre',
                'Écoutez le livre en version audio (3h30) narré par l\'auteur. Parfait pour les trajets ! Valeur : 47€',
                '17€'
            ),

            // Payment form
            $this->form(5, 'Vos informations', [
                ['name' => 'email', 'label' => 'Email (pour recevoir le livre)', 'type' => 'email', 'required' => true],
                ['name' => 'full_name', 'label' => 'Nom complet', 'type' => 'text', 'required' => true],
            ], 'PAYER 27€ ET TÉLÉCHARGER 🔐', [
                'maxWidth' => '500px',
            ]),

            // Trust badges
            $this->trustBadges(6, [
                ['icon' => '🔒', 'text' => 'SSL Sécurisé'],
                ['icon' => '💳', 'text' => 'Stripe/PayPal'],
                ['icon' => '✅', 'text' => 'Garantie 30j'],
            ]),
        ]);
    }

    // =========================================================================
    // PAGE 5: TÉLÉCHARGEMENT / MERCI
    // =========================================================================
    private function buildDownloadPage(Funnel $funnel): void
    {
        $page = $this->createPage($funnel, [
            'type' => PageType::THANK_YOU,
            'title' => 'Téléchargement',
            'sort_order' => 5,
        ]);

        $this->createBlocks($page, [
            // Progress complete
            $this->progress(1, 100, 'Commande terminée ! 🎉'),

            $this->heroTitle(2, '🎉 MERCI POUR VOTRE ACHAT !', [
                'color' => '#10B981',
            ]),

            $this->subtitle(3, 'Votre livre et tous vos bonus sont prêts'),

            // Success message
            $this->richText(4, '
                <div class="bg-gradient-to-r from-green-900/40 to-emerald-900/40 p-8 rounded-2xl border border-green-500/30 text-center mb-8">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">Paiement confirmé !</h3>
                    <p class="mb-2">Un email de confirmation a été envoyé à votre adresse.</p>
                    <p class="text-sm text-gray-400">Vérifiez aussi vos spams si vous ne le trouvez pas.</p>
                </div>
            '),

            // Download Icon Boxes
            $this->richText(5, '<h3 class="text-2xl font-bold text-center mb-6">📥 VOS TÉLÉCHARGEMENTS</h3>'),

            $this->iconBox(6, '📄', 'Format PDF', 'Pour ordinateur — <a href="#" class="text-purple-400">Télécharger →</a>'),
            $this->iconBox(7, '📱', 'Format EPUB', 'Pour tablettes — <a href="#" class="text-purple-400">Télécharger →</a>'),
            $this->iconBox(8, '📚', 'Format MOBI', 'Pour Kindle — <a href="#" class="text-purple-400">Télécharger →</a>'),

            // Bonus downloads
            $this->richText(9, '<h3 class="text-2xl font-bold text-center mb-6 mt-8">🎁 VOS BONUS</h3>'),

            $this->iconBox(10, '📋', 'Pack Templates (25 fichiers)', 'ZIP • 15 MB — <a href="#" class="text-yellow-400">Télécharger →</a>'),
            $this->iconBox(11, '🎥', 'Vidéos Explicatives (3h)', 'Accès streaming — <a href="#" class="text-yellow-400">Accéder →</a>'),
            $this->iconBox(12, '💬', 'Communauté Privée', 'Groupe Facebook — <a href="#" class="text-yellow-400">Rejoindre →</a>'),

            // Reading tips
            $this->richText(13, $this->stepsHtml([
                'Lisez d\'abord l\'introduction pour comprendre la structure',
                'Faites les exercices à la fin de chaque chapitre',
                'Utilisez les templates pour passer à l\'action',
                'Rejoignez la communauté pour poser vos questions',
            ], '💡 CONSEILS DE LECTURE :')),

            // Support
            $this->richText(14, '
                <div class="text-center mt-8">
                    <h3 class="font-bold text-xl mb-4">💬 Une question ?</h3>
                    <p class="text-gray-400 mb-6">Je suis là pour vous aider</p>
                </div>
            '),

            $this->whatsappButton(15, 'Contacter l\'Auteur 📞', '+33123456789', 'Bonjour ! Je viens d\'acheter votre livre et j\'ai une question.'),

            // Social icons
            $this->socialIcons(16, [
                ['platform' => 'facebook', 'url' => '#'],
                ['platform' => 'instagram', 'url' => '#'],
                ['platform' => 'youtube', 'url' => '#'],
            ]),
        ]);
    }
}
