<x-guest-layout>
    <div class="bg-surface-container-lowest w-full max-w-md p-lg md:p-xl rounded-3xl shadow-xl border border-outline-variant/30 relative overflow-hidden">
        <!-- Back Button -->
        <div class="mb-lg">
            <a href="{{ route('login') }}" class="group flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors active:scale-95 duration-150">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                <span class="font-label-md text-label-md">Retour</span>
            </a>
        </div>

        <div class="mb-xl">
            <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface tracking-tight">Mot de passe oublié</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-sm">
                Pas de problème. Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-lg">
            @csrf
            @honeypot

            <!-- Email Address -->
            <div class="space-y-xs">
                <label class="block font-label-md text-label-md text-on-surface-variant" for="email">Adresse email <span class="text-red-500 font-bold">*</span></label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">mail</span>
                    <input class="w-full pl-xl pr-md py-3 rounded-lg border border-outline-variant bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-on-surface" 
                           id="email" type="email" name="email" :value="old('email')" required autofocus maxlength="255" placeholder="jean.dupont@exemple.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-md">
                <button type="submit" class="w-full bg-primary text-on-primary font-label-md text-label-md py-4 rounded-xl shadow-md hover:bg-primary-container transition-all active:scale-95 duration-150 flex items-center justify-center gap-sm cursor-pointer font-semibold">
                    <span>Envoyer le lien de réinitialisation</span>
                    <span class="material-symbols-outlined">trending_flat</span>
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
