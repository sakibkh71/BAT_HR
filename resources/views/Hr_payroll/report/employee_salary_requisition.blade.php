@extends('layouts.app')
@section('content')
 <style>
        table.dataTable{
            border-collapse: collapse !important;
        }
        .locked{
            background: #ffa3a3 !important;
            color: #fff;
        }
    </style>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        <h2>{{  implode(", ",  $posted['status']) }}  Employee Salary Requisition</h2>
                        <div class="ibox-tools"></div>
                    </div>
                    <div class="ibox-content">
                        <form action="{{route('employee-salary-requisition')}}" method="post" id="employeeListForm" class="mb-4">
                            @csrf

                            <div class="row">
                                {!! __getCustomSearch('employee_list_report', $posted) !!}

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select class="form-control form-control multi" name="status[]" multiple>
                                            <option value="Active" @if( isset($posted['status']) && in_array("Active", $posted['status'])) selected @endif>Active</option>
                                            <option value="Inactive" @if( isset($posted['status']) && in_array("Inactive", $posted['status'])) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Filter</button>
                                    <button type="button" id="makepdf" class="btn btn-success btn-xs"><i class="fa fa-file-pdf-o"></i> PDF</button>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $report_data_html; ?>
                            </div>
                            <div class="col-md-12 mt-2">{{ $report_data->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(function ($) {
            //Date Range Picker
            $('#date_range').daterangepicker({
                locale: {
                    format: 'Y-M-DD'
                },
                autoApply: true,
            });
        });

        $('#makepdf').click(function () {
            var form = $('#employeeListForm');
            var action = form.attr('action');
            form.attr('action', action+'/pdf').attr("target","_blank");
            form.submit();
            form.attr('action', action);
            form.removeAttr('target');
        });

    </script>
@endsection