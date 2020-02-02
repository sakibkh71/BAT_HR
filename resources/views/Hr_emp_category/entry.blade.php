@extends('layouts.app')
@section('content')
    @php
        $hr_emp_category_name = isset($category) && !empty($category->hr_emp_category_name) ? $category->hr_emp_category_name : '';
        $sub_type_of = isset($category) && !empty($category->sub_type_of) ? $category->sub_type_of : '';
        $description = isset($category) && !empty($category->description) ? $category->description : '';
        $parents = isset($category) && !empty($category->parents) ? $category->parents : '';
        $status = isset($category) && !empty($category->status) ? $category->status : '';
        $sub_type_name = isset($category) && !empty($category->sub_type_name) ? $category->sub_type_name : '';
    @endphp

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>Employee Category Entry</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content pad10">
                        <form action="{{route('emp-category-store')}}" method="post" id="categoryForm" data-toggle="validator" data-disable="false">
                            @csrf
                            @if(isset($category) && !empty($category->hr_emp_categorys_id))
                                <input type="hidden" name="hr_emp_categorys_id" value="{{$category->hr_emp_categorys_id}}">
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><span class="required">*</span>  <strong>Category Name :</strong></label>
                                        <div class="col-sm-12">
                                            <input type="text"
                                                   class="form-control text-left"
                                                   name="hr_emp_category_name"
                                                   id="hr_emp_category_name"
                                                   autocomplete="off"
                                                   value="{{$hr_emp_category_name}}"
                                                   data-error="Please Enter Category Name"
                                                   required>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><strong>Sub Type Of :</strong></label>
                                        <div class="input-group" id="sub_type_name">
                                            <input type="text"
                                                   class="form-control text-left"
                                                   name="sub_type_name"
                                                   id="sub_type_select"
                                                   autocomplete="off"
                                                   value="{{$sub_type_name}}"
                                                   style="cursor: pointer;"
                                                   data-error="Please enter sub type of" readonly>
                                            <div class="input-group-addon">
                                                <i class="fa fa-list-alt"></i>
                                            </div>
                                            <div class="help-block with-errors has-feedback"></div>

                                            <input type="hidden" name="sub_type_of" id="sub_type_of" value="{{$sub_type_of}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><strong>Status:</strong></label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="status" data-error="Status mandatory" required="">
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><strong>Parents :</strong></label>
                                        <div class="col-sm-12">
                                            <input type="text"
                                                   class="form-control text-left"
                                                   name="parents"
                                                   id="parents"
                                                   autocomplete="off"
                                                   value="{{$parents}}"
                                                   data-error="Please Enter Parents"  readonly>
                                            <div class="help-block with-errors has-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label"><strong>Description :</strong></label>
                                        <div class="col-sm-12">
                                            <textarea rows="1" name="description" id="description"  class="form-control text-left">{{$description}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @if(isset($category) && !empty($category->product_categorys_id))
                                        <button type="submit" class="btn btn-primary btn-lg" id="updatePo">Update Category</button>
                                    @else
                                        <button type="submit" class="btn btn-primary btn-lg" id="makePo">Create Category</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Open Product Modal--}}
    <div class="modal inmodal fade" id="subtypeModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h2 class="modal-title">Employee Category Tree</h2>
                </div>
                <div class="modal-body">
                    {!! $subtype_of_list !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                window.location = "{{URL::to('grid/hr_emp_category')}}";
            });
        }
        sessionStorage.setItem('shown-' + popupId, '1');
        @endif
    </script>


    <script>
        (function($){
            $('#sub_type_name').click(function () {
                $('#subtypeModal').modal('show');
            });
            $('.sub-type-list label').click(function(){
                var subtype = $(this).data('id');
                var subtype_name = $(this).data('name');
                var parents = $(this).data('parent');
                $('#sub_type_of').val( subtype );
                $('#sub_type_select').val( subtype_name );
                $('#parents').val( parents );
                $('#subtypeModal').modal('hide');
            })
        })(jQuery)
    </script>

@endsection
