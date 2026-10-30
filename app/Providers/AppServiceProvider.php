<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->booted(fn () => app(\Saola\Core\Engines\ViewContextManager::class)->registerContext('web', [
            'base' => 'web.themes.default.views',
        ]));
        //
    }
}
