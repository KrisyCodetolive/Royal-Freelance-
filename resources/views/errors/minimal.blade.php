<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Royal LeadPro</title>

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

<body
    class="bg-slate-50 min-h-screen flex flex-col justify-center relative overflow-hidden font-sans antialiased text-slate-600">

    <!-- Background Patterns -->
    <div
        class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 z-0 mix-blend-overlay">
    </div>
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-400/20 rounded-full blur-3xl z-0 pointer-events-none">
    </div>
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-red-600/5 rounded-full blur-3xl z-0 pointer-events-none"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center">

        <!-- Logo -->
        <a href="/" class="mb-12 inline-block transition-transform hover:scale-105">
            <span class="text-2xl font-bold tracking-tight text-slate-900 font-serif">Royal LeadPro</span>
        </a>

        <!-- Main Content Box -->
        <div
            class="bg-white/80 backdrop-blur-sm border border-slate-200/60 shadow-xl shadow-slate-200/50 rounded-2xl p-8 sm:p-12 w-full max-w-lg">

            <div class="flex justify-center items-center mb-6">
                <!-- Error Code Badge -->
                <span
                    class="bg-red-50 text-red-600 font-bold px-4 py-1.5 rounded-full text-base sm:text-lg border border-red-100 uppercase tracking-widest shadow-sm">
                    Erreur @yield('code')
                </span>
            </div>

            <!-- Error Title -->
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-slate-900 mb-4 tracking-tight">
                @yield('message')
            </h1>

            <p class="text-slate-500 mb-8 leading-relaxed max-w-md mx-auto">
                @yield('description', 'Une erreur inattendue est survenue. Notre équipe a été informée.')
            </p>

            <!-- Action Button -->
            <a href="/"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 font-semibold text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition-all duration-200 shadow-lg shadow-slate-900/20 hover:shadow-slate-900/30 w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</body>

</html>