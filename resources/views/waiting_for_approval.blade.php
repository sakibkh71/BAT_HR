@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="padding: 20px">
                <h1 style="font-weight: bold; text-decoration: underline; color: #0000F0;">Waiting for approval</h1>
                @php
                $ref_id_field = $id_logic->ref_id_field;
                $ref_status_field = $id_logic->ref_status_field;
                @endphp
                <table>
                    <tr>
                        <th>Requsition ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @foreach($results as $k=>$v)
                        <tr>
                            <td>{{$v->$ref_id_field}}</td>
                            <td>{{$v->$ref_status_field}}</td>
                            <td><a class="btn btn-success" href="{{URL::to('delegation-link-view-test/'.$v->$ref_id_field)}}">Details</a> </td>
                        </tr>
                    @endforeach
                </table>


                <br/>
                <br/>
                <br/>
                <br/>
            </div>




        </div>
    </div>
</div>


@endsection
