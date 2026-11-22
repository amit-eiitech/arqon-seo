<?php

namespace Arqon\Seo;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ArqonSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/arqon-seo.php', 'arqon-seo');

        $this->app->singleton(Services\SeoResolver::class);
        $this->app->alias(Services\SeoResolver::class, 'arqon-seo.resolver');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'arqon-seo');
        
        Blade::componentNamespace('Arqon\\Seo\\View\\Components', 'arqon-seo');
        Blade::component('seo', \Arqon\Seo\View\Components\Seo::class, 'arqon-seo');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/arqon-seo.php' => config_path('arqon-seo.php'),
            ], 'arqon-seo-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'arqon-seo-migrations');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/arqon-seo'),
            ], 'arqon-seo-views');

            $this->publishes([
                __DIR__ . '/../database/settings' => database_path('migrations'),
            ], 'arqon-seo-settings-migrations');
        }

    }

}