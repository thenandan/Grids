<?php

namespace TheNandan\Grids;


use Illuminate\Support\Facades\View;
use TheNandan\Grids\Grid as NayGrid;
use TheNandan\Grids\Components\HtmlTag;
use TheNandan\Grids\Components\CsvExport;
use TheNandan\Grids\Components\ColumnsHider;
use TheNandan\Grids\Components\ColumnHeadersRow;
use TheNandan\Grids\Components\Base\RenderableRegistry;
use TheNandan\Grids\Components\ExcelExport;
use TheNandan\Grids\Components\FiltersRow;
use TheNandan\Grids\Components\Laravel5\Pager;
use TheNandan\Grids\Components\OneCellRow;
use TheNandan\Grids\Components\RecordsPerPage;
use TheNandan\Grids\Components\RenderFunc;
use TheNandan\Grids\Components\ShowingRecords;
use TheNandan\Grids\Components\TFoot;
use TheNandan\Grids\Components\THead;
use TheNandan\Grids\Helpers\Row;
use TheNandan\Grids\Helpers\Column;
use Illuminate\Support\Facades\Gate;
use Collective\Html\HtmlFacade as HTML;

/**
 * Class LaravelGrid
 *
 * @package TheNandan\Grids
 */
class TheNandanGrid
{
    public const OPERATOR_LIKE = 'like';
    public const OPERATOR_EQ = '=';
    public const OPERATOR_NOT_EQ = '<>';
    public const OPERATOR_GT = '>';
    public const OPERATOR_LS = '<';
    public const OPERATOR_LSE = '<=';
    public const OPERATOR_GTE = '>=';

    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    private $gridConfig;
    private $customHeaderRow;
    private $hasDateRangePicker = false;
    private $hiddenColumns = [];
    private $exportPermission = null;

    /**
     * @param $source
     */
    public function setGridConfig($source): void
    {
        $this->gridConfig = new GridConfig($source);
    }

    /**
     * Set the default page size
     *
     * @param $number
     */
    public function setDefaultPageSize($number): void
    {
        $this->gridConfig->setPageSize($number);
    }

    /**
     * This method can be used to set the grid name
     *
     * @param $name
     */
    public function setGridName($name): void
    {
        $this->gridConfig->setName($name);
    }

    /**
     * This method can be used to set the caching time in minute
     *
     * @param $timeInMinute
     */
    public function setCachingTime($timeInMinute): void
    {
        $this->gridConfig->setCachingTime($timeInMinute);
    }

    /**
     * This method can be used to set column of grid
     *
     * @param $column
     * @param false $label
     * @param false $relation
     *
     * @return Column
     */
    public function addColumn($column, $label = false, $relation = false)
    {
        $column = new Column($column, $label, $relation);
        $column->setGrid($this);
        $this->gridConfig->addColumn($column->getColumn());
        return $column;
    }

    /**
     * @param $datePicker
     * @param $name
     */
    public function setDateRangePicker($datePicker, $name)
    {
        $filtersRow = $this->gridConfig->getComponentByNameRecursive(FiltersRow::NAME);
        $filtersRow->addComponent($datePicker);
        if (!$this->hasDateRangePicker) {
            $renderAssets = (new RenderFunc(function () {
                return HTML::style(asset('vendor/grids/datepicker.css'))
                    .HTML::script(asset('vendor/grids/moment.min.js'))
                    .HTML::script(asset('vendor/grids/datepicker.min.js'));
            }))
                ->setRenderSection('filters_row_column_'.$name);
            $filtersRow->addComponent($renderAssets);
        }
        $this->hasDateRangePicker = true;

    }

    /**
     * @param array|null $components
     * @return mixed
     */
    private function getFiltersRow(array $components = null)
    {
        $filtersRow = $this->gridConfig->getComponentByNameRecursive(FiltersRow::NAME);

        if (!empty($components)) {
            $filtersRow->addComponents($components);
        }

        return $filtersRow;
    }

    /**
     * @param array|int[] $recordsPerPage
     *
     * @return RecordsPerPage
     */
    public function setRecordsPerPage(array $recordsPerPage = [10, 20, 50, 100, 200]): RecordsPerPage
    {
        return (new RecordsPerPage())->setVariants($recordsPerPage);
    }

