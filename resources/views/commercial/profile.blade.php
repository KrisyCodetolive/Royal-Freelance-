@extends('commercial.layouts.app')

@section('title', 'Mon Profil')
@section('header', 'Mon Profil')

@section('content')
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
                <div class="px-6 py-8">
                    <h3 class="text-lg font-serif font-semibold text-slate-900 mb-6">Informations Personnelles</h3>
                    
                    <form action="{{ route('commercial.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Avatar -->
                        <div class="bg-slate-50 p-6 rounded-lg border border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-4">Photo de profil</label>
                            <div class="flex items-center gap-6">
                                <div class="relative group">
                                    <div class="h-24 w-24 rounded-full overflow-hidden border-4 border-white shadow-md">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full bg-slate-200 flex items-center justify-center text-slate-400 font-bold text-2xl">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute inset-0 rounded-full border border-black/5"></div>
                                </div>
                                
                                <div class="flex-1">
                                    <input type="file" name="avatar" accept="image/*"
                                        class="block w-full text-sm text-slate-500
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-amber-50 file:text-amber-700
                                        hover:file:bg-amber-100
                                        transition-colors cursor-pointer">
                                    <p class="mt-2 text-xs text-slate-500">JPG, GIF ou PNG. 1MB max.</p>
                                </div>
                            </div>
                            @error('avatar')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Shop Name -->
                            <div class="col-span-2">
                                <label for="shop_name" class="block text-sm font-medium text-slate-700">Nom de la boutique / Marque</label>
                                <div class="mt-2 relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 001-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="shop_name" id="shop_name"
                                        value="{{ old('shop_name', $user->shop_name) }}" 
                                        class="block w-full rounded-lg border-gray-300 pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2.5"
                                        placeholder="Ma Boutique Pro">
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Ce nom apparaîtra en haut de vos tunnels de vente.</p>
                                @error('shop_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- WhatsApp -->
                            <div class="col-span-2">
                                <label for="whatsapp_number" class="block text-sm font-medium text-slate-700">Numéro WhatsApp Business</label>
                                <div class="mt-2 relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="whatsapp_number" id="whatsapp_number"
                                        value="{{ old('whatsapp_number', $user->whatsapp_number) }}" 
                                        class="block w-full rounded-lg border-gray-300 pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2.5"
                                        placeholder="+33 6 12 34 56 78">
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Indispensable pour que vos prospects vous contactent directement.</p>
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Bio -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-slate-700">Bio / Message de bienvenue</label>
                            <div class="mt-2">
                                <textarea name="bio" id="bio" rows="4" 
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm p-3"
                                    placeholder="Ex: Passionné par l'immobilier, je vous aide à trouver le bien de vos rêves...">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">S'affiche sur votre page de profil publique si activée.</p>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-slate-800 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 transition-all duration-200">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info & Stats -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Account Info -->
            <div class="bg-white shadow-sm border border-slate-100 rounded-xl p-6">
                <h4 class="text-lg font-serif font-semibold text-slate-900 mb-4">Votre Compte</h4>

                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-slate-50 rounded-lg">
                        <div class="flex-shrink-0 mr-3">
                             <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Nom Complet</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 bg-slate-50 rounded-lg">
                         <div class="flex-shrink-0 mr-3">
                             <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $user->email }}</p>
                        </div>
                    </div>

                    @if($user->subdomain)
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-lg">
                            <p class="text-xs font-bold text-amber-600 uppercase tracking-wide mb-1">Votre Sous-domaine</p>
                            <div class="flex items-center justify-between">
                                <a href="http://{{ $user->subdomain }}.{{ config('app.subdomain_base', 'localhost') }}" target="_blank" class="text-sm font-bold text-slate-900 hover:text-amber-600 transition-colors">
                                    {{ $user->subdomain }}.{{ config('app.subdomain_base') }}
                                </a>
                                <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-slate-100 rounded-lg text-center">
                            <p class="text-sm text-slate-500">Pas de sous-domaine configuré.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-indigo-900 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
                <h4 class="text-lg font-serif font-semibold mb-6 relative z-10">Performance Globale</h4>

                @php
                    $stats = $user->getCommercialStats();
                @endphp

                <div class="grid grid-cols-2 gap-4 relative z-10">
                    <div class="bg-indigo-800/50 p-3 rounded-lg backdrop-blur-sm border border-indigo-700/50">
                        <p class="text-xs text-indigo-200 uppercase">Leads</p>
                        <p class="text-2xl font-bold">{{ $stats['total_leads'] }}</p>
                    </div>
                    <div class="bg-emerald-500/20 p-3 rounded-lg backdrop-blur-sm border border-emerald-500/30">
                        <p class="text-xs text-emerald-100 uppercase">Convertis</p>
                        <p class="text-2xl font-bold text-emerald-300">{{ $stats['conversions'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection