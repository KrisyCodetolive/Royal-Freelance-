<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunnel indisponible | Royal LeadPro</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700|playfair-display:400,500,600,700"
        rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col justify-center relative overflow-hidden">

    <!-- Background Patterns -->
    <div
        class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0 mix-blend-overlay">
    </div>
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-400/10 rounded-full blur-3xl z-0"></div>
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-slate-400/10 rounded-full blur-3xl z-0"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Icon / Visual -->
        <div
            class="mx-auto h-24 w-24 bg-white rounded-full shadow-xl shadow-red-900/5 flex items-center justify-center mb-8 border border-red-100">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                </path>
            </svg>
        </div>

        <!-- Typography -->
        <h1 class="font-serif text-4xl sm:text-5xl font-bold text-slate-900 mb-6 tracking-tight">
            Ce tunnel est momentanément <br />
            <span class="text-red-500">indisponible</span>
        </h1>

        <p class="text-lg text-slate-600 mb-4 max-w-xl mx-auto leading-relaxed">
            L'espace auquel appartient cette page a été suspendu. Le propriétaire ne peut plus publier de tunnel
            pour le moment.
        </p>

        <p class="text-base text-slate-500 mb-10 max-w-xl mx-auto leading-relaxed">
            Vous pensez qu'il s'agit d'une erreur ou souhaitez faire une réclamation&nbsp;? Écrivez-nous à
            <a href="mailto:support@royalleadpro.com"
                class="font-semibold text-slate-900 underline decoration-red-300 decoration-2 underline-offset-4 hover:text-red-500 transition-colors">support@royalleadpro.com</a>.
        </p>

        <!-- Logo Footer -->
        <div class="pt-8 border-t border-slate-200/60 mt-8">
            <div class="flex items-center justify-center gap-2 opacity-60">
                <span class="text-sm font-medium text-slate-500">Propulsé par</span>
                <span class="text-base font-bold tracking-tight text-slate-900 font-serif">Royal LeadPro</span>
            </div>
        </div>
    </div>
</body>

</html>
