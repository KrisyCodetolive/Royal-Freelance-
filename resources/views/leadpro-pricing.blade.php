<x-layouts.leadpro
    title="Tarifs - Royal LeadPro"
    description="Des plans simples et transparents, du solo entrepreneur au workspace d'équipe complet. Commencez gratuitement, changez de plan à tout moment.">

    <!-- Navigation -->
    @include('components.leadpro.nav')

    <!-- En-tête de page -->
    <section class="pt-32 pb-8 lg:pt-44 lg:pb-12 bg-white dark:bg-zinc-950 text-center px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 dark:text-white mb-4">Tarifs</h1>
        <p class="text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto">
            Un plan pour chaque étape de votre croissance — sans engagement, changez à tout moment.
        </p>
    </section>

    <!-- Tarifs (dynamique) -->
    <x-leadpro.pricing :plans="$plans" />

    <!-- FAQ -->
    <x-leadpro.faq />

    <!-- CTA finale -->
    <x-leadpro.cta />

    <!-- Footer -->
    <x-leadpro.footer />

</x-layouts.leadpro>
