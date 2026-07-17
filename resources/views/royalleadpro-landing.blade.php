<x-layouts.leadpro>

    <!-- Navigation -->
    @include('components.leadpro.nav')

    <!-- Hero -->
    <x-leadpro.hero />

    <!-- Comment ça marche -->
    <x-leadpro.how />

    <!-- Fonctionnalités -->
    <x-leadpro.features />

    <!-- Tarifs (dynamique) -->
    <x-leadpro.pricing :plans="$plans" />

    <!-- FAQ -->
    <x-leadpro.faq />

    <!-- CTA finale -->
    <x-leadpro.cta />

    <!-- Footer -->
    <x-leadpro.footer />

</x-layouts.leadpro>
