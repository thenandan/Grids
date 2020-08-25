<?php
/**
 * @author: Vitaliy Ofat <i@vitaliy-ofat.com>
 *
 * @var $grid TheNandan\Grids\Grid
 */
use TheNandan\Grids\Components\CsvExport;
?>
<span>
    <a
        href="<?= $grid
            ->getInputProcessor()
            ->getUrl([CsvExport::INPUT_PARAM => 1])
        ?>"
        class="btn btn-sm btn-default"
        >
        <span class="glyphicon glyphicon-export"></span>
        CSV Export
    </a>
</span>
