<x-admin-layout>
    <div x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        showDeleteModal: false,
        selectedUser: { id: '', name: '', email: '', status: 'active', tier: 'free', is_admin: false },
        userToDelete: { id: '', name: '' }
    }" class="relative">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Gestion des Utilisateurs</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Gérez les comptes, les abonnements et les permissions des membres de la plateforme.</p>
            </div>
            <button @click="showAddModal = true" class="flex items-center gap-2 bg-[#00288e] text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-[#1e40af] transition-all active:scale-95 shadow-md shadow-[#00288e]/10">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Ajouter un Utilisateur
            </button>
        </div>

        <!-- Search & Filters Bento Grid -->
        <div class="bg-white dark:bg-[#1a2b3c] p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 mb-6">
            <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-6 lg:col-span-7 relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-[#00288e] text-sm placeholder-slate-400 transition-all" placeholder="Rechercher par nom, email ou ID..." type="text"/>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-3 lg:col-span-2">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-[#00288e] text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>

                <!-- Tier Filter -->
                <div class="md:col-span-3 lg:col-span-2">
                    <select name="tier" onchange="this.form.submit()" class="w-full py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-[#00288e] text-sm">
                        <option value="">Toutes les offres</option>
                        <option value="free" {{ request('tier') == 'free' ? 'selected' : '' }}>Gratuit (Free)</option>
                        <option value="premium" {{ request('tier') == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ request('tier') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-12 lg:col-span-1 flex gap-2">
                    <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 py-2.5 rounded-xl text-xs font-semibold transition-all">
                        Filtrer
                    </button>
                    @if(request('search') || request('status') || request('tier'))
                        <a href="{{ route('admin.users') }}" class="bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/20 dark:text-red-400 p-2.5 rounded-xl flex items-center justify-center transition-all">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white dark:bg-[#1a2b3c] rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-left border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">DÉTAILS UTILISATEUR</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">STATUT</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">OFFRE</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">RÔLE</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">INSCRIPTION</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700">
                                            @if($user->email == 'elena.r@financely.com')
                                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuChdaow9V89v_Irg0djr_eHn9l6X67OAcp1TYMktlYt_MaDWnsKM5H-lobfu2EAuM5m-B1kPZJVqJ8_ANXZ4x-QQmmMpyDqsz2CytILPbrR1PPfMgoMdzz88KmJywH4A2X1VDCf5lOazLzRqqaZqO3iyu3wyhzKYFAfRb0I_B8fbtoQp97O4fd7kkblg9E3xmB9O93dvyc1GjhendwO0eVFB-wgDjHixrLtYP44fNyYiKyhPYZHXhe9JIPrUmiCZzl1UwzLk5cNMTqz"/>
                                            @elseif($user->email == 'm.sterling@agency.io')
                                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWLPYkIHS6-CmoBNawG6DNpTFy22eQ8KcwSQKpTBjtSoBZeyEY6UH7dhxD0H1UUH_hwgnZV2ygaq_RB4WYLwDXUAq6NSfjgWYxIbBdHElT3iSGuZ8p4a23XTRT58jJE4VXvrMV9LhDJt3lBFOM9htetys9XK8awn2fih3K-qBWVk-cO1hqmXSH9GgtyG5vyR8RAZvSEwUjuyGnQy1m-EllseI6SsS2sHpJyCIieRBRGocJH_LuH-dkYClhJGGXrt6f8Hs1aaZYdxPF"/>
                                            @elseif($user->email == 'sarah.chen@tech.com')
                                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwTonGDHBwEW4GzwC0cDk24vcWN-qFly-Ua_-5P9UBArJtOElOuGfJnqmRlgeliMx0USL8GJeFna6pu1dnwRnPUWtTdmoRsvBzoZ8l5Krx98WQ-0mQ-2-KRkBNYx-DQwG8wbD97q9SrDO0KkMyO80_jb3nB0_ppLIgEvnI3fKZfpGtVpvpYPU7WSOnGBTYu666yAYL_5u6i8RWYcBXYQFrFrXMzu7LMIYdN-ykZOvl7G1VWlUtQ_G_xKkBM69Vb6t5QIXU6Lu1ZVXJ"/>
                                            @elseif($user->email == 'j.smith@freelance.org')
                                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJSKCyRo9RmHRpVTTV5Xtd2gRlaWBYguESN6lLSE7vAhjGfBknTu1B8YkjgFodS70QiILTFUsQcCVBqyN2Y68JGb-HywnvVnYUDjVHRQ7tFuI-RFEHYyqdIz1L6eDN85pjURdC0fqfr_4gQErUXet76u215ilsY6XsMcmOVa_pVhq0mAClijyLxrbJgixkQCUzGsly3MzTsjQCLJxkNMpGIpdkiu_WpI3mMXidhHIsHC1DAdANdR8YPU_TYYTuNyhjfGdeties-8Hs"/>
                                            @else
                                                <span class="font-bold text-[#00288e] dark:text-[#b8c4ff] text-xs uppercase">{{ substr($user->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1 bg-[#6cf8bb]/15 text-[#006c49] dark:bg-[#005236]/20 dark:text-[#4edea3] text-xs px-2.5 py-1 rounded-full font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#006c49] dark:bg-[#4edea3]"></span>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-[#ffdad6]/20 text-[#ba1a1a] dark:bg-[#93000a]/20 dark:text-[#ffdad6] text-xs px-2.5 py-1 rounded-full font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#ba1a1a]"></span>
                                            Suspendu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold capitalize">{{ $user->tier }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_admin)
                                        <span class="bg-[#00288e]/10 text-[#00288e] dark:bg-[#b8c4ff]/10 dark:text-[#b8c4ff] text-xs font-bold px-2 py-0.5 rounded uppercase">Admin</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500">Utilisateur</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 dark:text-slate-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Edit button -->
                                        <button @click="
                                            selectedUser = { 
                                                id: '{{ $user->id }}', 
                                                name: '{{ addslashes($user->name) }}', 
                                                email: '{{ $user->email }}', 
                                                status: '{{ $user->status }}', 
                                                tier: '{{ $user->tier }}', 
                                                is_admin: {{ $user->is_admin ? 'true' : 'false' }}
                                            };
                                            showEditModal = true;
                                        " class="p-1.5 text-slate-400 hover:text-[#00288e] dark:hover:text-[#b8c4ff] rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        <!-- Delete Form -->
                                        @if($user->id !== auth()->id())
                                             <button @click="
                                                 userToDelete = { 
                                                     id: '{{ $user->id }}', 
                                                     name: '{{ addslashes($user->name) }}' 
                                                 };
                                                 showDeleteModal = true;
                                             " type="button" class="p-1.5 text-slate-400 hover:text-[#ba1a1a] rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                 <span class="material-symbols-outlined text-[18px]">delete</span>
                                             </button>
                                         @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Asymmetric Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Growth Insights -->
            <div class="lg:col-span-2 bg-white dark:bg-[#1a2b3c] p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold mb-4">Aperçu de la Croissance</h3>
                    <div class="h-40 w-full bg-slate-50 dark:bg-slate-800/40 rounded-xl flex items-end p-4 gap-4">
                        <div class="flex-1 bg-[#00288e]/20 dark:bg-[#b8c4ff]/20 rounded-t-lg h-[30%]"></div>
                        <div class="flex-1 bg-[#00288e]/35 dark:bg-[#b8c4ff]/35 rounded-t-lg h-[45%]"></div>
                        <div class="flex-1 bg-[#00288e]/50 dark:bg-[#b8c4ff]/50 rounded-t-lg h-[60%]"></div>
                        <div class="flex-1 bg-[#00288e]/70 dark:bg-[#b8c4ff]/70 rounded-t-lg h-[85%]"></div>
                        <div class="flex-1 bg-[#00288e] dark:bg-[#b8c4ff] rounded-t-lg h-[95%]"></div>
                    </div>
                </div>
                <div class="mt-4 flex justify-between items-center text-xs">
                    <span class="text-slate-400 dark:text-slate-500">Nouveaux inscrits ce mois-ci : +24%</span>
                    <a href="{{ route('admin.dashboard') }}" class="text-[#00288e] dark:text-[#b8c4ff] font-semibold flex items-center gap-1 hover:underline">Voir les stats <span class="material-symbols-outlined text-[14px]">trending_up</span></a>
                </div>
            </div>

            <!-- Premium Conversion Card -->
            <div class="bg-[#00288e] dark:bg-[#1e40af] text-white p-6 rounded-2xl flex flex-col justify-between shadow-lg relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[100px]">verified_user</span>
                </div>
                <div>
                    <span class="material-symbols-outlined text-3xl mb-3 text-white">verified_user</span>
                    <h3 class="text-base font-bold mb-2">Conversion Premium</h3>
                    <p class="text-xs text-white/70">Les comptes Premium et Enterprise représentent désormais un taux important des utilisateurs de la plateforme.</p>
                </div>
                <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm mt-6">
                    <p class="text-[10px] uppercase font-bold text-white/70 mb-2 tracking-wider">Objectif de Conversion</p>
                    <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-[#6cf8bb] dark:bg-[#4edea3]" style="width: {{ $premiumConversionRate }}%;"></div>
                    </div>
                    <div class="mt-2 flex justify-between items-end">
                        <span class="text-xl font-bold">{{ $premiumConversionRate }}%</span>
                        <span class="text-[10px] text-white/70">Cible trimestrielle</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak>
            <!-- Backdrop -->
            <div @click="showAddModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <!-- Modal Body -->
            <div class="bg-white dark:bg-[#1a2b3c] w-full max-w-lg rounded-2xl shadow-2xl relative z-10 overflow-hidden" x-transition>
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/40">
                    <h3 class="font-bold text-base">Ajouter un nouvel utilisateur</h3>
                    <button @click="showAddModal = false" class="material-symbols-outlined text-slate-400 hover:text-slate-600">close</button>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
                    @csrf
                    @honeypot

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nom Complet <span class="text-red-500 font-bold">*</span></label>
                        <input name="name" required minlength="2" maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="text" placeholder="Ex: Jean Dupont"/>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Adresse Email <span class="text-red-500 font-bold">*</span></label>
                        <input name="email" required maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="email" placeholder="Ex: jean.dupont@email.com"/>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Mot de Passe <span class="text-red-500 font-bold">*</span></label>
                        <input name="password" required minlength="8" maxlength="100" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="password" placeholder="Mot de passe temporaire"/>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Statut <span class="text-red-500 font-bold">*</span></label>
                            <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                <option value="active">Actif</option>
                                <option value="suspended">Suspendu</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Offre <span class="text-red-500 font-bold">*</span></label>
                            <select name="tier" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                <option value="free">Gratuit</option>
                                <option value="premium">Premium</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input id="is_admin_add" name="is_admin" type="checkbox" value="1" class="rounded text-[#00288e] focus:ring-[#00288e] border-slate-300 bg-slate-50 dark:bg-slate-800 dark:border-slate-700"/>
                        <label for="is_admin_add" class="text-xs font-semibold text-slate-600 dark:text-slate-400">Définir comme Administrateur</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">Annuler</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#00288e] hover:bg-[#1e40af] text-white rounded-xl text-xs font-semibold shadow-md">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak>
            <!-- Backdrop -->
            <div @click="showEditModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <!-- Modal Body -->
            <div class="bg-white dark:bg-[#1a2b3c] w-full max-w-lg rounded-2xl shadow-2xl relative z-10 overflow-hidden" x-transition>
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-800/40">
                    <h3 class="font-bold text-base">Modifier l'utilisateur</h3>
                    <button @click="showEditModal = false" class="material-symbols-outlined text-slate-400 hover:text-slate-600">close</button>
                </div>
                <form method="POST" :action="'/admin/users/' + selectedUser.id" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    @honeypot

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nom Complet <span class="text-red-500 font-bold">*</span></label>
                        <input name="name" x-model="selectedUser.name" required minlength="2" maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="text"/>
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Adresse Email <span class="text-red-500 font-bold">*</span></label>
                        <input name="email" x-model="selectedUser.email" required maxlength="255" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]" type="email"/>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Statut <span class="text-red-500 font-bold">*</span></label>
                            <select name="status" x-model="selectedUser.status" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                <option value="active">Actif</option>
                                <option value="suspended">Suspendu</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Offre <span class="text-red-500 font-bold">*</span></label>
                            <select name="tier" x-model="selectedUser.tier" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#00288e]">
                                <option value="free">Gratuit</option>
                                <option value="premium">Premium</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input id="is_admin_edit" name="is_admin" type="checkbox" value="1" :checked="selectedUser.is_admin" @change="selectedUser.is_admin = $el.checked" class="rounded text-[#00288e] focus:ring-[#00288e] border-slate-300 bg-slate-50 dark:bg-slate-800 dark:border-slate-700"/>
                        <label for="is_admin_edit" class="text-xs font-semibold text-slate-600 dark:text-slate-400">Définir comme Administrateur</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold">Annuler</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#00288e] hover:bg-[#1e40af] text-white rounded-xl text-xs font-semibold shadow-md">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak>
            <!-- Backdrop -->
            <div @click="showDeleteModal = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <!-- Modal Body -->
            <div class="bg-white dark:bg-[#1a2b3c] w-full max-w-md rounded-2xl shadow-2xl relative z-10 overflow-hidden" x-transition>
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-2xl">warning</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Confirmer la suppression</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                        Êtes-vous sûr de vouloir supprimer l'utilisateur <strong x-text="userToDelete.name" class="font-semibold text-slate-800 dark:text-slate-200"></strong> ? Cette action est irréversible et supprimera toutes ses transactions et données associées.
                    </p>
                    <form method="POST" :action="'/admin/users/' + userToDelete.id" class="flex gap-3 justify-center">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-semibold transition-all">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold transition-all shadow-md shadow-red-600/10">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
