<?php
/** @var TheNandan\Grids\Filter $filter */
?>

<input
    class="form-control form-control-sm"
    name="<?= $filter->getInputName() ?>"
    value="<?= $filter->getValue() ?>"
    />
<?php if($label): ?>
    <span><?= $label ?></span>
<?php endif ?>
