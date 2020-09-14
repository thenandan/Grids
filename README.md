Grids
=====

### `Data Grids Framework for Laravel`
This package is forked from [Nayjest/Grids](https://github.com/Nayjest/Grids) and is wrapper for the package,
does not support (laravel < 5) and used bootstrap 4 by default.

## Requirements

* Laravel >= 5+
* php >= 7.1
* bootstrap 4
* fontawesome 5

## Installation

```composer 
    composer require thenandan/grids
```

## Publishing Assets
```php
    php artisan vendor:publish --tag=public
```

## Creating the Grid Class
 
```php
    php artisan make:grid CompanyGrid
```
This will generate the CompanyGrid class as below - 
```php
<?php

namespace App\Grids;

use Illuminate\Database\Eloquent\Model;
use TheNandan\Grids\BaseGrid;

class CompanyGrid extends BaseGrid
{
    /**
     * Set root model for the grid query
     *
     * @return Model
     */
    protected function setModel(): Model
    {
        // return new model instance
    }

    /**
     * Configure your grid
     *
     * @return void
     */
    protected function configureGrid(): void
    {
        // Configure your grid column
    }
}

```
Now we are going to configure our grid as below - 
```php
<?php

namespace App\Grids;

use Illuminate\Database\Eloquent\Model;
use TheNandan\Grids\BaseGrid;

class CompanyGrid extends BaseGrid
{
    /**
     * Set root model for the grid query
     *
     * @return Model
     */
    protected function setModel(): Model
    {
        return new Company();
    }

    /**
     * Configure your grid
     *
     * @return void
     */
    protected function configureGrid(): void
    {
        $this->grid->setCachingTime(0);
        $this->grid->addColumn('id', 'Id')->setSortable();
        $this->grid->addColumn('unique_id', 'Unique ID')->setSortable()->setSearchFilter();
        $this->grid->addColumn('name', 'Company Name')->setSortable()->setSearchFilter();
        $this->grid->addColumn('created_at', 'Added On')->setCallback(function ($createdAt) {
            if (null === $createdAt || !$createdAt instanceof Carbon) {
                return '-';
            }
            return Carbon::createFromTimestamp($createdAt->timestamp)->isoFormat('LLLL');
        })->setDateFilter();

        $this->grid->addColumn('edit_client', 'Edit')->setCallback(function ($val, $row) {
            return "<a href='#'><i class='fas fa-edit'></i></a>";
        });

        $this->grid->addColumn('delete_client', 'Delete')->setCallback(function ($val, $row) {
            return "<a href='#' class='text-danger'><i class='fas fa-trash'></i></a>";
        });
    }
}
```
That's all, our grid is configured.

## Rendering the Grid in UI
Create a blade(view) file (ex. company.blade.php) and include the grid in it. see below example - 
```blade
@extends('layouts.app')

@section('content')
    @include('grids::default')
@endsection

```

# Note
Make you to add the below in your main layout -
1. In you header - 
```blade
@yield('grid_css')
```
1. Before closing the body tag - 
```blade
@yield('grid_js')
```
Now create you route and return the company view. You will see a grid something similar to following.

![screenshot](https://opnsrc.s3.ap-south-1.amazonaws.com/grid.png)
 
 
## Available Grid Methods
* setGridName($name) - Set the name of the grid
* setDefaultPageSize($number) - Set the default page size.
* setCachingTime($timeInMinute) - Set the caching time in minute.
* addColumn($column, $label = false, $relation = false) - Add a new column in grid.
* setRecordsPerPage(array $recordsPerPage) - Set records perPage dropdown list.

## Available nested column methods
* setSortable() - When used column becomes sortable
* setCallback($function) - Can be used to customize the value of the cell
* shorten(int $noOfChar = 20) - Can be used to limit the no. of character in the grid cell,  When used below methods become available
    * setTitle() - Set the cell original value in html title attribute
    * setToolTip($isHtml = false) - Set the cell original value in tooltip
    * setPopover($isHtml = false, $title = null) - Set the cell original value in a popover, Here $title variable can be used to set the title of popover
* setLink($link, $name) - Can be used to set a link
* setSearchFilter($operator = TheNandanGrid::OPERATOR_LIKE) - Can be used to make the column searchable
* More methods are coming soon - 


## License

© 2020&mdash;2020 Keshari Nandan

License: Proprietary
