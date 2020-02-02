<table id="employee_list" border="1">
    <thead>
    <tr>
        <td rowspan="2" class="text-center">SL No.</td>

        <td rowspan="2" class="text-center">Distributor Point</td>
        <td rowspan="2" class="text-center">Employee Name</td>
        <td rowspan="2" class="text-center">Employee Code</td>
        <td rowspan="2" class="text-center">Designation</td>
        <td colspan="3" class="text-center">Leave</td>

    </tr>
    <tr>
        <td>Entitle Days</td>
        <td>Enjoyed Days</td>
        <td>Balance Days</td>
    </tr>

    </thead>

    <tbody>

    @php($i=0)
    @if(!empty($user_info))
    @foreach($user_info as $user)
        <tr>
            <td class="text-center">{{$i+1}}</td>
            <td class="text-center">{{$user->point}}</td>
            <td class="text-center">{{$user->user_name}}</td>
            <td class="text-center"> {{$user->user_code}}</td>
            <td class="text-center">{{$user->designations_name}}</td>
            <td class="text-center">{{isset($user->entitled_leave)?$user->entitled_leave:0}}</td>
            <td class="text-center">{{isset($user->leave_taken)?$user->leave_taken:0}}</td>
            <td class="text-center">{{isset($user->balance_leaves)?$user->balance_leaves:0}}</td>
        </tr>
        @php($i++)
    @endforeach
        @endif
    </tbody>

</table>
<style>
    table{
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    td{
        padding: 4px;
    }
</style>