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
                    <h2>Create Configuration</h2>
                    <div class="ibox-tools">

                    </div>
                </div>
                <div class="ibox-content">
                    <form action="#" method="post" id="createConfigForm">
                        <div class="col-md-12 row" style="margin-bottom: 5px;">
                            <div class="col-md-3">
                                <label class="font-normal"><strong>Configuration Name</strong> <span class="required">*</span> </label>
                                <input type="text" placeholder="Configuration Name" name="config_name" id="config_name" class="form-control">
                            </div>
                            <div class="col-md-2" style="padding-right:0px;">
                                <label class="font-normal"><strong>Evaluation For</strong> <span class="required">*</span> </label>
                                <select name="designation[]" id="designation" class="form-control multi" multiple="1">
                                    @foreach($designations as $info)
                                        <option value="{{$info->designations_id}}">{{$info->designations_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2" style="padding-right:0px;">
                                <label class="font-normal"><strong>Evaluate By</strong> <span class="required">*</span> </label>
                                <select name="designation_by[]" id="designation_by" class="form-control multi" multiple="1">
                                    @foreach($designations as $info)
                                        <option value="{{$info->designations_id}}">{{$info->designations_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2" style="margin-left:14px;">
                                <label class="font-normal"><strong>Year</strong> <span class="required">*</span> </label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    @for($i=2019;$i<=2025;$i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="field_wrapper">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="head_ary[1234]" id="" class="form-control head_dd">
                                            <option value="">Select Head</option>
                                            @if(count($heads) > 0)
                                                @foreach($heads as $info)
                                                    <option value="{{$info->pe_head_titles_id}}">{{$info->pe_head_titles_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" placeholder="Weight(%)" name="head_weight[1234]" class="form-control">
                                    </div>
                                    <div class="col-md-3 row">
                                        <button class="add_button_question btn btn-primary" >Add Question</button>
                                        <button class="add_button btn btn-success" style="margin-left: 4px;">Add Head</button>
                                    </div>
                                </div>
                                <div class="inner-div">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success createConfig" style="margin-top: 10px;">Create New Config</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function(){
            var maxField = 10; //Input fields increment limitation
            var addButton = $('.add_button'); //Add button selector
            var wrapper = $('.field_wrapper'); //Input field wrapper
            var heads = @json($heads);
            var questions = @json($questions);
            var head_ary = [];

            $.each(heads, function( key, value ) {
                head_ary[value.pe_head_titles_id] = value.has_child_kpi;
            });

            // console.log(head_ary);

            //Once add button is clicked
            $(addButton).click(function(e){
                e.preventDefault();
                var val_ary = Math.floor(1000 + Math.random() * 9000);

                var fieldHTML = '<div class="col-md-12">' +
                    '<div class="row" style="margin-top: 5px;">'+
                    '<div class="col-md-6">'+
                    '<select name="head_ary['+val_ary+']" id="" class="form-control head_dd">'+
                    '<option value="">Select Head</option>';
                $.each(heads, function( key, value ) {
                    // alert( key + ": " + value.pe_head_titles_name );
                    fieldHTML  +=  '<option value="'+value.pe_head_titles_id+'">'+value.pe_head_titles_name+'</option>';
                });

                fieldHTML  +=  '</select>'+
                    '</div>'+
                    '<div class="col-md-2">'+
                    '<input type="text" placeholder="Weight(%)" name="head_weight['+val_ary+']" class="form-control">'+
                    '</div>'+
                    '<div class="col-md-4 row">'+
                    '<button class="add_button_question btn btn-primary">Add Question</button>'+
                    '<button class="remove_button btn btn-danger" style="margin-left: 4px;"><i class="fa fa-times" aria-hidden="true"></i></button>'+
                    '</div>'+
                    '</div>'+
                    '<div class="inner-div">'+
                    '</div>'
                '</div>';

                $(wrapper).append(fieldHTML); //Add field html

            });

            //Once remove button is clicked
            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();
                $(this).parent('div').parent('div').parent('div').remove(); //Remove field html
            });

            $(wrapper).on('click', '.add_button_question', function(e){
                e.preventDefault();
                var val = $(this).parent('div').prev('div').prev('div').find('select').attr('name').match(/\d+/);
                // alert(val);
                var questionField = '<div class="row">' +
                    '<div class="col-md-5" style="margin-top: 5px;">' +
                    '<select name="question_ary['+val+'][]" id="" class="form-control">'+
                    '<option value="">Select Question</option>';
                $.each(questions, function( key, value ) {
                    // alert( key + ": " + value.pe_head_titles_name );
                    questionField  +=  '<option value="'+value.pe_kpi_questions_id+'">'+value.question+'</option>';
                });
                questionField += '</select>'+
                    '</div>' +
                    '<div class="col-md-2" style="margin-top: 5px;">' +
                    '<button class="btn remove_question btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>' +
                    '</div>' +
                    '</div>';
                $(this).parent('div').parent('div').next('div').append(questionField);
            });

            $(wrapper).on('click', '.remove_question', function(e){
                e.preventDefault();
                $(this).parent('div').parent('div').remove();
            });

            $(wrapper).on('change', '.head_dd', function(e){
                e.preventDefault();
                // $(this).parent('div').parent('div').remove();

                if(head_ary[$(this).val()] == 'No'){
                    $(this).parent('div').next('div').next('div').find('.add_button_question').prop("disabled", true);
                    $(this).parent('div').parent('div').next('div').empty();
                }
                else{
                    $(this).parent('div').next('div').next('div').find('.add_button_question').prop("disabled", false);
                }
            });
        });

        $('.createConfig').click(function(e){
            e.preventDefault();

            swalConfirm('to Confirm Create New Configuration?').then(function (e) {
                if (e.value) {
                    var data_year = $('#year').val();
                    var designation = $('#designation').val();

                    if (data_year.length > 0 && designation.length > 0 && designation_by.length > 0) {
                        var url = "{{URL::to('pe-store-config')}}";
                        var $form = $('#createConfigForm');
                        var data = {};
                        var redirectUrl = '{{ URL::current()}}';
                        data = $form.serialize() + '&' + $.param(data);
                        makeAjaxPost(data, url).done(function (response) {

                            console.log(response);
                            if (response.code == 200) {
                                // if(response.insert_or_update > 0){
                                swalRedirect(redirectUrl, 'Configuration Store Successfully', 'success');
                                // }else{
                                //     swalRedirect(redirectUrl, 'Question Add Successfully', 'success');
                                // }

                            }
                            else {
                                // swalRedirect(redirectUrl, 'Sorry! something wrong, please try later', 'error');
                                swalError(response.msg);
                            }
                        });
                    }
                    else {
                        swalError('Please Select Year/Designation First!');
                    }
                }
            });
        });
    </script>
@endsection
