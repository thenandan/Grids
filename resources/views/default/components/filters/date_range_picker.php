<?php
/** @let TheNandan\Grids\Components\Filters\DateRangePicker $component */
$id = uniqid();
?>
<?php if($component->getLabel()): ?>
    <span>
        <i class="fas fa-calendar-alt"></i>
        <?php echo $component->getLabel() ?>
    </span>
<?php endif ?>
<input
    class="form-control form-control-sm"
    style="display: inline; width: 165px; margin-right: 10px"
    name="<?php echo $component->getInputName() ?>"
    type="text"
    id="<?php echo $id ?>"
    >

<script>
    $(function(){
        let options = <?php echo json_encode($component->getJsOptions())?>;
        if (!options.format) {
            options.format = 'YYYY-MM-DD';
        }
        let cb = function(start, end) {
            let text;
            if (start.isValid() && end.isValid()) {
                text = start.format(options.format) + '—' + end.format(options.format);
            } else {
                text = '';
            }
            $('#<?php echo$id?>').val(text);
        };
        let onApplyDate = function(ev, picker) {
            let start = $('[name="<?php echo $component->getStartInputName() ?>"]');
            start.val(picker.startDate.format(options.format));
            let end = $('[name="<?php echo $component->getEndInputName() ?>"]');
            end.val(picker.endDate.format(options.format));
            <?php if($component->isSubmittedOnChange()): ?>
            	end.get(0).form.submit();
            <?php endif ?>
        };
        $('#<?php echo $id ?>')
            .daterangepicker(options, cb)
            .on('apply.daterangepicker', onApplyDate)
            .on('change', function () {
              if (!$('#<?php echo$id?>').val()) {
                $('[name="<?php echo $component->getStartInputName() ?>"]').val('');
                $('[name="<?php echo $component->getEndInputName() ?>"]').val('');

                <?php if($component->isSubmittedOnChange()): ?>
                let end = $('[name="<?php echo $component->getEndInputName() ?>"]');
                end.get(0).form.submit();
                <?php endif ?>
              }
            })
            .on('cancel.daterangepicker', function () {
              $(this).val('');
              $(this).trigger("change");
            });
        cb(
            moment("<?php echo $component->getStartValue() ?>"),
            moment("<?php echo $component->getEndValue() ?>")
        );
    })
</script>
<?php echo Form::hidden($component->getStartInputName(), $component->getStartValue()) ?>
<?php echo Form::hidden($component->getEndInputName(), $component->getEndValue()) ?>

