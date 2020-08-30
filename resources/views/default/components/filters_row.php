<?php # ========== FILTERS ROW ==========
/**
 * @var TheNandan\Grids\Components\FiltersRow $component
 * @var TheNandan\Grids\FieldConfig $column
 */
?>
<?php if($grid->getFiltering()->available()): ?>
    <tr>
            <?php foreach($columns as $column): ?>
                <td
                    class="column-<?= $column->getName() ?>"
                    <?php echo $column->isHidden()?'style="display:none"':'' ?>
                    >
                    <?php if ($column->hasFilters()): ?>
                        <?php foreach($column->getFilters() as $filter): ?>
                            <?php echo $grid->getFiltering()->render($filter) ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    <?php echo $component->renderComponents('filters_row_column_' . $column->getName()) ?>
                </td>
            <?php endforeach ?>
            <?php echo $grid->getInputProcessor()->getSortingHiddenInputsHtml() ?>
    </tr>
<?php endif ?>
