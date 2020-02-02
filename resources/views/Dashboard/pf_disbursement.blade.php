<div class="widget">
    <div class="row">
        <div class="col-4 b-r text-center">
            <h3><i class="fa fa-money"></i> <span class="company_total"></span></h3>
            <span>Company</span>
        </div>
        <div class="col-4 b-r text-center">
            <h3><i class="fa fa-money"></i> <span class="employee_total"></span></h3>
            <span>Employee</span>
        </div>
        <div class="col-4 text-center">
            <h3><i class="fa fa-money"></i> <span class="total_pf"></span></h3>
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
        var url = "{{url('dashboard-last-month-pf')}}";

        $.get(url, function(data, status){
            var employee_total = parseFloat(data.employee_total,2);
            var company_total = parseFloat(data.company_total,2);
            var total = parseFloat(employee_total+company_total,2);
            $('.employee_total').html(apsis_money(employee_total));
            $('.company_total').html(apsis_money(company_total));
            $('.total_pf').html(apsis_money(total));
        });

    });
</script>