<x-admin-layout>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Console d'Administration</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">État du système : <span class="text-[#006c49] dark:text-[#4edea3] font-bold">Sain</span> • Dernière synchronisation il y a 2m</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.logs') }}" class="flex items-center gap-2 bg-white dark:bg-[#1a2b3c] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">file_download</span> Exporter les Logs
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 bg-[#00288e] text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-[#1e40af] transition-all active:scale-95 shadow-md shadow-[#00288e]/10">
                <span class="material-symbols-outlined text-[18px]">settings_suggest</span> Diagnostic Système
            </a>
        </div>
    </div>

    <!-- Bento Grid Dashboard -->
    <div class="bento-grid">
        
        <!-- System Health Status -->
        <div class="bento-item col-span-12 md:col-span-4 flex flex-col justify-between bg-white dark:bg-[#1a2b3c] border-l-4 border-[#006c49] dark:border-l-4 dark:border-l-[#4edea3]">
            <div class="flex justify-between items-start mb-4">
                <span class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Santé du Système</span>
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#6cf8bb] opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#006c49] dark:bg-[#4edea3]"></span>
                </span>
            </div>
            <div class="flex items-end justify-between mt-4">
                <div>
                    <div class="text-4xl font-bold text-[#006c49] dark:text-[#4edea3]">99.98%</div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Uptime (30j)</p>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold">12ms</div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Latence</p>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bento-item col-span-12 sm:col-span-6 md:col-span-4 flex flex-col justify-between bg-white dark:bg-[#1a2b3c]">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[#00288e] dark:text-[#b8c4ff]">group</span>
                    <span class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Utilisateurs Enregistrés</span>
                </div>
                <div class="text-3xl font-bold">{{ number_format($totalUsers) }}</div>
            </div>
            <div class="flex items-center gap-1.5 text-[#006c49] dark:text-[#4edea3] text-xs mt-4">
                <span class="material-symbols-outlined text-[16px]">trending_up</span>
                <span>+12.4% ce mois</span>
            </div>
        </div>

        <!-- Daily Active Users -->
        <div class="bento-item col-span-12 sm:col-span-6 md:col-span-4 flex flex-col justify-between bg-white dark:bg-[#1a2b3c]">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[#00288e] dark:text-[#b8c4ff]">person_check</span>
                    <span class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-wider">Actifs Aujourd'hui</span>
                </div>
                <div class="text-3xl font-bold">{{ number_format($activeToday) }}</div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#00288e] dark:bg-[#b8c4ff] h-full rounded-full" style="width: 65%;"></div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] mt-1.5">65% de la moyenne hebdomadaire</p>
            </div>
        </div>

        <!-- User Growth Chart Area -->
        <div class="bento-item col-span-12 md:col-span-8 bg-white dark:bg-[#1a2b3c] overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-base font-bold">Croissance Utilisateurs</h3>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-0.5">Nouvelles inscriptions (7 derniers jours)</p>
                </div>
                <select class="bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-xs font-medium focus:ring-2 focus:ring-[#00288e] py-1.5 pl-3 pr-8">
                    <option>7 derniers jours</option>
                    <option>30 derniers jours</option>
                </select>
            </div>
            <div class="h-[280px] w-full relative">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Total Volume Processed (Mini Card in Grid) -->
        <div class="bento-item col-span-12 md:col-span-4 bg-[#00288e] dark:bg-[#1e40af] text-white flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-300">
                <span class="material-symbols-outlined text-[180px]">payments</span>
            </div>
            <div class="relative z-10">
                <span class="text-white/70 text-xs font-bold uppercase tracking-wider block">Volume Global (24h)</span>
                <div class="mt-4">
                    <div class="text-4xl font-bold">{{ number_format($volume24h, 2) }} FCFA</div>
                    <p class="text-white/70 text-xs mt-1">Traité via {{ number_format($transactionCount) }} transactions</p>
                </div>
            </div>
            <div class="mt-8 flex gap-3 relative z-10">
                <div class="flex-1 p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <span class="text-[10px] block text-white/70">Taux de Succès</span>
                    <span class="text-base font-bold">{{ $successRate }}%</span>
                </div>
                <div class="flex-1 p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <span class="text-[10px] block text-white/70">Moyenne</span>
                    <span class="text-base font-bold">{{ number_format($avgSize, 2) }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Recent Critical Logs -->
        <div class="bento-item col-span-12 bg-white dark:bg-[#1a2b3c]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-base font-bold">Logs Critiques Récents</h3>
                <a href="{{ route('admin.logs') }}" class="text-[#00288e] dark:text-[#b8c4ff] text-xs font-semibold hover:underline">Voir tous les logs</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed min-w-[950px]">
                    <thead>
                        <tr class="text-slate-400 dark:text-slate-500 text-xs font-bold border-b border-slate-100 dark:border-slate-800">
                            <th class="px-4 pb-3 font-semibold w-[16%]">HORODATAGE</th>
                            <th class="px-4 pb-3 font-semibold w-[22%]">TYPE D'ÉVÉNEMENT</th>
                            <th class="px-4 pb-3 font-semibold w-[16%]">UTILISATEUR AFFECTÉ</th>
                            <th class="px-4 pb-3 font-semibold w-[10%]">GRAVITÉ</th>
                            <th class="px-4 pb-3 font-semibold w-[31%]">DESCRIPTION</th>
                            <th class="px-4 pb-3 font-semibold text-right w-[5%]">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($criticalLogs as $log)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                                <td class="px-4 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log['timestamp'] }}</td>
                                <td class="px-4 py-4 font-semibold break-words">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-[#ba1a1a] shrink-0">warning</span>
                                        <span class="break-words">{{ $log['event_type'] }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-medium break-words">{{ $log['user'] }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#93000a]/20 dark:text-[#ffdad6] px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        {{ $log['severity'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-500 dark:text-slate-400 break-all">
                                    <div class="line-clamp-2 cursor-pointer hover:line-clamp-none transition-all duration-200" title="{{ $log['description'] }}">
                                        {{ $log['description'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.logs', ['search' => $log['event_type']]) }}" class="text-[#00288e] dark:text-[#b8c4ff] material-symbols-outlined p-1 hover:bg-[#00288e]/10 rounded-full transition-colors">
                                        visibility
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">Aucun log critique récent.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

    <!-- Chart Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            const primaryColor = isDark ? '#b8c4ff' : '#00288e';
            const gridColor = isDark ? '#1e293b' : '#e5eeff';

            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, isDark ? 'rgba(184, 196, 255, 0.2)' : 'rgba(0, 40, 142, 0.2)');
            gradient.addColorStop(1, 'rgba(0, 40, 142, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($growthLabels),
                    datasets: [{
                        label: 'Nouveaux Utilisateurs',
                        data: @json($growthData),
                        borderColor: primaryColor,
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: primaryColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { 
                                font: { size: 10, family: 'Inter' },
                                color: isDark ? '#94a3b8' : '#64748b'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 10, family: 'Inter' },
                                color: isDark ? '#94a3b8' : '#64748b'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>
