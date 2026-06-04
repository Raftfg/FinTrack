<x-admin-layout>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Monitoring & Logs Système</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Visualisez la santé des serveurs et suivez la piste d'audit de sécurité de la plateforme.</p>
    </div>

    <!-- KPIs Section -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- API Health Status -->
        <div class="bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="material-symbols-outlined text-[#006c49] bg-[#6cf8bb]/15 p-2 rounded-lg text-lg">dns</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-[#6cf8bb]/20 text-[#006c49] dark:text-[#4edea3] rounded-full uppercase">Opérationnel</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Passerelle API</p>
                <p class="text-xl font-bold mt-1">99.98% Uptime</p>
            </div>
            <div class="mt-4 flex gap-1 items-end h-8">
                <div class="flex-1 bg-[#006c49] h-4 rounded-sm"></div>
                <div class="flex-1 bg-[#006c49] h-6 rounded-sm"></div>
                <div class="flex-1 bg-[#006c49] h-5 rounded-sm"></div>
                <div class="flex-1 bg-[#006c49] h-7 rounded-sm"></div>
                <div class="flex-1 bg-[#006c49] h-8 rounded-sm"></div>
                <div class="flex-1 bg-[#006c49] h-6 rounded-sm"></div>
            </div>
        </div>

        <!-- Security Alerts -->
        <div class="bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="material-symbols-outlined text-[#ba1a1a] bg-[#ffdad6] p-2 rounded-lg text-lg">shield_with_heart</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-[#ffdad6] text-[#ba1a1a] rounded-full uppercase">2 Alertes</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Menaces de Sécurité</p>
                <p class="text-xl font-bold mt-1">Sévérité Faible</p>
            </div>
            <div class="mt-4 flex -space-x-1.5 items-center">
                <div class="w-6 h-6 rounded-full bg-[#ba1a1a] border border-white dark:border-[#1a2b3c] flex items-center justify-center text-[10px] text-white font-bold">!</div>
                <div class="w-6 h-6 rounded-full bg-[#ffdad6] text-[#ba1a1a] border border-[#ba1a1a]/30 flex items-center justify-center text-[10px] font-bold">?</div>
                <span class="text-[10px] text-slate-400 dark:text-slate-500 ml-2 font-medium">Flux d'attaque stoppé</span>
            </div>
        </div>

        <!-- Latency -->
        <div class="bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="material-symbols-outlined text-[#00288e] bg-[#eff4ff] p-2 rounded-lg text-lg">speed</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Latence Système</p>
                <p class="text-xl font-bold mt-1">42ms</p>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#00288e] h-full rounded-full" style="width: 66%;"></div>
                </div>
                <p class="text-slate-400 text-[9px] mt-1.5 font-medium">Limite acceptable : 200ms</p>
            </div>
        </div>

        <!-- CPU Load -->
        <div class="bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="material-symbols-outlined text-slate-500 bg-slate-100 dark:bg-slate-800 p-2 rounded-lg text-lg">memory</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Charge Processeur (CPU)</p>
                <p class="text-xl font-bold mt-1">14% Moyenne</p>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                <span class="inline-flex w-2 h-2 rounded-full bg-[#006c49]"></span>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Performance stable</span>
            </div>
        </div>
    </section>

    <!-- Monitoring Controls & Filter -->
    <section class="flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-slate-800/20 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 mb-6">
        <form method="GET" action="{{ route('admin.logs') }}" class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative flex-grow md:w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#1a2b3c] border-none rounded-xl focus:ring-2 focus:ring-[#00288e] text-xs transition-all" placeholder="Rechercher des logs par utilisateur ou description..."/>
            </div>
            
            <select name="severity" onchange="this.form.submit()" class="py-2 bg-white dark:bg-[#1a2b3c] border-none rounded-xl focus:ring-2 focus:ring-[#00288e] text-xs font-semibold">
                <option value="">Toutes sévérités</option>
                <option value="Critical" {{ request('severity') == 'Critical' ? 'selected' : '' }}>Critique (Critical)</option>
                <option value="Warning" {{ request('severity') == 'Warning' ? 'selected' : '' }}>Avertissement (Warning)</option>
                <option value="Informational" {{ request('severity') == 'Informational' ? 'selected' : '' }}>Info (Informational)</option>
                <option value="Success" {{ request('severity') == 'Success' ? 'selected' : '' }}>Succès (Success)</option>
            </select>
        </form>

        <div class="flex items-center gap-3 w-full md:w-auto justify-end">
            <div class="flex items-center gap-2 px-3.5 py-1.5 bg-[#6cf8bb]/15 text-[#006c49] dark:text-[#4edea3] rounded-full text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-[#006c49] dark:bg-[#4edea3] animate-ping"></span>
                Flux en direct
            </div>
            <a href="{{ route('admin.logs', array_merge(request()->query(), ['export' => 'json'])) }}" class="px-4 py-2 bg-[#00288e] text-white rounded-xl text-xs font-semibold hover:bg-[#1e40af] transition-all active:scale-95">Exporter en JSON</a>
        </div>
    </section>

    <!-- Logs Table -->
    <div class="bg-white dark:bg-[#1a2b3c] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 text-xs font-bold border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 font-semibold">TIMESTAMP</th>
                        <th class="px-6 py-4 font-semibold">TYPE D'ÉVÉNEMENT</th>
                        <th class="px-6 py-4 font-semibold">INITIÉ PAR</th>
                        <th class="px-6 py-4 font-semibold">GRAVITÉ</th>
                        <th class="px-6 py-4 font-semibold">DESCRIPTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                            <td class="px-6 py-4 text-slate-400">{{ $log['timestamp'] }}</td>
                            <td class="px-6 py-4 font-semibold">
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] {{ $log['severity'] === 'Critical' ? 'text-[#ba1a1a]' : ($log['severity'] === 'Warning' ? 'text-[#ffb3ad] dark:text-[#ffdad6]' : 'text-[#00288e] dark:text-[#b8c4ff]') }}">
                                        {{ $log['icon'] }}
                                    </span>
                                    {{ $log['event_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[9px] font-bold border border-slate-200 dark:border-slate-700">
                                        {{ $log['user_initials'] }}
                                    </div>
                                    <span class="font-medium">{{ $log['user'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log['severity'] === 'Critical')
                                    <span class="bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#93000a]/20 dark:text-[#ffdad6] px-2 py-0.5 rounded text-[10px] font-bold uppercase">Critique</span>
                                @elseif($log['severity'] === 'Warning')
                                    <span class="bg-yellow-50 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Avertissement</span>
                                @elseif($log['severity'] === 'Success')
                                    <span class="bg-[#6cf8bb]/20 text-[#006c49] dark:bg-[#005236]/20 dark:text-[#4edea3] px-2 py-0.5 rounded text-[10px] font-bold uppercase">Succès</span>
                                @else
                                    <span class="bg-blue-50 text-blue-800 dark:bg-blue-950/20 dark:text-blue-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Info</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $log['description'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Aucun log trouvé correspondant aux critères.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-slate-400">
            <span>Affichage de {{ count($logs) }} logs système simulés</span>
            <div class="flex gap-2">
                <button class="p-1 rounded-lg border border-slate-200 dark:border-slate-800 disabled:opacity-40" disabled>
                    <span class="material-symbols-outlined text-[16px] block">chevron_left</span>
                </button>
                <button class="p-1 rounded-lg border border-slate-200 dark:border-slate-800 disabled:opacity-40" disabled>
                    <span class="material-symbols-outlined text-[16px] block">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Asymmetric Section -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Event Frequency Chart -->
        <div class="md:col-span-2 bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <h3 class="text-base font-bold mb-2">Fréquence des Événements</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 mb-6 font-medium">Volume d'événements cumulés sur la plateforme au cours des dernières 24 heures.</p>
            <div class="flex items-end gap-3 h-32 mb-4 pt-4">
                <div class="flex-1 bg-[#00288e]/30 rounded-t-lg h-[25%]"></div>
                <div class="flex-1 bg-[#00288e]/40 rounded-t-lg h-[40%]"></div>
                <div class="flex-1 bg-[#00288e] rounded-t-lg h-[80%]"></div>
                <div class="flex-1 bg-[#00288e]/65 rounded-t-lg h-[50%]"></div>
                <div class="flex-1 bg-[#00288e]/50 rounded-t-lg h-[45%]"></div>
                <div class="flex-1 bg-[#ba1a1a] rounded-t-lg h-[30%]"></div>
                <div class="flex-1 bg-[#00288e]/60 rounded-t-lg h-[35%]"></div>
            </div>
            <div class="flex justify-between text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                <span>00:00</span>
                <span>06:00</span>
                <span>12:00</span>
                <span>18:00</span>
                <span>Maintenant</span>
            </div>
        </div>

        <!-- Node Health -->
        <div class="bg-[#00288e] dark:bg-[#1e40af] text-white p-6 rounded-2xl shadow-lg flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold mb-1">État des Serveurs</h3>
                <p class="text-xs text-white/70 mb-6">Suivi opérationnel des infrastructures géographiques.</p>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-semibold">
                            <span>Nœud US-East-1 (AWS)</span>
                            <span class="text-[#6cf8bb]">Stable</span>
                        </div>
                        <div class="w-full bg-white/20 h-1.5 rounded-full">
                            <div class="w-[98%] bg-[#6cf8bb] h-full rounded-full"></div>
                        </div>
                    </div>
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-xs font-semibold">
                            <span>Nœud EU-West-1 (GCP)</span>
                            <span class="text-[#6cf8bb]">Stable</span>
                        </div>
                        <div class="w-full bg-white/20 h-1.5 rounded-full">
                            <div class="w-[99%] bg-[#6cf8bb] h-full rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
            <button onclick="alert('Chargement du rapport d\'infrastructure...')" class="mt-8 w-full py-2.5 bg-white text-[#00288e] dark:text-[#1e40af] rounded-xl text-xs font-bold hover:bg-slate-50 transition-colors active:scale-95">Rapport d'Infrastructure</button>
        </div>
    </section>
</x-admin-layout>
