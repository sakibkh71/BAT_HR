@if(!empty(Session::get('success')))
    @php($message = Session::get('success'))
    <script>
        var popupId = "{{ uniqid() }}";
        if(!sessionStorage.getItem('shown-' + popupId)) {
            swal.fire("Successfull!", '<?php echo $message; ?>', "success");
        }
        sessionStorage.setItem('shown-' + popupId, '1');
    </script>

@elseif (Session::has('error'))
    @php($message = Session::get('error'))
    <script>
        var popupId = "{{ uniqid() }}";
        if(!sessionStorage.getItem('shown-' + popupId)) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: '<?php echo $message; ?>',
            });
        }
        sessionStorage.setItem('shown-' + popupId, '1');
    </script>
@elseif (Session::has('warning'))
    @php($message = Session::get('warning'))
    <script>
        var popupId = "{{ uniqid() }}";
        if(!sessionStorage.getItem('shown-' + popupId)) {
            swal.fire("Cautions!", '<?php echo $message; ?>', "warning");
        }
        sessionStorage.setItem('shown-' + popupId, '1');
    </script>

@elseif (Session::has('info'))
    @php($message = Session::get('info'))
    <script>
        var popupId = "{{ uniqid() }}";
        if(!sessionStorage.getItem('shown-' + popupId)) {
            swal.fire("Note it!", '<?php echo $message; ?>', "info");
        }
        sessionStorage.setItem('shown-' + popupId, '1');
    </script>
@else
@endif

