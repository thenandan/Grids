<?php
/** @let TheNandan\Grids\Components\SelectFilter $component */
$value = $component->getValue();
if (!is_array($value)) $value = [];
$id = uniqid() . mt_rand();
?>
<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <?php echo $component->getLabel() ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-form"
        role="menu"
        id="<?php echo$id ?>"
        style="padding: 10px"
    >
        <li>
            <div>
                <label>
                    <input
                        type="checkbox"
                        class="checkAll"
                    >
                    <span><u>Check All</u></span>
                </label>
            </div>
        </li>
        <?php foreach($component->getletiants() as $val => $label): ?>
            <?php if(is_array($label)):?>
                <?php
                $class = '';
                if(array_intersect(array_keys($label['values']), array_keys($value))) {
                    $class = ' in';
                }
                ?>
                <li>
                    <a href="#" data-target="#collapse<?php echo$val?>" class="collapsible">
                        <span class="glyphicon glyphicon-collapse-down"></span>
                        <b><?php echo $label['name'] ?></b>
                    </a>

                    <div class="collapse<?php echo$class?>" id="collapse<?php echo$val?>" style="margin-left: 25px;">
                        <?php if (count($label['values']) > 1):?>
                            <div>
                                <label>
                                    <input
                                        type="checkbox"
                                        class="checkGroup"
                                    >
                                    <span><u>Check Group</u></span>
                                </label>
                            </div>
                        <?php endif ?>
                        <?php foreach($label['values'] as $option_val=>$option_label):?>
                            <div>
                                <label>
                                    <input
                                        type="checkbox"
                                        <?php if(!empty($value[$option_val])) echo "checked='checked'" ?>
                                        name="<?php echo $component->getInputName() ?>[<?php echo $option_val ?>]"
                                    >
                                    <span><?php echo $option_label ?></span>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                </li>
            <?php else:?>
                <li style="white-space: nowrap">
                    <label>
                        <input
                            type="checkbox"
                            <?php if(!empty($value[$val])) echo "checked='checked'" ?>
                            name="<?php echo $component->getInputName() ?>[<?php echo $val ?>]"
                        >
                        <span><?php echo $label ?></span>
                    </label>
                </li>
            <?php endif ?>
        <?php endforeach ?>
    </ul>
</div>
<script>
    $(function(){
        $('#<?php echo $id ?>.dropdown-menu').on('click', function(e) {
            if($(this).hasClass('dropdown-menu-form')) {
                e.stopPropagation();
            }
        });
        $('#<?php echo $id ?> input').change(function(){
            let $this = $(this);
            setTimeout(function(){
                let isCheckedGroup = true;
                $this.closest('li').find('input[type=checkbox]').not('.checkGroup').each(function(){
                    isCheckedGroup = isCheckedGroup && $(this).prop('checked');
                });
                $this.closest('li').find('.checkGroup').prop('checked', isCheckedGroup);
            }, 50);
            setTimeout(isCheckedAll,50);
        });
        $('#<?php echo $id ?> .collapsible').click(function(e){
            $(this).next('.collapse').toggleClass('in');
            $(this).find('i').toggleClass('glyphicon-collapse-down').toggleClass('glyphicon-collapse-up');
            e.preventDefault();
        });
        $('#<?php echo $id ?> .checkAll').change(function(e){
            let checked = $(this).prop('checked');
            $(this).closest('ul').find('input[type=checkbox]').prop('checked', checked);
        });
        $('#<?php echo $id ?> .checkGroup').change(function(e){
            let checked = $(this).prop('checked');
            $(this).closest('li').find('input[type=checkbox]').prop('checked', checked);
            setTimeout(isCheckedAll,50);
        });

        let isCheckedAll = function() {
            let isChecked = true;
            $('#<?php echo $id ?> input[type=checkbox]').not('.checkAll').each(function(){
                isChecked = isChecked && $(this).prop('checked');
            });
            $('#<?php echo $id ?> .checkAll').prop('checked', isChecked);
        };
    });

</script>
