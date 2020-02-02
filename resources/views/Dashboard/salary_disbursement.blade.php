<div class="widget">
    <div class="row">
        <div class="col-4 b-r text-center">
            <h3><i class="fa fa-money"></i> <span class="fixed_salary"></span></h3>
            <span>Fixed</span>
        </div>
        <div class="col-4 b-r text-center">
            <h3><i class="fa fa-money"></i> <span class="pfp_salary"></span></h3>
            <span>PFP</span>
        </div>
        <div class="col-4 text-center">
            <h3><i class="fa fa-money"></i> <span class="total_salary"></span></h3>
            <span>Total</span>
        </div>

    </div>
</div>
<style>
    div>span{
        font-size: 16px;
    }
    .widget{
        color: #000;
        font-weight: bold;
        box-shadow: none;
        text-shadow:none;
    }
</style>


<script>
    $(document).ready(function () {

        var date = "";
        var url = "{{url('dashboard-last-month-salary')}}";

        $.get(url, function(data, status){
            var fixed = parseFloat(data.fixed_salary,2);
            var pfp = parseFloat(data.pfp_salary,2);
            var total = fixed+pfp;
            $('.fixed_salary').html(apsis_money(fixed));
            $('.pfp_salary').html(apsis_money(pfp));
            $('.total_salary').html(apsis_money(total));
        });

    });
</script>