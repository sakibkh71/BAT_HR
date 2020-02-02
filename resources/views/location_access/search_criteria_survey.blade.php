<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <h4>Location Tree</h4>
            <span><button data-toggle="tooltip" class="btn btn-sm next btn-success" title="Click to Choose Region" data-type="region">Region</button></span> &nbsp;&nbsp;
            <span><button data-toggle="tooltip" class="btn btn-sm next" title="Click to Choose Area" data-type="area">Area</button></span> &nbsp;&nbsp;
            <span><button data-toggle="tooltip" class="btn btn-sm next" title="Click to Choose House" data-type="house">House</button></span> &nbsp;&nbsp;
            <span><button data-toggle="tooltip" class="btn btn-sm next" title="Click to Choose Territory" data-type="territory">Territory</button></span> &nbsp;&nbsp;
            <span><button data-toggle="tooltip" class="btn btn-sm next" title="Click to Choose Point" data-type="point">Point</button></span> &nbsp;&nbsp;
        </div>
    </div>
</div>
<div class="row">    
    <div class="form-group col-sm-2" id="region_div" style="margin-right: 30px">
        @php if(isset($regions) && count($regions) > 0) { @endphp
        <label for="region">Region</label>
        <select name="selRegion[]" id="region" class="form-control" multiple="multiple">            
            @foreach($regions as $region)
                <option value="{{$region->id}}" selected="selected">{{$region->slug}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div> 
    <div class="form-group col-sm-2" id="area_div" style="display: none; margin-right: 30px">        
        @php if(isset($areas) && count($areas) > 0) { @endphp
        <label for="area">Area</label>
        <select name="area[]" id="area" class="form-control " multiple="multiple">            
            @foreach($areas as $area)
                <option value="{{$area->id}}" selected="selected">{{$area->slug}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>
    <div class="form-group col-sm-2" id="house_div" style="display: none; margin-right: 30px">
        @php if(isset($companies) && count($companies) > 0) { @endphp
        <label for="house">Distribution House</label>
        <select name="house[]" id="house" class="form-control col-sm-2" multiple="multiple">
            @foreach($companies as $house)
                <option value="{{$house->id}}" selected="selected">{{$house->name}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>
    <div class="form-group col-sm-2" id="territory_div" style="display: none; margin-right: 30px">
        @php if(isset($territory) && count($territory) > 0) { @endphp
        <label for="territory">Territory</label>
        <select name="territory[]" id="territory" class="form-control col-sm-2" multiple="multiple">
            @foreach($territory as $territ)
                <option value="{{$territ->id}}" selected="selected">{{$territ->name}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>
    <div class="form-group col-sm-2" id="point_div" style="display: none; margin-right: 30px">
    @php if(isset($points) && count($points) > 0) { @endphp
        <label for="point">Distribution Point</label>
        <select name="point[]" id="point" class="form-control col-sm-2" multiple="multiple">
            @foreach($points as $point)
                <option value="{{$point->id}}" selected="selected">{{$point->name}}</option>
            @endforeach
        </select>
        @php } @endphp
    </div>
    
</div>
<div class="clear-both"></div>
@section('js')
<script type="text/javascript">
    $(document).ready(function () {
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
                    buttonWidth: '200px',
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    onChange: function() {
                        var selectedOptions = [];
                        $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeRegion(selectedOptions);
                    },
                    onSelectAll: function() {
                        var selectedOptions = [];
                        // triggerOnSelectAll = true;
                        $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        // onChangeRegion(selectedOptions);                    
                    },
                    onDeselectAll:function(){
                        var selectedOptions =[];
                        $('#region option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeRegion(selectedOptions);
                    }
                });

                $('#area').multiselect({
                    buttonWidth: '200px',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering : true,
                    includeSelectAllOption: true,
                    onChange: function() {
                        var selectedOptions = [];
                        $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});                        
                        onChangeArea(selectedOptions);
                    },
                    onSelectAll: function() {
                        var selectedOptions = [];
                        // triggerOnSelectAll = true;
                        $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        // onChangeArea(selectedOptions);
                    },
                    onDeselectAll:function(){
                        var selectedOptions =[];
                        $('#area option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeArea(selectedOptions);
                    }
                });

                $('#territory').multiselect({
                    buttonWidth: '200px',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering : true,
                    includeSelectAllOption: true,
                    onChange: function() {
                        var selectedOptions = [];
                        $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeTerritory(selectedOptions);
                    },
                    onSelectAll: function() {
                        var selectedOptions = [];
                        // triggerOnSelectAll = true;
                        $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        // onChangeTerritory(selectedOptions);
                    },
                    onDeselectAll:function(){
                        var selectedOptions =[];
                        $('#territory option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeTerritory(selectedOptions);
                    }
                });
                
                $('#house').multiselect({
                    buttonWidth: '200px',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering : true,
                    includeSelectAllOption: true,
                    onChange: function() {
                        var selectedOptions = [];
                        $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeHouse(selectedOptions);
                    },
                    onSelectAll: function() {
                        var selectedOptions = [];
                        // triggerOnSelectAll = true;
                        $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        // onChangeHouse(selectedOptions);
                    },
                    onDeselectAll:function(){
                        var selectedOptions =[];
                        $('#house option:selected').map(function(a, item){return selectedOptions.push(parseInt(item.value));});
                        onChangeHouse(selectedOptions);
                    }
                });

                $('#point').multiselect({
                    buttonWidth: '200px',
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering : true,
                    includeSelectAllOption: true,                    
                });
                                
                $('#include_report').multiselect('rebuild');
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
            return sel;
        }

        function setCompany(sel_val, comp){
            var arr = [];
            var sel = ['multiselect-all'];                
            DT.companies.forEach(function(e){
                if($.inArray(e[comp],sel_val) >=0 || e[comp] == sel_val){
                    var group = {label: e.name, value: e.id};
                    arr.push(group);
                    sel.push(e.id);                        
                };                    
            });
            $('#house').multiselect('dataprovider', arr);
            $('#house').multiselect('select', sel);
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
    });
</script>
@endsection