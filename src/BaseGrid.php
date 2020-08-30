<?php

namespace TheNandan\Grids;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
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
     * BaseGrid constructor.
     */
    public function __construct()
    {
        $this->grid = new TheNandanGrid();
    }

    /**
     * Set root model for the grid query
     *
     * @return Model
     */
    abstract protected function setModel(): Model;

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
        $this->grid->setGridConfig($this->setModel()->newQuery());
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
