<?php

namespace Cubotecnologia\JwtIdentityGuard;

use Cubotecnologia\JwtIdentityGuard\Http\Middleware\JwtIdentityMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class JwtIdentityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge da config (não força publish)
        $this->mergeConfigFrom(
            __DIR__ . '/../config/jwt-identity.php',
            'jwt-identity'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/jwt-identity.php' => config_path('jwt-identity.php'),
            ], 'jwt-identity-config');
        }

        $router->aliasMiddleware('jwt.guard', JwtIdentityMiddleware::class);
    }
}
