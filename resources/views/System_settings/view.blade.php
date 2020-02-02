@extends('layouts.app')
@section('content')
    <link href="{{asset('public/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
    <script src="{{asset('public/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('public/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>System Settings</h2>
                    </div>

                    <div class="ibox-title">
                        <div class="ibox-tools">
                            
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="userTable">
                                <thead>
                                <tr>
                                    <th>Option Group</th>
                                    <th>Option Key</th>
                                    <th>OPtion Value</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                
                                @if(!empty($lists))
                                @foreach($lists as $list)
                                    <?php 
                                        $clsInput = "clsInput".$list->id; 
                                        $clsSelect = "clsSelect".$list->id; 
                                        $clsGroup = "clsGroup".$list->id; 
                                        $clsKey = "clsKey".$list->id; 

                                        // dd($clsSelect, $clsInput);
                                    ?>
                                    
                                        <tr class="row-select-toggle">
                                            <td>
                                                <input type="text" class="form-control {{$clsGroup}}" required name="option_value" value="{{ $list->option_group ?? 'N/A'}}" readonly="">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control {{$clsKey}}" required name="option_value" value="{{ $list->option_key ?? 'N/A'}}" readonly="">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control {{$clsInput}}" required name="option_value" value="{{$list->option_value}}">
                                            </td>
                                            <td>
                                                <select class="form-control {{$clsSelect}}" name="status">
                                                    <option value="Active" @if($list->status=='Active') selected="" @endif>Active</option>
                                                    <option value="Inactive" @if($list->status=='Inactive') selected="" @endif>Inactive</option>
                                                </select>
                                            </td>
                                            <td><button data-id="{{$list->id}}" class="btn btn-xs btn-success updateValue">Update</button></td>
                                        </tr>

                                @endforeach
                                @endif
                                
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <dir class="col-md-12">
                                <h3>Add New Value: </h3>
                            </dir>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Option Group <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="groupId" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Option Key <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="keyId" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Option Value <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="valueId" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Status <span class="required">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" id="statusId">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label" style="margin-top: 17px;"></label>
                                    <div class="input-group">
                                        <!-- <input type="text" class="form-control" name=""> -->
                                        <button data-id="" class="btn btn-xs btn-primary updateValue">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $('#userTable').dataTable();


        $(document).on('click', '.updateValue', function(e){

            var id = $(this).attr('data-id');
            var url = '<?php echo URL::to('update-system-settings');?>';

            if(id.length > 0){
                var clsInput = 'clsInput'+id;
                var clsSelect = 'clsSelect'+id;
                var clsGroup = 'clsGroup'+id;
                var clsKey = 'clsKey'+id;

                var inputVal = $("."+clsInput).val();
                var selectVal = $("."+clsSelect).val();
                var groupVal = $("."+clsGroup).val();
                var keyVal = $("."+clsKey).val();

                if(inputVal.length > 0 && selectVal.length > 0){
                    swalConfirm('Are you sure?').then(function(s) {
                        if(s.value){
                            var data = {group_val:groupVal, key_val:keyVal, option_value:inputVal, option_status:selectVal, id:id};

                            makeAjaxPostText(data, url, null).done(function(sresult){
                                swalRedirect('', sresult, 'success');
                            });
                        }
                    });    
                }
            }
            else{

                var groupVal = $('#groupId').val();
                var keyVal = $('#keyId').val();
                var valueVal = $('#valueId').val();
                var statusVal = $('#statusId').val();

                if(groupVal.length > 0 && keyVal.length > 0 && valueVal.length > 0 && statusVal.length > 0){
                    swalConfirm('Are you sure?').then(function(s) {
                        if(s.value){
                            var data = {groupVal:groupVal, keyVal:keyVal, valueVal:valueVal, statusVal:statusVal, id:id};

                            makeAjaxPostText(data, url, null).done(function(sresult){
                                swalRedirect('', sresult, 'success');
                            });
                        }
                    }); 
                }
                else{
                    alert('Please Fillup All Fields')
                }
            }

                

        });

    </script>
@endsection
