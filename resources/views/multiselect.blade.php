@extends('layouts.app')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 no-padding">
                <div class="ibox">
                    <div class="ibox-title">
                        {{ $title }}
                    </div>
                    
                    <?php
                        echo getLocationDropDownData();
                    ?>
                </div>
            </div>
        </div>
    </div>
@endsection
