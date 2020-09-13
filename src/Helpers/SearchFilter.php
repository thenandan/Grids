<?php

namespace TheNandan\Grids\Helpers;

use TheNandan\Grids\FilterConfig;
use TheNandan\Grids\EloquentDataProvider;
use TheNandan\Grids\TheNandanGrid;

class SearchFilter
{
    protected $filter;
    protected $operator = TheNandanGrid::OPERATOR_LIKE;
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
                $val = strtolower($val);
                if ($this->operator === TheNandanGrid::OPERATOR_LIKE) {
                    $query->whereRaw("LOWER($name)='%$val%'");
                } else {
                    $query->whereRaw("LOWER($name)='$val'");
                }
                //$query->where($name, $this->operator, $this->operator == TheNandanGrid::OPERATOR_LIKE ? '%'.$val.'%' : $val);
            });
        });
    }

    public function getFilter()
    {
        return $this->filter;
    }
}
