@extends('layouts.app')
@section('content')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="{{asset('public/js/plugins/packery/packery.pkgd.js')}}"></script>
    <script src="{{asset('public/js/plugins/packery/draggabilly.pkgd.js')}}"></script>
    <div class="wrapper wrapper-content animated showRight">
        <div class="row">
            <div class="col-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        {{--<h2>Dashboard For {!! session::get('MODULE_LANG') !!} Module</h2>--}}
                        <div class="ibox-tools">
                            <a class="btn btn-xs btn-primary" id="" href="{{URL::to('dashboard')}}"><i class="fa fa-check-square" aria-hidden="true"></i> Customize My widget</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if(empty($default_ds_data['userwisedata']))
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="widget style3 red-bg mt-0">
                                        <div class="row">
                                            <div class="col-4">
                                                <i class="fa fa-close fa-5x"></i>
                                            </div>
                                            <div class="col-8 text-right">
                                                <span>No Widget</span>
                                                <h2 class="font-bold">No Widget</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="grid col-12 m-0 p-0">
                                <div class="grid-spacing col-1 m-0 p-0"></div>
                                @foreach($default_ds_data['userwisedata'] as $key => $ds_data)
                                    @php
                                        $sqlparts = [
                                            'widget_id' => $ds_data->sys_dashboard_widget_id,
                                            'select_sql' => $ds_data->select_sql,
                                            'source_sql' => $ds_data->source_sql,
                                            'condition_sql' => $ds_data->condition_sql,
                                            'having_sql' => $ds_data->having_sql,
                                            'groupby_sql' => $ds_data->groupby_sql,
                                            'orderby_sql' => $ds_data->orderby_sql,
                                            'limit_sql' => $ds_data->limit_sql
                                        ];
                                        $sql = getMergedQueryForDashboard($sqlparts);
                                        $widget_id = 'widget-'.$ds_data->sys_dashboard_widget_id;
                                        $widget_div = 'div-'.$ds_data->sys_dashboard_widget_id;
                                        $pie_series_name = $ds_data->pie_series_name;
                                        $column_plot_option = !empty($ds_data->column_plot_option) ? $ds_data->column_plot_option : "{depth: 20}";
                                        $column_3d_option = !empty($ds_data->column_3d_option) ? $ds_data->column_3d_option : "{enabled: true,alpha: 0,beta: 0,depth: 20,viewDistance: 25}";
                                        $widget_div = 'div-'.$ds_data->sys_dashboard_widget_id;
                                        $pie_plot_option = !empty($ds_data->pie_plot_option) ? $ds_data->pie_plot_option : "{allowPointSelect:true,innerSize:100,depth:45,dataLabels:{enabled:true,format:'{point.name}'}}";
                                        $pie_3d_option = !empty($ds_data->pie_3d_option) ? $ds_data->pie_3d_option : "{enabled:true,alpha:45}";
                                    @endphp
                                    <div class="col-md-{{$ds_data->grid_space}} m-0 p-1 grid-item" data-id="{{$ds_data->sys_dashboard_widget_id}}" id="{{$widget_div}}">
                                        <div class="dashboard-container">
                                            <div class="ibox">
                                                <div class="ibox-title dashboard-title">
                                                    <h3 class="title-for-default">{{$ds_data->title}}</h3>
                                                    <div class="ibox-tools">
                                                        <a class=""> <i class="fa fa-arrows-alt draggable-icon"></i> </a>
                                                        <a class="collapse-link"> <i class="fa fa-chevron-up"></i></a>
                                                    </div>
                                                </div>
                                                <div class="ibox-content table-responsive dashboard-content">
                                                    @if($ds_data->widget_type == 'list')
                                                        @include('Dashboard.list')
                                                    @elseif($ds_data->widget_type == 'piechart')
                                                        @include('Dashboard.piechart')
                                                    @elseif($ds_data->widget_type == 'columnchart')
                                                        @include('Dashboard.columnchart')
                                                    @elseif($ds_data->widget_type == 'summary')
                                                        @include('Dashboard.summary')
                                                    @elseif($ds_data->widget_type == 'c3')
                                                        @include('Dashboard.c3')
                                                    @elseif($ds_data->widget_type == 'custom')
                                                        @include($ds_data->custom_widget_view_page)
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $user_id = session::get('USER_ID');
        $module_id = session::get('SELECTED_MODULE');
    @endphp
    <style>
        input.custom-check + label {
            display: initial;
        }
        .dashboard-container{
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-flex: 1;
            -ms-flex-positive: 1;
            flex-grow: 1;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);
            box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);
            background-color: #ffffff;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .dashboard-title{
            border: none;
            border-radius: 4px 4px 0 0;
            padding: 10px;
            min-height: 40px;
        }
        .dashboard-title h4{
            margin: 0;
        }
        .dashboard-content{
            border-top: 1px solid #ebedf2;
            background:#fff;
        }
        .dashboard-content .lazur-bg{
            background-color:#fff !important;
            color: #000;
            box-shadow:none;
            text-shadow: none;
        }
    </style>
    <script>
        Packery.prototype.getShiftPositions = function( attrName ) {
            attrName = attrName || 'id';
            var _this = this;
            return this.items.map( function( item ) {
                return {
                    attr: item.element.getAttribute( attrName ),
                    x: item.rect.x / _this.packer.width
                }
            });
        };
        Packery.prototype.initShiftLayout = function( positions, attribute ) {
            if ( !positions ) {
                this.layout();
                return;
            }
            if( typeof positions == 'string' ) {
                try {
                    positions = JSON.parse( positions );
                } catch( error ) {
                    console.error( 'JSON parse error: ' + error );
                    this.layout();
                    return;
                }
            }

            attribute = attribute || 'id'; // default to id attribute
            this._resetLayout();
            // set item order and horizontal position from saved positions
            this.items = positions.map( function(itemPosition) {
                var selector = '[' + attribute + '="' + itemPosition.attr  + '"]'
                var itemElem = this.element.querySelector( selector );
                var item = this.getItem( itemElem );
                item.rect.x = itemPosition.x * this.packer.width;
                return item;
            }, this );
            this.shiftLayout();
        };
        // -----------------------------//
        var $grid = $('.grid').packery({
            itemSelector: '.grid-item',
            columnWidth: '.grid-spacing',
            //columnWidth: 5,
            percentPosition: true,
            initLayout: true
        });
        var initPositions = '<?php echo $layouts; ?>';
        $grid.packery( 'initShiftLayout', initPositions, 'data-id' );
        $grid.find('.grid-item').each( function( i, itemElem ) {
            var draggie = new Draggabilly( itemElem, {
                handle: ".draggable-icon"
            } );
            $grid.packery( 'bindDraggabillyEvents', draggie );
        });
        $grid.on( 'dragItemPositioned', function() {
            var positions = $grid.packery('getShiftPositions', 'data-id');
            var data = {position:JSON.stringify(positions)};
            savePostionToDB(data);
            console.log(JSON.stringify( positions ));
        });
        function savePostionToDB(data){
            var url = '<?php echo url('dashboard-setposition').'/'.$user_id.'/'.$module_id;?>';
            makeAjaxPost(data, url).then(response => {
                //saved
            });
        }
    </script>
@endsection
