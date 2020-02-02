<link href="{{asset('public/css/plugins/summernote/summernote-bs4.css')}}" rel="stylesheet">
<script src="{{asset('public/js/plugins/summernote/summernote-bs4.js')}}"></script>
<div class="row p-3"></div>
<div class="footer" >
    <div class="float-right">
@php
$menu_id = session()->get('MENU_ID');
$levels = session()->get('USER_LEVELS');
if(in_array(1,$levels)){
    $user_level_id = 1;
}else{
    $user_level_id = '';
}

@endphp
        {{--{{$menu_id}}--}}
        <?php
        if($user_level_id == 1){ ?>
            <button class="btn btn-warning btn-sm  manual-btn" type="developer"><i class="fa fa-code" ></i></button>
        <?php } ?>
        <button class="btn btn-danger btn-sm  manual-btn"  type="user"><i class="fa fa-info"></i></button>
    </div>
    <div class="float-left">
        <strong>Copyright</strong> Apsis Solutions Limited &copy; 2018
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>


<!-- Modal For Open Existing Voucher -->
<div id="manual-modal-form" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="" style="width: 980px;">
        <div class="modal-content" style="overflow: hidden; padding-bottom: 15px;">
            <div class="modal-header">
                <h3 class="modal-title">Developer Manual Prepare!</h3>
            </div>
            <div class="modal-body show modal_content" style="overflow: hidden;">
                <input type="hidden" name="level-id" value="{{$user_level_id}}" class="get-user-level-id">
                <input type="hidden" class="selected_type" value="">
                <form method="post" action="{{url('save-manual')}}" id="manual-form">
                    {{csrf_field()}}
                    <input type="hidden" name="menu-id" value="{{session()->get('MENU_ID')}}" class="get-menu-id">

                    <div class="form-group">
                        <?php
                        if($user_level_id == 1){ ?>
                            <textarea name="user_manual" cols="" rows="" class="form-control user_manual summernote" id="user_manual_summernote"></textarea>
                        <?php }else{ ?>
                            <p class="user_manual"></p>
                        <?php } ?>

                        <textarea name="developer_manual" cols="" rows="" class="form-control manual summernote"></textarea>
                    </div>
                </form>
                    <div class="text-center">

                        <button class="btn btn-primary manual-data" id="update-manual">Save</button>
                        <button class="btn btn-bitbucket clear-manual" id="clear-manual">Clear</button>
                        <button class="btn btn-danger" id="close_modal">Close</button>
                    </div>

            </div>
        </div>
    </div>
</div>





<script>
    $(document).ready(function() {
        $('.summernote').summernote({height: 200});
    });
    $(document).on('click','.manual-btn', function (e) {
        e.preventDefault();
        Ladda.bind(this);
        var obj = $(this);

        var l = obj.ladda();
        var url = '<?php echo URL::to('get-existing-manual-info');?>';
        var menu_id = $('.get-menu-id').val();
        var type = $(this).attr('type');
        $('.selected_type').val(type);
        var user_level_id = $('.get-user-level-id').val();
        if(type == 'developer'){
            $('.user_manual').css("display", "none");
            $('.note-editor').last().css("display", "block");
            $('.note-editor').first().css("display", "none");
            $("#update-manual").css("display","");
            $("#clear-manual").css("display","");
        }else{
            $('.note-editor').last().css("display", "none");
            if(user_level_id == 1){
                $('.note-editor').first().css("display", "block");
                $('#update-manual').css("display", "");
                $('#clear-manual').css("display", "");
                $('.user_manual').css("display", "none");
            }else{
                $('.note-editor').first().css("display", "none");
                $('.user_manual').css("display", "block");
                $('#update-manual').css("display", "none");
                $('#clear-manual').css("display", "none");
            }
        }
        var data = {menu_id:menu_id,type:type,"_token":"{{csrf_token()}}"};
        makeAjaxPostText(data, url, l).then(function (success) {
            // console.log(success);
            if(type == 'developer'){
                $('.note-editable').last().html(success);
                $('.summernote').summernote({height: 200});
            }else{
                if(user_level_id == 1){
                    $('.note-editable').first().html(success);
                }else{
                    $('.user_manual').html(success);
                }
            }
            $('#manual-modal-form').modal('show');
            l.ladda('stop')
        });
    });

    $(document).on('click','#update-manual', function (e) {
        e.preventDefault();
        Ladda.bind(this);
        var obj = $(this);
        var l = obj.ladda();
        var url = '<?php echo URL::to('update-manual-info');?>';
        var menu_id = $('.get-menu-id').val();
        var selected_type = $('.selected_type').val();
        if(selected_type == "developer"){
            var prepared_manual = $('.note-editable').last().html();
        }else{
            var prepared_manual = $('.note-editable').first().html();
        }

        var data = {menu_id:menu_id,manual:prepared_manual,selected_type:selected_type,"_token":"{{csrf_token()}}"};
        makeAjaxPostText(data,url,l).done((response) =>{
            if (response){
                swalRedirect('','Developer Manual Successfully Updated!');
            }
            l.ladda('stop')
        })
    });

    $('#close_modal').on('click', function (e) {
        e.preventDefault();
        $('#manual-modal-form').modal('hide');
    });

    $('#clear-manual').on('click', function (e) {
        e.preventDefault();
        var selected_type = $('.selected_type').val();
        if(selected_type == "developer"){
            $('.note-editable').last().text('');
        }else{
            $('.note-editable').first().text('');
        }

    });

</script>