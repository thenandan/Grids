<?php

namespace TheNandan\Grids;


use TheNandan\Grids\Grid as NayGrid;
use TheNandan\Grids\GridConfig;
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
use TheNandan\Grids\EloquentDataProvider;
use TheNandan\Grids\Build\Helpers\Row;
use TheNandan\Grids\Build\Helpers\Column;
use Illuminate\Support\Facades\Gate;

class LaravelGrid
{
    const OPERATOR_LIKE = 'like';
    const OPERATOR_EQ = '=';
    const OPERATOR_NOT_EQ = '<>';
    const OPERATOR_GT = '>';
    const OPERATOR_LS = '<';
    const OPERATOR_LSE = '<=';
    const OPERATOR_GTE = '>=';

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    private $gridConfig;
    private $customHeaderRow;
    private $hasDateRangePicker = false;
    private $hiddenColumns = [];
    private $exportPermission = null;

    public function __construct($source)
    {
        $this->gridConfig = new GridConfig();
        $this->setDataSource($source);
        $this->gridConfig->setPageSize(20);
        $this->setDefaultSort('id', SORT_DESC);
    }

    public function setDataSource($source)
    {
        $this->gridConfig->setDataProvider(
            new EloquentDataProvider($source)
        );
    }

    public function setDefaultPageSize($number)
    {
        $this->gridConfig->setPageSize($number);
    }

    public function setDefaultSort($columnName, $direction)
    {
        $this->gridConfig->setDefaultSort($columnName, $direction);
    }

    public function setGridName($name)
    {
        $this->gridConfig->setName($name);
    }

    public function addColumn($name, $label = false, $relation = false)
    {
        $column = new Column($name, $label, $relation);
        $column->setGrid($this);
        $this->gridConfig->addColumn($column->getColumn());

        return $column;
    }

    public function addSelectColumn()
    {
        $this->addColumn('select_column', 'Select')->setWidth('55px')->setTextAlignment('center')->setCallback(function ($val, $row) {
            return '<input class="export_checkbox" type="checkbox" value="'.$row->getSrc()->id.'" name="selected[]" />';
        });

        $filter = (new RenderFunc(function () {
            return '<span style="width:100%; text-align:center; display:block;"><input class="select_all_checkbox" type="checkbox" /></span>';
        }))->setRenderSection('filters_row_column_select_column');

        $this->getFiltersRow([$filter]);
    }

    public function setDateRangePicker($datePicker, $name)
    {
        $filtersRow = $this->gridConfig->getComponentByNameRecursive(FiltersRow::NAME);
        $filtersRow->addComponent($datePicker);
        if (!$this->hasDateRangePicker) {
            $renderAssets = (new RenderFunc(function () {
                return HTML::style('admin/plugins/daterangepicker/daterangepicker-bs3.css')
                    .HTML::script('admin/plugins/daterangepicker/moment.min.js')
                    .HTML::script('admin/plugins/daterangepicker/daterangepicker.js');
            }))
                ->setRenderSection('filters_row_column_'.$name);
            $filtersRow->addComponent($renderAssets);
        }
        $this->hasDateRangePicker = true;
    }

    public function setExportPermission($permission)
    {
        $this->exportPermission = $permission;
    }

    public function addHiddenColumn($name)
    {
        $this->hiddenColumns[] = $name;
    }

    public function addComponent($component)
    {
        $this->gridConfig->addComponent($component);
    }

    public function setComponents(array $components)
    {
        $this->gridConfig->setComponents($components);
    }

    public function getThead(array $components)
    {
        return (new THead())->setComponents($components);
    }

    public function getTFoot(array $components)
    {
        return (new TFoot())->setComponents($components);
    }

    public function getOneCellRow(array $components)
    {
        return (new OneCellRow())->setComponents($components);
    }

    public function getColumnHeadersRow()
    {
        return (new ColumnHeadersRow());
    }

    public function getFiltersRow(array $components = null)
    {
        $filtersRow = $this->gridConfig->getComponentByNameRecursive(FiltersRow::NAME);

        if (!empty($components)) {
            $filtersRow->addComponents($components);
        }

        return $filtersRow;
    }

    public function getRecordsPerPage()
    {
        return (new RecordsPerPage())->setVariants([10, 20, 50, 100, 200]);
    }

    public function getShowingRecords()
    {
        return (new ShowingRecords());
    }

    public function getPagination()
    {
        return (new Pager());
    }

    public function getHtmlTag(array $attributes, $component)
    {
        return (new HtmlTag())
            ->setAttributes($attributes)
            ->addComponent($component);
    }

    public function getColumnsHider()
    {
        return (new ColumnsHider());
    }

    public function getCsvExport($fileName)
    {
        return (new CsvExport())->setFileName($fileName);
    }

    public function getExcelExport($fileName)
    {
        return (new ExcelExport())->setFileName($fileName);
    }

    public function getResetFiltersButton()
    {
        return (new HtmlTag())
            ->setContent('<span class="glyphicon glyphicon-repeat"></span> Reset')
            ->setTagName('button')
            ->setRenderSection(RenderableRegistry::SECTION_END)
            ->setAttributes([
                'type' => 'button',
                'class' => 'btn btn-success btn-sm clear_filters',
            ]);
    }

    public function getRow($columns)
    {
        return (new Row())->setColumns($columns);
    }

    public function addCustomRow($columns)
    {
        $this->customHeaderRow = $this->getRow($columns);
    }

    public function setDefaultComponents()
    {
        $pagination = $this->getPagination();
        $columnHeadersRow = $this->getColumnHeadersRow();
        $filersRow = $this->getFiltersRow();
        $recordsPerPage = $this->getRecordsPerPage();
        $showingRecords = $this->getShowingRecords();
        $columnsHider = $this->getColumnsHider()->setHiddenByDefault($this->hiddenColumns);
        $exportExcel = $this->getExcelExport('Excel-'.date('d-m-Y-h-i-s'));
        // $exportCSV = $this->getCsvExport('CSV-' . date('d-m-Y-h-i-s'));
        $recordsPerPageTag = $this->getHtmlTag(['class' => 'pull-left margin-right pipe'], $recordsPerPage);
        $showingRecordsTag = $this->getHtmlTag(['class' => 'pull-left'], $showingRecords);
        $columnsHiderTag = $this->getHtmlTag(['class' => 'pull-right margin-right'], $columnsHider);
        // $exportCsvTag = $this->getHtmlTag(['class' => 'pull-right'], $exportCSV);
        $exportExcelTag = $this->getHtmlTag(['class' => 'pull-right margin-right excel_export'], $exportExcel);
        $resetFiltersButton = $this->getResetFiltersButton();
        $resetFiltersButtonTag = $this->getHtmlTag(['class' => 'pull-right'], $resetFiltersButton);
        $paginationTag = $this->getHtmlTag(['class' => 'pull-right'], $pagination);
        $head[] = $recordsPerPageTag;
        $head[] = $showingRecordsTag;
        $head[] = $resetFiltersButtonTag;
        if (empty($this->exportPermission) || Gate::allows($this->exportPermission)) {
            $head[] = $exportExcelTag;
        }
        $head[] = $columnsHiderTag;
        $headOneCellRow = $this->getOneCellRow($head);
        $footOneCellRow = $this->getOneCellRow([$paginationTag]);
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

    public function render()
    {
        $this->setDefaultComponents();
        $grid = new NayGrid($this->gridConfig);
        if (!empty($this->customHeaderRow)) {
            $this->customHeaderRow->setGrid($grid);
        }

        return $grid->render();
    }
}
