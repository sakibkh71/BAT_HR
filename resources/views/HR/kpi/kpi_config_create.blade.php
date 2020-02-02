@extends('layouts.app')
@section('content')
    <?php 
        $config_name = !empty($config_val)?$config_val->bat_kpi_configs_name: '';
        $config_id = !empty($config_val)?$config_val->bat_kpi_configs_id: 0;
        $month_from = !empty($config_val)?$config_val->start_month: '';
        $month_to = !empty($config_val)?$config_val->end_month: '';
        $parameter = '';
        $have_range = !empty($config_val)?$config_val->have_range: '';
        $ranges = '';
    ?>

    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>KPI Configuration</h2>
                        <div class="ibox-tools">
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="row col-md-12">
                            <div class="col-md-2">
                                
                            </div>
                            <div class="col-md-8">
                                <form id="formId">

                                    <input type="hidden" name="hdn_conf_id" id="hdn_conf_id" value="{{$config_id}}">
                                    <div class="row col-md-12">
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Config Name</label>
                                            <div class="input-group">
                                                <input type="text" placeholder="Enter Name" class="form-control config_name" value="{{$config_name}}" id="" name="config_name" required/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Select Month</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control month_from"
                                                       value="{{$month_from}}"
                                                       id="month_from"
                                                       data-date-format="yyyy-mm"
                                                       name="month_from" required/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group col-md-4">
                                            <label class="form-label">Month To</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       placeholder=""
                                                       class="form-control month_to"
                                                       value="{{$month_to}}"
                                                       id="month_to"
                                                       data-date-format="yyyy-mm"
                                                       name="month_to" required/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div> -->

                                    </div>
                                    <div class="col-md-12">
                                        @php echo $multiple_search_criteria; @endphp
                                    </div>
                                        
                                    
                                        @if(!empty($kpi_properties))
                                            @foreach($kpi_properties as $info)
                                            <div class="parent row col-md-12">
                                                <div class="col-md-6">

                                                    <div class="form-group">                           
                                                       <div class="checkbox" style="margin-top: 30px">
                                                          <label><input type="checkbox" value="{{$info->bat_kpi_properties_id}}" class="property_cls" name="properties[]" @if(in_array($info->bat_kpi_properties_id, $properties_id_ary)) checked = "checked" @endif> {{$info->bat_kpi_properties_name}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Weight(%)</label>
                                                        <input type="number" class="form-control weights_cls" name="weights[<?php echo $info->bat_kpi_properties_id; ?>]" value="@if(in_array($info->bat_kpi_properties_id, $properties_id_ary)){{$weight_ary[$info->bat_kpi_properties_id]}}@endif">
                                                    </div>        
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                   

                                    <!-- <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">
                                                   <h4> <input type="checkbox" value="true" @if($have_range == 'true') checked="checked" @endif class="have_range_cls" name="have_range">Have Range</h4></label>
                                            </div>
                                        </div>
                                    </div> -->

                                    <div class="row col-md-12 range_cls">
                                        
                                        <div class="col-md-4 default-range-cls">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Range From(%)</label>
                                                <!-- <select class="form-control" name="range_from[]">
                                                    @for($i=1; $i<=100; $i++)
                                                        <option>{{$i}}</option>
                                                    @endfor
                                                </select> -->
                                                <input type="number" class="form-control range_from_cls" name="range_from[]">
                                            </div>        
                                        </div>
                                        <div class="col-md-4 default-range-cls">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Range To(%)</label>
                                                <!-- <select class="form-control" name="range_to[]">
                                                    @for($i=5; $i<=100; $i++)
                                                        <option>{{$i}}</option>
                                                    @endfor
                                                </select> -->
                                                <input type="number" class="form-control" name="range_to[]">
                                            </div> 
                                        </div>
                                        <div class="col-md-3 default-range-cls">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Value(%)</label>
                                                <!-- <select class="form-control" name="range_val[]">
                                                    @for($i=5; $i<=100; $i++)
                                                        <option>{{$i}}</option>
                                                    @endfor
                                                </select> -->
                                                <input type="number" class="form-control" name="range_val[]">
                                            </div> 
                                        </div>
                                        <div class="col-md-1 @if($config_id>0 && $have_range='true') offset-md-9 @endif">
                                            <button class="btn btn-xs btn-success add_new" style="margin-top: 33px;">
                                                Add New
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary form-submit" style="margin-top: 20px;">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--region_div--}}
<script>

$(document).ready(function () {

    $('.territory_div').hide();
    jQuery('#btn_house').click();
    $('.btn_tree_div').hide();

    $( "#region_div" ).removeClass( "col-md-2" ).addClass( "col-md-4" );
    $( "#area_div" ).removeClass( "col-md-2" ).addClass( "col-md-4" );
    $( "#house_div" ).removeClass( "col-md-2" ).addClass( "col-md-4" );

    function createInputField(r_from = null, r_to = null, r_value = null){
        var add_additional_var = '';

        add_additional_var += '<div class=" row col-md-12 remov_div">';
        add_additional_var += '<div class="col-md-4">';
            add_additional_var += '<div class="form-group">';
                add_additional_var += '<label for="exampleInputEmail1">Range From(%)</label>';
                     
                     add_additional_var += '<input type="number" value="'+r_from+'" class="form-control range_from_cls" name="range_from[]">';
             add_additional_var += '</div>';        
        add_additional_var += '</div>';
        add_additional_var += '<div class="col-md-4">';
             add_additional_var += '<div class="form-group">';
                 add_additional_var += '<label for="exampleInputEmail1">Range To(%)</label>';
                     
                     add_additional_var += '<input type="number" value="'+r_to+'" class="form-control" name="range_to[]">';
                 add_additional_var += '</div>'; 
             add_additional_var += '</div>';
             add_additional_var += '<div class="col-md-3">';
                 add_additional_var += '<div class="form-group">';
                     add_additional_var += '<label for="exampleInputEmail1">Value(%)</label>';
                    
                     add_additional_var += '<input type="number" value="'+r_value+'" class="form-control" name="range_val[]">';
             add_additional_var += '</div>'; 
         add_additional_var += '</div>';
         add_additional_var += '<div class="col-md-1">';
             add_additional_var += '<button class="btn btn-xs btn-danger remove_new" style="margin-top: 33px;">x</button>';
         add_additional_var += '</div>';
         add_additional_var += '</div>';

         return add_additional_var;
     }



     if($('#hdn_conf_id').val() > 0){
        
         $('#house').val(<?php echo json_encode($ary_house); ?>);
         $('#region').val(<?php echo json_encode($ary_region); ?>);
         $('#area').val(<?php echo json_encode($ary_area); ?>);
         $('#house').multiselect("refresh");
         $('#region').multiselect("refresh");
         $('#area').multiselect("refresh");



             //have range
            if($('.have_range_cls').is(':checked')) {
                $('.default-range-cls').hide();

                var range_from_ary = <?php echo json_encode($range_from_ary); ?>;
                var range_to_ary = <?php echo json_encode($range_to_ary); ?>;
                var range_value_ary = <?php echo json_encode($range_value_ary); ?>;

                for(var i=0; i<range_from_ary.length; i++){
                    $('.range_cls').append(createInputField(range_from_ary[i], range_to_ary[i], range_value_ary[i]));
                }
                 // alert(range_from_ary.length);
             }
         }


         $(function ($) {
             $("#month_from").datepicker( {
                 format: "yyyy-mm",
                 viewMode: "months",
                 minViewMode: "months",
                 //startDate: '+0d',
                autoclose: true,
            });
        });

        $(function ($) {
            $("#month_to").datepicker( {
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                 //startDate: '+0d',
                 autoclose: true,
             });
         });

        $(document).on('click','.form-submit',function (e) {
        // $(".form-submit").on('click', function (e) {
             e.preventDefault();

            var weightSum = 0;
            var weightCheck = 1;
             $('input[name="properties[]"]:checked').each(function() {
                 var parent = $(this).parents('.parent');
                 var weight = parent.find('.weights_cls').val();

                 console.log(weight);
                 if (weight =='') {
                     // swalError('Selected weight can not be null!');
                     weightCheck = 0;
                 }else{
                     weightSum = parseInt(weightSum) + parseInt(weight);
                     //swalSuccess("success");
                   
                 }
             });            
            
             if($('.config_name').val().length > 0 && $('.month_from').val().length > 0 && weightCheck > 0){
                 if(weightSum == 100){
                     swalConfirm('Are you sure?').then(function(e) {
                        if(e.value){
                             var data = $('#formId').serialize();

                             if($('#hdn_conf_id').val() > 0){
                                 var url = "{{url('kpi-config-update')}}";
                             }
                             else{
                                var url = "{{url('kpi-config-store')}}"; 
                             }
                            
                             makeAjaxPost(data,url,null).done(function (response) {
                                 if(response.code == 500){
                                     swalError(response.msg); 
                                 }
                                 else{
                                     swalRedirect("{{url('kpi-config')}}", response.msg, 'success'); 
                                 }
                             });
                        }
                     });
                 }
                 else{
                     swalError('Sum of weights must be 100');
                 }
                
             }
             else{
                 swalError("Please, Fill up mendatory field!");
             } 
         });

         if($('.have_range_cls').is(':checked')) {
             $('.range_cls').show();
         }else{
             $('.range_cls').hide();
         }

        
         $(document).on('click','.add_new',function (e) {
             e.preventDefault();
             $('.range_cls').append(createInputField());
         });

         $(document).on('click','.remove_new',function (e) {
             e.preventDefault();
             $(this).closest('.remov_div').remove();
         });

         $(document).on('change','.have_range_cls',function (e) {
             e.preventDefault();
            
             if($('.have_range_cls').is(':checked')) {
                 $('.range_cls').show();
             }else{
                 $('.range_cls').hide();
             }
         });


     });
</script>
@endsection