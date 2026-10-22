<?php

namespace App\Modules\Home;

use App\Modules\Home\Http\Controllers\Web\HomeController;
use Saola\Core\Providers\ModuleServiceProvider as CoreModuleServiceProvider;
use Saola\Core\System;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    public function routes(): void
    {
        System::context('web')->module(['slug' => 'home', 'prefix' => '/', 'priority' => 1])
            ->controller(HomeController::class)
            ->as('home')
            ->group(function ($module) {
                foreach (config('site.pages') as $slug => $view) {
                    $module->get('/'.($slug === '' ? '' : $slug.'.html'), 'show')->name($view)->viewModule($view);
                }
            });
    }
}
