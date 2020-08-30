<?php
use TheNandan\Grids\Components\TotalsRow;
/** @var TotalsRow $component */
?>
<tr>
    <?php foreach($columns as $column): ?>
        <td
            class="column-<?= $column->getName() ?>"
            <?php echo $column->isHidden()?'style="display:none"':'' ?>
            >
            <?php
            if ($component->uses($column)):
                $label = '';
                switch($component->getFieldOperation($column->getName())) {
                    case \TheNandan\Grids\Components\TotalsRow::OPERATION_SUM:
                        $label = '∑';
                        break;
                    case \TheNandan\Grids\Components\TotalsRow::OPERATION_COUNT:
                        $label = 'Count';
                        break;
                    case \TheNandan\Grids\Components\TotalsRow::OPERATION_AVG:
                        $label = 'Avg.';
                        break;
                }
                echo $label, '&nbsp;', $column->getValue($component);
            endif;
            ?>
        </td>
    <?php endforeach ?>
</tr>
