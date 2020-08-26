<?php
namespace TheNandan\Grids\Components\Laravel5;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use TheNandan\Grids\Components\Base\RenderableComponent;
use TheNandan\Grids\Grid;

class Pager extends RenderableComponent
{
    protected $input_key;

    protected $previous_page_name;

    protected $name = 'pager';

    public function render()
    {
        $this->setupPaginationForLinks();
        $result = (string)$this->links();
        return $result;
    }

    protected function setupPaginationForReading()
    {
        Paginator::currentPageResolver(function () {
          if (version_compare(App::version(), '6.0', '>=')) {
            return \Illuminate\Support\Facades\Request::input("$this->input_key.page", 1);
          } else {
            return \Illuminate\Support\Facades\Input::get("$this->input_key.page", 1);
          }
        });
    }

    protected function setupPaginationForLinks()
    {
        /** @var  Paginator $paginator */
        $paginator = $this->grid->getConfig()
            ->getDataProvider()
            ->getPaginator();
        $paginator->setPageName("{$this->input_key}[page]");
    }

    /**
     * Renders pagination links & returns rendered html.
     */
    protected function links()
    {
        /** @var  Paginator $paginator */
        $paginator = $this->grid->getConfig()
            ->getDataProvider()
            ->getPaginator();
        $input = $this->grid->getInputProcessor()->getInput();
        if (isset($input['page'])) {
            unset($input['page']);
        }
        return str_replace('/?', '?',$paginator->appends($this->input_key, $input)->render());
    }

    public function initialize(Grid $grid)
    {
        parent::initialize($grid);
        $this->input_key = $grid->getInputProcessor()->getKey();
        $this->setupPaginationForReading();
    }
}
