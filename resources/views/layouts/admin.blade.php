<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FinTrack') }} - Administration</title>

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

    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Scripts & Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }
        .bento-item {
            border-radius: 1rem;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
            padding: 24px;
            transition: all 0.2s ease-in-out;
        }
        .bento-item:hover {
            box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
</head>
<body class="bg-[#f8f9ff] dark:bg-[#0b1c30] text-[#0b1c30] dark:text-[#f8f9ff] font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Top AppBar -->
    <header class="bg-white dark:bg-[#1a2b3c] border-b border-slate-200 dark:border-slate-800 shadow-sm fixed top-0 left-0 right-0 h-16 z-40 flex justify-between items-center px-4 md:px-8 transition-colors duration-200">
        <div class="flex items-center gap-4">
            <span class="font-bold text-2xl text-[#00288e] dark:text-[#b8c4ff]">FinTrack</span>
            <span class="bg-[#eff4ff] dark:bg-[#1e40af] text-[#00288e] dark:text-[#b8c4ff] text-xs font-semibold px-2.5 py-0.5 rounded">Admin</span>
            
            <!-- Desktop Top Menu Links -->
            <div class="hidden md:flex ml-8 items-center gap-6">
                <a class="text-sm font-medium hover:text-[#00288e] dark:hover:text-[#b8c4ff] transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-[#00288e] dark:text-[#b8c4ff] font-semibold' : 'text-slate-500 dark:text-slate-400' }}" href="{{ route('admin.dashboard') }}">Vue Globale</a>
                <a class="text-sm font-medium hover:text-[#00288e] dark:hover:text-[#b8c4ff] transition-colors {{ request()->routeIs('admin.users') ? 'text-[#00288e] dark:text-[#b8c4ff] font-semibold' : 'text-slate-500 dark:text-slate-400' }}" href="{{ route('admin.users') }}">Utilisateurs</a>
                <a class="text-sm font-medium hover:text-[#00288e] dark:hover:text-[#b8c4ff] transition-colors {{ request()->routeIs('admin.settings') ? 'text-[#00288e] dark:text-[#b8c4ff] font-semibold' : 'text-slate-500 dark:text-slate-400' }}" href="{{ route('admin.settings') }}">Configuration</a>
                <a class="text-sm font-medium hover:text-[#00288e] dark:hover:text-[#b8c4ff] transition-colors {{ request()->routeIs('admin.logs') ? 'text-[#00288e] dark:text-[#b8c4ff] font-semibold' : 'text-slate-500 dark:text-slate-400' }}" href="{{ route('admin.logs') }}">Logs Système</a>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors active:scale-95 duration-100">
                <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
            </button>

            <!-- Notifications Dropdown Placeholder -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="material-symbols-outlined p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors active:scale-95 duration-100">
                    notifications
                </button>
                <!-- Simple alerts dropdown -->
                <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-[#1a2b3c] border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-2 z-50">
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <span class="font-bold text-sm">Alertes Système</span>
                        <span class="bg-[#ffdad6] text-[#ba1a1a] text-[10px] font-bold px-2 py-0.5 rounded-full">2 Crises</span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-60 overflow-y-auto">
                        <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <p class="text-xs font-semibold text-[#ba1a1a]">Tentatives de connexion suspectes</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">IP 192.168.1.104 bloquée après 5 échecs.</p>
                            <span class="text-[9px] text-slate-400">Il y a 2 min</span>
                        </div>
                        <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <p class="text-xs font-semibold text-[#ba1a1a]">Latence base de données critique</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Délai de réplication supérieur à 300s.</p>
                            <span class="text-[9px] text-slate-400">Il y a 35 min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Info & Logout -->
            <div class="flex items-center gap-3 border-l border-slate-200 dark:border-slate-800 pl-4">
                <div class="hidden lg:block text-right">
                    <p class="text-xs font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Admin Principal</p>
                </div>
                <div class="w-8 h-8 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700">
                    <img alt="User Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCVtXQ9PUEkWzRdRpD30sZImAzsesYy62UvDi-GS0dHdWXAj5oRJjhhKDGRKenEpQ1aUqyqUvlRTIZfli3kQgU7gSt0cM53oWDTRh3eXEi1YaVXcKrUsFRc8RaZ3aoLcL2EF0H5vXi6joObOWhiNefG8I0lAlFZQlfGuQYFjYQX8aYeHR_ARQWv4dm9K63DN7Nd_nK2Lcip5IlQed1qy0hawjwync3repy4ecXHkMNumfagQRydlczI3FHfN6zf6GmDHOulTQJ61KgQ"/>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-[#ba1a1a] rounded-full transition-colors active:scale-95 duration-100">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Sidebar & Content Wrapper -->
    <div class="flex flex-1 pt-16 pb-20 md:pb-0">
        
        <!-- Desktop Sidebar Navigation -->
        <aside class="hidden lg:block w-64 bg-white dark:bg-[#1a2b3c] border-r border-slate-200 dark:border-slate-800 fixed top-16 bottom-0 left-0 transition-colors duration-200">
            <div class="p-6">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Navigation Admin</p>
                <nav class="space-y-1">
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-[#00288e]/10 text-[#00288e] dark:text-[#b8c4ff] dark:bg-[#b8c4ff]/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300' }}" href="{{ route('admin.dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        Console d'Accueil
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium text-sm {{ request()->routeIs('admin.users') ? 'bg-[#00288e]/10 text-[#00288e] dark:text-[#b8c4ff] dark:bg-[#b8c4ff]/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300' }}" href="{{ route('admin.users') }}">
                        <span class="material-symbols-outlined">group</span>
                        Gestion Utilisateurs
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium text-sm {{ request()->routeIs('admin.settings') ? 'bg-[#00288e]/10 text-[#00288e] dark:text-[#b8c4ff] dark:bg-[#b8c4ff]/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300' }}" href="{{ route('admin.settings') }}">
                        <span class="material-symbols-outlined">settings</span>
                        Paramètres Plateforme
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium text-sm {{ request()->routeIs('admin.logs') ? 'bg-[#00288e]/10 text-[#00288e] dark:text-[#b8c4ff] dark:bg-[#b8c4ff]/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300' }}" href="{{ route('admin.logs') }}">
                        <span class="material-symbols-outlined">receipt_long</span>
                        Logs & Monitoring
                    </a>
                </nav>
            </div>
            
            <div class="absolute bottom-6 left-6 right-6 p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl">
                <span class="text-xs font-semibold block text-slate-400">Statut Système</span>
                <span class="inline-flex items-center gap-1.5 mt-2 bg-[#6cf8bb]/20 dark:bg-[#005236]/30 text-[#006c49] dark:text-[#4edea3] text-[11px] font-bold px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#006c49] dark:bg-[#4edea3] animate-pulse"></span>
                    Serveur Connecté
                </span>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64 px-4 py-8 md:px-8 max-w-7xl mx-auto w-full">
            
            <!-- Alert Display -->
            @if(session('success'))
                <div class="bg-[#6cf8bb]/15 border border-[#006c49]/30 text-[#006c49] p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-[#ffdad6]/20 border border-[#ba1a1a]/30 text-[#ba1a1a] p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Page Content Slot -->
            {{ $slot }}

            <!-- Footer -->
            <footer class="mt-16 py-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                    Réalisé par Dynamic Technology Group
                </p>
            </footer>
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-20 bg-white dark:bg-[#1a2b3c] border-t border-slate-200 dark:border-slate-800 z-40 flex justify-around items-center px-4 rounded-t-2xl shadow-lg transition-colors duration-200">
        <a class="flex flex-col items-center justify-center gap-1 transition-all text-xs font-semibold {{ request()->routeIs('admin.dashboard') ? 'text-[#00288e] dark:text-[#b8c4ff]' : 'text-slate-400 dark:text-slate-500' }}" href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Console</span>
        </a>
        <a class="flex flex-col items-center justify-center gap-1 transition-all text-xs font-semibold {{ request()->routeIs('admin.users') ? 'text-[#00288e] dark:text-[#b8c4ff]' : 'text-slate-400 dark:text-slate-500' }}" href="{{ route('admin.users') }}">
            <span class="material-symbols-outlined">group</span>
            <span>Utilisateurs</span>
        </a>
        <a class="flex flex-col items-center justify-center gap-1 transition-all text-xs font-semibold {{ request()->routeIs('admin.settings') ? 'text-[#00288e] dark:text-[#b8c4ff]' : 'text-slate-400 dark:text-slate-500' }}" href="{{ route('admin.settings') }}">
            <span class="material-symbols-outlined">settings</span>
            <span>Réglages</span>
        </a>
        <a class="flex flex-col items-center justify-center gap-1 transition-all text-xs font-semibold {{ request()->routeIs('admin.logs') ? 'text-[#00288e] dark:text-[#b8c4ff]' : 'text-slate-400 dark:text-slate-500' }}" href="{{ route('admin.logs') }}">
            <span class="material-symbols-outlined">receipt_long</span>
            <span>Logs</span>
        </a>
    </nav>

</body>
</html>
