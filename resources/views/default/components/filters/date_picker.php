<?php
/** @var TheNandan\Grids\Components\Filter $component */
$id = uniqid('', true);
?>
<?php if($component->getLabel()): ?>
    <span><?php echo $component->getLabel() ?></span>
<?php endif ?>
<input
    class="form-control input-sm"
    style="display: inline; width: 85px; margin-right: 10px"
    name="<?php echo $component->getInputName() ?>"
    type="text"
    value="<?php echo $component->getValue() ?>"
    id="<?php echo $id ?>"
    >
<script>
    $(function(){
        $('#<?php echo $id ?>').datepicker({format: 'yyyy-mm-dd'});
    })
</script>
