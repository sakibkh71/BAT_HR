@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Manual KPI Achievement</h2>
                        <div class="ibox-tools">
                            <button type="button"  class="btn btn-xs btn-primary" id="get_achievement_btn"><i class="fa fa-plus-circle"></i> Get Achievement</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form action="" method="post">
                            @csrf
                        <div class="col-md-12">
                            @php echo $multiple_search_criteria; @endphp
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Select Date</label>
                            <div class="input-group">
                                <input type="text"
                                       placeholder=""
                                       class="form-control date"
                                       id="month_from"
                                       data-date-format="yyyy-mm-dd"
                                       name="achievement_date" required/>
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-primary form-submit" style="margin-top: 20px;">Submit</button>
                        </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
    $(document).ready(function () {
        $(".date").datepicker( {
            format: "yyyy-mm-dd",
            endDate: '-1d',
            autoclose: true,
        });

        selected = [];

        $('#get_achievement_btn').hide();

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
                    $('#get_achievement_btn').hide();
                }
                else if (arr_length == 1) {

                    $('#get_achievement_btn').show();
                }
                else {
                    $('#get_achievement_btn').hide();
                }
            }
        });

        $("#get_achievement_btn").on('click', function (e) {
            e.preventDefault();
            
            Ladda.bind('#get_achievement_btn');
            var load = $('#get_achievement_btn').ladda();
            
            var trItem = $('#table-to-print tr.selected');
            var kpi_house_id = selected;
            var target_month = trItem.data('target_month');
            
            console.log(kpi_house_id, target_month);

            if (kpi_house_id.length === 0) {
                swalError("Please select only one House");
                return false;
            } else {
                swalConfirm('Confirm to set achievement?').then(function (e) {
                   if(e.value){
                       var url = "{{URL::to('kpi-achievement-get')}}";
                       var data = {kpi_house_id: kpi_house_id[0], target_month: target_month};
                       makeAjaxPost(data,url,load).then(function(response) {
                               
                            if(response.code == 200){
                                var redirect_url = "{{URL::to('kpi-achievement')}}";
                                swalRedirect(redirect_url, response.msg, 'success');
                            }
                            else{
                                swalError(response.msg);
                            }
                       });
                       // alert('get');
                   }
                });

            }
        });
    });
</script>
@endsection