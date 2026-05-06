<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Mobile PWA — Royal LeadMagnet</title>
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
        <span class="text-amber-500 font-medium text-sm">App Mobile (PWA)</span>
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
                App Mobile <br><span class="text-gradient-gold">PWA</span>
            </h1>
            <p class="text-xl text-slate-400 leading-relaxed">
                L'espace commercial s'installe sur l'écran d'accueil de votre téléphone comme une vraie application — sans passer par l'App Store. iOS et Android compatibles.
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
                    <div class="text-amber-500 text-3xl font-bold mb-3">📱</div>
                    <div class="text-white font-semibold mb-2">Installable</div>
                    <p class="text-slate-400 text-sm">L'app s'ajoute sur l'écran d'accueil iOS et Android. Elle s'ouvre sans barre d'adresse, en plein écran.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">⚡</div>
                    <div class="text-white font-semibold mb-2">Offline</div>
                    <p class="text-slate-400 text-sm">Les pages déjà visitées restent accessibles sans connexion. Le service worker gère le cache automatiquement.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">🎨</div>
                    <div class="text-white font-semibold mb-2">Branded</div>
                    <p class="text-slate-400 text-sm">Icône Royal LeadMagnet, splash screen aux couleurs de l'app, barre de statut amber. Identité visuelle cohérente.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- INSTALLATION iOS -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center text-3xl mb-4"> </div>
                    <h3 class="text-amber-500 font-bold tracking-widest uppercase mb-2">iOS — iPhone / iPad</h3>
                    <h2 class="font-[Playfair-Display] text-3xl font-bold mb-4">Installer sur Safari</h2>
                    <p class="text-slate-400 text-sm leading-relaxed">Sur iOS, l'installation se fait uniquement via <strong class="text-white">Safari</strong>. Chrome et Firefox iOS ne le supportent pas.</p>
                </div>
                <div class="w-full md:w-2/3 space-y-4">
                    @foreach ([
                        ['1', 'Ouvrir Safari', 'Ouvre l\'adresse de l\'espace commercial dans Safari (pas Chrome).', 'https://royalleadpro.com/commercial'],
                        ['2', 'Appuyer sur Partager', 'En bas de l\'écran, tape l\'icône carrée avec une flèche vers le haut ⬆.', null],
                        ['3', 'Faire défiler et choisir', 'Dans le menu qui s\'ouvre, fais défiler vers le bas jusqu\'à trouver "Sur l\'écran d\'accueil".', null],
                        ['4', 'Confirmer', 'Un nom est pré-rempli "LeadMagnet". Appuie sur Ajouter en haut à droite.', null],
                        ['5', 'Lancer depuis l\'écran d\'accueil', 'L\'icône apparaît. Lance l\'app depuis l\'écran d\'accueil — elle s\'ouvre sans barre Safari, comme une vraie app.', null],
                    ] as [$num, $title, $desc, $code])
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">{{ $num }}</div>
                        <div>
                            <div class="font-semibold text-white mb-1">{{ $title }}</div>
                            <p class="text-slate-400 text-sm">{{ $desc }}</p>
                            @if($code)
                            <code class="mt-2 inline-block bg-slate-900 text-amber-400 text-xs font-mono px-3 py-1 rounded">{{ $code }}</code>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- INSTALLATION ANDROID -->
    <section class="py-24 bg-slate-800 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-700 border border-slate-600 flex items-center justify-center text-3xl mb-4">🤖</div>
                    <h3 class="text-amber-500 font-bold tracking-widest uppercase mb-2">Android — Chrome</h3>
                    <h2 class="font-[Playfair-Display] text-3xl font-bold mb-4">Installer sur Chrome</h2>
                    <p class="text-slate-400 text-sm leading-relaxed">Sur Android, Chrome détecte automatiquement la PWA et propose un <strong class="text-white">bouton d'installation</strong> ou un bandeau en bas.</p>
                </div>
                <div class="w-full md:w-2/3 space-y-4">
                    @foreach ([
                        ['1', 'Ouvrir Chrome', 'Ouvre l\'adresse de l\'espace commercial dans Google Chrome.', 'https://royalleadpro.com/commercial'],
                        ['2', 'Bandeau automatique', 'Chrome affiche un bandeau en bas "Ajouter Royal LeadMagnet à l\'écran d\'accueil". Appuie dessus.', null],
                        ['3', 'Ou via le menu', 'Si le bandeau n\'apparaît pas : menu (3 points) en haut à droite → "Ajouter à l\'écran d\'accueil".', null],
                        ['4', 'Confirmer l\'installation', 'Une fenêtre de confirmation s\'ouvre. Appuie sur "Installer".', null],
                        ['5', 'Lancer l\'app', 'L\'icône LeadMagnet apparaît dans le tiroir d\'applications et sur l\'écran d\'accueil.', null],
                    ] as [$num, $title, $desc, $code])
                    <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-700/50 border border-slate-600">
                        <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">{{ $num }}</div>
                        <div>
                            <div class="font-semibold text-white mb-1">{{ $title }}</div>
                            <p class="text-slate-400 text-sm">{{ $desc }}</p>
                            @if($code)
                            <code class="mt-2 inline-block bg-slate-900 text-amber-400 text-xs font-mono px-3 py-1 rounded">{{ $code }}</code>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CE QUE L'APP OFFRE -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                Ce que l'app <span class="text-amber-500">permet</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ([
                    ['📊', 'Dashboard en temps réel', 'Voir ses KPIs, leads du jour, taux de conversion — depuis le téléphone.'],
                    ['👥', 'Gestion des leads', 'Consulter, filtrer et mettre à jour le statut de ses leads en mobilité.'],
                    ['🔔', 'Alertes instantanées', 'Recevoir les notifications de nouveaux leads directement sur l\'écran d\'accueil.'],
                    ['📡', 'Mode hors-ligne', 'Les données déjà chargées restent consultables sans connexion internet.'],
                    ['🔗', 'Accès rapide', 'Raccourcis directs vers "Mes Leads" et "Alertes" depuis l\'icône de l\'app.'],
                    ['🚫', 'Sans App Store', 'Aucune installation via l\'App Store ou Play Store. Mise à jour automatique à chaque déploiement.'],
                ] as [$icon, $title, $desc])
                <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800/50 border border-slate-700 hover:border-amber-500/30 transition-colors">
                    <span class="text-2xl shrink-0">{{ $icon }}</span>
                    <div>
                        <h3 class="font-semibold text-white mb-1">{{ $title }}</h3>
                        <p class="text-slate-400 text-sm">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FOOTER NAV -->
    <section class="py-16 bg-slate-800 text-center flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('presentation.email-builder.fallback') }}"
            class="inline-flex items-center gap-2 border border-slate-600 bg-slate-700/50 text-slate-300 px-6 py-3 rounded-full font-medium hover:bg-slate-700 transition-all text-sm">
            ← Voir le Builder Email
        </a>
        <a href="{{ route('presentation.client.fallback') }}"
            class="inline-flex items-center gap-2 border border-slate-600 bg-slate-800/50 text-white px-8 py-4 rounded-full font-bold hover:bg-slate-700 transition-all">
            ↩ Retour à la présentation
        </a>
    </section>

</body>
</html>
