<span>Records per page</span>
<?php
/** @var TheNandan\Grids\Components\RecordsPerPage $component */
echo \Form::select(
    $component->getInputName(),
    $component->getVariants(),
    $component->getValue(),
    [
        'class' => "form-control form-control-sm grids-control-records-per-page",
        'style' => 'display: inline; width: 80px; margin-right: 10px'
    ]
);
?>
