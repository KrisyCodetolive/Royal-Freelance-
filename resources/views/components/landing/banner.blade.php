<section
    class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 min-h-[500px] sm:min-h-[600px] md:min-h-0">
    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>

    <!-- Image Container -->
    <div class="relative min-h-[500px] sm:min-h-[600px] md:min-h-0">
        <img src="{{ asset('assets/banner.png') }}" alt="Coach Loukou KOFFI - Formateur en Vente"
            class="w-full h-full md:h-auto object-cover object-center min-h-[500px] sm:min-h-[600px] md:min-h-0"
            loading="lazy">
    </div>

    <!-- Overlay CTA -->
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/95 via-slate-900/70 to-slate-900/30 flex items-end">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 sm:pb-12 md:pb-16">
            <div class="max-w-3xl">
                <div
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-amber-500/90 backdrop-blur-sm text-white text-xs font-bold tracking-wider uppercase mb-3 sm:mb-4">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-[10px] sm:text-xs">Expert Reconnu</span>
                </div>
                <h2
                    class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white mb-3 sm:mb-4 leading-tight">
                    Transformez Votre Approche Commerciale
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-slate-200 mb-6 sm:mb-8 leading-relaxed">
                    Rejoignez les <strong class="text-amber-400">1000+ entrepreneurs</strong> qui ont déjà
                    transformé leur business grâce aux formations et outils de Coach Loukou KOFFI.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 text-sm sm:text-base font-bold text-slate-900 bg-amber-500 hover:bg-amber-400 rounded-xl shadow-2xl shadow-amber-500/30 transition-all duration-300 transform hover:-translate-y-1">
                        <span>COMMENCER MAINTENANT</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#coach"
                        class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-4 text-sm sm:text-base font-bold text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl transition-all duration-300">
                        <span>EN SAVOIR PLUS</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>