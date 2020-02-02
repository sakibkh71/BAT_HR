<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! getOptionValue('application_name') !!}</title>
    <link rel="shortcut icon" type="image/png" href="{{asset(getOptionValue('company_logo2'))}}"/>
    @include('includes.assets')
    @php(date_default_timezone_set('Asia/Dhaka'))
</head>
<body>
<div id="wrapper" style="padding: 0">
    <div class="srcil-bg" style="padding: 0; height: 100vh">

        <?php $data = json_encode($logData);?>
        <div id="tableDynamic" style="overflow: auto">

        </div>
    </div>

</div>
</body>
</html>


<script>
    var divSelector = '#tableDynamic'
    var jsonStr = <?= $data?>;
console.log(jsonStr);
    tableCreator(  jsonStr,divSelector )
    function tableCreator(e, t) {
        function i(e, t) {
            var n = "";
            var r = "";
            var s = "";

            $.each(t[0], function(e, t) {
                s += "<th>" + e + "</th>"
            });

            $.each(t, function(e, t) {
                r += "<tr>";
                $.each(t, function(e, t) {
                    var n = 1 + Math.floor(Math.random() * 90 + 10);
                    var s = $.isPlainObject(t);
                    var o = [];
                    if (s) {
                        o = $.makeArray(t)
                    }
                    if ($.isArray(t) && t.length > 0) {
                        r += "<td><div><a href='#" + n + "' data-toggle='collapse' data-parent='#msgReport'><span class='glyphicon glyphicon-plus'></span></a><div id='" + n + "' aria-expanded='true' class='panel-collapse in collapse'>" + i(e, t) + "</div></div></td>"
                    } else {
                        if (o.length > 0) {
                            r += "<td><div><a href='#" + n + "' data-toggle='collapse' data-parent='#msgReport'><span class='glyphicon glyphicon-plus'></span></a><div id='" + n + "' aria-expanded='true' class='panel-collapse in collapse'>" + i(e, o) + "</div></div></td>"
                        } else {
                            r += "<td>" + t + "</td>"
                        }
                    }
                });
                r += "</tr>"
            });
            n += "<table class='table table-bordered table-hover table-condensed'><thead>" + s + "</thead><tbody>" + r + "</tbody></table>";
            return n
        }
        $(t).empty();
        $(t).append("<table id='parentTable' class='table table-bordered table-hover table-condensed'><thead></thead><tbody></tbody></table>");
        var n = "";
        var r = "";
        $.each(e, function(e, t) {
            n += "<tr><th>" + e + "</th></tr>";
            var s = 1 + Math.floor(Math.random() * 90 + 10);
            var o = $.isPlainObject(t);
            var u = [];
            if (o) {
                u = $.makeArray(t)
            }
            if ($.isArray(t) && t.length > 0) {
                r += "<tr><td><div id='accordion'><a href='#" + s + "' data-toggle='collapse' data-parent='#msgReport'><span class='glyphicon glyphicon-plus'></span></a><div id='" + s + "' aria-expanded='true' class='panel-collapse in collapse'>" + i(e, t) + "</div></div></td></tr>"
            } else {
                if (u.length > 0) {
                    r += "<tr><td><div id='accordion'><a href='#" + s + "' data-toggle='collapse' data-parent='#msgReport'><span class='glyphicon glyphicon-plus'></span></a><div id='" + s + "' aria-expanded='true' class='panel-collapse in collapse'>" + i(e, u) + "</div></div></td></tr>"
                } else {
                    r += "<tr><td>" + t + "</td></tr>"
                }
            }
        });
        // $("#parentTable thead").append("<tr>" + n + "</tr>");
        $("#parentTable tbody").append("<tr>" + r + "</tr>");
        $(".glyphicon ").on("click", function() {
            var e = $(this).attr("class");
            switch (e) {
                case "glyphicon glyphicon-plus":
                    $(this).removeClass("glyphicon-plus").addClass("glyphicon-minus");
                    break;
                case "glyphicon glyphicon-minus":
                    $(this).removeClass("glyphicon-minus").addClass("glyphicon-plus");
                    break;
                default:
            }
        })
    }
</script>