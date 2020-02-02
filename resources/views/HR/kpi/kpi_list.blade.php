@extends('layouts.app')
@section('content')
    <style>
        .row-select-toggle{
            cursor: default;
        }
        .dropdown-item {
            margin: 0;
            padding: 5px;
        }
    </style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox-title">
                <h2>Kpi List</h2>
                <div class="ibox-tools">
                    {{--<button class="btn btn-secondary @if($list_group == 'point') btn-md @else btn-xs @endif ladda-button" id="point_view"><i class="fa fa-eye" aria-hidden="true"></i> Point Wise View</button>--}}
                    {{--<button class="btn btn-primary btn-xs @if($list_group == 'house') btn-md @else btn-xs @endif  ladda-button" id="house_view"><i class="fa fa-eye" aria-hidden="true"></i> House Wise View</button>--}}

                    <button class="btn btn-success btn-xs  ladda-button no-display" id="kpi_view"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
                    <button class="btn btn-warning btn-xs ladda-button no-display" id="kpi_edit"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                    <button class="btn btn-danger btn-xs ladda-button no-display" id="kpi_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                </div>
            </div>
            <div class="ibox-content">
                <table id="kpi_list" class="table data-table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="15%">House</th>
                            <th width="25%">Point</th>
                            <th width="15%">Config Name</th>
                            <th width="10%">Config Code</th>
                            <th width="15%">Month</th>
                            <th width="15%">FF Name</th>
                            <th width="20%">Kpi Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($kpi_list as $list)
                        <tr id="{{$list->bat_kpi_configs_id}}" data-code="{{$list->kpi_config_code}}" data-month="{{strtotime($list->config_month)}}" data-type="{{$list_group??''}}"   @if($list_group == 'house') data-dsid={{$list->bat_company_id}}@endif>
                            <td>{{$list->company_name}}</td>
                            <td> @if($list_group == 'house') {{$list->point_group}} @else  {{$list->point_name}} @endif</td>
                            <td> {{$list->bat_kpi_configs_name}} </td>
                            <td>{{$list->kpi_config_code}}</td>
                            <td>{{$list->config_month}}</td>
                            <td>{{$list->designation}}</td>
                            <td>{{$list->kpi_name}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
       $('.data-table').dataTable({
           "pageLength":20
       } );
    });

    var selected = [];
    var selected_type = [];
    var selected_month = [];
    var selected_house=[];
    var selected_config_code=[];
    $(document).on('click','#kpi_list tbody tr',function (e) {
       $obj = $(this);
       if(!$(this).attr('id')){  return true; }

        /*add this for new customize*/
        selected = [];
        selected_type = [];
        selected_month = [];
        $('#kpi_list tbody tr').not($(this)).removeClass('selected');
        /* end this */

       $obj.toggleClass('selected');
       var id = $obj.attr('id');

       if ($obj.hasClass( "selected" )){
           selected.push(id);
           selected_type.push($obj.data('type'));
           selected_month.push($obj.data('month'));
           @if($list_group == 'house') selected_house.push($obj.data('dsid')); selected_config_code.push($obj.data('code'));  @endif
       }else{
           var index = selected.indexOf(id);
           selected.splice(index,1);
           selected_type.splice($.inArray($obj.data('type'), selected_type), 1);
           selected_month.splice($.inArray($obj.data('month'), selected_month), 1);

           @if($list_group == 'house') selected_house.splice($.inArray($obj.data('dsid'), selected_house), 1); selected_config_code.splice($.inArray($obj.data('code'), selected_config_code), 1); @endif
       }


       if(selected.length==1) {
           $('#kpi_view').show();
           var curent_month ="{{strtotime(date("Y-m"))}}";
           if(selected_month[0] > curent_month){
                $('#kpi_edit').show();
           }else{
                $('#kpi_edit').hide();
           }
       }else{
           $('#kpi_view').hide();
           $('#kpi_edit').hide();
       }

    });

    $(document).on('click','#kpi_view',function () {
        var add_url='';
        @if($list_group == 'house')  add_url=selected_config_code[0]+'/'+selected_type[0]+'/'+selected_house[0];
        @else
                add_url=selected[0]+'/'+selected_type[0];
        @endif
        //console.log(add_url);
       window.location='{{URL::to('location-wise-kpi')}}/'+add_url;
    });
    //Edit
    $(document).on('click','#kpi_edit',function () {
        var kpi_code = $('#kpi_list tr.selected').data('code');
        window.location='{{URL::to('kpi-create')}}/'+kpi_code;
    });

    $(document).on('click','#point_view',function () {
        window.location='{{URL::to('get-kpi-list')}}';
    });

    $(document).on('click','#house_view',function () {
        window.location='{{URL::to('get-kpi-list')}}/house';
    });



</script>
@endsection
