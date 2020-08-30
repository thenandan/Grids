<?php

namespace TheNandan\Grids\Helpers;

use DateTime;
use DateTimeZone;
use TheNandan\Grids\SelectFilterConfig;
use TheNandan\Grids\TheNandanGrid;
use TheNandan\Grids\FieldConfig;
use TheNandan\Grids\EloquentDataProvider;

/**
 * Class Column
 * @package TheNandan\Grids\Helpers
 */
class Column
{
    private $name;
    private $column;
    private $originalValue;
    private $relation;
    private $columnName;
    private $filter = null;
    private $grid;

    /**
     * Column constructor.
     *
     * @param $columnName
     * @param false $label
     * @param false $relation
     */
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

    /**
     * @return FieldConfig
     */
    public function getColumn(): FieldConfig
    {
        return $this->column;
    }

    /**
     * @param $grid
     */
    public function setGrid($grid): void
    {
        $this->grid = $grid;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;
        $this->column->setName($name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setSortable(): self
    {
        $this->column->setSortable(true);
        return $this;
    }

    /**
     * @param $filter
     *
     * @return $this
     */
    public function setFilter($filter): self
    {
        $this->column->addFilter($filter);
        return $this;
    }

    /**
     * @param $function
     * @return $this
     */
    public function setCallback($function): self
    {
        $this->column->setCallback($function);

        return $this;
    }

    /**
     * @param int $noOfChar
     * @return $this
     */
    public function shorten(int $noOfChar = 20): self
    {
        $this->column->setCallback(function ($val) use ($noOfChar) {
            $this->column->setValue($val);
            if (empty($val)) {
                return '-';
            }
            return substr($val,0,$noOfChar).'...';
        });
        return $this;
    }

    /**
     * @param $link
     * @param $name
     *
     * @return $this
     */
    public function setLink($link, $name): self
    {
        $this->setCallback(function ($val, $row) use ($link, $name) {
            $link = sprintf($link, $row->getSrc()->$name);

            return '<a href="'.$link.'" target="_blank">'.$val.'</a>';
        });
        return $this;
    }

    /**
     * @param string $operator
     *
     * @return $this
     */
    public function setSearchFilter($operator = TheNandanGrid::OPERATOR_LIKE): Column
    {
        $this->filter = new SearchFilter($this->getName(), $operator);
        if ($this->relation) {
            $this->filter->setDefaultFilteringFunc($this->columnName, $this->relation);
        }
        $this->setFilter($this->filter->getFilter());
        return $this;
    }

    /**
     * @param array $options
     * @param false $name
     * @param false $multiple
     *
     * @return $this
     */
    public function setSelectFilter($options = [], $name = false, bool $multiple = false): self
    {
        if (!$name) {
            $name = $this->columnName;
        }
        $this->column->addFilter(
            (new SelectFilterConfig())->setName($name)
            ->setOptions($options)
            ->setMultipleMode($multiple)
        );
        return $this;
    }

    /**
     * @return $this
     */
    public function setDateFilter(): self
    {
        $this->setDateRangeFilter();
        $this->filter->addOption('singleDatePicker', true);
        return $this;
    }

    /**
     * @return $this
     */
    public function setDateRangeFilter(): Column
    {
        $this->filter = new DateRangeFilter($this->getName());
        $this->grid->setDateRangePicker($this->filter->getFilter(), $this->getName());
        return $this;
    }

    /**
     * @return $this
     */
    public function setDateTimeRangeFilter(): Column
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

    /**
     * @param $function
     *
     * @return $this
     */
    public function setFilteringFunc($function): self
    {
        $this->filter->setFilteringFunc($function);
        return $this;
    }

    /**
     * @return $this
     */
    public function time(): self
    {
        $this->setCallback(function ($val){
            return (new DateTime($val))->format('h:i A');
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function date(): self
    {
        $this->setCallback(function ($val){
            return (new DateTime($val))->format('d-M-Y');
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function datetime(): Column
    {
        $this->setCallback(function ($val) {
            $datetime = new DateTime($val);
            $datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
            return $datetime->format('d-M-Y h:i A');
        });
        return $this;
    }

    /**
     * @return $this
     */
    public function status(): self
    {
        $this->boolean('Active', 'Inactive');
        return $this;
    }

    /**
     * @param string $trueValue
     * @param string $falseValue
     *
     * @return $this
     */
    public function boolean($trueValue = 'Yes', $falseValue = 'No'): self
    {
        if (!empty($this->filter)) {
            $options = array_combine([1, 0], [$trueValue, $falseValue]);
            $this->filter->setOptions($options);
        }
        $this->setCallback(function ($val) use ($trueValue, $falseValue) {
            return $val == '1' ? $trueValue : $falseValue;
        });
        return $this;
    }

    /**
     * @param false $manyRelation
     *
     * @return $this
     */
    public function hasCheck($manyRelation = false): self
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

    /**
     * @return $this
     */
    public function hide(): Column
    {
        $this->grid->addHiddenColumn($this->getName());
        return $this;
    }

    /**
     * @return $this
     */
    public function setTitle(): self
    {
        $this->column->setTitle();
        return $this;
    }

    /**
     * @param false $isHtml
     * @return $this
     */
    public function setToolTip($isHtml = false): self
    {
        $this->column->setTooltip($isHtml);
        return $this;
    }

    /**
     * @param null $title
     * @param false $isHtml
     * @return $this
     */
    public function setPopover($isHtml = false, $title = null): self
    {
        if (null === $title) {
            $title = $this->column->getLabel();
        }
        $this->column->setPopover($title, $isHtml);
        return $this;
    }
}
