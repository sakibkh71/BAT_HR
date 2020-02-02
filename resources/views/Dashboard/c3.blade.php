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
            var titleYaxis = '<?php echo $ds_data->columnYtitle; ?>';
            var data = {"query" : sql};
            var url = "<?php echo url('dashboard-fetch-c3');?>";
            makeAjaxPost(data, url).then(response => {
                // console.log(response.data);
                Highcharts.chart(widget_id, {
                chart: {
                    height: 200,
                    type: 'column'
                },
                title: {
                    text: '<a href="{{route('attendance-entry')}}">View Attendance</a> ',
                    useHtml: true
                },
                xAxis: {
                    categories: response.labels
                },
                credits: { enabled: false },
                yAxis: {
                    min: 0,
                    title: {
                        text: titleYaxis
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style &&
                                Highcharts.defaultOptions.title.style.color
                            ) || 'gray'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series:response.data
            });
        });
        });




    </script>