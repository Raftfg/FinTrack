<?php

namespace App\Providers;

use App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface;
use App\Infrastructure\Repositories\TransactionRepository;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications()->count());
                $view->with('recentNotifications', auth()->user()->unreadNotifications()->take(5)->get());
            }
        });

        // Listen to Authentication Events
        Event::listen(Login::class, function (Login $event) {
            $ip = request()->ip();
            Log::info("[Auth] User {$event->user->name} (ID: {$event->user->id}) logged in successfully from IP {$ip}");
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                Log::info("[Auth] User {$event->user->name} (ID: {$event->user->id}) logged out");
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            $ip = request()->ip();
            $email = $event->credentials['email'] ?? 'unknown';
            Log::warning("[Auth] Failed login attempt for email '{$email}' from IP {$ip}");
        });

        Event::listen(Registered::class, function (Registered $event) {
            $ip = request()->ip();
            Log::info("[Auth] New user registered: {$event->user->name} (ID: {$event->user->id}, Email: {$event->user->email}) from IP {$ip}");
        });
    }
}
