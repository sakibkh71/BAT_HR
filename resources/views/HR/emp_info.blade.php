<div class="row">
    <div class="col-lg-6" id="cluster_info">
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Employee Name:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{$emp_log->name}}</dd>
            </div>
        </dl>
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Employee ID:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{$emp_log->user_code}}</dd>
            </div>
        </dl>
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Date of Join:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{todated($emp_log->date_of_join)}}</dd>
            </div>
        </dl>
    </div>
    <div class="col-lg-6" id="cluster_info">
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Designation:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{$emp_log->designations_name}}</dd>
            </div>
        </dl>
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Distributor House:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{$emp_log->distributor_house}}</dd>
            </div>
        </dl>
        <dl class="row mb-0">
            <div class="col-sm-4 text-sm-right">
                <dt>Distributor Point:</dt>
            </div>
            <div class="col-sm-8 text-sm-left">
                <dd class="mb-1">{{$emp_log->distributor_point}}</dd>
            </div>
        </dl>
        {{--<dl class="row mb-0">--}}
            {{--<div class="col-sm-4 text-sm-right">--}}
                {{--<dt>Category:</dt>--}}
            {{--</div>--}}
            {{--<div class="col-sm-8 text-sm-left">--}}
                {{--<dd class="mb-1">{{$emp_log->hr_emp_category_name}}</dd>--}}
            {{--</div>--}}
        {{--</dl>--}}
    </div>
</div>