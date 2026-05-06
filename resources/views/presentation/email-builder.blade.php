<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Builder Email — Royal LeadMagnet</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700,800" rel="stylesheet" />
    <style>
        .text-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 50%, var(--color-primary-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-[Outfit] antialiased bg-slate-900 text-slate-100 selection:bg-amber-500 selection:text-white">

    <!-- NAV -->
    <nav class="sticky top-0 z-50 bg-slate-900/90 backdrop-blur border-b border-slate-800 px-6 py-4 flex items-center gap-4">
        <a href="{{ route('presentation.client.fallback') }}" class="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2">
            ← Retour
        </a>
        <span class="text-slate-700">|</span>
        <span class="text-amber-500 font-medium text-sm">Builder Email Visuel</span>
    </nav>

    <!-- HERO -->
    <section class="min-h-[50vh] flex flex-col items-center justify-center relative overflow-hidden p-6 py-20 border-b border-slate-800">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#f59e0b 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="z-10 text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center px-4 py-2 rounded-full border border-amber-500/30 bg-amber-500/10 text-amber-500 mb-2">
                <span class="animate-pulse mr-2">●</span> Fonctionnalité livrée
            </div>
            <h1 class="font-[Playfair-Display] text-4xl md:text-6xl font-bold leading-tight">
                Builder Email <br><span class="text-gradient-gold">Visuel</span>
            </h1>
            <p class="text-xl text-slate-400 leading-relaxed">
                Composez vos emails par blocs, sans toucher au code. Choisissez un template, personnalisez, sauvegardez — le HTML est généré automatiquement.
            </p>
        </div>
    </section>

    <!-- CE QUI A ÉTÉ FAIT -->
    <section class="py-24 bg-slate-800 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                Ce qui a été <span class="text-amber-500">construit</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">6</div>
                    <div class="text-white font-semibold mb-2">Types de blocs</div>
                    <p class="text-slate-400 text-sm">Titre, Texte, Image, Bouton CTA, Séparateur, Espace — tous personnalisables.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">4</div>
                    <div class="text-white font-semibold mb-2">Templates visuels</div>
                    <p class="text-slate-400 text-sm">Welcome, Offer, Follow-up, Relance — démarrez en un clic avec une structure prête.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">Auto</div>
                    <div class="text-white font-semibold mb-2">Export HTML</div>
                    <p class="text-slate-400 text-sm">La sauvegarde génère automatiquement le HTML final compatible email et le stocke dans la séquence.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- COMMENT Y ACCÉDER -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/3">
                    <h3 class="text-amber-500 font-bold tracking-widest uppercase mb-4">Accès</h3>
                    <h2 class="font-[Playfair-Display] text-4xl font-bold mb-6">Comment l'utiliser</h2>
                    <p class="text-slate-400 leading-relaxed">
                        Le builder s'ouvre directement depuis la fiche d'un email dans une séquence. Il fonctionne en plein écran, séparé du panel admin.
                    </p>
                </div>
                <div class="w-full md:w-2/3 space-y-4">
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">1</div>
                        <div>
                            <div class="font-semibold text-white mb-1">Ouvrir une séquence</div>
                            <p class="text-slate-400 text-sm">Admin → Séquences Email → choisir une séquence → onglet <strong class="text-white">Emails</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">2</div>
                        <div>
                            <div class="font-semibold text-white mb-1">Cliquer sur "Ouvrir le Builder"</div>
                            <p class="text-slate-400 text-sm">Le bouton apparaît dans la fiche de chaque email de la séquence. Il ouvre le builder en plein écran.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">3</div>
                        <div>
                            <div class="font-semibold text-white mb-1">Choisir un template ou partir de zéro</div>
                            <p class="text-slate-400 text-sm">4 templates disponibles au démarrage. Choisir en charge automatiquement les blocs. Sinon, <strong class="text-white">Commencer vide</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">4</div>
                        <div>
                            <div class="font-semibold text-white mb-1">Composer et sauvegarder</div>
                            <p class="text-slate-400 text-sm">Ajoutez des blocs, réordonnez avec ↑ ↓, modifiez le contenu dans le panneau de droite. Cliquez <strong class="text-white">Sauvegarder</strong> pour générer le HTML.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6 BLOCS -->
    <section class="py-24 bg-slate-800 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                Les 6 types de blocs
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ([
                    ['H', 'Titre', 'H1 / H2 / H3 au choix. Alignement gauche, centre ou droite. Couleur personnalisable.'],
                    ['T', 'Texte', 'Paragraphe libre. Alignement et couleur configurables.'],
                    ['🖼', 'Image', 'URL de l\'image, texte alternatif, alignement, largeur en % ou px.'],
                    ['→', 'Bouton CTA', 'Texte, URL de destination, couleur du fond, couleur du texte, alignement.'],
                    ['—', 'Séparateur', 'Ligne horizontale décorative. Couleur et marges configurables.'],
                    ['↕', 'Espace', 'Espace vertical vide. Hauteur en px pour aérer la mise en page.'],
                ] as [$icon, $name, $desc])
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-600 text-amber-500 font-bold text-xl flex items-center justify-center mb-4">{{ $icon }}</div>
                    <h3 class="font-bold text-white mb-2">{{ $name }}</h3>
                    <p class="text-slate-400 text-sm">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4 TEMPLATES -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                4 Templates visuels inclus
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ([
                    ['👋', 'Welcome', 'Bienvenue {first_name} !', 'Logo · Titre H1 · Texte d\'accueil · Bouton CTA', 'Premier email d\'onboarding. Ton chaleureux, appel à l\'action central.'],
                    ['🎯', 'Offer', '🎯 Offre exclusive pour {first_name}', 'Titre H1 · Texte bénéfices · Bouton rouge urgent · Texte urgence', 'Email de vente avec sentiment d\'urgence. Idéal pour les séquences de conversion.'],
                    ['💬', 'Follow-up', '{first_name}, on prend de vos nouvelles', 'Titre H2 · Texte personnalisé · Bouton Répondre', 'Relance douce pour les leads inactifs. Ton conversationnel et direct.'],
                    ['⏰', 'Relance', '{first_name}, il reste peu de temps !', 'Titre H1 · Texte urgence · Bouton orange · Bouton "Non merci"', 'Dernier email de séquence. Offre une sortie propre avec le bouton secondaire.'],
                ] as [$icon, $name, $subject, $structure, $usage])
                <div class="p-8 rounded-2xl bg-slate-800/50 border border-slate-700 hover:border-amber-500/50 transition-colors">
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-3xl">{{ $icon }}</span>
                        <div>
                            <h3 class="font-bold text-white text-lg">{{ $name }}</h3>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $subject }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-900/60 rounded-xl p-3 font-mono text-xs text-slate-400 mb-3">{{ $structure }}</div>
                    <p class="text-slate-400 text-sm">{{ $usage }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FOOTER NAV -->
    <section class="py-16 bg-slate-800 text-center flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('presentation.sequences-email') }}"
            class="inline-flex items-center gap-2 border border-slate-600 bg-slate-700/50 text-slate-300 px-6 py-3 rounded-full font-medium hover:bg-slate-700 transition-all text-sm">
            ← Voir les Séquences Email
        </a>
        <a href="{{ route('presentation.client.fallback') }}"
            class="inline-flex items-center gap-2 border border-slate-600 bg-slate-800/50 text-white px-8 py-4 rounded-full font-bold hover:bg-slate-700 transition-all">
            ↩ Retour à la présentation
        </a>
    </section>

</body>
</html>
