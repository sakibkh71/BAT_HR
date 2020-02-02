@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Document Upload</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content pad10">
                        <form action="{{route('document-upload')}}" method="post" enctype="multipart/form-data" id="docupload" data-toggle="validator" data-disable="false">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row {{ $errors->has('document_name') ? 'has-error' : ''}}">
                                        <label class="col-sm-12 col-form-label"><strong>Document Name :</strong> <span class="required">*</span>  </label>
                                        <div class="col-sm-12">
                                            <input type="text"
                                                   class="form-control text-left"
                                                   name="document_name"
                                                   id="document_name"
                                                   autocomplete="off"
                                                   data-error="Please Enter Document Name"
                                                   required>
                                            <div class="help-block with-errors has-feedback">{{ $errors->first('document_name') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row {{ $errors->has('reference') ? 'has-error' : ''}}">
                                        <label class="col-sm-12 col-form-label"><strong>Document Type :</strong> <span class="required">*</span>  </label>
                                        <div class="col-sm-12">
                                            <select name="reference" id="reference"  class="form-control text-left">
                                                <option value="file" selected> File</option>
                                                <option value="url"> URL</option>
                                            </select>
                                            <div class="help-block with-errors has-feedback">{{ $errors->first('reference') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row  {{ $errors->has('upload_file') ? 'has-error' : ''}}" id="fileWrap">
                                        <label class="col-sm-12 col-form-label"><strong>Browse Document :</strong> <span class="required">*</span>  </label>
                                        <div class="col-sm-12">
                                            <input type="file"
                                                   class="form-control text-left"
                                                   name="upload_file"
                                                   id="upload_file"
                                                   autocomplete="off"
                                                   data-error="Please select file"
                                                   required>
                                            <div class="help-block with-errors has-feedback"> {{ $errors->first('upload_file') }} </div>
                                        </div>
                                    </div>
                                    <div class="form-group row no-display {{ $errors->has('document_path') ? 'has-error' : ''}}" id="urlWrap">
                                        <label class="col-sm-12 col-form-label"><strong>Document Url :</strong> <span class="required">*</span>  </label>
                                        <div class="col-sm-12">
                                            <input type="text"
                                                   class="form-control text-left"
                                                   name="document_path"
                                                   id="document_path"
                                                   autocomplete="off"
                                                   data-error="Please Enter Url">
                                            <div class="help-block with-errors has-feedback"> {{ $errors->first('document_path') }} </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4 pt-2">
                                    @if(isset($attachment) && !empty($attachment->attachments_id))
                                        <button type="submit" class="btn btn-primary btn-lg">Update</button>
                                    @else
                                        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                                    @endif
                                </div>
                            </div>
                        </form>
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
        })
    </script>

@endsection
