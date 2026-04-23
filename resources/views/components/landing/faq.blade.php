<section class="py-24 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold tracking-wider uppercase mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>FAQ</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">Questions Fréquentes</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                Tout ce que vous devez savoir sur Royal LeadPro et la communauté Royal Freelance
            </p>
        </div>

        <div class="space-y-4 mb-12">
            <!-- QA 1 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Est-ce
                        réservé aux membres Royal Freelance ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Absolument. Royal LeadPro est la solution technique interne développée exclusivement pour donner un
                    avantage compétitif déloyal à nos partenaires commerciaux Royal Freelance. L'accès est réservé aux
                    membres actifs de la communauté.
                </div>
            </div>

            <!-- QA 2 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Ai-je
                        besoin de compétences techniques ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Aucune compétence technique n'est requise. Tout est "No-Code". Si vous savez envoyer un email, vous
                    savez utiliser Royal LeadPro. Les tunnels sont déjà créés, designés et connectés. Vous n'avez qu'à
                    activer et partager votre lien.
                </div>
            </div>

            <!-- QA 3 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span
                        class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Puis-je
                        personnaliser les pages ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Oui, bien que nous recommandions nos modèles éprouvés qui ont fait leurs preuves, vous avez la
                    liberté d'ajuster les textes, images et couleurs pour qu'ils correspondent parfaitement à votre
                    branding personnel et à votre niche.
                </div>
            </div>

            <!-- QA 4 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span
                        class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Comment
                        fonctionne la capture de leads ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Chaque tunnel intègre des formulaires intelligents qui qualifient automatiquement vos prospects. Les
                    leads sont capturés en temps réel et envoyés directement dans votre dashboard Royal LeadPro. Vous
                    recevez également des notifications instantanées pour un suivi immédiat.
                </div>
            </div>

            <!-- QA 5 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Quel
                        est le coût de Royal LeadPro ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Royal LeadPro est inclus dans votre adhésion Royal Freelance. Aucun frais supplémentaire n'est
                    requis. Vous bénéficiez de l'hébergement, de la maintenance technique et des mises à jour régulières
                    sans coût additionnel.
                </div>
            </div>

            <!-- QA 6 -->
            <div x-data="{ open: false }"
                class="bg-white rounded-xl shadow-sm border border-slate-200 hover:border-amber-300 transition-colors">
                <button @click="open = !open"
                    class="w-full px-6 py-5 flex items-center justify-between focus:outline-none group">
                    <span
                        class="font-bold text-slate-900 text-left group-hover:text-amber-600 transition-colors">Puis-je
                        intégrer mes outils existants ?</span>
                    <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse
                    class="px-6 pb-5 text-slate-600 border-t border-slate-100 pt-4 leading-relaxed">
                    Oui, Royal LeadPro s'intègre facilement avec vos outils CRM existants, vos plateformes d'email
                    marketing et vos systèmes de paiement. Notre équipe support vous accompagne dans la configuration de
                    ces intégrations.
                </div>
            </div>
        </div>

        <!-- CTA Contact -->
        <div
            class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 md:p-10 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500 rounded-full blur-3xl opacity-20"></div>
            <div class="relative">
                <h3 class="text-2xl md:text-3xl font-serif font-bold text-white mb-4">
                    Vous avez d'autres questions ?
                </h3>
                <p class="text-slate-300 mb-6 max-w-2xl mx-auto">
                    Notre équipe support est disponible 24/7 pour répondre à toutes vos interrogations
                </p>
                <a href="{{ route('guest.support') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-slate-900 bg-amber-500 hover:bg-amber-400 rounded-xl shadow-xl shadow-amber-500/30 transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>CONTACTER LE SUPPORT</span>
                </a>
            </div>
        </div>
    </div>
</section>