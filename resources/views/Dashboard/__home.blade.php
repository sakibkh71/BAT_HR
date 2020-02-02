@extends('layouts.app')
@section('content')
    @php($previlege_house = session()->get('PRIVILEGE_HOUSE'))

    <link href="{{asset('public/css/plugins/jsTree/style.min.css')}}" rel="stylesheet">
    <style>
        .table > thead{
            box-shadow: none;
        }
        .table > tbody > tr > td{
            padding: 10px !important;
        }
        .ibox {
            margin-bottom: 25px;
        }
        .form-control{
            box-shadow: none;
        }
        .jstree-open > .jstree-anchor > .fa-folder:before {
            content: "\f07c";
        }
        .jstree-default .jstree-icon.none {
            width: 0;
        }
        #treearea{
            max-height: 625px;
            overflow: auto;
        }
        .input-group-addon{
            box-shadow: none;
        }
        .input-group .btn-group{
            width: -webkit-calc(100% - 30px) !important;
            width: -moz-calc(100% - 30px) !important;
            width: calc(100% - 30px) !important;
        }
    </style>
    @inject('moduleController', 'App\Http\Controllers\ModuleController')
    <div class="wrapper wrapper-content animated fadeIn dashboard">
        <div class="row mt-2">
            <div class="col-md-8">
              {{--  <h1>{!! session::get('MODULE_LANG') !!}</h1>--}}
            </div>
            <div class="col-md-4 text-right mt-1">
                <div class="input-group">
                    {!! e(__combo('bat_company_multi', array('selected_value'=>'')))  !!}
                    <button type="submit" class="btn btn-primary  btn-xs" data-style="zoom-in" id="filterBtn"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="row mt-2">

            <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title" style="background: #fae4a8;">
                        <h5>Organogram <small></small></h5>
                        {{--<div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>--}}
                    </div>
                    <div class="ibox-content" id="treearea"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- ChartJS-->
    <script src="{{asset('public/js/plugins/chartJs/Chart.min.js')}}"></script>
    <script>
        var barData = {
            labels: ["January", "February", "March", "April", "May", "June", "July"],
            datasets: [
                {
                    label: "Data 1",
                    backgroundColor: 'rgba(220, 220, 220, 0.5)',
                    pointBorderColor: "#fff",
                    data: [65, 59, 80, 81, 56, 55, 40]
                },
                {
                    label: "Data 2",
                    backgroundColor: 'rgba(26,179,148,0.5)',
                    borderColor: "rgba(26,179,148,0.7)",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#fff",
                    data: [28, 48, 40, 19, 86, 27, 90]
                }
            ]
        };

        var barOptions = {
            responsive: true
        };

        var ctx2 = document.getElementById("barChart").getContext("2d");
        new Chart(ctx2, {type: 'bar', data: barData, options:barOptions});

    </script>

    <script>
        function chart(sm = null, ss = null, sr = null) {
            var fftypes =[];
            var titles = [];
            if (sm !=null){
                titles.push(' SM ' + sm);
                fftypes.push(sm);
            }
            if (ss !=null){
                titles.push(' SS ' + ss);
                fftypes.push(ss);
            }
            if (sr !=null){
                titles.push(' SR ' + sr);
                fftypes.push(sr);
            }
            //Pie Chart
            var doughnutData = {
                labels: titles,
                datasets: [{
                    data: fftypes,
                    backgroundColor: ["#f8ac59","#b5b8cf","#a3e1d4"]
                }]
            } ;

            var doughnutOptions = {
                responsive: true
            };

            var ctx4 = document.getElementById("doughnutChart").getContext("2d");
            new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});
        }
    </script>

    <script src="{{asset('public/js/plugins/jsTree/jstree.min.js')}}"></script>
    <script src="{{asset('public/js/apsisScript.js')}}"></script>
    <script>
        (function ($) {
            $('#bat_company_id option').each(function(){
                this.selected=true;
            });

            $('#bat_company_id').multiselect("refresh");

            var house = $("#bat_company_id").val();

            BAT = {
                //get Designations List
                getDesignation: function(company) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var url = "{{route('dashboard-fetch-designations')}}";
                    var data = {'company':company};
                    makeAjaxPost(data, url,null).then(function(response) {
                        var sm =null, ss=null, sr=null;
                        if (response.data){
                            $.each(response.data, function(i, item) {
                                if (item.designations_name =='SM') {
                                    sm = item.employees;
                                }
                                if (item.designations_name =='SS') {
                                    ss = item.employees;
                                }
                                if (item.designations_name =='SR') {
                                    sr = item.employees;
                                }
                            });
                        }
                        chart(sm,ss,sr);
                    })
                },

                //get Company Organogram
                getOrganogram: function(company) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    var url = "{{route('dashboard-company-organogram')}}";
                    var data = {'house':company};

                    makeAjaxPost(data, url,null).then(function(response) {
                        if (response.data){
                            $('#treearea').html(response.data);
                        }
                    })
                },

                //get Company PF

            };
            //CALL Get Designation
            // BAT.getDesignation(house);
            BAT.getOrganogram(house);
            // BAT.getcompanyPF(house);
            // BAT.salaryDisburge(house);

            $('#filterBtn').click(function () {
                Ladda.bind( '#filterBtn', { timeout: 2000 } );
                var $house = $('#bat_company_id').val();
                BAT.getOrganogram($house);
            });
        })(jQuery);
    </script>
@endsection
