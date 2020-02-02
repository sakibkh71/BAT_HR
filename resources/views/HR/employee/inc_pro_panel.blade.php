@if(isset($mode) && $mode=='view')
    <style>
        #newIncrementPromotion,
        #view_increment_letter,
        .send_for_approval_btn,
        .item_delete{
            display:none
        }
        .checkbox-clickable tr{
            pointer-events: none;
        }
    </style>
@endif

<section class="tab-pane fade active show" id="promotion" role="tabpanel" aria-labelledby="promotion-tab">
    <div class="step-header open-header" id="increment_promotion_head">
        <h2>Increment & Promotions</h2><br>
        @if(isset($employee))
            <div class="pull-right">
                <button class="btn btn-success btn-xs" id="newIncrementPromotion"><i class="fa fa-plus-circle" aria-hidden="true"></i> New</button>
                <button type="button" class="btn btn-primary btn-xs no-display" id="view_increment_letter"><i class="fa fa-eye"></i> Letter View</button>
                <button class="btn btn-primary btn-xs no-display" id="send_for_approval_btn" id_slug="hr_inc"><i class="fa fa-check-circle" aria-hidden="true"></i> Send For Approval</button>
                <button class="btn btn-danger btn-xs ladda-button no-display" id="item_delete"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
            </div>
            <div id="increment_promotion" class="collapsed" aria-labelledby="increment_promotion_head" data-parent="#EmployeeAccordion">
                <div class="table-responsive">
                    <table id="employee_increment_list" class="employee_increment_list checkbox-clickable table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Increment/ Promotions</th>
                            <th>Applicable Date</th>
                            <th>Designation</th>
                            <th>Previous Designation</th>
                            <th>Grade</th>
                            <th>Previous Grade</th>
                            <th>Previous Salary</th>
                            <th>Basic</th>
                            <th>Increment Amount</th>
                            <th>Gross Total</th>
                            <th>Delegation Location</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="edit_id" style="display: none"></div>
            </div>
        @endif
    </div>
</section>


<style>
    .selected{
        background-color: green;
        color: #FFF;
    }
    .selected:hover{
        background-color: green !important;
        color: #FFF;
    }
