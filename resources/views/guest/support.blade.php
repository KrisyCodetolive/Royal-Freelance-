@extends('guest.layout')

@section('title', 'Support & Contact')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-serif font-medium text-slate-900 mb-4">Comment pouvons-nous vous aider ?</h1>
            <p class="text-xl text-slate-500">Notre équipe dédiée est là pour répondre à toutes vos questions.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 items-center text-center">
                <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Email</h3>
                <p class="text-slate-500 mb-4">Pour les demandes générales et techniques.</p>
                <a href="mailto:support@royalleadpro.com"
                    class="text-amber-600 font-medium hover:underline">support@royalleadpro.com</a>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 items-center text-center">
                <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Live Chat</h3>
                <p class="text-slate-500 mb-4">Disponible du lundi au vendredi, 9h-18h.</p>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    En ligne
                </span>
            </div>
        </div>

        <div class="bg-slate-50 rounded-2xl p-8 border border-slate-200">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Envoyez-nous un message</h3>
            <form action="#" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nom complet</label>
                        <input type="text"
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email"
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Sujet</label>
                    <input type="text"
                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea rows="4"
                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"></textarea>
                </div>
                <div>
                    <button type="button"
                        class="inline-flex justify-center rounded-md border border-transparent bg-slate-900 py-3 px-6 text-sm font-medium text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                        Envoyer le message
                    </button>
                    <p class="text-xs text-slate-400 mt-2">*Ce formulaire est une démo visuelle.</p>
                </div>
            </form>
        </div>
    </div>
@endsection