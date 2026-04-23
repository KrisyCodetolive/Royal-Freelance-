@extends('auth.layouts.split')

@section('title', 'Inscription')

@section('content')
    <div>
        <h2 class="mt-2 text-3xl font-serif font-medium tracking-tight text-slate-900">
            Devenez partenaire.
        </h2>
        <p class="mt-2 text-sm text-slate-500">
            Démarrez votre aventure commerciale.
            <br>
            Déjà membre ?
            <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:text-amber-500 transition-colors underline decoration-amber-200 underline-offset-4">
                Connectez-vous à votre compte
            </a>
        </p>
    </div>

    <div class="mt-10">
        <div>
            <form action="{{ route('register.post') }}" method="POST" class="space-y-8">
                @csrf

                <div class="group relative">
                    <label for="name" class="absolute -top-2 left-3 bg-white px-1 text-xs font-medium text-amber-600">NOM COMPLET</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required
                            class="block w-full rounded-none border-[1px] border-slate-200 px-4 py-4 text-slate-700 shadow-sm placeholder:text-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm sm:leading-6 transition-all duration-300 bg-slate-50/30 group-hover:bg-white"
                            value="{{ old('name') }}"
                            placeholder="Jean Dupont">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="group relative">
                    <label for="shop_name" class="absolute -top-2 left-3 bg-white px-1 text-xs font-medium text-amber-600">NOM DE BOUTIQUE / MARQUE</label>
                    <div class="mt-1">
                        <input id="shop_name" name="shop_name" type="text" required
                            class="block w-full rounded-none border-[1px] border-slate-200 px-4 py-4 text-slate-700 shadow-sm placeholder:text-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm sm:leading-6 transition-all duration-300 bg-slate-50/30 group-hover:bg-white"
                            value="{{ old('shop_name') }}"
                            placeholder="Ex: Boutique Élégance">
                        @error('shop_name')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="group relative">
                    <label for="email" class="absolute -top-2 left-3 bg-white px-1 text-xs font-medium text-amber-600">EMAIL PROFESSIONNEL</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="block w-full rounded-none border-[1px] border-slate-200 px-4 py-4 text-slate-700 shadow-sm placeholder:text-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm sm:leading-6 transition-all duration-300 bg-slate-50/30 group-hover:bg-white"
                            value="{{ old('email') }}"
                            placeholder="votre@exemple.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-2">
                    <div class="group relative">
                        <label for="password" class="absolute -top-2 left-3 bg-white px-1 text-xs font-medium text-amber-600">MOT DE PASSE</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="block w-full rounded-none border-[1px] border-slate-200 px-4 py-4 text-slate-700 shadow-sm placeholder:text-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm sm:leading-6 transition-all duration-300 bg-slate-50/30 group-hover:bg-white"
                                placeholder="••••••••">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="group relative">
                        <label for="password_confirmation" class="absolute -top-2 left-3 bg-white px-1 text-xs font-medium text-amber-600">CONFIRMATION</label>
                        <div class="mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="block w-full rounded-none border-[1px] border-slate-200 px-4 py-4 text-slate-700 shadow-sm placeholder:text-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 sm:text-sm sm:leading-6 transition-all duration-300 bg-slate-50/30 group-hover:bg-white"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative flex w-full justify-center border border-transparent bg-slate-900 px-4 py-4 text-sm font-bold text-white uppercase tracking-widest hover:bg-slate-800 transition-all duration-300 shadow-xl shadow-slate-200 active:translate-y-0.5">
                        <span class="absolute inset-0 h-full w-full bg-gradient-to-r from-amber-200/0 via-amber-200/10 to-amber-200/0 opacity-0 transition-opacity duration-500 group-hover:opacity-100"></span>
                        <span class="relative flex items-center gap-2">
                             Créer mon compte
                             <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                             </svg>
                        </span>
                    </button>
                    <p class="mt-6 text-xs text-center text-slate-400">
                        En vous inscrivant, vous acceptez nos <a href="#" class="underline hover:text-amber-600 transition-colors">Conditions d'utilisation</a>.
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection