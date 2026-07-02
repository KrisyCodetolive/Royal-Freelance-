@extends('auth.layouts.split')

@section('title', 'Choisissez votre plan')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div>
        <h2 class="mt-2 text-3xl font-serif font-medium tracking-tight text-slate-900">
            Choisissez votre plan.
        </h2>
        <p class="mt-2 text-sm text-slate-500">
            Vous pourrez en changer à tout moment depuis votre espace.
        </p>
        <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-amber-600">Étape 2/2 — Votre plan</p>
    </div>

    <div class="mt-10" x-data="{ cycle: 'monthly', plan: '{{ old('plan', 'free') }}' }">
        <form action="{{ route('tenant.register.plan.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="cycle" x-bind:value="cycle">
            <input type="hidden" name="plan" x-bind:value="plan">

            <!-- Toggle mensuel / annuel -->
            <div class="flex items-center justify-center gap-3 text-sm">
                <button type="button" @click="cycle = 'monthly'"
                    :class="cycle === 'monthly' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'"
                    class="px-4 py-2 rounded-full font-semibold transition-colors">Mensuel</button>
                <button type="button" @click="cycle = 'yearly'"
                    :class="cycle === 'yearly' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500'"
                    class="px-4 py-2 rounded-full font-semibold transition-colors">Annuel</button>
            </div>

            <div class="space-y-4">
                @foreach($plans as $planOption)
                    <div @click="plan = '{{ $planOption->slug }}'"
                        :class="plan === '{{ $planOption->slug }}' ? 'border-amber-500 ring-1 ring-amber-500 bg-amber-50/30' : 'border-slate-200 hover:border-slate-300'"
                        class="cursor-pointer rounded-xl border-[1px] px-5 py-4 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">{{ $planOption->name }}</span>
                            <span class="text-sm font-semibold text-slate-700">
                                <template x-if="cycle === 'monthly'">
                                    <span>{{ number_format($planOption->price_monthly, 0, ',', ' ') }} FCFA/mois</span>
                                </template>
                                <template x-if="cycle === 'yearly'">
                                    <span>{{ number_format($planOption->price_yearly, 0, ',', ' ') }} FCFA/an</span>
                                </template>
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $planOption->max_tunnels === null ? 'Tunnels illimités' : $planOption->max_tunnels . ' tunnels' }}
                            · {{ $planOption->max_leads }} leads
                            · {{ $planOption->max_mailing_lists }} liste(s) mailing
                        </p>
                    </div>
                @endforeach
            </div>

            @error('plan')
                <p class="text-sm text-red-600 font-medium">{{ $message }}</p>
            @enderror

            <div>
                <button type="submit"
                    class="group relative flex w-full justify-center border border-transparent bg-slate-900 px-4 py-4 text-sm font-bold text-white uppercase tracking-widest hover:bg-slate-800 transition-all duration-300 shadow-xl shadow-slate-200 active:translate-y-0.5">
                    <span class="relative flex items-center gap-2">
                        Créer mon espace
                        <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </button>
                <p class="mt-6 text-xs text-center text-slate-400">
                    Aucune carte bancaire requise pour l'instant — activation immédiate.
                </p>
            </div>
        </form>
    </div>
@endsection