    /**
     * @param $fileName
     *
     * @return ExcelExport
     */
    public function setExcelExport($fileName): ExcelExport
    {
        return (new ExcelExport())->setFileName($fileName);
    }

    /**
     * @param array $attributes
     * @param $component
     *
     * @return HtmlTag
     */
    public function setHtmlTag(array $attributes, $component): HtmlTag
    {
        return (new HtmlTag())
            ->setAttributes($attributes)
            ->addComponent($component);
    }

    /**
     * @return HtmlTag
     */
    private function setResetFiltersButton(): HtmlTag
    {
        return (new HtmlTag())
            ->setContent('<i class="fas fa-filter"></i> Reset')
            ->setTagName('button')
            ->setRenderSection(RenderableRegistry::SECTION_END)
            ->setAttributes([
                'type' => 'button',
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom',
                'title' => 'Reset Filters',
                'class' => 'btn float-right m-1 btn-success btn-sm clear_filters',
            ]);
    }

    /**
     * @param array $components
     * @return OneCellRow
     */
    private function getOneCellRow(array $components): OneCellRow
    {
        return (new OneCellRow())->setComponents($components);
    }

    /**
     * @param array $components
     * @return THead
     */
    private function getThead(array $components): THead
    {
        return (new THead())->setComponents($components);
    }

    /**
     * @param array $components
     * @return TFoot
     */
    private function getTFoot(array $components): TFoot
    {
        return (new TFoot())->setComponents($components);
    }

    public function getCsvExport($fileName)
    {
        return (new CsvExport())->setFileName($fileName);
    }


    /**
     *
     */
    public function setDefaultComponents(): void
    {
        $pagination = new Pager();
        $columnHeadersRow = new ColumnHeadersRow();
        $filersRow = $this->getFiltersRow();
        $recordsPerPage = $this->setRecordsPerPage();
        $showingRecords = new ShowingRecords();
        $columnsHider = (new ColumnsHider())->setHiddenByDefault($this->hiddenColumns);
//        $exportExcel = $this->setExcelExport('file-'.date('d-m-Y-h-i-s'));
        $exportCSV = $this->getCsvExport('CSV-' . date('d-m-Y-h-i-s'));
        $recordsPerPageTag = $this->setHtmlTag(['class' => 'float-left m-1'], $recordsPerPage);
        $columnsHiderTag = $this->setHtmlTag(['class' => 'float-right m-1'], $columnsHider);
        $exportCsvTag = $this->setHtmlTag(['class' => 'float-right m-1'], $exportCSV);
//        $exportExcelTag = $this->setHtmlTag(['class' => 'float-right m-1 excel_export'], $exportExcel);
        $resetFiltersButton = $this->setResetFiltersButton();
        $resetFiltersButtonTag = $this->setHtmlTag(['class' => 'pull-right'], $resetFiltersButton);
        $paginationTag = $this->setHtmlTag(['class' => 'float-right'], $pagination);
        $showingRecordsTag = $this->setHtmlTag(['class' => 'float-left'], $showingRecords);
        $head[] = $recordsPerPageTag;
        $head[] = $resetFiltersButtonTag;
        $head[] = $exportCsvTag;
//        $head[] = $exportExcelTag;
//        $head[] = $columnsHiderTag;
        $tfoot[] = $showingRecordsTag;
        $tfoot[] = $paginationTag;
        $headOneCellRow = $this->getOneCellRow($head);
        $footOneCellRow = $this->getOneCellRow($tfoot);
        $thead = $this->getThead([]);
        $thead->addComponent($headOneCellRow);
        if (!empty($this->customHeaderRow)) {
            $thead->addComponent($this->customHeaderRow);
        }
        $thead->addComponent($columnHeadersRow);
        $thead->addComponent($filersRow);
        $tfoot = $this->getTFoot([$footOneCellRow]);
        $this->gridConfig->setComponents([$thead, $tfoot]);
    }



    /**
     * @return View|string
     */
    public function render()
    {
        $this->setDefaultComponents();
        $grid = new NayGrid($this->gridConfig);
        return $grid->render();
    }
}
