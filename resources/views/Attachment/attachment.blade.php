<div class="row" id="supporting-document">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <h3>{{__lang('Supporting_Document')}}</h3>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="form-group col-md-7">
                        <form data-toggle="validator" role="form" action="{{ route('support-document-upload') }}" method="post" id="supporting-document-form" enctype="multipart/form-data">
{{--                            {{csrf_field()}}--}}
                            <div class="row">
                                <div class="col-md-6 b-r">
                                    <input type="hidden" name="reference" id="reference" value="{{$reference}}">
                                    <input type="hidden" name="reference_id" id="reference_id" value="{{$reference_id}}">
                                    {{--<input type="hidden" name="file_folder" id="reference_id" value="{{$file_folder}}">--}}

                                    <div class="form-group has-feedback">
                                        <label class="form-label">{{__lang('Document_Name')}} </label>
                                        <input type="text" name="document_name" value="" placeholder="Enter document name"
                                               id="document_name"
                                               class="form-control"
                                               required>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <label class="control-label">{{__lang('Choose_File')}}</label>
                                        <input id="file" type="file" required name="select_file" id="select_file" class="form-control">
                                    </div>
                                </div>

                                <div class="col-sm-3 ml-3 mt-5">
                                    <div class="form-group">
                                        <input type="submit" name="upload" id="upload" class="btn btn-primary" value="{{__lang('Upload')}}">
                                    </div>
                                </div>
                            </div>

                        </form>



                        {{--<form action="{{ route('support-document-upload') }}" data-toggle="validator" method="post" id="supporting-document-form" enctype="multipart/form-data">--}}
                            {{--<input type="hidden" name="reference" id="reference" value="{{$reference}}">--}}
                            {{--<input type="hidden" name="reference_id" id="reference_id" value="{{$reference_id}}">--}}
                            {{--<input type="hidden" name="file_folder" id="reference_id" value="{{$file_folder}}">--}}
                            {{--<input type="hidden" name="reference_id" id="reference_id" value="">--}}
                            {{--<div class="row">--}}

                                {{--<div class="col-md-6 b-r">--}}
                                    {{--<label class="form-label">Document Name </label>--}}
                                    {{--<input type="text" name="document_name" value="" placeholder="Enter document name"--}}
                                           {{--id="document_name"--}}
                                           {{--class="form-control"--}}
                                           {{--required>--}}

                                    {{--<label class="form-label">Choose file</label>--}}
                                    {{--<div class="custom-file">--}}
                                        {{--<input id="file" type="file" required name="select_file" id="select_file" class="form-control">--}}
                                        {{--<div class="help-block with-errors has-feedback"></div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="col-sm-3 ml-3 mt-5">--}}
                                    {{--<input type="submit" name="upload" id="upload" class="btn btn-primary" value="Upload">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</form>--}}
                    </div>
                    <div class="form-group col-md-4 ml-1">
                        <label class="form-label">Supporting FIle Information </label>
                        <div class="row">
                            <div class="table table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTable">
                                    <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Document Name</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="supporting-files">
                                    @php
                                        $existing_attachment = getAttachmentInfo($reference,$reference_id);
                                    @endphp
                                    @if(!empty($existing_attachment))
                                        @php($i = 1)
                                        @foreach($existing_attachment as $document)
                                            <tr>
                                                <td style="padding-left: 18px; !important;"><i class="fa fa-file-picture-o"></i></td>
                                                <td>
                                                    <a download style="text-decoration: none;" title="Download" href="{{asset('public/'.$document->document_path)}}">  {!! $document->document_name !!}</a>
                                                </td>

                                                <td align="center">
                                                        <a download style="text-decoration: none;" title="Download" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-download"></i> </button></a>
                                                    <button type="button" data-attachments_id="{{$document->attachments_id}}" class="btn btn-danger btn-xs remove-attachment">
                                                        <i class="glyphicon glyphicon-remove-sign"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @php($i++)
                                    @else
                                        <td class="text-center" colspan="3">No Data Found!!!</td>
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
</div>


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    $('#supporting-document-form').validator().on('submit', function (e) {
        e.preventDefault;
        if (e.isDefaultPrevented()) {
            //alert('handle the invalid form...')
        } else {
            e.preventDefault();
            var reference_id = $('#reference_id').val();
//            var document_name = $('#document_name').val();
            if(reference_id){
                var formData = new FormData(this);
//                formData.append('document_name', document_name);
//                formData.append('reference_id', reference_id);
                $.ajax({
                    url:"<?php echo URL('support-document-upload');?>",
                    method:"POST",
                    data:formData,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(response){
                        if(response.status == 'success') {
                            $('#file').val(null);
                            swalSuccess(response.message)
                            getSupportingFiles();
                        }else{
                            swalError(response.message);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        swalError(errorThrown);
                    }
                });
            }else{
                swalError("First create a order");
            }
        }
    });


    function getSupportingFiles() {
        var reference = $('#reference').val();
        var reference_id = $('#reference_id').val();
        $.ajax({
            type: 'POST',
            cache: false,
            url: '<?php echo URL::to('get-supporting-files');?>',
            data: {'reference':reference,'reference_id':reference_id},
            success: function (response) {
                if(response.status == 'success'){
                    $('#supporting-files').html(response.support_doc_html)
                }else{
                    swalError("Something Wrong!!");
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                swalError("Something Wrong!!");
            }
        });
    }


    $(function () {
        $("body").on("click", ".remove-attachment", function (e) {
            e.preventDefault();
            var obj = $(this);

            swalConfirm('Are you sure?').then(function(s){
                if(s.value){
                    var attachments_id = obj.data('attachments_id');
                    var url = '<?php echo URL::to('delete-attachments-item');?>';
                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: url,
                        data: {'attachments_id':attachments_id},
                        success: function (response) {
                            if(response == 'success'){
                                obj.closest("tr").remove();
                            }
                        }
                    });
                }
            });
        });
    });
</script>