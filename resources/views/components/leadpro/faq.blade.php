<section id="faq" class="py-24 bg-white dark:bg-zinc-950 border-t border-slate-100 dark:border-white/[0.06]" x-data="{ open: null }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-bold tracking-wider uppercase mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>FAQ</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white mb-4">Questions fréquentes</h2>
            <p class="text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto">
                Tout ce que vous devez savoir avant de démarrer avec Royal LeadPro.
            </p>
        </div>

        <div class="space-y-4">
            @php
                $faqs = [
                    [
                        'q' => "Puis-je essayer Royal LeadPro sans carte bancaire ?",
                        'a' => "Oui. Le plan Gratuit ne demande aucune carte bancaire et n'expire jamais — il inclut 2 tunnels, 1 liste mailing et 1 000 leads.",
                    ],
                    [
                        'q' => "Puis-je changer de plan à tout moment ?",
                        'a' => "Oui, depuis la page « Mon abonnement » de votre espace admin, réservée au propriétaire (Owner) du workspace. Le changement de plan s'applique immédiatement.",
                    ],
                    [
                        'q' => "Combien de personnes puis-je inviter dans mon équipe ?",
                        'a' => "Vous pouvez générer des liens d'invitation pour vos collaborateurs et choisir leur rôle (Admin ou Commercial) à l'envoi. Chacun n'a accès qu'aux données de votre workspace.",
                    ],
                    [
                        'q' => "Que se passe-t-il si j'atteins la limite de mon plan ?",
                        'a' => "La création de nouveaux tunnels ou listes mailing est bloquée avec un message clair, et vous êtes invité à passer au plan supérieur pour continuer.",
                    ],
                    [
                        'q' => "Mes données sont-elles isolées des autres entreprises ?",
                        'a' => "Oui. Royal LeadPro repose sur une architecture multi-tenant stricte : vos tunnels, vos leads et vos statistiques ne sont jamais visibles par un autre workspace.",
                    ],
                ];
            @endphp

            @foreach ($faqs as $index => $faq)
                <div class="glass-card rounded-xl overflow-hidden" x-data="{ id: {{ $index }} }">
                    <button @click="open = (open === id ? null : id)"
                        class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left hover:bg-slate-50 dark:hover:bg-white/[0.03] transition-colors">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 text-slate-400 dark:text-zinc-500 flex-shrink-0 transition-transform"
                            :class="{ 'rotate-180': open === id }" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === id" x-collapse x-cloak class="px-6 pb-5 text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
