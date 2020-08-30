<?php

namespace TheNandan\Grids;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use TheNandan\Grids\Console\Commands\MakeGridCommand;

/**
 * Class LaravelGridServiceProvider
 *
 * @package TheNandan\Grids
 */
class GridsServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */


    protected $defer = false;

    /**
     * This method required for backward compatibility with Laravel 4.
     *
     * @deprecated
     * @return string
     */
    public function guessPackagePath()
    {
        return __DIR__;
    }

    /**
     *
     */
    public function boot()
    {
        $pkg_path = dirname(__DIR__);
        $views_path = $pkg_path . '/resources/views';


        $this->loadViewsFrom($views_path, 'grids');
        $this->loadViewsFrom($views_path.'/pages', 'grids');
        $this->loadTranslationsFrom($pkg_path . '/resources/lang', 'grids');



        if ($this->app->runningInConsole()) {
            $this->publishes([
                $views_path.'/pages' => base_path('resources/views/vendor/grids')
            ]);

            $this->publishes([
                __DIR__.'/../resources/grids' => public_path('vendor/grids'),
            ], 'public');
        }
    }

    /**
     *
     */
    public function register()
    {
        $this->app->register(HtmlServiceProvider::class);
        $this->app->alias(FormFacade::class, 'Form');
        $this->app->alias(HtmlFacade::class, 'HTML');
        $this->app->alias(Grids::class, 'Grids');

        // Register commands
        $this->registerCommands();
    }


    /**
     * This method register the console commands during the application boot process.
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeGridCommand::class
            ]);
        }
    }
}
