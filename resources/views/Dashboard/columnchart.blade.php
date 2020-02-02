<div id="{{$widget_id}}"></div>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        var sql = "<?php echo $sql; ?>";
        var widget_id = '<?php echo $widget_id; ?>';
        var widget_subtitle = '<?php echo $ds_data->subtitle; ?>';
        var columnXtitle = '<?php echo $ds_data->columnXtitle; ?>';
        var columnYtitle = '<?php echo $ds_data->columnYtitle; ?>';
        var columnplotoption = <?php echo $column_plot_option; ?>;
        var column3doption = <?php echo $column_3d_option; ?>;
        var data = {"query" : sql};
        var url = "<?php echo url('dashboard-fetch-pie');?>";
        makeAjaxPost(data, url).then(response => {
            Highcharts.chart(widget_id, {
            chart: {
                height: 200,
                type: 'column',
                options3d: column3doption
            },
            credits: { enabled: false },
            title: { text: '' },
            subtitle: {text: widget_subtitle},
            xAxis: {
                type: 'category',
                title: {text: columnXtitle},
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {text: columnYtitle}
            },
            legend: {enabled: false},
            tooltip: {
                pointFormat: '<b>{point.y:.1f}</b>'
            },
            plotOptions: {
                column: columnplotoption
            },
            series: [{
                name: 'Population',
                colorByPoint: true,
                data: response,
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#000000',
                    align: 'right',
                    format: '{point.y:.1f}', // one decimal
                    y: 10, // 10 pixels down from the top
                    style: {
                        fontSize: '10px'
                    }
                }
            }]
        });
        $grid.packery( 'initShiftLayout', initPositions, 'data-id' );
    });
    });
    /*************************************************************/

</script>