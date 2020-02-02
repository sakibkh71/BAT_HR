<section class="tab-pane fade active show" id="transfer" role="tabpanel" aria-labelledby="variable_salary-tab">
    <div class="step-header open-header" id="salary_wages_head">
        <h2>Variable Salary</h2>
        @if(isset($employee))
            <div class="pull-right">
                <button class="btn btn-success btn-xs" id="newSalary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp; New</button>
                <button id="editvsalary" class="btn btn-warning btn-xs item_edit_vsalary ladda-button" style="display: none"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                <button class="btn btn-danger btn-xs item_delete_vsalary ladda-button" style="display: none"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
            </div>
        @endif
        <div id="variable_salary" class="collapsed" aria-labelledby="variable_salary_head" data-parent="#EmployeeAccordion">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="checkbox-clickable-esalary table-striped table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>SL No.</th>
                            <th>Salary Month</th>
                            <th>Max Variable Salary</th>
                        </tr>

                        </thead>
                        <tbody>
                        @if(!empty($variable_salarys))
                            @foreach($variable_salarys as $i=>$salary)
                                <tr class="row-select-toggle" id="{{$salary->vsalary_id}}">
                                    <td>{{($i+1)}}</td>
                                    <td>{{$salary->vsalary_month}}</td>
                                    <td class="text-right">{{$salary->variable_salary_amount}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click','#newSalary',function () {
            var url = '<?php echo URL::to('set-emp-variable-salary-form');?>';
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


        $(document).on('click', '#variable_salary_submit', function (event) {
            event.preventDefault();

            if (!$('#variable_salary_form').validator('validate').has('.has-error').length) {

                var $form = $('#variable_salary_form');
                var data = {
                    'sys_users_id' : employeeId
                };
                data = $form.serialize() + '&' + $.param(data);
                var url = '<?php echo URL::to('store-variable-salary');?>';
                makeAjaxPost(data, url).done(function (response) {
                    if(response){
                        swalSuccess('Variable Salary Store Successfully.');
                        $('#medium_modal').modal('hide');
                        window.location.reload();
                    }
                });
            }

        });

        var selected_vsalary = [];
        $(document).on('click','.checkbox-clickable-esalary tbody tr',function (e) {
            $obj = $(this);
            if(!$(this).attr('id')){
                return true;
            }
            $obj.toggleClass('selected');
            var id = $obj.attr('id');
            if ($obj.hasClass( "selected" )){
                selected_vsalary.push(id);
            }else{
                var index = selected_vsalary.indexOf(id);
                selected_vsalary.splice(index,1);

            }
            $('#leave_edit_id').text(selected_vsalary);

            if(selected_vsalary.length==1){
                $('.item_delete_vsalary, .item_edit_vsalary').show();
            }else if(selected_vsalary.length==0){
                $('.item_delete_vsalary, .item_edit_vsalary').hide();
            }else{
                $('.item_delete_vsalary').show();
                $('.item_edit_vsalary').hide();
            }
        });

        $(document).on('click','#editvsalary',function () {
            var url = '<?php echo URL::to('set-emp-variable-salary-form');?>';
            var vsalary_id = selected_vsalary[0];
            var data = {'emp_id':employeeId,'vsalary_id':vsalary_id};
            Ladda.bind(this);
            var load = $(this).ladda();
            makeAjaxPostText(data,url,load).done(function(response){
                if(response){
                    $('#medium_modal .modal-content').html(response);
                    $('#medium_modal').modal('show');
                }
            });
        });

        $(document).on('click', '.item_delete_vsalary', function (e) {
            e.preventDefault();
            Ladda.bind(this);
            var load = $(this).ladda();
            var selected_vsalary_ids = selected_vsalary;
            console.log(selected_vsalary_ids);
            var data = {vsalary_ids:selected_vsalary};
            var url = '<?php echo URL::to('hr-vsalary-record-delete');?>';
            if(selected_vsalary) {
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

    </script>
</section>