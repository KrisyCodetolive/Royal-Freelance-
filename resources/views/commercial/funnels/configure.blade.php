@extends('commercial.layouts.app')

@section('title', 'Configurer - ' . $funnel->name)
@section('header', 'Configuration du Tunnel')

@section('content')
    <div class="mb-8">
        <a href="{{ route('commercial.funnels') }}"
            class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-amber-600 transition-colors">
            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-xl font-sans font-bold text-slate-900 flex items-center gap-2">
                        <span class="p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                            <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.506.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </span>
                        {{ $funnel->name }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 ml-11">Personnalisez les redirections et messages pour ce tunnel.
                    </p>
                </div>

                <div class="px-6 py-6">
                    <form action="{{ route('commercial.funnel.configure.save', $funnel) }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Custom Slug -->
                        <div>
                            <label for="custom_slug" class="block text-sm font-semibold text-slate-700 mb-1">
                                URL personnalisée (Slug)
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">/f/</span>
                                </div>
                                <input type="text" name="custom_slug" id="custom_slug"
                                    value="{{ old('custom_slug', $config['custom_slug'] ?? '') }}"
                                    placeholder="mon-super-tunnel"
                                    class="block w-full rounded-lg border-gray-300 pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2.5">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                Personnalisez la fin de votre lien. Laisser vide pour utiliser l'identifiant par défaut.
                            </p>
                            @error('custom_slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shop URL -->
                        <div>
                            <label for="shop_url" class="block text-sm font-semibold text-slate-700 mb-1">
                                URL de redirection finale
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">🔗</span>
                                </div>
                                <input type="url" name="shop_url" id="shop_url"
                                    value="{{ old('shop_url', $config['shop_url']) }}"
                                    placeholder="https://votre-boutique.com/produit/123"
                                    class="block w-full rounded-lg border-gray-300 pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2.5">
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                C'est ici que vos prospects seront redirigés après avoir cliqué sur le bouton principal (ex:
                                "Commander").
                            </p>
                            @error('shop_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- WhatsApp Number -->
                            <div class="col-span-2">
                                <label for="whatsapp_redirect" class="block text-sm font-semibold text-slate-700 mb-1">
                                    Numéro WhatsApp pour ce tunnel
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="whatsapp_redirect" id="whatsapp_redirect"
                                        value="{{ old('whatsapp_redirect', $config['whatsapp_redirect']) }}"
                                        placeholder="+33 6 12 34 56 78"
                                        class="block w-full rounded-lg border-gray-300 pl-10 focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2.5">
                                </div>
                                <p class="mt-2 text-xs text-slate-500">
                                    Laisser vide pour utiliser votre numéro par défaut.
                                </p>
                                @error('whatsapp_redirect')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- WhatsApp Message -->
                            <div class="col-span-2">
                                <label for="whatsapp_message" class="block text-sm font-semibold text-slate-700 mb-1">
                                    Message WhatsApp pré-rempli
                                </label>
                                <textarea name="whatsapp_message" id="whatsapp_message" rows="3"
                                    placeholder="Bonjour, je suis intéressé par {{ $funnel->name }}..."
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-3 text-slate-700">{{ old('whatsapp_message', $config['whatsapp_message']) }}</textarea>
                                <p class="mt-2 text-xs text-slate-500">
                                    Astuce : Un message clair aide vos prospects à démarrer la conversation.
                                </p>
                                @error('whatsapp_message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center rounded-lg bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-slate-800 transition-all duration-200">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Sauvegarder la configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Share Link Card -->
            <div
                class="bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg rounded-xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>

                <h4 class="text-lg font-serif font-semibold mb-4 relative z-10 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-amber-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Votre Lien Unique
                </h4>

                @php
                    // Utiliser le slug personnalisé du commercial s'il existe, sinon le slug du tunnel
                    $customSlug = $config['custom_slug'] ?: $funnel->slug;
                    $commercialUrl = $user->subdomain
                        ? 'https://' . $user->subdomain . '.' . config('app.subdomain_base') . '/f/' . $customSlug
                        : url('/f/' . $customSlug . '?ref=' . $user->id);
                @endphp

                <div class="relative z-10">
                    <div
                        class="flex rounded-lg shadow-sm bg-white/10 border border-white/20 overflow-hidden backdrop-blur-sm">
                        <input type="text" id="commercialUrlInput" readonly value="{{ $commercialUrl }}"
                            class="flex-1 min-w-0 block w-full border-none bg-transparent text-sm text-white placeholder-white/50 focus:ring-0">
                        <button type="button" id="copyButton" onclick="copyCommercialUrl()"
                            class="inline-flex items-center px-4 py-2 border-l border-white/20 text-white hover:bg-white/10 transition-colors">
                            <span id="copyIconNormal">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <span id="copyIconSuccess" class="hidden text-emerald-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <script>
                    function copyCommercialUrl() {
                        const input = document.getElementById('commercialUrlInput');
                        const btn = document.getElementById('copyButton');
                        const normalIcon = document.getElementById('copyIconNormal');
                        const successIcon = document.getElementById('copyIconSuccess');

                        input.select();
                        input.setSelectionRange(0, 99999);

                        try {
                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(input.value);
                            } else {
                                document.execCommand('copy');
                            }

                            // Visual Feedback
                            normalIcon.classList.add('hidden');
                            successIcon.classList.remove('hidden');

                            setTimeout(() => {
                                normalIcon.classList.remove('hidden');
                                successIcon.classList.add('hidden');
                            }, 2000);
                        } catch (err) {
                            console.error('Erreur de copie:', err);
                            alert('Erreur lors de la copie du lien');
                        }
                    }
                </script>

                <p class="mt-4 text-xs text-amber-100 leading-relaxed relative z-10">
                    ⚠️ Partagez <strong class="text-white">toujours</strong> ce lien précis. Si vous utilisez un autre lien,
                    les leads ne vous seront pas attribués.
                </p>

                <div class="mt-4 pt-4 border-t border-white/20 flex justify-center relative z-10">
                    <a href="{{ $commercialUrl }}" target="_blank"
                        class="flex items-center text-sm font-medium text-white hover:text-amber-100 transition-colors">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        </svg>
                        Tester le lien en direct
                    </a>
                </div>
            </div>

            <!-- Stats & Actions Card -->
            @php
                $pivot = $user->usableFunnels()->where('funnel_id', $funnel->id)->first()?->pivot;
                $myLeadsCount = \App\Models\Lead::where('brought_by', $user->id)
                    ->where('funnel_id', $funnel->id)
                    ->count();
                $conversionRate = $pivot && $pivot->leads_count > 0
                    ? round(($myLeadsCount / $pivot->leads_count) * 100, 1)
                    : 0;
            @endphp

            <div class="bg-white shadow-sm border border-slate-100 rounded-xl overflow-hidden">
                <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wide flex items-center">
                        <svg class="h-4 w-4 mr-2 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        Performance du Lien
                    </h4>
                </div>

                <div class="p-6">
                    @if($config['is_active'])
                        <!-- Statistiques -->
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <!-- Vues/Clics -->
                            <div class="p-3 bg-slate-50 rounded-lg text-center border border-slate-100">
                                <dt class="text-xs text-slate-500 font-medium uppercase mb-1">Clics</dt>
                                <dd class="text-2xl font-bold text-slate-900">
                                    {{ $pivot->leads_count ?? 0 }}
                                </dd>
                            </div>

                            <!-- Leads générés -->
                            <div class="p-3 bg-amber-50 rounded-lg text-center border border-amber-200">
                                <dt class="text-xs text-amber-600 font-medium uppercase mb-1">Leads</dt>
                                <dd class="text-2xl font-bold text-amber-700">
                                    {{ $myLeadsCount }}
                                </dd>
                            </div>

                            <!-- Taux de conversion -->
                            <div class="p-3 bg-emerald-50 rounded-lg text-center border border-emerald-200">
                                <dt class="text-xs text-emerald-600 font-medium uppercase mb-1">Taux</dt>
                                <dd class="text-2xl font-bold text-emerald-700">
                                    {{ $conversionRate }}%
                                </dd>
                            </div>
                        </div>

                        <!-- Bouton Voir mes Leads -->
                        @if($myLeadsCount > 0)
                            <a href="{{ route('commercial.leads', ['funnel_id' => $funnel->id]) }}"
                                class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-md hover:from-amber-600 hover:to-amber-700 transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <span>Voir mes {{ $myLeadsCount }} Lead{{ $myLeadsCount > 1 ? 's' : '' }}</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <p class="mt-2 text-sm text-slate-500">
                                    Aucun lead pour l'instant
                                </p>
                                <p class="mt-1 text-xs text-slate-400">
                                    Partagez votre lien unique pour commencer !
                                </p>
                            </div>
                        @endif
                    @else
                        <!-- Tunnel non activé -->
                        <div class="text-center py-6">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532 3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0 1 12 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 0 1-4.132 5.411m0 0L21 21" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-600 font-medium">Tunnel non activé</p>
                            <p class="text-xs text-slate-400 mt-1">Configurez et sauvegardez pour activer</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection