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
    @php
        if(!isset($single)){
            $location = app('App\Http\Controllers\LocationAccess')->getAllPoints();
            extract($location);
        }
    @endphp

    <div class="col-md-2" id="region_div">
        @if(isset($regions) && count($regions) > 0)
        <div class="form-group">
            <label class="form-label" for="region">Region</label>
            <select id="region" class="form-control multi" multiple="multiple">
                @foreach($regions as $region)
                    <option value="{{$region->id}}" selected="selected">{{$region->slug}}</option>
                @endforeach
            </select>
        </div>
       @endif
    </div>

    <div class=" col-md-2" id="area_div">
        @if(isset($areas) && count($areas) > 0)
        <label class="form-label" for="area">Area</label>
        <select id="area" class="form-control multi" multiple="multiple">
            @foreach($areas as $area)
                <option value="{{$area->id}}" selected="selected">{{$area->slug}}</option>
            @endforeach
        </select>
        @endif
    </div>

    <div class=" col-md-2" id="house_div">
        @if(isset($companies) && count($companies) > 0)
        <label class="form-label" for="house">Distribution House</label>
        <select id="house" class="form-control multi" multiple="multiple">
            @foreach($companies as $house)
                <option value="{{$house->bat_company_id}}" selected="selected">{{$house->name}}</option>
            @endforeach
        </select>
        @endif
    </div>

    <div class=" col-md-2" id="territory_div">
        @if(isset($territory) && count($territory) > 0)
        <label for="territory" class="form-label">Territory</label>
        <select id="territory" class="form-control multi" multiple="multiple">
            @foreach($territory as $territ)
                <option value="{{$territ->id}}" selected="selected">{{$territ->name}}</option>
            @endforeach
        </select>
        @endif
    </div>
    @php
        if(!empty($dpids)){ $selected = explode(",",$dpids);}else{$selected = false;}
    @endphp

    <div class="col-md-2" id="point_div">
        <div class="form-group">
        @if(isset($points) && count($points) > 0)
        <label for="point" class="form-label">Distribution Point</label>
        <select name="{{isset($serachArray['point_name']) ? $serachArray['point_name'] : 'point[]'}}" id="point" class="form-control multi" multiple="multiple">
            @foreach($points as $point)
                <option value="{{$point->id}}" @php if($selected){ if(in_array($point->id, $selected)){ echo 'selected="selected"'; }} else{ echo 'selected="selected"';} @endphp >{{$point->name}}</option>
            @endforeach
        </select>
        @endif
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

        $.ajax({
            url: '{{route('get-tree-places')}}',
            async: false,
            success: function (data) {
                DT = data;
                console.log(DT);
            },
            error : function() {
                alert('Something is Missing!');
            }
        });

        /*
         * Change Functions
         ---------------------------------------------------------*/
        //Onchange Region
        $(document).on('change', '#region', function(){
            var selectedRegion = [];
            $('#region option:selected').map(function(a, item){return selectedRegion.push(parseInt(item.value));});
            onChangeRegion(selectedRegion);
        });

        //Onchange Region
        $(document).on('change', '#area', function(){
            var selectedArea = [];
            $('#area option:selected').map(function(a, item){return selectedArea.push(parseInt(item.value));});
            onChangeArea(selectedArea);
        });

        //Onchange Region
        $(document).on('change', '#house', function(){
            var selectedHouse = [];
            $('#house option:selected').map(function(a, item){return selectedHouse.push(parseInt(item.value));});
            onChangeHouse(selectedHouse);
        });

        //Onchange Territory
        $(document).on('change', '#territory', function(){
            var selectedTerritory = [];
            $('#territory option:selected').map(function(a, item){return selectedTerritory.push(parseInt(item.value));});
            onChangeTerritory(selectedTerritory);
        });

        //Onchange Territory
        $(document).on('change', '#point', function(){
            var selectedPoint = [];
            $('#point option:selected').map(function(a, item){return selectedPoint.push(parseInt(item.value));});
            onChangePoint(selectedPoint);
        });


        /*  Call Functions
        --------------------------------------*/
        //change region function
        function onChangeRegion(selectedOptions){
            var area = setArea(selectedOptions, 'region');
            var company = setCompanies(area, 'area');
            var territoryArr = setTerritory(company, 'company');
            var point = setPoint(territoryArr, 'territory');
        }

        // Change Area function
        function onChangeArea(selectedOptions){
            var company =  setCompanies(selectedOptions, 'area');
            var territoryArr = setTerritory(company, 'company');
            var point = setPoint(territoryArr, 'territory');

            var region = setRegion(selectedOptions, 'area');
        }

        // Change House
        function onChangeHouse(selectedOptions){
            var territoryArr = setTerritory(selectedOptions, 'company');
            var point = setPoint(territoryArr, 'territory');

            var area = setArea(selectedOptions, 'company');
            var region = setRegion(area, 'area');
        }

        //Change Territory
        function onChangeTerritory(selectedOptions){
            var point = setPoint(selectedOptions, 'territory');

            var company =  setCompanies(selectedOptions, 'territory');
            var area = setArea(company, 'company');
            var region = setRegion(area, 'area');
        }


        //Change Territory
        function onChangePoint(selectedOptions){
            var territory = setTerritory(selectedOptions, 'point');
            var company =  setCompanies(territory, 'territory');
            var area = setArea(company, 'company');
            var region = setRegion(area, 'area');
        }


        /*
         * Set Functions
        ------------------------------------------------------*/
        //Set Area
        function setRegion(areaArray, option ='area'){
            var region = [];
            var sel = ['multiselect-all'];
            $.map(DT.areas,function(e,key) {
                if (areaArray.includes(e.id)){
                    sel.push(e.region);
                }
            });
            $('#region').val(sel);
            $('#region').multiselect('rebuild');
            return sel;
        }

        //Set Area
        function setArea(regionArray, option = 'region'){
            var sel = ['multiselect-all'];

            if(option=='region'){
                $.map(DT.areas,function(e,key) {
                    if (regionArray.includes(e.region)){
                        sel.push(e.id);
                    }
                });
            }else if(option=='company'){
                $.map(DT.companies,function(e,key) {
                    if (regionArray.includes(e.bat_company_id)){
                        sel.push(e.area);
                    }
                });
            }

            $('#area').val(sel);
            $('#area').multiselect('rebuild');
            return sel;
        }

        //Set House
        function setCompanies(optionsArray,  option = 'area'){
            var sel_house = ['multiselect-all'];
            if(option=='area') {
                $.map(DT.companies, function (e, key) {
                    if (optionsArray.includes(e.area)) {
                        sel_house.push(e.bat_company_id);
                    }
                });
            }else if(option=='territory') {
                $.map(DT.territory, function (e, key) {
                    if (optionsArray.includes(e.id)) {
                        sel_house.push(e.company);
                    }
                });
            }
            $('#house').val(sel_house);
            $('#house').multiselect('rebuild');
            return sel_house;
        }

        //Set Territory
        function setTerritory(companyArray, option = 'company'){
            var sel_territory = ['multiselect-all'];
            if(option=='company') {
                $.map(DT.territory, function (e, key) {
                    if (companyArray.includes(e.company)) {
                        sel_territory.push(e.id);
                    }
                });
            }else if(option=='point') {
                $.map(DT.points, function (e, key) {
                    if (companyArray.includes(e.id)) {
                        sel_territory.push(e.territory);
                    }
                });
            }
            $('#territory').val(sel_territory);
            $('#territory').multiselect('rebuild');
            return sel_territory;
        }

        //Set Point
        function setPoint(territoryArray, territory){
            //console.log(territoryArray);
            var pointArr = [];
            var sel_point = ['multiselect-all'];
            $.map(DT.points,function(e,key) {
                if (territoryArray.includes(e.territory)){
                    var group = {label: e.name, value: e.id};
                    pointArr.push(group);
                    sel_point.push( e.id);
                }
            });
            $('#point').multiselect('dataprovider', pointArr);
            $('#point').val(sel_point);
            $('#point').multiselect('rebuild');
            return sel_point;
        }

    /*
     * Button Action
     -----------------------------------------------------------*/
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
</script>