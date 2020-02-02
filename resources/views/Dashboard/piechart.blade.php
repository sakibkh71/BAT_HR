<div id="{{$widget_id}}"> </div>
<script>

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        var sql = "<?php echo $sql; ?>";
        var widget_id = '<?php echo $widget_id; ?>';
        var widget_title = '<?php echo $ds_data->title; ?>';
        var widget_subtitle = '<?php echo $ds_data->subtitle; ?>';
        var pie_series_name = '<?php echo $pie_series_name; ?>';
        var pieplotoption = <?php echo $pie_plot_option; ?>;
        var pie3doption = <?php echo $pie_3d_option; ?>;
        var data = {"query" : sql};
        var url = "<?php echo url('dashboard-fetch-pie');?>";
        makeAjaxPost(data, url).then(response => {
            Highcharts.chart(widget_id, {
            chart: {
                height: 200,
                type: 'pie',
                //options3d: pie3doption,
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
            },
            title: { text: '' },
            subtitle: { text: widget_subtitle },
            tooltip: {
                pointFormat: '{series.name}: {point.y}'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}',
                    }
                }
            },
            credits: { enabled: false },
            series: [{
                name: pie_series_name,
                data: response
            }]
        });
        $grid.packery( 'initShiftLayout', initPositions, 'data-id' );
    });
    });
    /*************************************************************/

</script>