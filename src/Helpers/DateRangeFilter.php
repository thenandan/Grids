<?php

namespace TheNandan\Grids\Helpers;

use TheNandan\Grids\Components\Filters\DateRangePicker;

class DateRangeFilter
{
    protected $filter;
    protected $options;
    protected $isRelated = false;

    public function __construct($name)
    {
        $this->filter = new DateRangePicker();
        $this->filter->setName($name)->setRenderSection('filters_row_column_'.$name)->setDefaultValue([null, null]);
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
        $this->filter->setJsOptions($this->options);
    }

    public function setFilteringFunc($function)
    {
        $this->filter->setFilteringFunc($function);
    }
}
