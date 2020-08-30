<?php
/** @var TheNandan\Grids\Filter $filter */
/** @var TheNandan\Grids\SelectFilterConfig $cfg */
$cfg = $filter->getConfig();
$onchange = '';
if (method_exists($cfg, 'isSubmittedOnChange') && $cfg->isSubmittedOnChange()) {
    $onchange = 'onchange="this.form.submit()"';
}
?>
<select
    class="form-control form-control-sm"
    name="<?php echo $filter->getInputName() ?><?= $cfg->isMultipleMode() ? '[]' : '' ?>"
    <?php echo $onchange ?>
    <?php echo ($size = $cfg->getSize()) ? 'size="'.$size.'"' : '' ?>
    <?php echo ($cfg->isMultipleMode()) ? 'multiple="multiple"' : '' ?>
    >
    <?php echo (!$cfg->isMultipleMode()) ? '<option value="">--//--</option>' : '' ?>
    <?php foreach ($filter->getConfig()->getOptions() as $value => $label): ?>
        <?php
        $maybe_selected = (
            (
                (is_array($filter->getValue()) && in_array($value, $filter->getValue(), false)) ||
                $filter->getValue() == $value
            )
            && $filter->getValue() !== ''
            && $filter->getValue() !== null
        ) ? 'selected="selected"' : ''
        ?>
        <option <?php echo $maybe_selected ?> value="<?php echo $value ?>">
            <?php echo $label ?>
        </option>
    <?php endforeach ?>
</select>
