<link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<h3>Current Month Achievement Summary</h3>
<div class="inner-tbl" style="min-height: 175px;">

</div>



<script>
    $(document).ready(function () {

        var date = "";
        var url = "{{url('kpi-achievement-current-month')}}";

        $.get(url, function(data, status){

            $('.inner-tbl').html(data);
        });

    });
</script>