<?php

namespace TypiCMS\Modules\Places\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use TypiCMS\Modules\Core\Facades\TypiCMS;
use TypiCMS\Modules\Core\Observers\SlugObserver;
use TypiCMS\Modules\Places\Composers\SidebarViewComposer;
use TypiCMS\Modules\Places\Facades\Places;
use TypiCMS\Modules\Places\Models\Place;
use TypiCMS\Modules\Places\Repositories\EloquentPlace;

class ModuleProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'typicms.places'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/permissions.php', 'typicms.permissions'
        );

        $modules = $this->app['config']['typicms']['modules'];
        $this->app['config']->set('typicms.modules', array_merge(['places' => ['linkable_to_page']], $modules));

        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'places');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/places'),
        ], 'typicms-views');
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path(),
        ], 'typicms-assets');

        AliasLoader::getInstance()->alias('Places', Places::class);

        // Observers
        Place::observe(new SlugObserver());

        /*
         * Sidebar view composer
         */
        $this->app->view->composer('core::admin._sidebar', SidebarViewComposer::class);

        /*
         * Add the page in the view.
         */
        $this->app->view->composer('places::public.*', function ($view) {
            $view->page = TypiCMS::getPageLinkedToModule('places');
        });
    }

    public function register()
    {
        $app = $this->app;

        /*
         * Register route service provider
         */
        $app->register(RouteServiceProvider::class);

        $app->bind('Places', EloquentPlace::class);
    }
}
