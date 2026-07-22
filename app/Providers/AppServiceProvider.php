<?php

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
        // Forzar HTTPS si la petición viene de un proxy (Cloudflare, NPM, etc.)
        if ($this->app->runningInConsole()) {
            return;
        }

        /** @var Request $request */
        $request = $this->app->make('request');
        if ($request->header('X-Forwarded-Proto') === 'https' || $request->isSecure()) {
            URL::forceScheme('https');
        }
    }
}
