<x-layouts.leadpro
    title="Fonctionnalités - Royal LeadPro"
    description="Tunnels de vente, capture et scoring de leads, séquences email automatisées, gestion d'équipe multi-rôles... Découvrez tout ce que Royal LeadPro offre pour transformer vos visiteurs en clients.">

    <!-- Navigation -->
    @include('components.leadpro.nav')

    <!-- En-tête de page -->
    <section class="pt-32 pb-8 lg:pt-44 lg:pb-12 bg-white dark:bg-zinc-950 text-center px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white mb-4">Fonctionnalités</h1>
        <p class="text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto">
            Tout ce dont vous avez besoin pour capturer, suivre et convertir vos prospects — dans un espace de
            travail pensé pour votre équipe.
        </p>
    </section>

    <!-- Fonctionnalités -->
    <x-leadpro.features />

    <!-- CTA finale -->
    <x-leadpro.cta />

    <!-- Footer -->
    <x-leadpro.footer />

</x-layouts.leadpro>
