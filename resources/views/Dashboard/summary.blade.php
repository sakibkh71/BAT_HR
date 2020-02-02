<div class="row">
    <div class="col-4">
        <i class="{{$ds_data->icon_class}}"></i>
    </div>
    <div class="col-8 text-right">
        <span>{{$ds_data->title}}</span>
        <h2 class="font-bold" id="{{$widget_id}}"></h2>
    </div>
</div>
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
        var url = "<?php echo url('dashboard-fetch-summary');?>";
        makeAjaxPost(data, url).then(response => {
            $('#'+widget_id).empty();
        $('#'+widget_id).html(response);
        $grid.packery( 'initShiftLayout', initPositions, 'data-id' );
    });

    });
</script>