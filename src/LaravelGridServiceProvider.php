<?php

namespace TheNandan\Grids;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Support\ServiceProvider;

class LaravelGridServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    /**
     *
     */
    public function register()
    {
        $this->app->register(ServiceProvider::class);
        $this->app->register(HtmlServiceProvider::class);
        $this->app->alias(FormFacade::class, 'Form');
        $this->app->alias(HtmlFacade::class, 'HTML');
        $this->app->alias(Grids::class, 'Grids');
    }
}
