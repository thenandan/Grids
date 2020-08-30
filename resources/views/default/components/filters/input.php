<?php
/** @var TheNandan\Grids\Components\Filter $component */
?>
<?php if($component->getLabel()): ?>
    <span><?php echo $component->getLabel() ?></span>
<?php endif ?>
<input
    class="form-control form-control-sm"
    style="display: inline; width: 80px; margin-right: 10px"
    type="text"
    name="<?php echo $component->getInputName() ?>"
    value="<?php echo htmlspecialchars($component->getValue()) ?>"
    >
