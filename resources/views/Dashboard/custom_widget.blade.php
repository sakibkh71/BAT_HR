<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<div style="min-height:270px;">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">New Join</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">PFP Target</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Insurance</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div style="padding: 10px;">
                <h5>Newly Joined Employees This Month</h5>
                <div id="inner_div">
                    <table class="table table-bordered" id="new-join-table">
                        <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Joining Date</th>
                            <th>Point</th>
                        </tr>
                        </thead>
                        <tbody id="new-join-body">

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div style="padding: 10px;">
                <p>
                <h5>Pending Insurance Claim List</h5>
                </p>
                <div id="inner_div_insurance">
                    <table class="table table-bordered" id="insurance-table">
                        <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Amount</th>

                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="insurance-body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div style="padding: 10px;">
                <p>
                <h5>Target Not Set List</h5>
                </p>
                <div id="inner_div_pfp_target">
                    <table class="table table-bordered" id="ff-target-table">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Point</th>
                            </tr>
                        </thead>
                        <tbody id="ff-target-body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var sql = '<?php echo $sql; ?>';
        var widget_id = '<?php echo $widget_id; ?>';
        var data = {"query" : sql};
        var url = "<?php echo url('dashboard-fetch-summary');?>";
//        makeAjaxPost(data, url).then(response => {
//            $('#'+widget_id).empty();
//            $('#'+widget_id).html(response);
//        });


        // var user_id = $(this).val();
        var date = "";
        var url = "{{url('new-join')}}";

        $.get(url, function(data, status){

            var monthNames = [
                "JAN", "FEB", "MAR","APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"
            ];
            var tstring_newjoin = "";
            $.each(data, function (a, b) {
                var current_datetime = new Date(b.date_of_join);
                var formatted_date = current_datetime.getDate() + "-" + monthNames[current_datetime.getMonth()] + "-" + current_datetime.getFullYear()
                // console.log(date.getDate());
                tstring_newjoin +="<tr>";
                tstring_newjoin +="<td>"+b.user_code+"</td>";
                tstring_newjoin +="<td>"+b.emp_name+"</td>";
                tstring_newjoin +="<td>"+b.designations_name+"</td>";
                tstring_newjoin +="<td>"+formatted_date+"</td>";
                tstring_newjoin +="<td>"+b.point_name+"</td>";
                tstring_newjoin +="<\tr>";
            });

            $('#new-join-body').html(tstring_newjoin);
            $('#new-join-table').DataTable({
                "searching": false,
                "lengthChange": false
            });

            // $('#insurance-body').html(tstring_newjoin);
            // $('#insurance-table').DataTable({
            //     "searching": false,
            //     "lengthChange": false
            // });

        });

        //insurance
        var url="{{url('new-insurance')}}";

        $.get(url, function(data, status){
            var html='';
            $.each(data,function (i,v) {
                var monthNames = [
                    "JAN", "FEB", "MAR","APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"
                ];
              // var claim_type=v.claim_type;
               var date=v.claim_date;
                var date=new Date(date);
               var claim_date=date.getDate() + "-" + monthNames[date.getMonth()] + "-" + date.getFullYear()
                html +="<tr>";
                html +="<td>"+v.name+"("+v.user_code+")</td>";
                html +="<td>"+v.claim_type+"</td>";
                html +="<td>"+claim_date+"</td>";
                html +="<td>"+v.claim_amount+"</td>";
                html +="<td>"+v.claim_status+"</td>";
                html +="</tr>";

            });

            $('#insurance-body').html(html);
            $('#insurance-table').DataTable({
                "searching": false,
                "lengthChange": false
            });
        });

        var url = "{{url('pfp-target')}}";

        $.get(url, function(data, status){

            var tstring = "";
            $.each(data, function (a, b) {
                tstring +="<tr>";
                tstring +="<td>"+b.user_code+"</td>";
                tstring +="<td>"+b.emp_name+"</td>";
                tstring +="<td>"+b.designations_name+"</td>";
                tstring +="<td>"+b.point_name+"</td>";
                tstring +="<\tr>";
            });

            $('#ff-target-body').html(tstring);
            $('#ff-target-table').DataTable({
                "searching": false,
                "lengthChange": false
            });
        });

    });



</script>