<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Infrastructure\Repositories\Contracts\TransactionRepositoryInterface::class,
            \App\Infrastructure\Repositories\TransactionRepository::class
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
    }
}
