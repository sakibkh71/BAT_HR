<div id="{{$widget_id}}"></div>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        var sql = '<?php echo $sql; ?>';
        var widget_id = '<?php echo $widget_id; ?>';

        var data = {"query" : sql};
        var url = "<?php echo url('dashboard-fetch-list');?>";
        makeAjaxPost(data, url).then(response => {
            createListTable(response, widget_id);
        $grid.packery( 'initShiftLayout', initPositions, 'data-id' );
    });
    });
    function createListTable(response, widget_id){
        var html = '';
        html += '<table class="table table-hover no-margins">';
        html += '<tbody>';
        $.each(response.table_data, (i, row)=>{
            html += '<tr>';
        $.each(row, (key, data)=>{
            html += '<td class="p-1">'+data+'</td>';
    });
        html += '</tr>';
    });
        html += '</tbody>';
        html += '</table>';
        html += '<div class="row"></div>';

        $('#'+widget_id).empty();
        $('#'+widget_id).html(html);
    }
</script>