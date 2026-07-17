<section class="relative pt-32 pb-20 lg:pt-44 lg:pb-0 overflow-hidden bg-white dark:bg-zinc-950">
    <!-- Background Decor -->
    <div class="absolute inset-0 hero-pattern"></div>
    <div
        class="absolute top-0 right-0 -mt-20 -mr-20 w-[600px] h-[600px] bg-amber-50 dark:bg-amber-500/10 rounded-full blur-3xl opacity-50 dark:opacity-100 pointer-events-none">
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
            Générez des leads. <br>
            <span class="text-gradient-gold">Faites-les grandir.</span>
        </h1>

        <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-600 dark:text-zinc-400 font-light mb-10 leading-relaxed">
            Créez vos tunnels de vente, capturez vos prospects et automatisez vos relances email — le tout
            dans un espace de travail pensé pour votre équipe, du solo entrepreneur au workspace complet.
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

            <div class="flex text-left">
                <!-- Sidebar -->
                <div class="hidden sm:flex w-16 flex-col items-center gap-5 py-6 border-r border-slate-100 dark:border-white/[0.06] bg-slate-50/50 dark:bg-white/[0.015]">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white text-xs font-bold">R</div>
                    <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-white/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 dark:text-zinc-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                </div>

                <!-- Main panel -->
                <div class="flex-1 p-5 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="text-[11px] uppercase tracking-widest text-slate-400 dark:text-zinc-500 font-semibold mb-1">Dashboard</div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Tunnel « Coaching Premium »</div>
                        </div>
                        <div class="hidden sm:block px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-200 dark:border-emerald-500/20">
                            Actif
                        </div>
                    </div>

                    <!-- Stat chips -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        <div class="glass-card rounded-xl p-4">
                            <div class="text-[11px] text-slate-400 dark:text-zinc-500 font-medium mb-1">Vues</div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white">4 218</div>
                        </div>
                        <div class="glass-card rounded-xl p-4">
                            <div class="text-[11px] text-slate-400 dark:text-zinc-500 font-medium mb-1">Leads</div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white">312</div>
                        </div>
                        <div class="glass-card rounded-xl p-4">
                            <div class="text-[11px] text-slate-400 dark:text-zinc-500 font-medium mb-1">Conversion</div>
                            <div class="text-xl font-bold text-amber-600 dark:text-amber-400">7,4%</div>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-5 gap-5">
                        <!-- Bar chart -->
                        <div class="sm:col-span-3 glass-card rounded-xl p-5">
                            <div class="text-xs font-semibold text-slate-500 dark:text-zinc-400 mb-4">Leads / semaine</div>
                            <div class="flex items-end gap-2.5 h-24">
                                <div class="flex-1 rounded-t bg-slate-200 dark:bg-white/10" style="height:35%"></div>
                                <div class="flex-1 rounded-t bg-slate-200 dark:bg-white/10" style="height:55%"></div>
                                <div class="flex-1 rounded-t bg-slate-200 dark:bg-white/10" style="height:40%"></div>
                                <div class="flex-1 rounded-t bg-gradient-to-t from-amber-600 to-amber-400" style="height:75%"></div>
                                <div class="flex-1 rounded-t bg-slate-200 dark:bg-white/10" style="height:60%"></div>
                                <div class="flex-1 rounded-t bg-slate-200 dark:bg-white/10" style="height:48%"></div>
                                <div class="flex-1 rounded-t bg-gradient-to-t from-amber-600 to-amber-400" style="height:90%"></div>
                            </div>
                        </div>

                        <!-- Leads list -->
                        <div class="sm:col-span-2 glass-card rounded-xl p-5">
                            <div class="text-xs font-semibold text-slate-500 dark:text-zinc-400 mb-4">Derniers leads</div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-amber-300 to-amber-500 flex-shrink-0"></div>
                                    <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-white/10"></div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 dark:from-white/20 dark:to-white/10 flex-shrink-0"></div>
                                    <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-white/10"></div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 dark:from-white/20 dark:to-white/10 flex-shrink-0"></div>
                                    <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-white/10"></div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-white/20"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fade to page background at the bottom -->
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white dark:from-zinc-950 to-transparent pointer-events-none"></div>
        </div>
    </div>
</section>
