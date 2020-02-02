@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <link href="{{asset('public/css/plugins/summernote/summernote-bs4.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 no-padding">
            <div class="ibox ">
                <div class="ibox-title">
                    <h2>Leave Encashment List</h2>
                    <div class="ibox-tools">
                        <a href="{{route('leave-encashment-create')}}" class="btn btn-primary btn-xs" id="newEntry"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
                        <button class="btn btn-success btn-xs hide" id="editbtn"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            {{--<form action="{{route('leave-encashment-history')}}" method="post" class="ptype-form">
                                @csrf
                                <div class="row">
                                    --}}{{--{!! __getCustomSearch('po_list', $posted) !!}--}}{{--
                                </div>
                                <div class="row">
                                    <div class="col-md-3"> <label class="col-sm-12 col-form-label"><strong>&nbsp;</strong></label>
                                        <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Filter</button>
                                    </div>
                                </div>
                            </form>--}}
                        </div>
                        <div class="col-md-12 mt-3">
                            <table class="checkbox-clickable table table-striped table-bordered table-hover dataTables-example table-responsive">
                                <thead>
                                    <tr>
                                        <th width="25%">Employee Name</th>
                                        <th width="20%">Date</th>
                                        <th width="20%">Days</th>
                                        <th width="10%">Amount</th>
                                        <th width="35%">Note</th>
                                        <th width="10">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(!empty($encashment_records))
                                    @foreach($encashment_records as $list)
                                        <tr class="row-select-toggle" id="{{$list->hr_leave_encashments_id}}">
                                            <td>{{ $list->name ?? 'N/A' }} ({{$list->user_code}})</td>
                                            <td>{{ toDated($list->encashment_date) }}</td>
                                            <td>{{ $list->encashment_days }} Days</td>
                                            <td>{{ $list->encashment_amount }}</td>
                                            <td>{{ $list->encashment_note }}</td>
                                            <td>{{ $list->status}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--@if (Session::has('success'))
    <script>
        $(document).ready(function(){
            var url = "{{URL::to('leave-encashment-history')}}";
            var mode = 'success';
            var msg = 'Success!';

            swalRedirect(url, msg, mode);
        });
    </script>
@endif--}}

<script>
    //segment value
    (function ($) {
        $('.dataTables-example').dataTable({
            order: [[ 1, 'desc' ]],
        });

        var selected_item = [];

        $(document).on('click','.row-select-toggle',function (e) {
            $obj = $(this);

            if(!$(this).attr('id')){
                return true;
            }

            $obj.toggleClass('selected');

            var id = $obj.attr('id');
            if ($obj.hasClass( "selected" )){
                selected_item.push(id);
            }else{
                var index = selected_item.indexOf(id);
                selected_item.splice(index,1);

            }
            if(selected_item.length==1){
                $('#editbtn').show();
            } else{
                $('#editbtn').hide();
            }
        });
        //Edit Item
        $('#editbtn').click(function(){
            if (selected_item.length == 1 ) {
                swalConfirm("to Edit this item?").then(function (s) {
                    if (s.value){
                        window.location.replace("{{ URL::to('/leave-encashment-create') }}/"+ selected_item[0]);
                    }
                });
            }
        });
    }(jQuery));
</script>

@endsection