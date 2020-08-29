<?php
/**
 * @author: Vitaliy Ofat <i@vitaliy-ofat.com>
 *
 * @var $grid TheNandan\Grids\Grid
 */
use TheNandan\Grids\Components\ExcelExport;
?>
<span>
    <a data-toggle="tooltip" data-placement="bottom" title="Excel Export"
        href="<?php echo $grid
            ->getInputProcessor()
            ->getUrl([ExcelExport::INPUT_PARAM => 1])
        ?>"
        class="btn btn-sm btn-default"
        >
        <i class="fas fa-file-excel"></i>
        Export
    </a>
</span>
