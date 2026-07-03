<section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 hero-pattern"></div>
    <div
        class="absolute top-0 right-0 -mt-20 -mr-20 w-[600px] h-[600px] bg-amber-50 rounded-full blur-3xl opacity-50 pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 -mb-20 -ml-20 w-[600px] h-[600px] bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 text-white text-xs font-semibold tracking-wider uppercase mb-8 shadow-md ring-1 ring-slate-900/5 cursor-default">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
            La plateforme SaaS Royal LeadPro
        </div>

        <h1 class="text-5xl md:text-7xl font-serif font-bold tracking-tight text-slate-900 mb-8 leading-[1.1]">
            Générez des leads. <br>
            <span class="text-gradient-gold">Faites-les grandir.</span>
        </h1>

        <p class="mt-4 max-w-3xl mx-auto text-xl text-slate-600 font-light mb-8 leading-relaxed">
            Créez vos tunnels de vente, capturez vos prospects et automatisez vos relances email — le tout
            dans un espace de travail pensé pour votre équipe, du solo entrepreneur au workspace complet.
        </p>

        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 text-amber-700 text-sm font-medium border border-amber-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Tunnels de vente
            </span>
            <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium border border-indigo-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Séquences email
            </span>
            <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-700 text-sm font-medium border border-emerald-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Multi-utilisateurs
            </span>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('tenant.register') }}"
                class="px-8 py-4 text-base font-bold text-white bg-amber-600 hover:bg-amber-500 rounded-lg shadow-xl shadow-amber-600/20 transition-all duration-300 transform hover:-translate-y-1">
                DÉMARRER GRATUITEMENT
            </a>
            <a href="#tarifs"
                class="px-8 py-4 text-base font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-lg shadow-sm transition-all duration-300">
                VOIR LES TARIFS
            </a>
        </div>
        <p class="mt-4 text-sm text-slate-500">Aucune carte bancaire requise pour l'offre Gratuite.</p>

        <!-- Stats -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-slate-100 pt-10">
            <div class="p-4 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="text-3xl font-bold text-slate-900">2 min</div>
                <div class="text-xs text-amber-600 uppercase tracking-widest mt-1 font-semibold">Pour démarrer</div>
            </div>
            <div class="p-4 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="text-3xl font-bold text-slate-900">100%</div>
                <div class="text-xs text-amber-600 uppercase tracking-widest mt-1 font-semibold">Multi-tenant</div>
            </div>
            <div class="p-4 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="text-3xl font-bold text-slate-900">3 plans</div>
                <div class="text-xs text-amber-600 uppercase tracking-widest mt-1 font-semibold">Adaptés à votre taille</div>
            </div>
            <div class="p-4 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="text-3xl font-bold text-slate-900">24/7</div>
                <div class="text-xs text-amber-600 uppercase tracking-widest mt-1 font-semibold">Support dédié</div>
            </div>
        </div>
    </div>
</section>
