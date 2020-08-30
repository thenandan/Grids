
jQuery(document).ready(function ($) {

    /**
     * Handle Pagination click
     */
    $('.grid').on('click', '.pagination li a', function (e) {
        e.preventDefault();
        let grid = $(this).closest('.grid');
        let url = $(this).prop('href');
        submitGridForm(grid, url, null);
        return false;
    }).on('click', '.table th small a', function (e) {
        e.preventDefault();
        let grid = $(this).closest('.grid');
        let url = $(this).prop('href');
        submitGridForm(grid, url, null);
        return false;
    }).on('submit', 'form', function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        let grid = $(this).closest('.grid');
        submitGridForm(grid, "", data);
        return false;
    });

    $('body').on('click', 'button.cancelBtn', function (e) {
        $('.grid form').submit();
    }).on('click', 'button.applyBtn', function (e) {
        $('.grid form').submit();
    }).on('change', 'select', function () {
        $('.grid form').submit();
    })


    /**
     * Tooltip & Popover enable
     */
    $('[data-toggle="tooltip"]').not('.column_hider').tooltip()
    $('[data-toggle="popover"]').not('.column_hider').popover()
});

/**
 * Submit the Grid
 *
 * @param grid
 * @param url
 * @param data
 */
function submitGridForm(grid, url, data)
{
    jQuery(".ajax-loader").css('display', 'absolute')
    jQuery.ajax(url, {
        type: "GET",
        data: data,
        cache: false,
        success: function(data) {
            grid.html(data);
            jQuery(".ajax-loader").css('display', 'none')
        },
        error: function(data) {
            console.log(data)
        }
    });
}

