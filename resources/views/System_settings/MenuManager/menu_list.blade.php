@extends('layouts.app')
@section('content')
    <style>
        .no-action .multiselect{
            pointer-events: none;
        }
    </style>
    <script src="{{asset('public/js/plugins/bootstrap-validator/validator.min.js')}}"></script>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2>Menu Manage List</h2>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="ibox contex-box">
                                    <div class="ibox-title">
                                        <h5>Quick Menu</h5>
                                        <div class="ibox-tools">
                                            <button  class="btn btn-primary btn-xs" id="saveOrder"><i class="fa fa-save" aria-hidden="true"></i> Save Menu Order</button>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="form-group">
                                            <label for="module"> Short By Module</label>
                                            {{__combo('modules', array('selected_value'=>isset($nav_item->modules_id)?$nav_item->modules_id:'1', 'attributes'=> array( 'name'=>'module', 'required'=>'required', 'id'=>'module', 'class'=>'form-control multi')))}}
                                        </div>
                                        <div class="dd" id="nestableNav">
                                            <ol class='dd-list dd3-list' id="dd-placeholder">
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="ibox contex-box">
                                    <div class="ibox-title">
                                        <h5>Menu Information</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <form action="{{route('store-menu-item')}}" method="post" id="navForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-4">
                                                        <label class="font-normal" for="MenuLabel"><strong>Menu Label</strong>  <span class="required">*</span></label>
                                                        <input type="text" name="label" placeholder="Menu Label" id="MenuLabel" class="form-control" value="{{isset($nav_item->name)?$nav_item->name:''}}" required>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label class="font-normal" for="MenuLink"><strong>Menu Link </strong>  <span class="required">*</span></label>
                                                        <input type="text" name="link" placeholder="Menu Link " id="MenuLink" class="form-control" value="{{isset($nav_item->menu_url)?$nav_item->menu_url:''}}"  required>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label class="font-normal" for="icon"><strong>Icon Class (optional) </strong></label>
                                                        <input type="text" name="icon" placeholder="Icon Class" id="icon" class="form-control" value="{{isset($nav_item->icon_class)?$nav_item->icon_class:''}}">
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group  mb-4 {{isset($nav_item->modules_id)?'no-action':''}}" id="moduleWrap">
                                                        <label class="font-normal" for="modules_id"><strong>Module For</strong> <span class="required">*</span></label>
                                                        {{__combo('modules', array('selected_value'=>isset($nav_item->modules_id)?$nav_item->modules_id:'', 'attributes'=> array( 'name'=>'modules_id', 'required'=>true, 'id'=>'modules_id', 'class'=>'form-control multi')))}}
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                    <div class="form-group  mb-4">
                                                        <label class="font-normal" for="desc"><strong>Description</strong></label>
                                                        <textarea name="menus_description" id="desc" class="form-control">{{isset($nav_item->menus_description)?$nav_item->menus_description:''}}</textarea>
                                                        <div class="help-block with-errors has-feedback"></div>
                                                    </div>
                                                    <div class="form-group  mb-4">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <button type="submit" class="btn btn-primary full-width"> SUBMIT</button>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="reset" id="reset" class="btn btn-warning full-width"> RESET</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="menu_id" id="menu_id" value="{{isset($nav_item->id)?$nav_item->id:''}}">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('public/js/plugins/nestable/jquery.nestable.js')}}"></script>
    <script>
        var makeNav ='';
          $(document).ready(function(){
            //Form Validator
            $('#navForm').validator();

            $.ajaxSetup({
              headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            //Build Tree Nav
            makeNav = function($module){
                var data = {'module':$module};
                var url = "{{route('menu-items')}}";
                makeAjaxPost(data, url ,null).then(function (response) {
                    if (response.data.length>0) {
                        var obj = JSON.stringify(response.data); //'[{"id":1, "title":"home"},{"id":2, "title":"about"},{"id":3, "title":"service", "children":[{"id":4, "title":"company"},{"id":5,  "title":"contact"}]}]';
                        var output = '';
                        $.each(JSON.parse(obj), function (index, item) {
                            output += buildItem(item);
                        });
                        $('#dd-placeholder').html(output);
                        $('#nestableNav').nestable({group:1});
                    }
                });
            };

            var modudle = $('#module').val();
            makeNav(modudle);

            $('#module').change(function () {
                var module = $(this).val();
                makeNav(module);
            });

            //nuildFunction
            function buildItem(item) {
                var html = "<li class='dd-item ' data-id='" + item.id + "'>"+
                    "<div class='dd-handle dd-nodrag'>";

                    html += "<div class='btn-group float-right'><button class='btn btn-sm btn-secondary editbtn' onclick='editItem(this)' data-id='"+ item.id +"'>Edit</button>";
                    if (!item.children) {
                        html += "<button class='btn btn-sm btn-secondary deletebtn' onclick='deleteItem(this)'  data-id='"+ item.id +"'><i class='fa fa-trash'></i></button>";
                    }
                    html += "</div>";

                var icon = 'fa fa-caret-left';
                if ( item.icon_class !='' && item.icon_class !=null){ icon = item.icon_class; }else{icon = 'fa fa-caret-left'; }

                html += "<span class='label label-info'><i class=' "+ icon +"'></i></span><span>"+item.name +"</span></div>";

                if (item.children) {
                    html += "<ol class='dd-list'>";
                    $.each(item.children, function (index, sub) {
                        html += buildItem(sub);
                    });
                    html += "</ol>";
                }

                html += "</li>";
                return html;
            }


            /*
             * Save Menu Order
             */
            $('#saveOrder').click(function () {
                var l = Ladda.create( document.querySelector( '#saveOrder' ) );
                // Start loading
                l.start();
                var serializeItem = $('#nestableNav').nestable('serialize');
                var url = "{{route('save-menu-order')}}";
                var data = {'menus':serializeItem};
                var redirectUrl = window.location;

                makeAjaxPost(data, url, null).then(function (response) {
                    if (response.status =='success'){
                        l.stop();
                    }else{
                        swalRedirect(redirectUrl,'Something wrong, please try later', 'error');
                    }
                });

            });



            $('#reset').click(function (e) {
                e.preventDefault();
                location.reload();
            });

            @if(!empty(Session::get('succ_msg')))
                var popupId = "{{ uniqid() }}";
                if(!sessionStorage.getItem('shown-' + popupId)) {
                    swalSuccess("{{Session::get('succ_msg')}}");
                }
                sessionStorage.setItem('shown-' + popupId, '1');
            @endif

        });

        //Edit Menu Item
        function editItem(elem){
            swalConfirm('Edit this item?').then(function (s) {
                if (s.value){
                    var id = $(elem).data("id");
                    var url = "{{route('single-menu-item')}}";
                    var data = {'id':id};
                    makeAjaxPost(data, url, null).then(function (response) {
                        $('#menu_id').val(response.data.id);
                        $('#MenuLabel').val(response.data.name);
                        $('#modules_id').val(response.data.modules_id);
                        $('#MenuLink').val(response.data.menu_url);
                        $('#icon').val(response.data.icon_class);
                        $('#desc').val(response.data.menus_description);

                        $('#modules_id').attr("readonly","true");
                        $('#modules_id').multiselect('rebuild');
                        $('#moduleWrap').addClass('no-action');
                        $("#navForm").validator('update');
                    });
                }
            })
        }

        //delete Items
        function deleteItem(elem){
            swalConfirm('Edit this item?').then(function (s) {
                if (s.value){
                    var id = $(elem).data("id");
                    var url = "{{route('delete-menu-item')}}";
                    var data = {'id':id};
                    var redirectUrl = window.location;
                    makeAjaxPost(data, url, null).then(function (response) {
                        if(response.status=="success"){
                            swalSuccess('Menu item deleted successfully');
                            var modudle = $('#module').val();
                            makeNav(modudle);
                             //swalRedirect(redirectUrl, "Menu item deleted successfully", "success");
                        }else{
                            swalRedirect(redirectUrl, "Something wrong please try again", "error")
                        }
                    });
                }
            })
        }
    </script>
@endsection