</style>
<script>

    var selected_emp = [];
    var send = [];
    var record_type = '';
    $(document).on('click','.checkbox-clickable tbody tr',function (e) {
        $obj = $(this);
        if(!$(this).attr('id')){
            return true;
        }
        $obj.toggleClass('selected');
        var id = $obj.attr('id');

        if ($obj.hasClass( "selected" )){
            selected_emp.push(id);
            send.push($obj.data('status'));
            record_type = $obj.data('record_type');
        }else{
            var index = selected_emp.indexOf(id);
            selected_emp.splice(index,1);
            send.splice($.inArray($obj.data('status'), send), 1);
            record_type = '';
        }

        if(selected_emp.length==1 && send[0] == 50) {
            $('#view_increment_letter').show();
        }else{
            $('#view_increment_letter').hide();
        }

        if(send.includes(49)||send.includes(50)){
            $('#item_delete').hide();
            $('#item_edit').hide();
            $('#send_for_approval_btn').hide();
        }else{
            if (selected_emp.length > 0) {
                $('#item_delete').show();
                $('#item_edit').show();
                $('#send_for_approval_btn').show();
            }else{
                $('#item_delete').hide();
                $('#item_edit').hide();
                $('#send_for_approval_btn').hide();
            }
        }


    });

    //Send for approval
    $(document).on('click', '#send_for_approval_btn', function (e) {
        e.preventDefault();
        var id_slug = 'hr_inc';
        var url = '<?php echo URL::to('go-to-hr-delegation-process');?>';
        var job_value = selected_emp;
        if(job_value.length){
            swalConfirm().then(function (e) {
                if(e.value){
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {slug:id_slug,code:job_value,'delegation_type':'send_for_approval'},
                        success: function (data) {
                            var url = window.location;
                            swalRedirect(url,data,'success');
                        },
                        failure: function() {
                            swalError('Failed');
                        }
                    });
                }
            });
        }else{
            swalWarning("Please select at least one job!");
        }
    });


    //Increment Letter view
    $(document).on('click','#view_increment_letter', function (e) {
        if (selected_emp.length == 1) {
            if(record_type == 'promotion'){
                var url= '<?php echo URL::to('get-hr-promotion-letter');?>/'+selected_emp[0];
            }else{
                var url= '<?php echo URL::to('get-hr-increment-letter');?>/'+selected_emp[0];
            }
            swalConfirm('To view Increment Letter.').then(function (e) {
                if(e.value){
                    window.open(url,'_blank');
                }
            });
        } else {
            swalWarning("Please select single item");
            return false;
        }
    });


    //Delete Item
    $(document).on('click', '#item_delete', function (e) {
        e.preventDefault();
        Ladda.bind(this);
        var load = $(this).ladda();
        var data = {log_id:selected_emp};
        var url = '<?php echo URL::to('hr-record-delete');?>';
        if(selected_emp.length) {
            swalConfirm("Delete Selected Items").then(function (e) {
                if (e.value) {
                    makeAjaxPost(data,url,load).done(function (response) {
                        var url2 = window.location;
                        if(response.success){
                            swalRedirect(url2,"Successfully Delete",'success');
                        }else{
                            swalWarning('Operation Failed!');
                        }
                    });
                }else{
                    load.ladda('stop');
                }
            });
        }else{
            swalWarning("Please select at least one job!");
        }
    });

    // increment & promotion
    $(document).on('click','#newIncrementPromotion',function () {
        var url = '<?php echo URL::to('get-emp-inc-pro-form');?>';
        var data = {'emp_id':employeeId};
        Ladda.bind(this);
        var load = $(this).ladda();
        makeAjaxPostText(data,url,load).done(function(response){
            if(response){
                $('#medium_modal .modal-content').html(response);
                $('#medium_modal').modal('show');
            }
        });
    });


    $(document).on("input", "#new_gross_salary", function () {
        var basic_salary = parseFloat($("#inc_basic_salary").data('amount'));
        var new_gross_salary = $("#new_gross_salary").val();
        var old_gross_salary = parseFloat($("#new_gross_salary").data('amount'));
        var increment_amount = parseFloat(new_gross_salary-old_gross_salary);
        var increment_ratio = parseFloat((increment_amount*100)/basic_salary);
        $('#inc_increment_amount').val(increment_amount.toFixed(2));
        $('#inc_increment_ratio').val(increment_ratio.toFixed(2));
    });

    $(document).on("input", "#inc_increment_ratio", function () {

        var ratio_based = $("input[name='based_on']:checked").val();

        var basic_salary = parseFloat($("#inc_basic_salary").data('amount'));
        var old_gross_salary = parseFloat($("#new_gross_salary").data('amount'));
        var increment_ratio = parseFloat($("#inc_increment_ratio").val());

        if(ratio_based == 'basic'){
            var increment_amount = parseFloat(basic_salary*(increment_ratio/100));
        }else{
            var increment_amount = parseFloat(old_gross_salary*(increment_ratio/100));
        }


        $('#inc_increment_amount').val(increment_amount.toFixed(2));
        var new_gross_salary = parseFloat(old_gross_salary+increment_amount);
        $('#new_gross_salary').val(new_gross_salary.toFixed(2));
    });

    $(document).on("input", "#inc_increment_amount", function () {
        var ratio_based = $("input[name='based_on']:checked").val();
        var basic_salary = parseFloat($("#inc_basic_salary").data('amount'));
        var old_gross_salary = parseFloat($("#new_gross_salary").data('amount'));
        var increment_amount = parseFloat($("#inc_increment_amount").val());

        if(ratio_based == 'basic'){
            var increment_ratio = parseFloat((increment_amount*100)/basic_salary);
        }else{
            var increment_ratio = parseFloat((increment_amount*100)/old_gross_salary);
        }

        var new_gross_salary = parseFloat(old_gross_salary+increment_amount);
        $('#new_gross_salary').val(new_gross_salary.toFixed(2));
        $('#inc_increment_ratio').val(increment_ratio.toFixed(2));
    });



    $(document).on('change','.inc_pro_type', function (e) {
        var inc_pro_type = $("input.inc_pro_type:checked").val();
        if(inc_pro_type == 'salary_restructure'){
            $('body').find('#increment_item').show();
            $('body').find('#IncrementBased').show();
            $('body').find('#promotion_item').hide();
        }else{
            $('body').find('#increment_item').hide();
            $('body').find('#IncrementBased').hide();
            $('body').find('#promotion_item').show();
        }
    });

    //on change grade change gross salary value
    $(document).on('change','#hr_emp_grades_id', function (e) {
       var salary_grade_id = $(this).val();
        var old_gross_salary = parseFloat($("#new_gross_salary").data('amount'));
        var url = "<?php echo url('get-hr-grade-wise-salary')?>/"+salary_grade_id;
        makeAjax(url,null).done(function (resp) {
            $.each( resp, function( key, val ) {
                $("#new_gross_salary").val(val.gross_salary);
                $("#inc_increment_ratio").val('');
                $("#inc_increment_amount").val(val.gross_salary-old_gross_salary);
            });
        });
    });


    $(document).on('click', '#inc_pro_submit', function (event) {
        if (!$('#inc_pro_form').validator('validate').has('.has-error').length) {
            if (employeeId !='null'){
                var url = '{{route('hr-store-inc-pro')}}';
                var $form = $('#inc_pro_form');
                var data = {
                    'sys_users_id' : employeeId,
                    'inc_pro_type' : $('input[name=inc_pro_type]:checked').val()
                };
                data = $form.serialize() + '&' + $.param(data);
                makeAjaxPost(data, url).done(function (response) {
                    if(response.success){
                        swalSuccess('Salary Increment Successfully.');
                        $('#medium_modal').modal('hide');
                        incriment_promotion_list();
                    }else{
                        swalError("Sorry! Something wrong, please provide correct data");
                    }
                });
            }else{
                swalError("Sorry! you need to add personal information first");
            }
        }else{
            swalError("Sorry! please provide correct data");
        }
    });

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: "yyyy-mm-dd"
    });

    function incriment_promotion_list() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        var url2 = '{{route('hr-inc-pro-history')}}';
        var data2 = {
            'sys_users_id' : employeeId
        };
        $.ajax({
            url:url2,
            type:'post',
            data:data2,
            async:false,
            success:function (response) {
                if (response) {
                    $('#employee_increment_list tbody').html(response.data);
                    // $('#employee_increment_list').DataTable();
                }
            }
        });
    }

    $(function () {
        incriment_promotion_list();
    });

    $(document).on("change", "input[name='based_on']", function () {
        ratioChange();
    });

    //Increment Ratio
    function ratioChange() {
        var ratio_based = $("input[name='based_on']:checked").val();

        var increment_ratio = $('#inc_increment_ratio').val() || null;

        var basic_salary = parseFloat($("#inc_basic_salary").data('amount'));
        var old_gross_salary = parseFloat($("#new_gross_salary").data('amount'));
        var increment_ratio = parseFloat($("#inc_increment_ratio").val());

        if(ratio_based == 'basic'){
            var increment_amount = parseFloat(basic_salary*(increment_ratio/100));
        }else{
            var increment_amount = parseFloat(old_gross_salary*(increment_ratio/100));
        }

        $('#inc_increment_amount').val(increment_amount.toFixed(2));
        var new_gross_salary = parseFloat(old_gross_salary+increment_amount);
        $('#new_gross_salary').val(new_gross_salary.toFixed(2));

    }
</script>
