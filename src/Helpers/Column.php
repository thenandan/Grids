<?php

namespace TheNandan\Grids\Helpers;

use DateTime;
use DateTimeZone;
use TheNandan\Grids\TheNandanGrid;
use TheNandan\Grids\FieldConfig;
use TheNandan\Grids\EloquentDataProvider;

class Column
{
    private $name;
    private $column;
    private $relation;
    private $columnName;
    private $filter = null;
    private $grid;

    public function __construct($columnName, $label = false, $relation = false)
    {
        $this->relation = $relation;
        $this->columnName = $columnName;
        $this->column = new FieldConfig();
        $name = $columnName;
        if ($relation) {
            $name = $relation.'.'.$name;
        }
        $this->setName($name);
        if ($label) {
            $this->column->setLabel($label);
        }
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function setGrid($grid)
    {
        $this->grid = $grid;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->column->setName($name);

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSortable()
    {
        $this->column->setSortable(true);

        return $this;
    }

    public function setFilter($filter)
    {
        $this->column->addFilter($filter);

        return $this;
    }

    public function setCallback($function)
    {
        $this->column->setCallback($function);

        return $this;
    }

    public function setLink($link, $name)
    {
        $this->setCallback(function ($val, $row) use ($link, $name) {
            $link = sprintf($link, $row->getSrc()->$name);

            return '<a href="'.$link.'" target="_blank">'.$val.'</a>';
        });

        return $this;
    }

    public function setWidth($width)
    {
        $this->column->addStyleAttribute('width', $width);

        return $this;
    }

    public function setTextAlignment($align)
    {
        $this->column->addStyleAttribute('text-align', $align);

        return $this;
    }

    public function setSearchFilter($operator = TheNandanGrid::OPERATOR_LIKE)
    {
        $this->filter = new SearchFilter($this->getName(), $operator);
        if ($this->relation) {
            $this->filter->setDefaultFilteringFunc($this->columnName, $this->relation);
        }
        $this->setFilter($this->filter->getFilter());

        return $this;
    }

    public function setSelectFilter($options = [], $name = false)
    {
        $this->filter = new SelectFilter($this->getName(), $options);
        if (!$name) {
            $name = $this->columnName;
        }
        $this->filter->setDefaultFilteringFunc($name, $this->relation);
        $this->setFilter($this->filter->getFilter());

        return $this;
    }

    public function setDateFilter()
    {
        $this->setDateRangeFilter();
        $this->filter->addOption('singleDatePicker', true);

        return $this;
    }

    public function setDateRangeFilter()
    {
        $this->filter = new DateRangeFilter($this->getName());
        $this->grid->setDateRangePicker($this->filter->getFilter(), $this->getName());

        return $this;
    }

    public function setDateTimeRangeFilter()
    {
        $this->setDateRangeFilter();
        $this->filter->addOption('timePicker', true);
        $this->filter->addOption('format', 'YYYY-MM-DD HH:mm:ss');
        $columnName = $this->columnName;
        $this->setFilteringFunc(function ($val, $dp) use ($columnName) {
            $builder = $dp->getBuilder();
            $from = new DateTime($val[0], new DateTimeZone(date_default_timezone_get()));
            $from->setTimezone(new DateTimeZone('UTC'));
            $from = $from->format('Y-m-d H:m:s');
            $to = new DateTime($val[1], new DateTimeZone(date_default_timezone_get()));
            $to->setTimezone(new DateTimeZone('UTC'));
            $to = $to->format('Y-m-d H:m:s');
            $builder->where($columnName, '>=', $from)
                ->where($columnName, '<=', $to);
        });

        return $this;
    }

    public function setFilteringFunc($function)
    {
        $this->filter->setFilteringFunc($function);

        return $this;
    }

    public function time()
    {
        $this->setCallback(function ($val){
            return (new DateTime($val))->format('h:i A');
        });

        return $this;
    }

    public function date()
    {
        $this->setCallback(function ($val){
            return (new DateTime($val))->format('d-M-Y');
        });

        return $this;
    }

    public function datetime()
    {
        $this->setCallback(function ($val) {
            $datetime = new DateTime($val);
            $datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
            return $datetime->format('d-M-Y h:i A');
        });

        return $this;
    }

    public function status()
    {
        $this->boolean('Active', 'Inactive');

        return $this;
    }

    public function boolean($trueValue = 'Yes', $falseVaue = 'No')
    {
        if (!empty($this->filter)) {
            $options = array_combine([1, 0], [$trueValue, $falseVaue]);
            $this->filter->setOptions($options);
        }
        $this->setCallback(function ($val) use ($trueValue, $falseVaue) {
            return $val == '1' ? $trueValue : $falseVaue;
        });

        return $this;
    }

    public function hasCheck($manyRelation = false)
    {
        if (!empty($this->filter)) {
            $this->filter->setOptions([1 => 'Yes', 0 => 'No']);

            if ($this->relation || $manyRelation) {
                $relation = $this->relation;
                $columnName = $this->columnName;
                $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($relation, $columnName, $manyRelation) {
                    $builder = $dp->getBuilder();
                    if ($val == '1') {
                        if ($manyRelation) {
                            $builder->has($manyRelation);
                        } else {
                            $builder->whereHas($relation, function ($subQuery) use ($columnName) {
                                $subQuery->whereNotNull($columnName);
                            });
                        }
                    } else {
                        if ($manyRelation) {
                            $builder->whereHas($manyRelation, function ($subQuery) {}, '=', 0);
                        } else {
                            $builder->whereHas($relation, function ($subQuery) use ($columnName) {
                                $subQuery->whereNotNull($columnName);
                            }, '=', 0);
                        }
                    }
                });
            } else {
                $name = $this->columnName;
                $this->setFilteringFunc(function ($val, EloquentDataProvider $dp) use ($name) {
                    $builder = $dp->getBuilder();
                    if ($val == '1') {
                        $builder->whereNotNull($name);
                    } else {
                        $builder->whereNull($name);
                    }
                });
            }
        }

        if ($manyRelation) {
            $this->setCallback(function ($val, $row) use ($manyRelation) {
                $object = $row->getSrc();
                $relations = explode('.', $manyRelation);
                foreach ($relations as $relation) {
                    $object = $object->$relation;
                }

                return $object->isEmpty() ? 'No' : 'Yes';
            });
        } else {
            $this->setCallback(function ($val) {
                return empty($val) ? 'No' : 'Yes';
            });
        }

        return $this;
    }

    public function hide()
    {
        $this->grid->addHiddenColumn($this->getName());

        return $this;
    }

    public function integer()
    {
        $this->setFormat('integer');

        return $this;
    }

    public function float()
    {
        $this->setFormat('float');

        return $this;
    }

    public function setFormat($format)
    {
        $this->column->setFormat($format);

        return $this;
    }
}
