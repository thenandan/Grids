$('.grid').on('click', '.clear_filters', function(){
    var form = $(this).closest('form');
    form.find('input').val('');
    form.find('select').prop('selectedIndex', 0);
    form.submit();
    return false;
});

$('.grid').on('change', 'select', function(e) {
    $(this).closest('.grid form').submit();
    return false;
});

$('.grid').on('click', '.pagination li a', function() {
    var grid = $(this).closest('.grid');
    var url = $(this).prop('href');
    submitGridForm(grid, url, null);
    return false;
});

$('.grid').on('click', 'thead th small a[title~="Sort"]', function() {
    var grid = $(this).closest('.grid');
    var url = $(this).prop('href');

    if (url === '')
        return false;

    submitGridForm(grid, url, null);
    return false;
});

$('.grid').on('submit', 'form', function() {
    var data = $(this).serialize();
    var grid = $(this).closest('.grid');
    submitGridForm(grid, "", data);
    return false;
});

function submitGridForm(grid, url, data) {
    $('.cache_form .changed_through_ajax').val('1');
    $('.ajax-loader').show();
    $.ajax(url, {
        type: "GET",
        data: data,
        cache: false,
        success: function(data) {
            grid.html(data);
            $('.ajax-loader').hide();
        },
        error: function(data) {

        }
    });
}

$('.grid').on('change', '.export_checkbox', function() {
    var grid = $(this).closest('.grid');
    var exportButton = grid.find('.excel_export a');
    exportButton.prop('href', exportButton.prop('href').replace('&' + $(this).prop('name') + '=' + $(this).val(), ''));
    exportButton.prop('href', exportButton.prop('href').replace($(this).prop('name') + '=' + $(this).val(), ''));
    if (this.checked)
        exportButton.prop('href', addParameter(exportButton.prop('href'), $(this).prop('name'), $(this).val(), false));
});

$('.grid').on('change', '.select_all_checkbox', function(){
    var checkboxes = $(this).closest('.grid').find('.export_checkbox');
    checkboxes.filter(':checked').trigger('click');
    if(this.checked){
        checkboxes.trigger('click');
    }
});

function addParameter(url, parameterName, parameterValue, replaceDuplicates) {
    var cl;
    if (url.indexOf('#') > 0) {
        cl = url.indexOf('#');
        urlhash = url.substring(url.indexOf('#'), url.length);
    } else {
        urlhash = '';
        cl = url.length;
    }
    sourceUrl = url.substring(0, cl);

    var urlParts = sourceUrl.split("?");
    var newQueryString = "";

    if (urlParts.length > 1) {
        var parameters = urlParts[1].split("&");
        for (var i = 0;
             (i < parameters.length); i++) {
            var parameterParts = parameters[i].split("=");
            if (!(replaceDuplicates && parameterParts[0] == parameterName)) {
                if (newQueryString === "")
                    newQueryString = "?";
                else
                    newQueryString += "&";
                newQueryString += parameterParts[0] + "=" + (parameterParts[1] ? parameterParts[1] : '');
            }
        }
    }
    if (newQueryString === "")
        newQueryString = "?";

    if (newQueryString !== "" && newQueryString != '?')
        newQueryString += "&";
    newQueryString += parameterName + "=" + (parameterValue ? parameterValue : '');

    return urlParts[0] + newQueryString + urlhash;
}

if($('.cache_form .changed_through_ajax').val() == 1){
    $( ".grid" ).each(function( index ) {
        var tableID = $(this).find('table').attr('id');
        data = tableID + "[xls]=0";
        submitGridForm($(this), '', data);
    });
}
