@extends('layouts.app')
@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Bonus Policy</h2>
                        <div class="ibox-tools">
                            <button class="btn btn-primary btn-xs" id="new_bonus_policy"><i class="fa fa-plus-circle" >&nbsp;</i>New Item</button>
                            <button class="btn btn-success btn-xs" id="bonus_policy_edit"><i class="fa fa-pencil" aria-hidden="true">&nbsp;</i>Edit</button>
                            <button class="btn btn-danger btn-xs" id="bonus_policy_delete"><i class="fa fa-minus-circle" aria-hidden="true">&nbsp;</i> Delete</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('hr_emp_bonus_policy',1) !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>

        $('#bonus_policy_edit').hide();
        $('#bonus_policy_delete').hide();
        var selected = [];

        $(document).on('click','#table-to-print tbody tr', function () {
            var self = $(this);
            var id = self.attr('id');

            /*add this for new customize*/
            selected = [];
            $('#table-to-print tbody tr').not($(this)).removeClass('selected');
            /* end this */

            if ($(this).toggleClass('selected')) {
                if ($(this).hasClass('selected')) {
                    selected.push(id);
                    self.find('input[type=checkbox]').prop("checked", true);
                } else {
                    selected.splice(selected.indexOf(id), 1);
                    self.find('input[type=checkbox]').prop("checked", false);
                }

                var arr_length = selected.length;
                if (arr_length > 1) {
                    $('#bonus_policy_edit').hide();

                }
                else if (arr_length == 1) {
                    $('#bonus_policy_edit').show();
                    $('#bonus_policy_delete').show();

                }
                else {
                    $('#bonus_policy_edit').hide();
                    $('#bonus_policy_delete').hide();
                }
            }

        });

        $("#new_bonus_policy").on('click', function (e) {
                window.location = '<?php echo URL::to('new_bonus_policy');?>';
        });

        $("#bonus_policy_edit").on('click',function(e){
           var bonus_policy_id=selected[0];
            window.location = '<?php echo URL::to('new_bonus_policy');?>/'+ bonus_policy_id;
        });

        $("#bonus_policy_delete").on('click',function(e){
           var data={
               'selected_ids':selected
           }
           var url='{{url('delete-bonus-policy')}}';
           $.ajax({
              type:'get',
              data:data,
              url:url,
              success:function(data){
                  swalSuccess('Bonus Policy Deleted Successfully');
                  window.location.href = '{{ route('hr-bonus-policy')}}';

              }
           });
        });
    </script>
@endsection