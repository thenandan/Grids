<?php

namespace TheNandan\Grids\Build\Helpers;

use TheNandan\Grids\FilterConfig;
use TheNandan\Grids\EloquentDataProvider;
use TheNandan\Grids\Build\Grid;

class SearchFilter
{
    protected $filter;
    protected $operator = Grid::OPERATOR_LIKE;
    protected $isRelated = false;

    public function __construct($name, $operator = false)
    {
        $this->filter = new FilterConfig();
        $this->filter->setName($name);
        if ($operator) {
            $this->filter->setOperator($operator);
        }
    }

    public function setRelated()
    {
        $this->isRelated = true;
    }

    public function setFilteringFunc($function)
    {
        $this->filter->setFilteringFunc($function);
    }

    public function setDefaultFilteringFunc($name, $relation)
    {
        $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($relation, $name) {
            $builder = $dp->getBuilder();
            $builder->whereHas($relation, function ($query) use ($val, $name) {
                $query->where($name, $this->operator, $this->operator == Grid::OPERATOR_LIKE ? '%'.$val.'%' : $val);
            });
        });
    }

    public function getFilter()
    {
        return $this->filter;
    }
}
