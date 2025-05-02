<?php

namespace App\Providers;

use App\Services\ArtistService;
use App\Services\ArtworkService;
use App\Services\AuthService;
use App\Services\NewsService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
        
        $this->app->singleton(ArtistService::class, function ($app) {
            return new ArtistService();
        });

        $this->app->singleton(ArtworkService::class, function ($app) {
            return new ArtworkService();
        });

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });
        
        $this->app->singleton(NewsService::class, function ($app) {
            return new NewsService();
        });
        
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
