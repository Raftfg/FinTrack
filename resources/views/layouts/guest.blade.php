<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FinTrack') }} - Authentification</title>

        <!-- Dark Mode Flash Prevention Script -->
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts / Icons -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: radial-gradient(circle at 100% 0%, #e5eeff 0%, #f8f9ff 50%, #eff4ff 100%);
                font-family: 'Inter', sans-serif;
                min-height: 100vh;
            }
            .dark body {
                background: radial-gradient(circle at 100% 0%, #0c1a30 0%, #070e1b 50%, #050a14 100%);
            }
        </style>
    </head>
    <body class="text-on-background antialiased flex items-center justify-center p-6 md:p-12 selection:bg-primary-fixed selection:text-primary overflow-x-hidden relative">
        <!-- Animated Atmospheric Background Blobs -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-primary/5 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] rounded-full bg-secondary/5 blur-[120px]"></div>
        </div>

        {{ $slot }}
    </body>
</html>
