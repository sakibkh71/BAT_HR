<div class="row">
    <div class="col-md-12 btn_tree_div">
        <div class="form-group no-display">
            <h4 class="pull-left mt-2 mr-4">Location Tree</h4>
            <span><button id="btn_region" data-toggle="tooltip" class="btn btn-sm next @if(isset($serachArray) && $serachArray['location'] =='Region')  btn-success @endif" title="Click to Choose Region" data-type="region">Region</button></span> &nbsp;&nbsp;
            <span><button id="btn_area" data-toggle="tooltip" class="btn btn-sm next @if(isset($serachArray) && $serachArray['location'] =='Area')  btn-success @endif" title="Click to Choose Area" data-type="area">Area</button></span> &nbsp;&nbsp;
            <span><button id="btn_house" data-toggle="tooltip" class="btn btn-sm next @if(isset($serachArray) && $serachArray['location'] =='House')  btn-success @endif" title="Click to Choose House" data-type="house">House</button></span> &nbsp;&nbsp;
            <span><button id="btn_territory" data-toggle="tooltip" class="btn btn-sm next @if(isset($serachArray) && $serachArray['location'] =='Territory')  btn-success @endif" title="Click to Choose Territory" data-type="territory">Territory</button></span> &nbsp;&nbsp;
            <span><button id="btn_point" data-toggle="tooltip" class="btn btn-sm next @if(isset($serachArray) && $serachArray['location'] =='Point')  btn-success @endif" title="Click to Choose Point" data-type="point">Point</button></span> &nbsp;&nbsp;
        </div>
    </div>
    <?php
        if(!isset($single)){
            $location  = app('App\Http\Controllers\LocationAccess')->getAllPoints();
            extract($location);
        }
    ?>

    <div class="col-md-2" id="region_div">
        @php if(isset($regions) && count($regions) > 0) { @endphp
        <div class="form-group">
            <label class="form-label" for="region">Region</label>
            <select id="region" class="form-control" multiple="multiple">
                @foreach($regions as $region)
                    <option value="{{$region->id}}" selected="selected">{{$region->slug}}</option>
                @endforeach
            </select>
        </div>
        @php } @endphp
    </div>

    <div class=" col-md-2" id="area_div">
        @php if(isset($areas) && count($areas) > 0) { @endphp
        <label class="form-label" for="area">Area</label>
        <select id="area" class="form-control " multiple="multiple">
            @foreach($areas as $area)
                <option value="{{$area->id}}" selected="selected">{{$area->slug}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>

    <div class=" col-md-2" id="house_div">
        @php if(isset($companies) && count($companies) > 0) { @endphp
        <label class="form-label" for="house">Distribution House</label>
        <select id="house" class="form-control" multiple="multiple">
            @foreach($companies as $house)
                <option value="{{$house->bat_company_id}}" selected="selected">{{$house->name}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>
    <div class=" col-md-2" id="territory_div">
        @php if(isset($territory) && count($territory) > 0) { @endphp
        <label for="territory" class="form-label">Territory</label>
        <select id="territory" class="form-control multi" multiple="multiple">
            @foreach($territory as $territ)
                <option value="{{$territ->id}}" selected="selected">{{$territ->name}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>

    <div class="col-md-2" id="point_div">
        <div class="form-group">
        @php if(isset($points) && count($points) > 0) { @endphp
        <label for="point" class="form-label">Distribution Point</label>
        <select name="{{isset($serachArray['point_name']) ? $serachArray['point_name'] : 'point[]'}}" id="point" class="form-control multi" multiple="multiple">
            @foreach($points as $point)
                <option value="{{$point->id}}" selected="selected">{{$point->name}}</option>
            @endforeach
        </select>
        @php } @endphp
        </div>
    </div>
</div>
<style>
    .multiselect-container{
        max-height: 300px;
        overflow: auto;
    }
    .multiselect{
        overflow: hidden;
    }
</style>
<script type="text/javascript">

    var DT, compare;
    // Change Region
    function onChangeRegion(selectedOptions){
        var area = setArea(selectedOptions, 'region');
        var company = setCompany(area, 'area');
        var sel_ter = setTerritory(company, 'company');
        var point = setPoint(sel_ter, 'territory', company);
    }
    // Change Area
    function onChangeArea(selectedOptions){
        compare = 'area';
        var company = setCompany(selectedOptions,'area');
        var sel_ter = setTerritory(company, 'company');
        var point = setPoint(sel_ter, 'territory', company);
    }
    // Change Territory
    function onChangeTerritory(selectedOptions){
        compare = 'territory';
        var company = [];
        $('#house option:selected').map(function(a, item){return company.push(item.value);});
        var point = setPoint(selectedOptions, 'territory', company);
    }
    // Change House
    function onChangeHouse(selectedOptions){
        var sel_ter = setTerritory(selectedOptions, 'company');
        var point = setPoint(sel_ter, 'territory', selectedOptions);
    }

    $.ajax({
        url: '{{route('getAllPlaces')}}',
        async: false,
        success: function (data) {
            //DT = $.parseJSON(data);
            DT = data;
            $('#region').multiselect({
                buttonWidth: '100%',
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onDropdownShown: function(even) {
                    this.$filter.find('.multiselect-search').focus();
                },
                onChange: function() {
                    var selectedOptions = [];
                    $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeRegion(selectedOptions);
                },
                onSelectAll: function() {
                    var selectedOptions = [];
                    // triggerOnSelectAll = true;
                    $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeRegion(selectedOptions);
                },
                onDeselectAll:function(){
                    var selectedOptions =[];
                    $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeRegion(selectedOptions);
                }
            });

            $('#area').multiselect({
                buttonWidth: '100%',
                enableFiltering: true,
                enableCaseInsensitiveFiltering : true,
                includeSelectAllOption: true,
                onDropdownShown: function(even) {
                    this.$filter.find('.multiselect-search').focus();
                },
                onChange: function() {
                    var selectedOptions = [];
                    $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeArea(selectedOptions);
                },
                onSelectAll: function() {
                    var selectedOptions = [];
                    // triggerOnSelectAll = true;
                    $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeArea(selectedOptions);
                },
                onDeselectAll:function(){
                    var selectedOptions =[];
                    $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeArea(selectedOptions);
                }
            });

            $('#territory').multiselect({
                buttonWidth: '100%',
                enableFiltering: true,
                enableCaseInsensitiveFiltering : true,
                includeSelectAllOption: true,
                onDropdownShown: function(even) {
                    this.$filter.find('.multiselect-search').focus();
                },
                onChange: function() {
                    var selectedOptions = [];
                    $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeTerritory(selectedOptions);
                },
                onSelectAll: function() {
                    var selectedOptions = [];
                    // triggerOnSelectAll = true;
                    $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeTerritory(selectedOptions);
                },
                onDeselectAll:function(){
                    var selectedOptions =[];
                    $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeTerritory(selectedOptions);
                }
            });

            $('#house').multiselect({
                buttonWidth: '100%',
                enableFiltering: true,
                enableCaseInsensitiveFiltering : true,
                includeSelectAllOption: true,
                onDropdownShown: function(even) {
                    this.$filter.find('.multiselect-search').focus();
                },
                onChange: function() {
                    var selectedOptions = [];
                    $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeHouse(selectedOptions);
                },
                onSelectAll: function() {
                    var selectedOptions = [];
                    // triggerOnSelectAll = true;
                    $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeHouse(selectedOptions);
                },
                onDeselectAll:function(){
                    var selectedOptions =[];
                    $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                    onChangeHouse(selectedOptions);
                }
            });

            $('#point').multiselect({
                buttonWidth: '100%',
                enableFiltering: true,
                enableCaseInsensitiveFiltering : true,
                includeSelectAllOption: true,
                onDropdownShown: function(even) {
                    this.$filter.find('.multiselect-search').focus();
                },
            });

        },
        error : function() {
            alert('Something is Missing!');
        }
    });

    function setArea(sel_val, comp){
        var arr = [];
        var sel = ['multiselect-all'];
        DT.areas.forEach(function(e){
            if($.inArray(e[comp],sel_val) >=0 || e[comp] == sel_val){
                var group = {label: e.slug, value: e.id};
                arr.push(group);
                sel.push(e.id);
            }
        });
        $('#area').multiselect('dataprovider', arr);
        $('#area').multiselect('select', sel);
        var selectconfig = {
            enableFiltering: true,
            includeSelectAllOption: true
        };
        $('#area').multiselect('setOptions', selectconfig);
        $('#area').multiselect('rebuild');

        return sel;
    }

    function setTerritory(sel_val, comp){
        var arr = [];
        var sel = ['multiselect-all'];
        DT.territory.forEach(function(e){
            if($.inArray(e[comp],sel_val) >=0 || e[comp] == sel_val){
                var group = {label: e.name, value: e.id};
                arr.push(group);
                sel.push(e.id);
            };
        });
        $('#territory').multiselect('dataprovider', arr);
        $('#territory').multiselect('select', sel);
        var selectconfig = {
            enableFiltering: true,
            includeSelectAllOption: true
        };
        $('#territory').multiselect('setOptions', selectconfig);
        $('#territory').multiselect('rebuild');
        return sel;
    }

    function setCompany(sel_val, comp){
        var arr = [];
        var sel = ['multiselect-all'];
        DT.companies.forEach(function(e){
            if($.inArray(e[comp],sel_val) >=0 || e[comp] == sel_val){
                var group = {label: e.name, value: e.bat_company_id};
                arr.push(group);
                sel.push(e.bat_company_id);
            };
        });
        $('#house').multiselect('dataprovider', arr);
        $('#house').multiselect('select', sel);
        var selectconfig = {
            enableFiltering: true,
            includeSelectAllOption: true
        };
        $('#house').multiselect('setOptions', selectconfig);
        $('#house').multiselect('rebuild');
        return sel;
    }

    function setPoint(sel_val, comp, dsids){
        var pt_obj = [];
        var arr = [];
        var sel = ['multiselect-all'];
        DT.points.forEach(function(e){
            if($.inArray(e[comp], sel_val) >=0 || e[comp] == sel_val){
                var group = {label: e.name, value: e.id};
                arr.push(group);
                sel.push(e.id);
                if($.inArray(e.dsid,pt_obj) < 0 ) pt_obj.push(e.dsid);
            };
        });
        $('#point').multiselect('dataprovider', arr);
        $('#point').multiselect('select', sel);
        var selectconfig = {
            enableFiltering: true,
            includeSelectAllOption: true
        };
        $('#point').multiselect('setOptions', selectconfig);
        $('#point').multiselect('rebuild');
        return sel;
    }

    $(document).on('click','.next',function(e){
        var access_level = $(this).attr('data-type');

        if(access_level=='region'){
            $("#region_div").show();

            $("#area_div").hide();
            $("#house_div").hide();
            $("#territory_div").hide();
            $("#point_div").hide();
            $("#route_div").hide();
            $("#retailer_div").hide();

            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');

            $("#type_input").val('region');
        }else if(access_level=='area'){
            $("#region_div").show();
            $("#area_div").show();

            $("#house_div").hide();
            $("#territory_div").hide();
            $("#point_div").hide();
            $("#route_div").hide();
            $("#retailer_div").hide();

            $("#type_input").val('area');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');

        }else if(access_level=='house'){
            $("#region_div").show();
            $("#area_div").show();
            $("#house_div").show();

            $("#territory_div").hide();
            $("#point_div").hide();
            $("#route_div").hide();
            $("#retailer_div").hide();

            $("#type_input").val('house');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }else if(access_level=='territory'){
            $("#region_div").show();
            $("#area_div").show();
            $("#house_div").show();
            $("#territory_div").show();

            $("#point_div").hide();
            $("#route_div").hide();
            $("#retailer_div").hide();

            $("#type_input").val('territory');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }else if(access_level=='point'){
            $("#region_div").show();
            $("#area_div").show();
            $('#house_div').show();
            $('#territory_div').show();
            $('#point_div').show();

            $("#route_div").hide();
            $("#retailer_div").hide();

            $("#type_input").val('point');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }else if(access_level=='route'){
            $("#region_div").show();
            $("#area_div").show();
            $('#house_div').show();
            $('#territory_div').show();
            $('#point_div').show();
            $("#route_div").show();

            $('#route_file').prop('disabled', false);
            $("#retailer_div").hide();

            $("#type_input").val('point');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }else if(access_level=='retailer'){
            $("#region_div").show();
            $("#area_div").show();
            $('#house_div').show();
            $('#territory_div').show();
            $('#point_div').show();
            $("#route_div").show();
            $("#retailer_div").show();

            $('#route_file').prop('disabled', true);

            $("#type_input").val('point');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }else {
            $("#region_div").show();
            $("#area_div").show();
            $('#house_div').show();
            $('#territory_div').show();
            $('#point_div').show();
            $("#route_div").show();
            $("#retailer_div").show();

            $("#type_input").val('route');
            $('.next').removeClass('btn-success');
            $(this).addClass('btn-success');
        }
        e.preventDefault();
    });


    /*
     * Added Some of Code by @Abu Bakar
     ----------------------------------------------*/
    $(document).on('change', '#house', function(){
        var selectedOptions = [];
        $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
        onChangeHouse(selectedOptions);
    });
    $(document).on('change', '#territory', function(){
        var selectedOptions = [];
        $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
        onChangeTerritory(selectedOptions);
    });
    $(document).on('change', '#area', function(){
        var selectedOptions = [];
        $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
        onChangeArea(selectedOptions);
    });
    $(document).on('change', '#region', function(){
        var selectedOptions = [];
        $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
        onChangeRegion(selectedOptions);
    });

</script>