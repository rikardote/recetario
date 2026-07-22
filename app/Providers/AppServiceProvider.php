<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Prevent lazy loading — strict in testing, logs in other envs
        if ($this->app->environment('testing')) {
            Model::preventLazyLoading(true);
        } elseif (! $this->app->isProduction()) {
            Model::preventLazyLoading(false);
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                logger()->warning('Lazy load: ' . $relation . ' on ' . get_class($model));
            });
        }

        if ($this->app->runningInConsole()) {
            return;
        }

        /** @var Request $request */
        $request = $this->app->make('request');

        $scheme = 'http';
        if ($request->header('X-Forwarded-Proto') === 'https' || $request->isSecure()) {
            $scheme = 'https';
            URL::forceScheme('https');
        }

        URL::forceRootUrl($scheme . '://' . $request->httpHost());
    }
}
