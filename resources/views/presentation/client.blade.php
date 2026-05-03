<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Présentation Livraison - Royal LeadMagnet</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700,800"
        rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }

        .text-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 50%, var(--color-primary-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient-gold {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-primary-500) 50%, var(--color-primary-600) 100%);
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Outfit] antialiased bg-slate-900 text-slate-100 selection:bg-amber-500 selection:text-white">

    <!-- 1. HERO SLIDE -->
    <section class="min-h-screen flex flex-col items-center justify-center relative overflow-hidden p-6 py-20">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20"
            style="background-image: radial-gradient(var(--color-primary-500) 1px, transparent 1px); background-size: 30px 30px;">
        </div>

        <div class="z-10 text-center max-w-4xl mx-auto space-y-8 animate-fade-in-up">
            <img src="{{ asset('assets/logowhite.png') }}" alt="Royal LeadPro" class="h-24 mx-auto mb-8">

            <div
                class="inline-flex items-center px-4 py-2 rounded-full border border-amber-500/30 bg-amber-500/10 text-amber-500 mb-6">
                <span class="animate-pulse mr-2">●</span> Livraison MVP v1.0.0
            </div>

            <h1 class="font-[Playfair-Display] text-5xl md:text-7xl font-bold leading-tight">
                L'Excellence Commerciale <br>
                <span class="text-gradient-gold">Délivrée Aujourd'hui</span>
            </h1>

            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Plateforme complète de Génération, Qualification et Gestion de Leads.
                Conçue pour transformer votre processus commercial en Côte d'Ivoire et au-delà.
            </p>

            <div class="pt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#overview"
                    class="bg-amber-600 text-white px-8 py-4 rounded-full font-bold hover:bg-amber-700 transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(245,158,11,0.3)]">
                    Découvrir la Livraison
                </a>
                <a href="https://royalleadpro.com/admin" target="_blank"
                    class="border border-slate-600 bg-slate-800/50 text-white px-8 py-4 rounded-full font-bold hover:bg-slate-700 transition-all">
                    Accès Admin
                </a>
            </div>
        </div>
    </section>

    <!-- 2. VUE D'ENSEMBLE (KPIs) -->
    <section id="overview" class="py-24 bg-slate-800 relative border-t border-slate-700">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                État du Projet : <span class="text-emerald-400">Succès</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div
                    class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-4xl font-bold mb-2">100%</div>
                    <div class="text-slate-300 font-medium">Progression MVP</div>
                    <p class="text-sm text-slate-500 mt-2">Délivré ce jour</p>
                </div>
                <!-- Card 2 -->
                <div
                    class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-4xl font-bold mb-2">14/14</div>
                    <div class="text-slate-300 font-medium">Fonctionnalités Core</div>
                    <p class="text-sm text-slate-500 mt-2">100% Fonctionnelles</p>
                </div>
                <!-- Card 3 -->
                <div
                    class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-4xl font-bold mb-2">+10</div>
                    <div class="text-slate-300 font-medium">Bonus Inclus</div>
                    <p class="text-sm text-slate-500 mt-2">Non prévus initialement</p>
                </div>
                <!-- Card 4 -->
                <div
                    class="p-8 rounded-2xl bg-slate-700/50 border border-slate-600 hover:border-amber-500/50 transition-colors">
                    <div class="text-amber-500 text-4xl font-bold mb-2">Tech</div>
                    <div class="text-slate-300 font-medium">Stack Moderne</div>
                    <p class="text-sm text-slate-500 mt-2">Laravel 12 + Filament v4</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. ARCHITECTURE VISUELLE -->
    <section class="py-24 bg-slate-900 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-12 items-center">
                <div class="w-full md:w-1/3">
                    <h3 class="text-amber-500 font-bold tracking-widest uppercase mb-4">Architecture</h3>
                    <h2 class="font-[Playfair-Display] text-4xl font-bold mb-6">Le Cœur du Système</h2>
                    <p class="text-slate-400 leading-relaxed mb-6">
                        Une architecture robuste conçue pour la performance. Les funnels capturent, le moteur
                        intelligent analyse, et le dashboard convertit.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center text-slate-300">
                            <span class="w-2 h-2 bg-amber-500 rounded-full mr-3"></span>
                            Tracking temps réel (23 points)
                        </li>
                        <li class="flex items-center text-slate-300">
                            <span class="w-2 h-2 bg-amber-500 rounded-full mr-3"></span>
                            Scoring automatique
                        </li>
                        <li class="flex items-center text-slate-300">
                            <span class="w-2 h-2 bg-amber-500 rounded-full mr-3"></span>
                            Attribution Commerciale
                        </li>
                    </ul>
                </div>

                <div class="w-full md:w-2/3">
                    <div class="relative p-8 rounded-3xl bg-slate-800 border border-slate-700 shadow-2xl">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <!-- Ingestion -->
                            <div class="space-y-4">
                                <div class="bg-indigo-900/40 border border-indigo-500/30 p-6 rounded-xl">
                                    <div class="text-indigo-400 font-bold mb-2">FUNNELS</div>
                                    <div class="text-xs text-slate-400">Pages de Capture<br>Sous-domaines</div>
                                </div>
                                <div class="text-slate-600 text-2xl">↓</div>
                                <div class="bg-indigo-900/40 border border-indigo-500/30 p-6 rounded-xl">
                                    <div class="text-indigo-400 font-bold mb-2">TRACKING</div>
                                    <div class="text-xs text-slate-400">23 Datapoints<br>Events</div>
                                </div>
                            </div>

                            <!-- Engine -->
                            <div class="space-y-4 pt-12">
                                <div
                                    class="bg-amber-900/40 border border-amber-500/30 p-6 rounded-xl transform scale-110 shadow-lg shadow-amber-900/20">
                                    <div class="text-amber-500 font-bold mb-2">MOTEUR LEAD</div>
                                    <div class="text-sm text-slate-300 font-mono my-2 py-1 bg-black/30 rounded">Scoring
                                        Auto</div>
                                    <div class="text-sm text-slate-300 font-mono my-2 py-1 bg-black/30 rounded">Tagging
                                    </div>
                                    <div class="text-sm text-slate-300 font-mono my-2 py-1 bg-black/30 rounded">Router
                                    </div>
                                </div>
                            </div>

                            <!-- Conversion -->
                            <div class="space-y-4">
                                <div class="bg-emerald-900/40 border border-emerald-500/30 p-6 rounded-xl">
                                    <div class="text-emerald-400 font-bold mb-2">DASHBOARD</div>
                                    <div class="text-xs text-slate-400">Pipeline Kanban<br>Alertes</div>
                                </div>
                                <div class="text-slate-600 text-2xl">↑</div>
                                <div class="bg-emerald-900/40 border border-emerald-500/30 p-6 rounded-xl">
                                    <div class="text-emerald-400 font-bold mb-2">CONVERSION</div>
                                    <div class="text-xs text-slate-400">Relances<br>Ventes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. FONCTIONNALITÉS CLÉS -->
    <section class="py-24 bg-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-5xl font-bold text-center mb-20">
                <span class="text-gradient-gold">Puissance & Innovation</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Block 1 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Tunnels Illimités</h3>
                    <p class="text-slate-400 text-sm">Créateur de pages intégré. Chaque funnel a son propre sous-domaine
                        pour un branding parfait.</p>
                </div>
                <!-- Block 2 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Scoring Intelligent</h3>
                    <p class="text-slate-400 text-sm">Le système détecte les "Leads HOT" (Score > 31) et alerte vos
                        commerciaux.</p>
                </div>
                <!-- Block 3 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Attribution Auto</h3>
                    <p class="text-slate-400 text-sm">Chaque commercial a son lien unique. Les leads lui sont assignés
                        automatiquement.</p>
                </div>
                <!-- Block 4 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Tracking 23 Points</h3>
                    <p class="text-slate-400 text-sm">Données device, temps passé, % vidéo scroll depth, localisation
                        précise.</p>
                </div>
                <!-- Block 5 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Alertes Temps Réel</h3>
                    <p class="text-slate-400 text-sm">Notifications instantanées lors d'un clic WhatsApp ou d'une
                        inscription.</p>
                </div>
                <!-- Block 6 -->
                <div
                    class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <h3 class="text-xl font-bold mb-3 text-white">Auto-Tagging</h3>
                    <p class="text-slate-400 text-sm">Tags automatiques : "Vidéo Complète", "Relance 7j", "Intéressé".
                    </p>
                </div>

                <!-- Block 7 -->
                <div class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold text-white">Séquences Email Auto</h3>
                        <span class="text-xs font-semibold bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full border border-amber-500/30 shrink-0">Nouveau</span>
                    </div>
                    <p class="text-slate-400 text-sm">Envoi automatique d'emails selon les actions du lead. 3 déclencheurs, délais configurables, anti-doublon.</p>
                    <a href="{{ route('presentation.sequences-email') }}" class="inline-block mt-3 text-amber-500 text-sm font-medium hover:underline">En savoir plus →</a>
                </div>
                <!-- Block 8 -->
                <div class="p-8 rounded-2xl bg-white/5 border border-white/5 hover:border-amber-500/30 transition-all group">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold text-white">Builder Email Visuel</h3>
                        <span class="text-xs font-semibold bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full border border-amber-500/30 shrink-0">Nouveau</span>
                    </div>
                    <p class="text-slate-400 text-sm">Éditeur visuel par blocs pour composer les emails des séquences. 6 types de blocs, 4 templates, export HTML.</p>
                    <a href="{{ route('presentation.email-builder') }}" class="inline-block mt-3 text-amber-500 text-sm font-medium hover:underline">En savoir plus →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. GUIDE D'UTILISATION & DEMO -->
    <section class="py-24 bg-slate-900 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-16">
                Guide d'Utilisation
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Admin Flow -->
                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-slate-700 mt-1 flex items-center justify-center text-2xl">
                            👑</div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Administrateur</h3>
                            <div class="text-sm text-slate-400">Gestion Globale</div>
                        </div>
                    </div>
                    <ul class="space-y-4 text-slate-300">
                        <li class="flex gap-3"><span class="text-amber-500">1.</span> Créer un Tunnel & configurer la
                            page</li>
                        <li class="flex gap-3"><span class="text-amber-500">2.</span> Ajouter des membres d'équipe
                            (commerciaux)</li>
                        <li class="flex gap-3"><span class="text-amber-500">3.</span> Suivre les KPIs globaux sur le
                            Dashboard</li>
                        <li class="flex gap-3"><span class="text-amber-500">4.</span> Gérer les tags et séquences email
                        </li>
                    </ul>
                    <div class="mt-8 pt-6 border-t border-slate-700/50">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mb-2">Accès</p>
                        <code class="block bg-slate-950 p-3 rounded text-amber-500 text-sm font-mono">/admin</code>
                    </div>
                </div>

                <!-- Commercial Flow -->
                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-slate-700 mt-1 flex items-center justify-center text-2xl">
                            💼</div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Commercial</h3>
                            <div class="text-sm text-slate-400">Vente & Conversion</div>
                        </div>
                    </div>
                    <ul class="space-y-4 text-slate-300">
                        <li class="flex gap-3"><span class="text-amber-500">1.</span> Se connecter à son Dashboard dédié
                        </li>
                        <li class="flex gap-3"><span class="text-amber-500">2.</span> Générer son lien unique <br><span
                                class="text-xs text-slate-500 ml-6">ex: /f/jean-formation</span></li>
                        <li class="flex gap-3"><span class="text-amber-500">3.</span> Partager son lien sur
                            WhatsApp/Réseaux</li>
                        <li class="flex gap-3"><span class="text-amber-500">4.</span> Recevoir ses leads qualifiés et
                            convertir</li>
                    </ul>
                    <div class="mt-8 pt-6 border-t border-slate-700/50">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mb-2">Accès</p>
                        <code class="block bg-slate-950 p-3 rounded text-amber-500 text-sm font-mono">/commercial</code>
                    </div>
                </div>
            </div>

            <!-- SCENARIO VISUAL -->
            <div
                class="mt-16 bg-gradient-to-r from-slate-900 to-slate-800 p-8 rounded-2xl border border-slate-700 text-center">
                <h3 class="text-lg font-bold text-white mb-8">Le Parcours d'un Lead Parfait</h3>
                <div class="flex flex-wrap justify-center items-center gap-4 md:gap-8 text-sm">
                    <div class="bg-slate-950 px-4 py-3 rounded-xl border border-slate-700 text-slate-300">
                        <span class="block text-2xl mb-2">👀</span> Visiteur
                    </div>
                    <div class="text-slate-600">→</div>
                    <div class="bg-slate-950 px-4 py-3 rounded-xl border border-slate-700 text-slate-300">
                        <span class="block text-2xl mb-2">🎥</span> Vidéo 100%
                    </div>
                    <div class="text-slate-600">→</div>
                    <div class="bg-slate-950 px-4 py-3 rounded-xl border border-amber-500/30 text-amber-400">
                        <span class="block text-2xl mb-2">📝</span> Inscription
                    </div>
                    <div class="text-slate-600">→</div>
                    <div class="bg-slate-950 px-4 py-3 rounded-xl border border-red-500/30 text-red-400 animate-pulse">
                        <span class="block text-2xl mb-2">🔥</span> Lead HOT
                    </div>
                    <div class="text-slate-600">→</div>
                    <div class="bg-slate-950 px-4 py-3 rounded-xl border border-emerald-500/30 text-emerald-400">
                        <span class="block text-2xl mb-2">💬</span> Conv. WhatsApp
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. COMPARATIF & BONUS (MVP vs LIVRÉ) -->
    <section class="py-24 bg-slate-800 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-1/2 h-full bg-amber-500/5 blur-3xl rounded-full"></div>
        <div class="max-w-5xl mx-auto px-6 relative z-10">
            <h2 class="font-[Playfair-Display] text-3xl md:text-4xl font-bold text-center mb-12">
                MVP Attendu vs <span class="text-emerald-400">MVP Livré</span>
            </h2>

            <div class="bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-700">
                <div class="grid grid-cols-3 bg-slate-950 p-6 border-b border-slate-700 font-bold text-lg">
                    <div class="text-slate-400">Fonctionnalité</div>
                    <div class="text-center text-slate-500">Standard</div>
                    <div class="text-center text-emerald-400">Royal LeadMagnet</div>
                </div>

                <!-- Row 1 -->
                <div
                    class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors">
                    <div class="font-medium text-white">Tracking Données</div>
                    <div class="text-center text-slate-500">8 Champs</div>
                    <div class="text-center text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">⚡</span> 23 Champs
                    </div>
                </div>
                <!-- Row 2 -->
                <div
                    class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors">
                    <div class="font-medium text-white">Dashboard</div>
                    <div class="text-center text-slate-500">Stats simples</div>
                    <div class="text-center text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">📊</span> 6 Widgets Temps Réel
                    </div>
                </div>
                <!-- Row 3 -->
                <div
                    class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors">
                    <div class="font-medium text-white">Pipeline</div>
                    <div class="text-center text-slate-500">Liste Tableau</div>
                    <div class="text-center text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">📋</span> Kanban Drag & Drop
                    </div>
                </div>
                <!-- Row 4 -->
                <div
                    class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors">
                    <div class="font-medium text-white">Géolocalisation</div>
                    <div class="text-center text-slate-500">1 API</div>
                    <div class="text-center text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">🛡️</span> 4 APIs (Fallback)
                    </div>
                </div>
                <!-- Row 5 -->
                <div class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors">
                    <div class="font-medium text-white">Alertes</div>
                    <div class="text-center text-slate-500">Aucune</div>
                    <div class="text-center text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">🔔</span> 6 Types (Temps réel)
                    </div>
                </div>
                <!-- Row 6 -->
                <div class="grid grid-cols-3 p-6 border-b border-slate-700 items-center hover:bg-slate-800 transition-colors bg-amber-500/5">
                    <div class="font-medium text-white">Email Marketing</div>
                    <div class="text-center text-slate-500">Outils externes</div>
                    <div class="text-center text-amber-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">📧</span> Séquences Auto (3 triggers)
                    </div>
                </div>
                <!-- Row 7 -->
                <div class="grid grid-cols-3 p-6 items-center hover:bg-slate-800 transition-colors bg-amber-500/5">
                    <div class="font-medium text-white">Builder Email</div>
                    <div class="text-center text-slate-500">Absent</div>
                    <div class="text-center text-amber-400 font-bold flex items-center justify-center gap-2">
                        <span class="text-xl">🎨</span> Visuel par blocs (6 types)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. PERFORMANCE & SECURITE -->
    <section class="py-24 bg-slate-900 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl font-bold text-center mb-16">Performances & Sécurité</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Performance -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Performance
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-400">Temps de chargement</span>
                                <span class="text-emerald-400 font-bold">2.1s (Objectif < 3s)</span>
                            </div>
                            <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 w-[95%]"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-400">Réponse API</span>
                                <span class="text-emerald-400 font-bold">180ms</span>
                            </div>
                            <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 w-[98%]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Securité -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Sécurité
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-800 p-4 rounded-lg flex items-center gap-3">
                            <span class="text-emerald-400">✓</span>
                            <span class="text-sm text-slate-300">Certificat SSL</span>
                        </div>
                        <div class="bg-slate-800 p-4 rounded-lg flex items-center gap-3">
                            <span class="text-emerald-400">✓</span>
                            <span class="text-sm text-slate-300">Protection CSRF</span>
                        </div>
                        <div class="bg-slate-800 p-4 rounded-lg flex items-center gap-3">
                            <span class="text-emerald-400">✓</span>
                            <span class="text-sm text-slate-300">Rôles Isolés</span>
                        </div>
                        <div class="bg-slate-800 p-4 rounded-lg flex items-center gap-3">
                            <span class="text-emerald-400">✓</span>
                            <span class="text-sm text-slate-300">Backup Quotidien</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. ROADMAP -->
    <section class="py-24 bg-slate-800">
        <div class="max-w-4xl mx-auto px-6">
            <h2 class="font-[Playfair-Display] text-3xl font-bold text-center mb-16">Prochaines Étapes</h2>

            <div class="space-y-8">
                <!-- 1. Livraison Anticipée (WOW Effect) -->
                <div class="flex items-start gap-6">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white border-4 border-slate-800 z-10 animate-pulse">
                        ✓</div>


                    <div class="bg-slate-700/50 p-6 rounded-2xl flex-grow border border-emerald-500/30 relative">
                        <div class="absolute -left-9 top-12 bottom-0 w-0.5 bg-slate-700 h-full"></div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-white">Livraison MVP</h3>
                            <span
                                class="bg-emerald-500/20 text-emerald-400 text-xs font-bold px-2 py-1 rounded border border-emerald-500/30">
                                21 Jours d'Avance
                            </span>
                        </div>
                        <p class="text-slate-400 mb-4">
                            Livré le <strong class="text-white">5 Février</strong> (au lieu du 26 Février). <br>
                            La plateforme est prête à l'emploi dès aujourd'hui.
                        </p>
                    </div>
                </div>

                <!-- 2. Maintenance 1 Mois (Garantie) -->
                <div class="flex items-start gap-6">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white border-4 border-slate-800 z-10">
                        M</div>
                    <div class="bg-slate-700/50 p-6 rounded-2xl flex-grow border border-slate-600 relative">
                        <div class="absolute -left-9 top-12 bottom-0 w-0.5 bg-slate-700 h-full"></div>
                        <h3 class="text-xl font-bold text-white mb-2">Maintenance Corrective (Offerte)</h3>
                        <p class="text-slate-400 mb-2">Période de garantie d'un mois incluse.</p>
                        <ul class="text-sm text-slate-300 space-y-1">
                            <li class="flex items-center gap-2">✓ Correction bugs</li>
                            <li class="flex items-center gap-2">✓ Support technique prioritaire</li>
                            <li class="flex items-center gap-2">✓ Formation équipes</li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Phase 2 (Optimisations V2) -->
                <div class="flex items-start gap-6">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-500 flex items-center justify-center font-bold text-slate-900 border-4 border-slate-800 z-10">
                        V2</div>
                    <div
                        class="bg-slate-700/30 p-6 rounded-2xl flex-grow border border-amber-500/30 relative overflow-hidden">
                        <div class="absolute -left-9 top-12 bottom-0 w-0.5 bg-slate-700 h-full"></div>
                        <!-- Badge "Sur Devis" -->
                        <div
                            class="absolute top-0 right-0 bg-amber-500 text-slate-900 text-xs font-bold px-3 py-1 rounded-bl-lg">
                            Sur Devis
                        </div>

                        <h3 class="text-xl font-bold text-white mb-2">Développement Continu (V2)</h3>
                        <p class="text-slate-400 mb-4">Fonctionnalités avancées pour accélérer la croissance.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center gap-3 text-emerald-400 font-medium">
                                <span>✓</span> Séquences Email Auto <span class="text-xs bg-emerald-500/20 px-2 py-0.5 rounded-full border border-emerald-500/30">Fait</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-300">
                                <span class="text-amber-500">🏷️</span> Leads & Tags Optimisés
                            </div>
                            <div class="flex items-center gap-3 text-emerald-400 font-medium">
                                <span>✓</span> Dashboard Commercial V2 <span class="text-xs bg-emerald-500/20 px-2 py-0.5 rounded-full border border-emerald-500/30">Fait</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-300">
                                <span class="text-amber-500">📱</span> App Mobile / PWA
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Phase 3 (Produit SaaS) -->
                <div class="flex items-start gap-6">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center font-bold text-white border-4 border-slate-800 z-10">
                        V3</div>
                    <div
                        class="bg-slate-700/30 p-6 rounded-2xl flex-grow border border-purple-500/30 relative overflow-hidden">
                        <div
                            class="absolute top-0 right-0 bg-purple-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                            Scalability
                        </div>

                        <h3 class="text-xl font-bold text-white mb-2">Produit SaaS (Phase 3)</h3>
                        <p class="text-slate-400 mb-4">Transformez votre outil en produit commercialisable à d'autres
                            entreprises.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center gap-3 text-slate-300">
                                <span class="text-amber-500">💳</span> Paiements GeniusPay
                            </div>
                            <div class="flex items-center gap-3 text-slate-300">
                                <span class="text-amber-500">🌍</span> Multi-Entreprises
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- 9. RÉCAPITULATIF & SUPPORT -->
    <section class="py-24 bg-slate-900 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <!-- Checklist -->
                <div>
                    <h2 class="font-[Playfair-Display] text-3xl font-bold mb-8">Récapitulatif Livraison</h2>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Plateforme fonctionnelle 100%
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Panel Admin Filament Complet
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Dashboard Commercial & Attribution
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Tracking & Scoring Avancé
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Documentation & Code Source
                        </li>
                        <li class="flex items-center gap-3 text-slate-300">
                            <div
                                class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                                ✓</div>
                            Séquences Email Automatiques (3 triggers, délais, variables)
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h2 class="font-[Playfair-Display] text-3xl font-bold mb-8">Support & Maintenance</h2>
                    <div class="bg-slate-800 p-8 rounded-2xl border border-slate-700">
                        <p class="text-slate-400 mb-6">Nous restons à vos côtés pour la prise en main et les évolutions.
                        </p>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                                <span class="text-white">Bugs Critiques</span>
                                <span class="text-emerald-400 font-bold">Inclus 3 mois</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                                <span class="text-white">Sécurité</span>
                                <span class="text-emerald-400 font-bold">Inclus 6 mois</span>
                            </div>
                            <div class="flex justify-between items-center pb-2">
                                <span class="text-white">Monitoring</span>
                                <span class="text-emerald-400 font-bold">24/7</span>
                            </div>
                        </div>

                        <a href="mailto:contact@geniusgroups.com"
                            class="block w-full bg-white text-slate-900 font-bold text-center py-3 rounded-lg hover:bg-slate-200 transition-colors">
                            Contacter le Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-950 py-12 border-t border-slate-800 text-center">
        <img src="{{ asset('assets/logowhite.png') }}" class="h-10 mx-auto mb-6 opacity-80" alt="Logo">
        <p class="text-slate-500 mb-4">Développé avec passion pour l'excellence commerciale.</p>
        <div class="text-sm text-slate-600">
            Version 1.0.0 • <span class="text-amber-600">Genius Groups SAS</span>
        </div>
    </footer>

</body>

</html>