<?php

namespace TheNandan\Grids;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use TheNandan\Grids\ServiceProvider as NayjestServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use TheNandan\Grids\Console\Commands\MakeGridCommand;

/**
 * Class LaravelGridServiceProvider
 *
 * @package TheNandan\Grids
 */
class GridsServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.DIRECTORY_SEPARATOR.'Views', 'grid');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.DIRECTORY_SEPARATOR.'Views' => resource_path('views/vendor/laravelgrid'),
            ]);
        }
    }

    /**
     *
     */
    public function register()
    {
        $this->app->register(NayjestServiceProvider::class);
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
