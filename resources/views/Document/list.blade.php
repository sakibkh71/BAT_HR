@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Document List</h2>
                        <div class="ibox-tools"><a class="btn btn-xs btn-primary" href="{{route('document-upload')}}"><i class="fa fa-plus" aria-hidden="true"></i> New Document</a></div>
                    </div>
                    <div class="ibox-content pad10">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td rowspan="2" style="vertical-align: middle" width="10">#</td>
                                    <td rowspan="2" style="vertical-align: middle">Document Name</td>
                                    <td rowspan="2" style="vertical-align: middle">Document Path</td>
                                    <td rowspan="2" style="vertical-align: middle" width="20%">Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $key=>$item)
                                <tr>
                                    <td>{{++$key}}</td>
                                    <td>{{$item->document_name}}</td>
                                    <td> @if($item->reference == 'url') {{$item->document_path}} @endif</td>
                                    <td>
                                        <a class="btn btn-xs btn-success text-white" href="{{url($item->document_path)}}" download="{{$item->document_name}}"><i class="fa fa-download"></i> Download</a>
                                        <a class="btn btn-xs btn-primary text-white" href="{{url($item->document_path)}}" target="_blank"><i class="fa fa-eye"></i> View</a>
                                        <button class="btn btn-xs btn-danger delete_btn" data-id="{{$item->attachments_id}}"><i class="fa fa-close"></i> Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--Scripts -->
    <script>
        @if(!empty(Session::get('succ_msg')))
        var popupId = "{{ uniqid() }}";
        if(!sessionStorage.getItem('shown-' + popupId)) {
            swal({
                title: "Success!",
                text: "{{Session::get('succ_msg')}}",
                type: "success"
            }, function() {
                window.location = "{{URL::to('document-list')}}";
            });
        }
        sessionStorage.setItem('shown-' + popupId, '1');
        @endif

        $('#reference').change(function () {
            $ref = $(this).val();
            if ($ref == 'file'){
                $('#urlWrap').hide();
                $('#fileWrap').show();
                $('#upload_file').attr("required",true);
                $('#document_path').removeAttr('required');

                $("#docupload").validator('update');
            } else{
                $('#fileWrap').hide();
                $('#urlWrap').show();
                $('#document_path').attr("required",true);
                $('#upload_file').removeAttr('required');
                $("#docupload").validator('update');
            }
        });

        $('.delete_btn').click(function () {
            var id = $(this).data('id');
            if (id !=''){
                swalConfirm('Confirm to delete this item?').then(function (e) {
                    if(e.value){
                        var url = "{{URL::to('document-delete')}}";
                        var data = {'id': id};
                        makeAjaxPost(data,url,null).then(function(response) {
                            swalRedirect('document-list', response.msg, 'success');
                        });
                    }
                });
            }

        });
    </script>

@endsection
