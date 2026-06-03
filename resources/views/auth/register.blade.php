<x-guest-layout>
    <!-- Centered Card Container -->
    <div class="w-full max-w-md mx-auto py-lg">
        <!-- Registration Card -->
        <div class="bg-surface-container-lowest w-full p-lg md:p-xl rounded-3xl shadow-xl border border-outline-variant/30 relative overflow-hidden">
            <!-- Atmospheric Gradient Element -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/5 rounded-full blur-3xl"></div>
            
            <!-- Back Button -->
            <div class="mb-lg">
                <a href="/" class="group flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors active:scale-95 duration-150">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    <span class="font-label-md text-label-md">Retour</span>
                </a>
            </div>
            
            <!-- Header Section -->
            <div class="mb-xl">
                <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface tracking-tight">Créer un compte</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-sm">Commencez votre voyage vers la liberté financière avec FinTrack.</p>
            </div>
            
            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-lg">
                @csrf
                @honeypot

                <!-- Name -->
                <div class="space-y-xs">
                    <label class="block font-label-md text-label-md text-on-surface-variant" for="name">Nom complet <span class="text-red-500 font-bold">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">person</span>
                        <input class="w-full pl-xl pr-md py-3 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-on-surface" 
                               id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" minlength="2" maxlength="255" placeholder="Jean Dupont" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="space-y-xs">
                    <label class="block font-label-md text-label-md text-on-surface-variant" for="email">Adresse email <span class="text-red-500 font-bold">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">mail</span>
                        <input class="w-full pl-xl pr-md py-3 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-on-surface" 
                               id="email" type="email" name="email" :value="old('email')" required autocomplete="username" maxlength="255" placeholder="jean.dupont@exemple.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-xs">
                    <label class="block font-label-md text-label-md text-on-surface-variant" for="password">Mot de passe <span class="text-red-500 font-bold">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                        <input class="w-full pl-xl pr-md py-3 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-on-surface" 
                               id="password" type="password" name="password" required autocomplete="new-password" minlength="8" maxlength="100" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-xs">
                    <label class="block font-label-md text-label-md text-on-surface-variant" for="password_confirmation">Confirmer le mot de passe <span class="text-red-500 font-bold">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                        <input class="w-full pl-xl pr-md py-3 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-on-surface" 
                               id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" minlength="8" maxlength="100" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="pt-md">
                    <button class="w-full bg-primary text-on-primary font-label-md text-label-md py-4 rounded-xl shadow-md hover:bg-primary-container transition-all active:scale-95 duration-150 flex items-center justify-center gap-sm cursor-pointer font-semibold" type="submit">
                        <span>Créer mon compte</span>
                        <span class="material-symbols-outlined">trending_flat</span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="my-xl flex items-center gap-md">
                <div class="h-[1px] flex-1 bg-outline-variant"></div>
                <span class="font-label-sm text-label-sm text-outline uppercase tracking-widest">Ou</span>
                <div class="h-[1px] flex-1 bg-outline-variant"></div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Déjà un compte ? 
                    <a class="text-primary font-semibold hover:underline transition-all" href="{{ route('login') }}">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Floating Asymmetric Security Badge (Desktop Only) -->
    <div class="hidden lg:flex fixed bottom-6 right-6 items-center gap-sm bg-surface-container-high/60 backdrop-blur-md px-lg py-md rounded-2xl shadow-sm border border-outline-variant/20 z-10 pointer-events-none">
        <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
        </div>
        <div>
            <p class="font-label-md text-label-md text-on-surface">Sécurité de pointe</p>
            <p class="font-label-sm text-label-sm text-on-surface-variant font-medium">Chiffrement fort & Anti-spam Honeypot</p>
        </div>
    </div>
</x-guest-layout>
