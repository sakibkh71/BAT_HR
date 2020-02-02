<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Profile of {{$user->name??''}} </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 mt-2">
                <label class="form-label">Full Name :</label>
               <strong> {{$user->name??''}} </strong>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">Email :</label>
                <strong>{{$user->email}}</strong>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">User Code :  </label>
                <strong>{{$user->user_code}}</strong>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">User Name : </label>
                <strong>{{$user->username}}</strong>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">Default URL : </label>
                <strong>{{$user->default_url}}</strong>
            </div>
            <div class="col-md-6  mt-2">
                <label class="form-label mt-2">Default Module : </label>
                <strong>{{$user->default_module}}</strong>
            </div>
            <div class="col-md-6  mt-2">
                <label class="form-label">User Levels Permission : </label> <br>
                <strong>{!! $user->levels !!}</strong>
            </div>
            <div class="col-md-6  mt-2">
                <label class="form-label">User Modules Permission : </label>
                <strong>{!!$user->modules  !!}</strong>
            </div>
            <div class="col-md-6  mt-2">
                <label class="form-label">User House Permission : </label> <br>
                <div style="max-height: 200px; overflow: auto">
                    {!! $user->house !!}
                </div>
            </div>
            <div class="col-md-6  mt-2">
                <label class="form-label">User Distributor Point Permission : </label> <br>
                <div style="max-height: 200px;overflow: auto">
                    {!! $user->point !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>


