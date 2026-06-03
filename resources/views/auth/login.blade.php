<x-guest-layout>
    <main class="w-full max-w-[1200px] grid md:grid-cols-2 gap-lg items-center py-lg">
        <!-- Left Side: Visual/Context (Desktop Only) -->
        <div class="hidden md:flex flex-col gap-lg pr-xl justify-center">
            <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-primary text-[40px]" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                <h1 class="font-headline-lg text-headline-lg font-bold text-primary">FinTrack</h1>
            </div>
            <div class="space-y-md">
                <h2 class="font-display-lg text-display-lg text-on-background max-w-md leading-tight">Gerez votre avenir, une transaction à la fois.</h2>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-sm">Rejoignez des milliers d'utilisateurs qui optimisent leur épargne avec notre intelligence financière.</p>
            </div>
            
            <!-- Bento Card Preview -->
            <div class="grid grid-cols-2 gap-sm mt-xl">
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-md border border-outline-variant/30 flex flex-col gap-xs transform hover:-translate-y-1 transition-all duration-300">
                    <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">trending_up</span>
                    <p class="font-label-sm text-label-sm text-outline">Croissance</p>
                    <p class="font-headline-sm text-headline-sm text-on-surface">+12.5%</p>
                </div>
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-md border border-outline-variant/30 flex flex-col gap-xs transform hover:-translate-y-1 transition-all duration-300 translate-y-4">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">shield</span>
                    <p class="font-label-sm text-label-sm text-outline">Sécurité</p>
                    <p class="font-headline-sm text-headline-sm text-on-surface">SSL 256-bit</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex justify-center">
            <div class="bg-surface-container-lowest w-full max-w-[440px] p-lg md:p-xl rounded-[2rem] shadow-xl border border-outline-variant/30 relative">
                <!-- Brand Mobile Only -->
                <div class="md:hidden flex items-center justify-center gap-sm mb-lg">
                    <span class="material-symbols-outlined text-primary text-[32px]" style="font-variation-settings: 'FILL' 1;">account_balance_wallet</span>
                    <h1 class="font-headline-lg-mobile text-headline-lg-mobile font-bold text-primary">FinTrack</h1>
                </div>
                
                <div class="mb-xl text-center md:text-left">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Bienvenue</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Connectez-vous pour accéder à votre tableau de bord.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-lg">
                    @csrf
                    @honeypot

                    <!-- Email Address -->
                    <div class="flex flex-col gap-xs">
                        <label class="font-label-md text-label-md text-on-surface pl-1" for="email">Email <span class="text-red-500 font-bold">*</span></label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-outline">mail</span>
                            <input class="w-full pl-12 pr-4 py-4 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none font-body-md text-on-surface" 
                                   id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" maxlength="255" placeholder="nom@exemple.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-xs">
                        <div class="flex justify-between items-center pl-1">
                            <label class="font-label-md text-label-md text-on-surface" for="password">Mot de passe <span class="text-red-500 font-bold">*</span></label>
                            @if (Route::has('password.request'))
                                <a class="font-label-sm text-label-sm text-primary hover:underline transition-all" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="relative flex items-center" x-data="{ show: false }">
                            <span class="material-symbols-outlined absolute left-4 text-outline">lock</span>
                            <input class="w-full pl-12 pr-12 py-4 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none font-body-md text-on-surface" 
                                   id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" minlength="8" maxlength="100" placeholder="••••••••" />
                            <button class="absolute right-4 text-outline hover:text-primary transition-colors" type="button" @click="show = !show">
                                <span class="material-symbols-outlined" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="h-5 w-5 rounded border-outline-variant text-primary focus:ring-primary/20 cursor-pointer" name="remember">
                            <span class="ms-2 font-body-sm text-body-sm text-on-surface-variant">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full py-4 bg-primary text-on-primary font-label-md text-label-md rounded-xl shadow-lg shadow-primary/20 hover:bg-primary-container active:scale-[0.98] transition-all duration-150 ease-in-out mt-sm font-semibold flex justify-center items-center cursor-pointer">
                        {{ __('Log in') }}
                    </button>
                </form>

                <!-- Signup Link -->
                <p class="mt-lg text-center font-body-sm text-body-sm text-on-surface-variant">
                    Nouveau sur FinTrack ? 
                    <a class="text-primary font-semibold hover:underline" href="{{ route('register') }}">Créer un compte</a>
                </p>
            </div>
        </div>
    </main>
</x-guest-layout>
