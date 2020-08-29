<?php
/**
 * @author: Vitaliy Ofat <i@vitaliy-ofat.com>
 *
 * @var $grid TheNandan\Grids\Grid
 */
use TheNandan\Grids\Components\CsvExport;
?>
<span>
    <a data-toggle="tooltip" data-placement="bottom" title="Csv Export"
        href="<?php echo $grid
            ->getInputProcessor()
            ->getUrl([CsvExport::INPUT_PARAM => 1])
        ?>"
        class="btn btn-sm btn-default"
        >
        <i class="fas fa-file-csv"></i>
        Export
    </a>
</span>
