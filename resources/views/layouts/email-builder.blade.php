<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Builder</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    <style>
        :root {
            --sidebar-width: 260px;
            --panel-width: 300px;
            --header-height: 52px;
        }

        .eb-scrollbar::-webkit-scrollbar { width: 5px; }
        .eb-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .eb-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px; }
        .eb-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        .block-wrapper { transition: outline 0.15s ease; cursor: pointer; }
        .block-wrapper:hover { outline: 2px solid rgba(99,102,241,0.5); outline-offset: 2px; }
        .block-wrapper.selected { outline: 2px solid #6366f1; outline-offset: 2px; }

        .sortable-ghost { opacity: 0.35; background: #4f46e5; border-radius: 4px; }

        .email-preview {
            background: #f3f4f6;
            min-height: calc(100vh - var(--header-height));
        }

        .email-canvas {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 400px;
        }
    </style>
</head>

<body class="h-full bg-gray-900 text-white font-['Inter'] antialiased">
    {{ $slot }}

    @livewireScripts
</body>

</html>
