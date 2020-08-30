<?php

namespace TheNandan\Grids\Components;

use TheNandan\Grids\FieldConfig;

/**
 * Class TableCell
 *
 * The component for rendering TD html tag inside grid.
 *
 * @package TheNandan\Grids\Components
 */
class TableCell extends HtmlTag
{
    protected $tag_name = 'td';

    /** @var  FieldConfig */
    protected $column;

    protected $row;

    /**
     * Constructor.
     *
     * @param FieldConfig $column
     */
    public function __construct(FieldConfig $column) {

        $this->setColumn($column);
    }

    public function getAttributes()
    {
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = 'column-' . $this->getColumn()->getName();
        }
        if ($this->column->isHidden()) {
            $this->attributes['style'] = 'display:none;';
        }

        $this->attributes['data-html'] = 'true';

        if ($this->column->hasPopover()) {
            $this->attributes['data-toggle'] = 'popover';
            $this->attributes['title'] = $this->column->getPopoverTitle();
            if (!$this->column->isHtml()) {
                $this->attributes['data-content'] = '<code><pre>'.$this->column->getCellValue().'</pre></code>';
            } else {
                $this->attributes['data-content'] = $this->column->getCellValue();
            }
        } elseif ($this->column->hasToolTip()) {
            $this->attributes['data-toggle'] = 'tooltip';
            $this->attributes['role'] = 'tooltip';
            if (!$this->column->isHtml()) {
                $this->attributes['title'] = '<pre>'.$this->column->getCellValue().'</pre>';
            } else {
                $this->attributes['title'] = $this->column->getCellValue();
            }
        } elseif ($this->column->hasTitle()) {
            $this->attributes['title'] = $this->column->getCellValue();
        }
        return $this->attributes;
    }

    /**
     * Returns component name.
     * By default it's column_{$column_name}
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name ? : 'column_' . $this->column->getName();
    }

    /**
     * Returns associated column.
     *
     * @return FieldConfig $column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param FieldConfig $column
     * @return $this
     */
    public function setColumn(FieldConfig $column)
    {
        $this->column = $column;
        return $this;
    }
}

