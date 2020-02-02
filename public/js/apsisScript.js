$(document).ready(function(){
    if($("select option:first").val() == 'no-value'){
        // $("select option:first").removeAttr('value');
    }
    $('.multi').multiselect({
        enableHTML: true,
        buttonWidth: '100%',
        filterPlaceholder: 'Search',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: false,
        maxHeight: '300',
        includeSelectAllOption: true,
        nonSelectedText: 'Choose an option!',
        templates: {
            filter: '<div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div>',
            li: '<li class=""><a tabindex="0"><label class="multi-checkbox"></label></a></li>'
        },
        onDropdownShown: function(even) {
            this.$filter.find('.multiselect-search').focus();
        },
        dropRight: true,
        maxHeight: 300
    });

    $('.validator').validator();

    $('.data-table').dataTable();

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    Ladda.bind('.ladda-button');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    $('form').attr('autocomplete', 'off');

});

$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});

$('.datepicker').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    autoApply:false,
    format: "yyyy-mm-dd"
});

$('.months_picker').datepicker({
    autoclose: true,
    minViewMode: 1,
    format: 'yyyy-mm'
});


$('.daterange').daterangepicker({
    locale: {
        format: 'Y-MM-DD'
    },
    autoUpdateInput: false,
    autoApply:true
});

$('.daterange').on('apply.daterangepicker', function (ev, picker) {
    var start_date = picker.startDate.format('Y-MM-DD')
    var end_date = picker.endDate.format('Y-MM-DD')
    $(this).val(start_date + ' - ' + end_date);
});

function makeAjaxText(url, load) {
    return $.ajax({
        url: url,
        type: 'get',
        cache: false,
        beforeSend: function(){
            if(typeof(load) != "undefined" && load !== null){
                load.ladda('start');
            }
        }
    }).always(function() {
        if(typeof(load) != "undefined" && load !== null){
            load.ladda('stop');
        }
    }).fail(function() {
        swalError();
    });
}

function makeAjaxPostText(data, url, load) {
    return $.ajax({
        url: url,
        type: 'post',
        data: data,
        cache: false,
        beforeSend: function(){
            if(typeof(load) != "undefined" && load !== null){
                load.ladda('start');
            }
        }
    }).always(function() {
        if(typeof(load) != "undefined" && load !== null){
            load.ladda('stop');
        }
    }).fail(function() {
        swalError();
    });
}

function makeAjax(url, load) {
    return $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        cache: false,
        beforeSend: function(){
            if(typeof(load) != "undefined" && load !== null){
                load.ladda('start');
            }
        }
    }).always(function() {
        if(typeof(load) != "undefined" && load !== null){
            load.ladda('stop');
        }
    }).fail(function() {
        swalError();
    });
}

function makeAjaxPost(data, url, load) {
    return $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        cache: false,
        beforeSend: function(){
            if(typeof(load) != "undefined" && load !== null){
                load.ladda('start');
            }
        }
    }).always(function() {
        if(typeof(load) != "undefined" && load !== null){
            load.ladda('stop');
        }
    }).fail(function() {
        swalError();
    });
}

function swalError(msg) {
    var message = typeof(msg) != "undefined" && msg !== null ? msg : "Something went wrong";
    Swal.fire({
        title: "Sorry !!",
        html: message,
        type: "error",
        showConfirmButton: false,
        // timer: 1000
    });
}

function swalWarning(msg) {
    var message = typeof(msg) != "undefined" && msg !== null ? msg : "Something went wrong";
    Swal.fire({
        title: "Warning !!",
        html: message,
        type: "warning",
        showConfirmButton: false,
        // timer: 1000
    });
}

function swalSuccess(msg) {
    var message = typeof(msg) != "undefined" && msg !== null ? msg : "Action has been Completed Successfully";
    Swal.fire({
        title: 'Successful !!',
        html: message,
        type: 'success',
        showConfirmButton: false,
        // timer: 1500
    });
}

function swalRedirect(url, msg, mode) {
    var message = typeof(msg) != "undefined" && msg !== null ? msg : "Action has been Completed Successfully";
    var title = 'Successful !!';
    var type = 'info';
    if(typeof(mode) != "undefined" && mode !== null){
        if(mode == 'success'){
            var title = 'Successful !!';
            var type = 'success';
        } else if(mode == 'error'){
            var title = 'Failed !!';
            var type = 'error';
        }else if(mode == 'warning'){
            var title = 'Warning !!';
            var type = 'warning';
        }else if(mode == 'question'){
            var title = 'Warning !!';
            var type = 'question';
        }else{
            var title = 'Successful !!';
            var type = 'info';
        }
    }
    return Swal.fire({
        title: title,
        html: message,
        type: type,
        reverseButtons : true,
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Thank You',
    }).then(function (s) {
        if(s.value){
            if(typeof(url) != "undefined" && url !== null){
                window.location.replace(url);
            }else{
                location.reload();
            }
        }
    });

    setTimeout(function(){  window.location.replace(url); }, 100);

}

function swalConfirm(msg) {
    var message = typeof(msg) != "undefined" && msg !== null ? msg : "You won't be able to revert this!";
    return Swal.fire({
        title: 'Are you sure?',
        html: message,
        type: 'warning',
        reverseButtons : true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Confirm!',
        cancelButtonText: 'Cancel'
    });
}

function apsis_money(nStr){
    var num =  parseFloat(nStr).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    return num;
}

/* Script Added by Abu Bakar
--------------------------------------*/
(function($) {
    $('.apsis_table').DataTable();

    $(window).on('load', function () { resizeTable(); });

    $( window ).resize(function() { resizeTable(); });

    function resizeTable() {
        if ( $( ".table-responsive" ).length ) {
            var win_height = $(window).height();
            var win_width = $(window).width();
            var TablePos = $('.table-responsive').offset().top;
            var tableMaxHeight = parseFloat(win_height - TablePos);

            if (win_width > 1000){
                $('.table-responsive').css({
                    'max-height': tableMaxHeight
                });
            }
        }
    }

    //Input only Number
    $(document).on("keypress keyup blur",".number", function (event) {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
})(jQuery);

