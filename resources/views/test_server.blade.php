@extends('layouts.app')
@section('title')
    Tabulization Example
@endsection

@section('css')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/fixedColumns.dataTables.min.css') }}" rel="stylesheet">
    <style>
        table{
            border-spacing: 0;
        }
        th, td {white-space: nowrap; }
        table.dataTable {
            clear: both;
            margin: 0 !important;
            background-color: #fff;
            max-width: none !important;
            border-collapse: separate !important;
        }
        div.DTFC_LeftWrapper table.dataTable, div.DTFC_RightWrapper table.dataTable {
            margin-bottom: 0;
            margin-top: -1px !important;
            z-index: 2;
            background-color: #fff;
        }
        div.DTFC_LeftWrapper table.dataTable.no-footer, div.DTFC_RightWrapper table.dataTable.no-footer {
            border-top: 1px solid #eee !important;
            border-bottom: 1px !important;
        }
    </style>
@endsection

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{$page_title or 'Report Grid Page'}}</h2>
        </div>
        <div class="col-lg-2">
        </div>
    </div>
    <div class="wrapper animated fadeInRight" style="padding: 0; margin: 0 -15px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{$grid_title or 'Report Grid'}}</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#" class="dropdown-item">Config option 1</a>
                                </li>
                                <li><a href="#" class="dropdown-item">Config option 2</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if(isset($custom_search) && !empty($custom_search))
                            {{-- CONTAIN DATA ELEMENTS -----
                                'type' == text / number / select / checkbox / date / daterange
                                'name' ==
                                'label' ==
                                'placeholder' ==
                                'id' ==
                                'class' ==
                                'dataprovider' == FUNCTION_NAME
                            ----------------}}
                            <form id="custom_search" action="" method="post">
                                <div class="col-md-12 row">
                                    @foreach($custom_search as $cust_src)
                                        @php
                                            /*dd($cust_src)*/
                                        @endphp
                                        @if($cust_src['type'] == 'text' || $cust_src['type'] == 'number' )
                                            <div class="col-md-3">
                                                <input type="{{$cust_src['type']}}"
                                                       placeholder="{{isset($cust_src['placeholder']) ? 'Search ' . $cust_src['placeholder'] : 'Search ' . $cust_src['name']}}"
                                                       class="form-control {{$cust_src['class']}}"
                                                       id="{{isset($cust_src['id']) ? $cust_src['id'] : $cust_src['name']}}"
                                                       name="{{$cust_src['name']}}"
                                                       value=""/>
                                            </div>
                                        @endif
                                        @if($cust_src['type'] == 'select')
                                            <div class="col-md-3">
                                                <select class="form-control {{$cust_src['class']}} selectbox"
                                                        id="{{isset($cust_src['id']) ? $cust_src['id'] : $cust_src['name']}}"
                                                        name="{{$cust_src['name']}}"
                                                        data-dataprovider = "{{$cust_src['dataprovider']}}"></select>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="col-md-3">
                                        <button type="submit" id="custom_search_button" class="btn btn-warning"><i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="hr-line-dashed"></div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-hover" id="posts">
                                <thead>
                                    @php
                                    @endphp
                                    @if(!empty($header))
                                        @foreach ($header as $rownum => $header_row)
                                            <tr>
                                                @foreach ($header_row as $header_data)
                                                    <th colspan = "{{isset($header_data['colspan']) ? $header_data['colspan'] : 0}}">
                                                        {{strtoupper(str_replace('_', ' ', $header_data['column']))}}
                                                    </th>
                                                    @if($rownum === 'last')
                                                        @php
                                                            $columnname[] = ['data' => $header_data['column']]
                                                        @endphp
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/dataTables.fixedColumns.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            var dataTable = $('#posts').DataTable({
                "pageLength": 10,
                "responsive": true,
//                "dom": '<"html5buttons"B>lTfgitp',
                "scrollY": "400px",
                "scrollX": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    leftColumns: '{{$column_fix_l or 0}}',
                    rightColumns: '{{$column_fix_r or 0}}'
                },
                "processing": true,
                "serverSide": true,
                "language": {
                    processing: '<span class="text-warning"><i class="fa fa-spinner fa-spin fa-fw"></i>&nbsp;&nbsp; Processing ...</span>'
                },
                "ajax":{
                    "url": "{{ url('dataTableSubmit') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(data) {
                        data._token = "{{csrf_token()}}";
                        data.grid_function = "<?php echo $grid_function ?>";
                        data.custom_search = $('#custom_search').serializeArray();
                    }
                },
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv', title: '{{$grid_title or 'Report Grid'}}'},
                    {extend: 'excel', title: '{{$grid_title or 'Report Grid'}}'},
                    {extend: 'pdf', title: '{{$grid_title or 'Report Grid'}}'},
                    {extend: 'print',
                        customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                "columns": <?php echo json_encode($columnname); ?>
            });
            $('#custom_search').on( 'submit', function (event) {
                event.preventDefault();
                //console.log($('#custom_search').serialize());
                dataTable.draw();
            });
            $('.selectbox').each(function () {
                var dataprovidermethod = $(this).data('dataprovider');
                var selectID = $(this).attr('id');
                $.ajax({
                    type: "GET",
                    url: dataprovidermethod,
                    data: function(d) {
                    },
                    beforeSend: function(){
                    },
                    success: function (data){
                        $('#'+selectID).html(data)
                    }
                });
            });
        });

    </script>
@endsection