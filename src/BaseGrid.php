<?php

namespace TheNandan\Grids;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @param array $params
     * @return mixed
     */
    abstract protected function setModel(Request $request, array $params);

    /**
     * Configure your grid
     *
     * @return void
     */
    abstract protected function configureGrid(): void;

    /**
     * @param Request $request
     * @param array $params
     *
     * @return View|string
     */
    public function getGrid(Request $request, $params = [])
    {
        $query = $this->setModel($request, $params);

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
     * @param Request $request
     * @param array $params
     *
     * @return Application|Factory|View|\Illuminate\View\View|string
     */
    public static function render($view, Request $request, $params = [])
    {
        if (null !== $request->route()) {
            $params = array_merge($params, $request->route()->parameters());
        }
        $grid = (new static())->getGrid($request, $params);
        if ($request->ajax()) {
            return $grid;
        }
        return \view($view, ['grid' => $grid]);
    }
}
