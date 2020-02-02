$(document).ready(function(){

        var hdn_val =  jQuery.parseJSON($('#hdn_val').val());
        var option_key_js =  jQuery.parseJSON($('#option_key_js').val());

        //url_dd globally come from view page 
        var url_dd = base_url+'/return_location';

            $('.geo_loc_cls').multiselect({
                includeSelectAllOption: true,
            });

                $('.geo_loc_cls option').each(function(){
                    this.selected=true;
                });
                $('.geo_loc_cls').multiselect("refresh");

            $(".geo_loc_cls").change(function(){

                var boxID = jQuery(this).attr("id");
                var boxIDHash = '#'+boxID;
                var locAry = {'geo_loc_1': 'geo_location_1', 'geo_loc_2': 'geo_location_2', 'geo_loc_3': 'geo_location_3', 'geo_loc_4': 'geo_location_4', 'geo_loc_5': 'geo_location_5', 'geo_loc_6': 'geo_location_6', 'geo_loc_7': 'geo_location_7', 'geo_loc_8': 'geo_location_8'};
                
                var data = {parent_id: $(boxIDHash).val(), req_from: locAry[boxID]};

                ajaxCall(boxID, data, url_dd);
            });


            function ajaxCall(change_id, data, url_dd){

                console.log(data);
                // alert(data);
            
                var chk_val = 0;
                var id_aary = [];

                if(data.parent_id.length > 0){
                    makeAjaxPost(data, url_dd, null).done(function(result){
                        console.log(result.prev.length==0?'no value': 'has value');

                        $.each(result.next, function(index, value){

                            var html = '';
                            var inner_html = '';
                            var change_id = change_id;
                            
                            for(i=0; i<value.length; i++){

                                html += '<option value="'+value[i].id+'">'+value[i].name+'</option>';  

                                if(index == 'geo_location_1'){ inner_html = "geo_loc_1"; chk_val = 1;}   
                                if(index == 'geo_location_2'){ inner_html = "geo_loc_2"; chk_val = 2;}   
                                if(index == 'geo_location_3'){ inner_html = "geo_loc_3"; chk_val = 3;}   
                                if(index == 'geo_location_4'){ inner_html = "geo_loc_4"; chk_val = 4;}   
                                if(index == 'geo_location_5'){ inner_html = "geo_loc_5"; chk_val = 5;}   
                                if(index == 'geo_location_6'){ inner_html = "geo_loc_6"; chk_val = 6;}   
                                if(index == 'geo_location_7'){ inner_html = "geo_loc_7"; chk_val = 7;}  
                                if(index == 'geo_location_8'){ inner_html = "geo_loc_8"; chk_val = 8;}  
                            }

                            if(($.inArray(index, option_key_js))>=0){
                                if(($.inArray(chk_val, hdn_val))<0){
                                    document.getElementById(inner_html).innerHTML = html;
                                }
                            }
                        });


                        for(var pp = 1; pp <= 8; pp++){
                            if(change_id != "geo_loc_"+pp){
                                $("#geo_loc_"+pp).multiselect('rebuild');
                                    $("#geo_loc_"+pp+" option").each(function(){
                                        this.selected=true;
                                    });
                                    $("#geo_loc_"+pp).multiselect("refresh");
                            }
                        }


                        var temp_val = 0;
                        if(result.prev.length!=0){
                           $.each(result.prev, function(index, value){
                                if(value.length > 0){

                                    id_aary = [];
                                    
                                    $.each(value, function(index_info, info){

                                        id_aary.push(info.id);
                                    });

                                    if(index == 'geo_location_1'){ inner_html = "#geo_loc_1";}   
                                    if(index == 'geo_location_2'){ inner_html = "#geo_loc_2";}   
                                    if(index == 'geo_location_3'){ inner_html = "#geo_loc_3";}   
                                    if(index == 'geo_location_4'){ inner_html = "#geo_loc_4";}   
                                    if(index == 'geo_location_5'){ inner_html = "#geo_loc_5";}   
                                    if(index == 'geo_location_6'){ inner_html = "#geo_loc_6";}   
                                    if(index == 'geo_location_7'){ inner_html = "#geo_loc_7";}  
                                    if(index == 'geo_location_8'){ inner_html = "#geo_loc_8";}

                                    $(inner_html).val(id_aary);
                                    $(inner_html).multiselect("refresh");

                                    console.log(inner_html, id_aary, index);
                                }
                           }); 
                        }

   
                    });        
                }
                else{

                    var ary_1 = ['geo_location_1', 'geo_location_2', 'geo_location_3', 'geo_location_4', 'geo_location_5', 'geo_location_6', 'geo_location_7', 'geo_location_8'];
                    var ary_2 = ["#geo_loc_1", "#geo_loc_2", "#geo_loc_3", "#geo_loc_4", "#geo_loc_5", "#geo_loc_6", "#geo_loc_7", "#geo_loc_8"];
                    
                    $chkk = 0;
                    $.each(ary_1 , function( value ) {
                        
                        if(data.req_from == ary_1[value] || $chkk > 0){
                            if(value < 7){
                                $(ary_2[value+1]).multiselect("disable");
                        
                                $chkk++;
                            }  
                        }
                    });

                }
                    
            }
            
        });