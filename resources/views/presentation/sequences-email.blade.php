<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Séquences Email — Royal LeadMagnet</title>
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
        <span class="text-amber-500 font-medium text-sm">Séquences Email Automatiques</span>
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
                Séquences Email <br><span class="text-gradient-gold">Automatiques</span>
            </h1>
            <p class="text-xl text-slate-400 leading-relaxed">
                Envoyez les bons emails, au bon moment, sans action manuelle. Le système tourne en arrière-plan 24h/24.
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
                    <div class="text-amber-500 text-3xl font-bold mb-3">3</div>
                    <div class="text-white font-semibold mb-2">Déclencheurs</div>
                    <p class="text-slate-400 text-sm">Inscription au tunnel, changement de statut du lead, franchissement d'un seuil de score.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">5</div>
                    <div class="text-white font-semibold mb-2">Templates prêts à l'emploi</div>
                    <p class="text-slate-400 text-sm">Onboarding, Vente, Relance inactifs, Nurturing long terme, Post-conversion.</p>
                </div>
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-3xl font-bold mb-3">7</div>
                    <div class="text-white font-semibold mb-2">Variables dynamiques</div>
                    <p class="text-slate-400 text-sm">Prénom, nom, email, score, nom du tunnel, nom de l'entreprise — personnalisation automatique.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- COMMENT ÇA FONCTIONNE -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/3">
                    <h3 class="text-amber-500 font-bold tracking-widest uppercase mb-4">Fonctionnement</h3>
                    <h2 class="font-[Playfair-Display] text-4xl font-bold mb-6">Comment ça marche</h2>
                    <p class="text-slate-400 leading-relaxed">
                        Dès qu'un lead déclenche une condition, il est automatiquement inscrit à la séquence correspondante. Les emails partent selon les délais configurés, sans aucune action manuelle.
                    </p>
                </div>
                <div class="w-full md:w-2/3">
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                            <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">1</div>
                            <div>
                                <div class="font-semibold text-white mb-1">Le lead fait une action</div>
                                <p class="text-slate-400 text-sm">Il remplit un formulaire, son statut passe à "hot", ou son score dépasse le seuil configuré.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                            <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">2</div>
                            <div>
                                <div class="font-semibold text-white mb-1">Le système détecte le déclencheur</div>
                                <p class="text-slate-400 text-sm">L'observer surveille chaque changement sur les leads. Si une séquence active correspond, le lead y est inscrit automatiquement.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-slate-700">
                            <div class="w-10 h-10 rounded-full bg-amber-500 text-slate-900 font-bold flex items-center justify-center shrink-0">3</div>
                            <div>
                                <div class="font-semibold text-white mb-1">Les emails partent selon les délais</div>
                                <p class="text-slate-400 text-sm">Email immédiat (0h), le lendemain (24h), à 7 jours (168h)… Le scheduler vérifie toutes les minutes.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-6 rounded-2xl bg-slate-800 border border-emerald-500/30">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center shrink-0">✓</div>
                            <div>
                                <div class="font-semibold text-white mb-1">Anti-doublon intégré</div>
                                <p class="text-slate-400 text-sm">Si le lead est déjà dans une séquence en cours, aucune nouvelle inscription n'est créée. La prochaine démarre une fois la précédente terminée.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODE D'EMPLOI -->
    <section class="py-24 bg-slate-800 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                Mode d'emploi
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Créer une séquence -->
                <div class="bg-slate-700/50 p-8 rounded-3xl border border-slate-600">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-amber-500 text-slate-900 text-sm font-bold flex items-center justify-center">A</span>
                        Créer une séquence
                    </h3>
                    <ul class="space-y-4 text-slate-300 text-sm">
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">1.</span> Admin → <strong class="text-white">Séquences Email</strong> → Nouvelle séquence</li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">2.</span> Choisir le déclencheur : <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">form_submit</code>, <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">status_changed</code> ou <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">score_threshold</code></li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">3.</span> Pour <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">status_changed</code> : renseigner la condition (ex : status = <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">hot</code>)</li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">4.</span> Passer le statut à <strong class="text-white">Actif</strong> pour l'activer</li>
                    </ul>
                    <div class="mt-6 pt-4 border-t border-slate-600">
                        <p class="text-xs text-slate-500">Ou utiliser un template prêt : <strong class="text-slate-400">Nouvelle séquence → Depuis un template</strong></p>
                    </div>
                </div>
                <!-- Ajouter des emails -->
                <div class="bg-slate-700/50 p-8 rounded-3xl border border-slate-600">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-amber-500 text-slate-900 text-sm font-bold flex items-center justify-center">B</span>
                        Ajouter des emails
                    </h3>
                    <ul class="space-y-4 text-slate-300 text-sm">
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">1.</span> Ouvrir la séquence → onglet <strong class="text-white">Emails</strong></li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">2.</span> Renseigner le sujet — utiliser les variables : <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">{first_name}</code>, <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">{score}</code>…</li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">3.</span> Rédiger le contenu HTML ou utiliser le Builder Email</li>
                        <li class="flex gap-3"><span class="text-amber-500 shrink-0">4.</span> Définir le délai en heures : <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">0</code> = immédiat, <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">24</code> = lendemain, <code class="text-amber-400 bg-slate-900 px-1.5 py-0.5 rounded text-xs">168</code> = 7 jours</li>
                    </ul>
                    <div class="mt-6 pt-4 border-t border-slate-600">
                        <p class="text-xs text-amber-500/80">Une séquence sans email se marque immédiatement complétée sans rien envoyer.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VARIABLES -->
    <section class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl font-bold text-center mb-16">
                Variables disponibles dans les emails
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                @foreach ([
                    ['{first_name}', 'Prénom du lead'],
                    ['{last_name}', 'Nom du lead'],
                    ['{full_name}', 'Prénom + Nom'],
                    ['{email}', 'Email'],
                    ['{score}', 'Score du lead'],
                    ['{funnel_name}', 'Nom du tunnel'],
                    ['{tenant_name}', "Nom de l'entreprise"],
                ] as [$var, $desc])
                <div class="p-5 rounded-2xl bg-slate-800 border border-slate-700 hover:border-amber-500/50 transition-colors text-center">
                    <code class="text-amber-500 font-mono text-sm block mb-2">{{ $var }}</code>
                    <span class="text-slate-400 text-xs">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5 TEMPLATES -->
    <section class="py-24 bg-slate-800 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-4">
                5 Templates inclus
            </h2>
            <p class="text-slate-400 text-center mb-16">Créez une séquence complète en un clic depuis Admin → Séquences Email → Depuis un template.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['👋', 'Onboarding 7 jours', 'form_submit', '4 emails — 0h · 24h · 72h · 168h', 'Accueil, guide de démarrage, témoignage, récapitulatif.'],
                    ['🎯', 'Séquence de vente', 'form_submit', '5 emails — 0h · 24h · 72h · 120h · 168h', 'Offre, bénéfices, preuves sociales, FAQ, urgence finale.'],
                    ['🔔', 'Relance inactifs', 'status_changed', '3 emails — 0h · 72h · 168h', "Message personnalisé, offre spéciale, dernier message."],
                    ['🌱', 'Nurturing long terme', 'form_submit', '4 emails — 0h · 168h · 336h · 504h', 'Conseil pratique, erreur courante, étude de cas, upsell.'],
                    ['🏆', 'Post-conversion', 'status_changed', '4 emails — 0h · 24h · 168h · 336h', 'Remerciement, guide, satisfaction, offre fidélité.'],
                ] as [$icon, $name, $trigger, $timing, $desc])
                <div class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-3xl mb-4">{{ $icon }}</div>
                    <div class="flex items-center gap-2 mb-3">
                        <h3 class="font-bold text-white">{{ $name }}</h3>
                    </div>
                    <p class="text-slate-400 text-sm mb-4">{{ $desc }}</p>
                    <div class="space-y-2">
                        <span class="text-xs bg-slate-900 text-amber-400 px-2 py-1 rounded font-mono inline-block">{{ $trigger }}</span>
                        <p class="text-xs text-slate-500">{{ $timing }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FOOTER NAV -->
    <section class="py-16 bg-slate-900 text-center">
        <a href="{{ route('presentation.client.fallback') }}"
            class="inline-flex items-center gap-2 border border-slate-700 bg-slate-800/50 text-white px-8 py-4 rounded-full font-bold hover:bg-slate-700 transition-all">
            ← Retour à la présentation
        </a>
    </section>

</body>
</html>
