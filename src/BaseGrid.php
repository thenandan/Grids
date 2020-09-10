<?php

namespace TheNandan\Grids;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;

abstract class BaseGrid
{
    /**
     * @var $name
     */
    protected $name;

    /**
     * @var TheNandanGrid
     */
    protected $grid;

    /**
     * @var $relations
     */
    protected $relations = [];

    /**
     * BaseGrid constructor.
     */
    public function __construct()
    {
        $this->grid = new TheNandanGrid();
    }

    /**
     * Set root model for the grid query
     *
     * @return Model|Builder|string
     */
    abstract protected function setModel();

    /**
     * Configure your grid
     *
     * @return void
     */
    abstract protected function configureGrid(): void;

    /**
     * @return View|string
     */
    public function getGrid()
    {
        $query = $this->setModel();

        if (is_string($query)) {
            $query = new $query();
        }

        if (!empty($this->relations)) {
            $query->with($this->relations);
        }

        $query = $query->newQuery();
        $this->grid->setGridConfig($query);
        $this->configureGrid();
        if (isset($this->name)) {
            $this->grid->setGridName($this->name);
        }
        return $this->grid->render();
    }

    /**
     * This method can be used to return the grid response
     *
     * @param $view
     * @param false $isAjax
     * @return Application|Factory|View|\Illuminate\View\View|string
     */
    public static function render($view, $isAjax = false)
    {
        $grid = (new static())->getGrid();
        if ($isAjax) {
            return $grid;
        }
        return \view($view, ['grid' => $grid]);
    }
}
