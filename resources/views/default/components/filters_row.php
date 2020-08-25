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
                    <?= $column->isHidden()?'style="display:none"':'' ?>
                    >
                    <?php if ($column->hasFilters()): ?>
                        <?php foreach($column->getFilters() as $filter): ?>
                            <?= $grid->getFiltering()->render($filter) ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    <?= $component->renderComponents('filters_row_column_' . $column->getName()) ?>
                </td>
            <?php endforeach ?>
            <?= $grid->getInputProcessor()->getSortingHiddenInputsHtml() ?>
    </tr>
<?php endif ?>
