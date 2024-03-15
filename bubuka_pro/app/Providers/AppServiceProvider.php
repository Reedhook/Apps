<?php

namespace App\Providers;

use App\Services\Auth\Password\TokenRepository;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TokenRepositoryInterface::class, TokenRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
