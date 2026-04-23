<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Builder - {{ $page->title ?? 'Éditeur' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Sortable.js for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Quill.js for Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        :root {
            --builder-sidebar-width: 280px;
            --builder-panel-width: 320px;
            --builder-header-height: 56px;
        }

        .builder-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .builder-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .builder-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        .builder-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        .block-wrapper {
            transition: all 0.2s ease;
        }

        .block-wrapper:hover {
            outline: 2px solid rgba(59, 130, 246, 0.5);
        }

        .block-wrapper.selected {
            outline: 2px solid #3b82f6;
        }

        .preview-desktop {
            max-width: 100%;
        }

        .preview-tablet {
            max-width: 768px;
        }

        .preview-mobile {
            max-width: 375px;
        }

        /* Animations */
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slide-up {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoom-in {
            from { 
                opacity: 0;
                transform: scale(0.95);
            }
            to { 
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        [data-animation="fade-in"] {
            animation: fade-in 0.6s ease-out;
        }

        [data-animation="slide-up"] {
            animation: slide-up 0.6s ease-out;
        }

        [data-animation="zoom-in"] {
            animation: zoom-in 0.6s ease-out;
        }

        [data-animation="bounce"] {
            animation: bounce 1s ease-in-out infinite;
        }

        /* Quill Editor Dark Theme */
        .ql-toolbar.ql-snow {
            background: #374151;
            border: 1px solid #4b5563 !important;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .ql-container.ql-snow {
            background: #1f2937;
            border: 1px solid #4b5563 !important;
            border-top: none !important;
            border-radius: 0 0 0.5rem 0.5rem;
            color: #e5e7eb;
        }

        .ql-editor {
            min-height: 300px;
            font-size: 14px;
        }

        .ql-editor.ql-blank::before {
            color: #9ca3af;
        }

        .ql-snow .ql-stroke {
            stroke: #9ca3af;
        }

        .ql-snow .ql-fill {
            fill: #9ca3af;
        }

        .ql-snow .ql-picker-label {
            color: #9ca3af;
        }

        .ql-toolbar.ql-snow .ql-picker-label:hover,
        .ql-toolbar.ql-snow .ql-picker-label.ql-active,
        .ql-toolbar.ql-snow button:hover,
        .ql-toolbar.ql-snow button.ql-active {
            color: #60a5fa;
        }

        .ql-toolbar.ql-snow .ql-picker-label:hover .ql-stroke,
        .ql-toolbar.ql-snow button:hover .ql-stroke,
        .ql-toolbar.ql-snow button.ql-active .ql-stroke {
            stroke: #60a5fa;
        }

        /* Sortable Ghost */
        .sortable-ghost {
            opacity: 0.4;
            background: #3b82f6;
        }

        /* Custom scrollbar for Quill */
        .ql-editor::-webkit-scrollbar {
            width: 6px;
        }

        .ql-editor::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .ql-editor::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }
    </style>
</head>

<body class="h-full bg-gray-900 text-white font-['Inter'] antialiased">
    {{ $slot }}

    <!-- Livewire Scripts -->
    @livewireScripts
</body>

</html>