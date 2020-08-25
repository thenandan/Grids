<?php
namespace TheNandan\Grids\Components;

/**
 * Class THead
 *
 * The component for rendering THEAD html tag inside grid.
 *
 * @package TheNandan\Grids\Components
 */
class THead extends HtmlTag
{
    const NAME = 'thead';

    /**
     * Returns default set of child components.
     *
     * @return \TheNandan\Grids\Components\Base\ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        return [
            new ColumnHeadersRow,
            new FiltersRow
        ];
    }
}
