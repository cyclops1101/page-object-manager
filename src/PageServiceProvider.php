<?php

namespace Cyclops1101\PageObjectManager;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Cyclops1101\PageObjectManager\Commands\CreateTemplate;
use Cyclops1101\PageObjectManager\Pages\Manager;
use Cyclops1101\PageObjectManager\Pages\Template;
use Cyclops1101\PageObjectManager\Pages\TemplatesRepository;

class PageServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the Container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'page-object-manager');

        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app->make(TemplatesRepository::class));
        });

        $this->app->bind(Template::class, function($app) {
            return $app->make(Manager::class)->find();
        });

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    public function registerBladeDirectives()
    {
        Blade::directive('get', function ($key) {
            return '<?= Page::get(\'' . trim($key, "'\"") . '\'); ?>';
        });

        Blade::directive('block', function($key) {
            list($name, $attribute) = explode('.', trim($key, "'\""), 2);
            return '<?= data_get(Page::block(\'' . $name . '\'),\'' . $attribute . '\'); ?>';
        });
    }

    public function registerCommands()
    {
        $this->commands([
            CreateTemplate::class
        ]);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config.php' => config_path('page-object-manager.php'),
        ], 'page-object-manager-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'page-object-manager-migrations');

        $this->app->booted(function() {
            $this->app->make(Manager::class)->booted();
        });

        $this->registerBladeDirectives();
    }

}
