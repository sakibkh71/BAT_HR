@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if($code == null)
                <div class="card" style="padding: 20px">
                    <h1 style="font-weight: bold; text-decoration: underline; color: #0000F0;">All Requisition List</h1>
                    <table>
                        <tr>
                            <th>Select </th>
                            <th>Requsition ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        @foreach($grid_data as $k=>$v)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="delegation_job_id"
                                           value="{{$v->purchase_requisitions_code}}"> </td>
                                <td>{{$v->purchase_requisitions_code}}</td>
                                <td>{{$v->status_flows_name}}</td>
                                <td><a class="btn btn-success" href="{{URL::to('delegation-link-view-test/'.$v->purchase_requisitions_code)}}">Details</a> </td>
                            </tr>
                        @endforeach
                    </table>

                    <span
                        id_slug="prc_req"
                        style="margin-top: 20px; width: 200px;"
                        class="btn btn-primary send_for_approval">Send For Approval</span>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
            @else
                <h1>Details of <b>{{$code}}</b></h1>
                <div class="card" style="padding: 20px">
                    @php
                        $check = App\Http\Controllers\Delegation\DelegationProcess::checkDeligationAccessibility($grid_data);
                    @endphp
                    @if($check)
                        <form id="approveOrDeclineFrm" action="" method="post">
                            @csrf
                            <input type="hidden" name="slug" value="prc_req">
                            <input type="hidden" name="code[]" value="{{$code}}">
                            <textarea name="comments" required></textarea>
                            <br/>
                            <input type="submit" class="btn btn-primary approvalordecline" value="Approve" actionType="approve">
                            <input type="submit" class="btn btn-danger approvalordecline" value="Decline" actionType="decline">

                            <br/>
                            <br/>
                            <br/>
                            <br/>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

    <script>
        $(document).on('click', '.approvalordecline', function (e) {
            e.preventDefault();
            var actionType = $(this).attr('actionType');

            var url = '';
            if(actionType == 'approve'){
                url = "{{URL::to('delegation-approval-process')}}";
            }else if(actionType == 'decline'){
                url = "{{URL::to('delegation-decline-process')}}";
            }

            $.ajax({
                url: url,
                type: 'POST',
//                data: $('#approveOrDeclineFrm').serialize()+'&actionType='+actionType,
                data: $('#approveOrDeclineFrm').serialize(),
//                    beforeSend: function(){ $('.loadingImage').show();},
                success: function (data) {
                    console.log(data);
                }
            });

        });









        $(document).on('click', '.send_for_approval', function (e) {
            e.preventDefault();
            var _token = '<?php echo csrf_token() ?>';
            var id_slug = $(this).attr('id_slug');
            var job_value = [];
            var url = "<?php echo URL::to('delegation-initialize'); ?>";

            $('.delegation_job_id:checkbox:checked').each(function(){
                var val = $(this).val();
                job_value.push(val);
            });

            if(job_value.length){
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token:_token,id_slug:id_slug,job_value:job_value},
//                    beforeSend: function(){ $('.loadingImage').show();},
                    success: function (data) {
                        console.log(data);
                    }
                });
            }else{
                alert('Please select at least one job!');
            }

        });
    </script>
@endsection
