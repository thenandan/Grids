<?php
/** @var TheNandan\Grids\Filter $filter */
?>

<input
    class="form-control form-control-sm"
    name="<?php echo $filter->getInputName() ?>"
    value="<?php echo $filter->getValue() ?>"
    />
<?php if($label): ?>
    <span><?php echo $label ?></span>
<?php endif ?>
