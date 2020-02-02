@extends('layouts.app')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{__lang('Employee_vacancy')}}</h2>
                        {{--<div class="ibox-tools">--}}
                            {{--<a href="{{route('hr-create-new-salary-sheet')}}" id="create_salary_sheet" class="btn btn-xs btn-primary"><i class="fa fa-plus-circle"></i> New Sheet</a>--}}
                            {{--<button type="button" id="edit_sheet_name" class="btn btn-xs btn-danger no-display"><i class="fa fa-edit"></i> Edit Salary Sheet</button>--}}
                            {{--<button id="generate_bank_advice" class="btn btn-primary btn-xs no-display"><span class="fa fa-recycle"></span> Generate Bank Advice</button>--}}
                            {{--<button id="show_bank_advice" class="btn btn-primary btn-xs no-display"><i class="fa fa-money"></i> View Bank Advice </button>--}}
                            {{--<button id="make_salary_disburse" class="btn btn-success btn-xs no-display"><i class="fa fa-money"></i> Make Salary Disburse </button>--}}
                            {{--<button id="sheet_data" class="btn btn-primary btn-xs no-display"><i class="fa fa-eye"></i> View </button>--}}
                            {{--<button id="sheet_report" class="btn btn-success btn-xs no-display"><i class="fa fa-print"></i> Print </button>--}}
                        {{--</div>--}}
                    </div>
                    <div class="ibox-content">
                        {!! __getMasterGrid('hr_company_emp_vacancy') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection