<section class="relative pt-32 pb-20 lg:pt-44 lg:pb-0 overflow-hidden bg-white dark:bg-zinc-950">
    <!-- Background Decor -->
    <div class="absolute inset-0 hero-pattern"></div>
    <div
        class="absolute top-0 right-0 -mt-20 -mr-20 w-[600px] h-[600px] bg-amber-100 dark:bg-amber-500/10 hero-blob blur-3xl opacity-35 dark:opacity-70 pointer-events-none">
    </div>
    <div
        class="absolute inset-x-0 top-0 h-[560px] hero-grid-overlay pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 -mb-20 -ml-20 w-[600px] h-[600px] bg-slate-50 dark:bg-white/[0.03] rounded-full blur-3xl opacity-50 pointer-events-none">
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900 dark:bg-white/[0.06] dark:ring-1 dark:ring-white/10 text-white text-xs font-semibold tracking-wider uppercase mb-8 shadow-md ring-1 ring-slate-900/5 dark:shadow-none cursor-default">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
            La plateforme SaaS Royal LeadPro
        </div>

        <h1 class="text-5xl md:text-7xl font-serif font-bold tracking-tight text-slate-900 dark:text-white mb-8 leading-[1.1]">
            Ne perdez plus vos prospects <br>
            <span class="text-gradient-gold">dans vos DM et vos tableurs.</span>
        </h1>

        <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-600 dark:text-zinc-400 font-light mb-10 leading-relaxed">
            Royal LeadPro capture chaque visiteur, le transforme en lead qualifié et relance automatiquement —
            pendant que vous vous concentrez sur la vente.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('tenant.register') }}"
                class="px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 rounded-lg shadow-xl shadow-amber-600/20 dark:shadow-amber-500/20 transition-all duration-300 transform hover:-translate-y-1">
                DÉMARRER GRATUITEMENT
            </a>
            <a href="#tarifs"
                class="px-8 py-4 text-base font-medium text-slate-700 dark:text-zinc-200 bg-white dark:bg-white/[0.04] border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/[0.08] hover:border-slate-300 dark:hover:border-white/20 rounded-lg shadow-sm dark:shadow-none transition-all duration-300">
                VOIR LES TARIFS
            </a>
        </div>
        <p class="mt-4 text-sm text-slate-500 dark:text-zinc-500">Aucune carte bancaire requise pour l'offre Gratuite.</p>
    </div>

    <!-- Product preview mockup -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 mt-16 lg:mt-20">
        <div class="relative rounded-t-2xl lg:rounded-2xl overflow-hidden glass-card shadow-2xl shadow-slate-900/10 dark:shadow-black/40 ring-1 ring-slate-900/5 dark:ring-white/10">
            <!-- Window chrome -->
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-slate-100 dark:border-white/[0.06] bg-white/60 dark:bg-white/[0.02]">
                <span class="w-3 h-3 rounded-full bg-red-400/80"></span>
                <span class="w-3 h-3 rounded-full bg-amber-400/80"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-400/80"></span>
                <div class="ml-4 px-3 py-1 rounded-md bg-slate-100 dark:bg-white/[0.06] text-[11px] text-slate-500 dark:text-zinc-400 font-medium">
                    votreboutique.royalleadpro.com
                </div>
            </div>

            <!-- Video de démo -->
            <div class="bg-black">
                <video
                    class="w-full aspect-video"
                    src="{{ asset('assets/RoyalLeadPro/ExplainSaas.mp4') }}"
                    controls
                    autoplay
                    loop
                    muted
                    playsinline
                    preload="auto">
                </video>
            </div>
        </div>
    </div>
</section>
