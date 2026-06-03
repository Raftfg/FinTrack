<x-admin-layout>
    <!-- Page Header -->
    <header class="mb-10">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Paramètres de la Plateforme</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Configurez les variables globales et les règles de sécurité de l'écosystème FinTrack.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Navigation Anchors (Desktop only) -->
        <aside class="hidden lg:block lg:col-span-3">
            <nav class="sticky top-24 space-y-1.5" x-data="{ activeSection: 'general' }">
                <a @click="activeSection = 'general'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all"
                   :class="activeSection === 'general' ? 'bg-[#00288e] text-white' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300'"
                   href="#general">
                    <span class="material-symbols-outlined">settings</span> Configuration Générale
                </a>
                <a @click="activeSection = 'security'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all"
                   :class="activeSection === 'security' ? 'bg-[#00288e] text-white' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300'"
                   href="#security">
                    <span class="material-symbols-outlined">security</span> Sécurité & Accès
                </a>
                <a @click="activeSection = 'financial'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all"
                   :class="activeSection === 'financial' ? 'bg-[#00288e] text-white' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300'"
                   href="#financial">
                    <span class="material-symbols-outlined">payments</span> Règles Financières
                </a>
                <a @click="activeSection = 'danger'" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                   :class="activeSection === 'danger' ? 'bg-red-50 dark:bg-red-950/20' : 'hover:bg-red-50 dark:hover:bg-red-950/10'"
                   href="#danger">
                    <span class="material-symbols-outlined">warning</span> Zone de Danger
                </a>
            </nav>
        </aside>

        <!-- Right Column: Settings Canvas -->
        <div class="lg:col-span-9 space-y-10">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-10">
                @csrf
                @honeypot

                <!-- Section: General Settings -->
                <section class="bg-white dark:bg-[#1a2b3c] rounded-2xl shadow-sm p-6 border border-slate-100 dark:border-slate-800" id="general">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-[#00288e]/10 text-[#00288e] dark:bg-[#b8c4ff]/10 dark:text-[#b8c4ff] rounded-lg">
                            <span class="material-symbols-outlined block">dns</span>
                        </div>
                        <h2 class="text-lg font-bold">Configuration Générale</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Nom de la Plateforme <span class="text-red-500 font-bold">*</span></label>
                            <input name="platform_name" required maxlength="255" value="{{ $settings['platform_name'] }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="text"/>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Email de Support <span class="text-red-500 font-bold">*</span></label>
                            <input name="support_email" required maxlength="255" value="{{ $settings['support_email'] }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="email"/>
                        </div>
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Description de la Plateforme <span class="text-red-500 font-bold">*</span></label>
                            <textarea name="platform_description" required maxlength="1000" class="w-full p-4 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" rows="3">{{ $settings['platform_description'] }}</textarea>
                        </div>
                    </div>
                </section>

                <!-- Section: Security Policy -->
                <section class="bg-white dark:bg-[#1a2b3c] rounded-2xl shadow-sm p-6 border border-slate-100 dark:border-slate-800" id="security">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#93000a]/20 dark:text-[#ffdad6] rounded-lg">
                            <span class="material-symbols-outlined block">lock_person</span>
                        </div>
                        <h2 class="text-lg font-bold">Politique de Sécurité</h2>
                    </div>
                    <div class="space-y-6">
                        <!-- Toggle Row: 2FA -->
                        <div class="flex items-center justify-between py-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-sm font-semibold mb-1">Double Authentification Obligatoire (2FA)</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Exiger le 2FA pour tous les comptes d'administrateurs et modérateurs.</p>
                            </div>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="mandatory_2fa" value="1" class="sr-only peer" {{ $settings['mandatory_2fa'] ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-[#00288e] dark:peer-checked:bg-[#b8c4ff]"></div>
                            </label>
                        </div>

                        <!-- Toggle Row: Timeout -->
                        <div class="flex items-center justify-between py-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <p class="text-sm font-semibold mb-1">Timeout de Session Inactive</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Déconnecter automatiquement les sessions inactives après 15 minutes.</p>
                            </div>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="session_timeout" value="1" class="sr-only peer" {{ $settings['session_timeout'] ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-[#00288e] dark:peer-checked:bg-[#b8c4ff]"></div>
                            </label>
                        </div>

                        <!-- Inputs Rows -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Longueur min. mot de passe <span class="text-red-500 font-bold">*</span></label>
                                <select name="min_password_length" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                    <option value="8" {{ $settings['min_password_length'] == 8 ? 'selected' : '' }}>8 caractères</option>
                                    <option value="12" {{ $settings['min_password_length'] == 12 ? 'selected' : '' }}>12 caractères</option>
                                    <option value="16" {{ $settings['min_password_length'] == 16 ? 'selected' : '' }}>16 caractères</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Expiration mot de passe (Jours) <span class="text-red-500 font-bold">*</span></label>
                                <input name="password_expiry" required min="1" max="365" step="1" value="{{ $settings['password_expiry'] }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="number"/>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section: Financial Rules -->
                <section class="bg-white dark:bg-[#1a2b3c] rounded-2xl shadow-sm p-6 border border-slate-100 dark:border-slate-800" id="financial">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-[#6cf8bb]/20 text-[#006c49] dark:bg-[#005236]/20 dark:text-[#4edea3] rounded-lg">
                            <span class="material-symbols-outlined block">account_balance</span>
                        </div>
                        <h2 class="text-lg font-bold">Règles Financières</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Limite Journalière (USD/XOF) <span class="text-red-500 font-bold">*</span></label>
                            <input name="daily_transaction_limit" required min="0.01" step="0.01" value="{{ $settings['daily_transaction_limit'] }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="number"/>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Devise de Référence <span class="text-red-500 font-bold">*</span></label>
                            <select name="default_currency" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                <option value="USD" {{ $settings['default_currency'] == 'USD' ? 'selected' : '' }}>USD - Dollar Américain</option>
                                <option value="EUR" {{ $settings['default_currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="XOF" {{ $settings['default_currency'] == 'XOF' ? 'selected' : '' }}>XOF - Franc CFA (BCEAO)</option>
                                <option value="GBP" {{ $settings['default_currency'] == 'GBP' ? 'selected' : '' }}>GBP - Livre Sterling</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Devises Actives</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($settings['enabled_currencies'] as $currency)
                                <span class="inline-flex items-center gap-1 bg-[#00288e] text-white text-xs px-3.5 py-1.5 rounded-full font-semibold">
                                    {{ $currency }}
                                    <input type="hidden" name="enabled_currencies[]" value="{{ $currency }}"/>
                                    <span class="material-symbols-outlined text-xs cursor-pointer opacity-70 hover:opacity-100">close</span>
                                </span>
                            @endforeach
                            <button type="button" class="inline-flex items-center gap-1 border border-dashed border-[#00288e] text-[#00288e] dark:border-[#b8c4ff] dark:text-[#b8c4ff] text-xs px-3.5 py-1.5 rounded-full font-semibold hover:bg-[#00288e]/10 transition-colors">
                                + Ajouter Devises
                            </button>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row gap-4 justify-between items-center">
                        <div class="flex items-center gap-2 text-[#00288e] dark:text-[#b8c4ff]">
                            <span class="material-symbols-outlined text-[#00288e] dark:text-[#b8c4ff] text-[18px]">info</span>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Les changements de règles monétaires peuvent nécessiter jusqu'à 24h pour être répercutés.</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.settings') }}" class="px-6 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Réinitialiser</a>
                            <button type="submit" class="px-8 py-2.5 bg-[#00288e] hover:bg-[#1e40af] text-white rounded-xl text-xs font-semibold shadow-md">Enregistrer</button>
                        </div>
                    </div>
                </section>
            </form>

            <!-- Section: Danger Zone (Separated since they are direct post actions) -->
            <section class="bg-red-50/50 dark:bg-red-950/10 rounded-2xl p-6 border border-red-200/50 dark:border-red-900/30" id="danger">
                <div class="flex items-center gap-3 mb-4 text-[#ba1a1a]">
                    <span class="material-symbols-outlined text-[24px]">warning</span>
                    <h2 class="text-lg font-bold">Zone de Danger</h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Actions irréversibles qui impactent directement les données d'exploitation et la configuration de la plateforme.</p>
                <div class="flex flex-wrap gap-4">
                    <form method="POST" action="#" onsubmit="alert('Cache vidé avec succès !'); return false;" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 border border-red-300 dark:border-red-800 hover:bg-red-100/50 dark:hover:bg-red-950/20 text-[#ba1a1a] rounded-xl text-xs font-semibold transition-colors">Vider le Cache Système</button>
                    </form>
                    <form method="POST" action="#" onsubmit="alert('Mode maintenance activé.'); return false;" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 border border-red-300 dark:border-red-800 hover:bg-red-100/50 dark:hover:bg-red-950/20 text-[#ba1a1a] rounded-xl text-xs font-semibold transition-colors">Activer le Mode Maintenance</button>
                    </form>
                </div>
            </section>
        </div>

    </div>
</x-admin-layout>
