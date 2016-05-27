jQuery(function($){

    $('.filterable .btn-filter').click(function(){

        var $panel      = $(this).parents('.filterable');
        var $filters    = $panel.find('.filters input');
        var $filtersBox = $panel.find('.filters');
        var $tbody      = $panel.find('.table tbody');

        if ( $filters.prop('disabled') == true ) {

            $filtersBox.show();
            $filters.css('display','block');
            $filters.prop('disabled', false);
            $filters.first().focus();

        } else {

            $filtersBox.hide();
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();

        }

    });

    $('.filterable .filters input').keyup(function(e){

        var code = e.keyCode || e.which;    /* IGNORE TAB KEY */
        if ( code === '9' ) return;

        var $input       = $(this);
        var inputContent = $input.val().toLowerCase();
        var $panel       = $input.parents('.filterable');
        var column       = $panel.find('.filters th').index($input.parents('th'));
        var $table       = $panel.find('.timetable_big');
        var $rows        = $table.find('tbody tr');

        _p.debugLog("inputContent: ", inputContent);

        _p.debugLog($rows);

        /* FILTER FUNCTION */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });

        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */

        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }

    });

});

