<?php

namespace TheNandan\Grids\Helpers;

use TheNandan\Grids\SelectFilterConfig;
use TheNandan\Grids\EloquentDataProvider;

class SelectFilter extends SearchFilter
{
    public function __construct($name, $options)
    {
        $this->filter = (new SelectFilterConfig())->setOptions($options);
        $this->filter->setName($name);
        parent::__construct($name, $options);
    }

    public function setDefaultFilteringFunc($name, $relation = false)
    {
        $this->filter->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($relation, $name) {
            $builder = $dp->getBuilder();
            if ($relation) {
                $builder->whereHas($relation, function ($query) use ($val, $name) {
                    $query->where($name, $val);
                });
            } else {
                $builder->where($name, $val);
            }
        });
    }

    public function setOptions(array $options)
    {
        $this->filter->setOptions($options);
    }
}
