
<script>
    $(document).on('click', '#top_search, .top_search', function () {
        $('#search_by').toggle();
        $('.advanchedSearchToggle').toggle();
    });

    $(document).on('click', '.search_submit', function (e) {
        e.preventDefault();

        var url = "<?php echo $ajaxUrl; ?>";
        var _token = '<?php echo csrf_token() ?>';
        var search_type = $(this).attr('search_type');
        var error = 0;

        Ladda.bind(this);
        var l = $(this).ladda();


        $('.mendatory').each(function(){
            var val = $(this).val();
            if(!val)
            {
                error = 1;
            }
        });

        if(error)
        {
            swal({
                title: "Sorry!",
                text: 'Star(*) marked fields are required.',
                type: "warning"
            });
        }
        else
        {
            $.ajax({
                url: url,
                type: 'POST',
                //data: $('#grid_list_frm').serialize(),
                data: $('#grid_list_frm').serialize()+'&_token='+_token+'&search_type[]='+search_type,
                beforeSend: function(){ l.ladda( 'start' );},
                success: function (d) {
                    //alert(d);
                    if(search_type == 'show')
                    {
                        $('.showSearchData').html(d);
//                        myConfiguration();
                        $('.top_search').click();
                    }
                    else{
                        window.location.href = './public/export/'+d;
                    }
                    l.ladda('stop');
                }
            });
        }

    });
</script>


