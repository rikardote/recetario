<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        /** @var Request $request */
        $request = $this->app->make('request');

        // Detectar esquema (HTTP/HTTPS) desde proxy
        $scheme = 'http';
        if ($request->header('X-Forwarded-Proto') === 'https' || $request->isSecure()) {
            $scheme = 'https';
            URL::forceScheme('https');
        }

        // Usar dinámicamente el host de la petición para evitar URLs absolutas hardcodeadas
        URL::forceRootUrl($scheme . '://' . $request->httpHost());
    }
}
