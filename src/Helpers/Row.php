<?php

namespace TheNandan\Grids\Helpers;

use TheNandan\Grids\Components\Tr;
use TheNandan\Grids\Components\TableCell;
use TheNandan\Grids\FieldConfig;

class Row extends Tr
{
    private $columns = [];

    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function renderCells()
    {
        $out = '';
        foreach ($this->columns as $key => $span) {
            $fieldConfig = (new FieldConfig())->setName($key)->addStyleAttribute('text-align', 'center');
            $component = new TableCell($fieldConfig);
            $component->setAttributes([
                'colspan' => $span
            ]);
            $component->initialize($this->grid);
            $component->setContent($key);
            $out .= $component->render();
        }

        return $out;
    }

    public function setGrid($grid)
    {
        $this->grid = $grid;
    }

    public function getContent()
    {
        return $this->renderCells();
    }
}
