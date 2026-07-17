<footer class="bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-950 relative overflow-hidden border-t border-white/[0.06]">
    <!-- Background Effects -->
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500 rounded-full blur-3xl opacity-10"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-amber-500 rounded-full blur-3xl opacity-5"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Main Footer Content -->
        <div class="py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Column 1: About -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('assets/RoyalLeadPro/logoLeadProBlanc.svg') }}" class="h-8 w-auto"
                        alt="Royal LeadPro">
                </div>
                <p class="text-slate-400 leading-relaxed mb-6 max-w-md">
                    La plateforme SaaS de génération de leads : tunnels de vente, capture et scoring de leads,
                    séquences email automatisées — dans un espace isolé et sécurisé pour votre équipe.
                </p>
            </div>

            <!-- Column 2: Quick Links -->
            <div>
                <h3 class="text-white font-bold text-lg mb-6">Liens Rapides</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="#fonctionnalites"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Fonctionnalités</a>
                    </li>
                    <li>
                        <a href="#tarifs" class="text-slate-400 hover:text-amber-400 transition-colors">Tarifs</a>
                    </li>
                    <li>
                        <a href="#faq" class="text-slate-400 hover:text-amber-400 transition-colors">FAQ</a>
                    </li>
                    <li>
                        <a href="{{ route('tenant.register') }}"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Créer mon espace</a>
                    </li>
                    <li>
                        <a href="{{ route('royal-freelance') }}"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Découvrir Royal Freelance</a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Resources -->
            <div>
                <h3 class="text-white font-bold text-lg mb-6">Ressources</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('guest.privacy') }}"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Politique de
                            Confidentialité</a>
                    </li>
                    <li>
                        <a href="{{ route('guest.terms') }}"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Conditions
                            d'Utilisation</a>
                    </li>
                    <li>
                        <a href="{{ route('guest.support') }}"
                            class="text-slate-400 hover:text-amber-400 transition-colors">Support & Aide</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-white/10 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-400 text-sm text-center md:text-left">
                    &copy; {{ date('Y') }} <strong class="text-white">Royal LeadPro</strong>. Tous droits réservés.
                </p>
                <p class="text-slate-500 text-xs">
                    Développé par <a href="https://geniusgroups.ci" target="_blank"
                        class="text-amber-400 hover:text-white transition-colors">GENIUS GROUPS SAS</a>
                </p>
            </div>
        </div>
    </div>
</footer>
