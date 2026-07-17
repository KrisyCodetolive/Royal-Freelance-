@props(['plans'])

<section id="tarifs" class="py-24 bg-slate-50 dark:bg-white/[0.02] border-t border-transparent dark:border-white/[0.06] relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-bold tracking-wider uppercase mb-4">
                Tarifs
            </div>
            <h2 class="text-3xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white mb-4">Un plan pour chaque étape de
                votre croissance</h2>
            <p class="text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto text-lg">Commencez gratuitement, évoluez quand vous en avez
                besoin. Changement de plan possible à tout moment depuis votre espace.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 items-start">
            @foreach ($plans as $plan)
                @php
                    $recommended = $plan->slug === 'starter';
                @endphp
                <div
                    class="relative glass-card rounded-2xl p-8 border-2 {{ $recommended ? 'border-amber-400 dark:border-amber-500/40 shadow-2xl shadow-amber-500/10 dark:shadow-amber-500/5 md:-translate-y-4' : 'border-slate-100 dark:border-white/[0.06] shadow-sm dark:shadow-none' }} transition-all duration-300">
                    @if ($recommended)
                        <div
                            class="absolute -top-4 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 text-white text-xs font-bold tracking-wider uppercase shadow-lg">
                            Recommandé
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $plan->name }}</h3>

                    <div class="mb-6">
                        @if ($plan->price_monthly == 0)
                            <span class="text-4xl font-bold text-slate-900 dark:text-white">Gratuit</span>
                        @else
                            <span class="text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($plan->price_monthly, 0, ',', ' ') }}</span>
                            <span class="text-slate-500 dark:text-zinc-400"> FCFA/mois</span>
                            <div class="text-sm text-slate-400 dark:text-zinc-500 mt-1">ou {{ number_format($plan->price_yearly, 0, ',', ' ') }} FCFA/an</div>
                        @endif
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $plan->isUnlimited('max_tunnels') ? 'Tunnels illimités' : $plan->max_tunnels . ' tunnel' . ($plan->max_tunnels > 1 ? 's' : '') }}</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $plan->isUnlimited('max_mailing_lists') ? 'Listes mailing illimitées' : $plan->max_mailing_lists . ' liste' . ($plan->max_mailing_lists > 1 ? 's' : '') . ' mailing' }}</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $plan->isUnlimited('max_leads') ? 'Leads illimités' : number_format($plan->max_leads, 0, ',', ' ') . ' leads' }}</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 {{ $plan->max_shared_tunnels === 0 ? 'text-slate-300 dark:text-zinc-700' : 'text-amber-600 dark:text-amber-400' }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="{{ $plan->max_shared_tunnels === 0 ? 'text-slate-400 dark:text-zinc-600' : '' }}">
                                @if ($plan->max_shared_tunnels === 0)
                                    Pas de tunnels partagés
                                @elseif ($plan->isUnlimited('max_shared_tunnels'))
                                    Tunnels partagés illimités
                                @else
                                    {{ $plan->max_shared_tunnels }} tunnels partagés
                                @endif
                            </span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Dashboard {{ $plan->dashboard_level }}</span>
                        </li>
                        <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-zinc-400">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Support {{ str_replace('_', ' ', $plan->support_level) }}</span>
                        </li>
                    </ul>

                    <a href="{{ route('tenant.register') }}"
                        class="block text-center px-6 py-3.5 rounded-lg font-bold transition-all duration-300 {{ $recommended ? 'bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white shadow-lg shadow-amber-600/20 hover:-translate-y-0.5' : 'bg-slate-100 dark:bg-white/[0.06] hover:bg-slate-200 dark:hover:bg-white/[0.1] text-slate-900 dark:text-white' }}">
                        {{ $plan->price_monthly == 0 ? 'Commencer gratuitement' : 'Choisir ' . $plan->name }}
                    </a>
                </div>
            @endforeach
        </div>

        <p class="text-center text-sm text-slate-500 dark:text-zinc-500 mt-10">
            Le plan choisi s'applique immédiatement, avec les limites correspondantes. Vous pouvez changer de plan à
            tout moment depuis "Mon abonnement".
        </p>
    </div>
</section>
