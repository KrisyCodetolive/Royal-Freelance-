<section class="py-24 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0 opacity-20 bg-[url('https://grainy-gradients.vercel.app/noise.svg')]"></div>
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500 rounded-full blur-[128px] opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-amber-500 rounded-full blur-[128px] opacity-20"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <div
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-500 text-white text-xs font-bold tracking-wider uppercase mb-8 shadow-xl shadow-amber-500/30">
            Prêt à démarrer ?
        </div>

        <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-white mb-6 leading-tight">
            Votre workspace Royal LeadPro <br>
            <span class="text-amber-400">vous attend.</span>
        </h2>

        <p class="text-lg sm:text-xl text-slate-300 mb-10 leading-relaxed max-w-2xl mx-auto">
            Créez votre espace, invitez votre équipe et lancez votre premier tunnel de vente — gratuitement.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tenant.register') }}"
                class="group inline-flex items-center justify-center gap-3 px-10 py-5 text-lg font-bold text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 rounded-xl shadow-2xl shadow-amber-500/40 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-amber-500/60">
                <span>DÉMARRER GRATUITEMENT</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>

            <a href="#tarifs"
                class="inline-flex items-center justify-center gap-2 px-10 py-5 text-lg font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm border-2 border-white/30 hover:border-white/50 rounded-xl transition-all duration-300">
                VOIR LES TARIFS
            </a>
        </div>

        <p class="mt-8 text-sm text-slate-400">
            🎁 Aucune carte bancaire requise • Plan Gratuit sans expiration
        </p>
    </div>
</section>
