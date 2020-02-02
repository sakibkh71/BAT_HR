<htmlpagefooter name="footera">
    @include('PdfTemplate.'.$footer_top)
    <br/><br/>
    <div style="border-top: 2px solid #ccc; overflow: hidden">
        <div style="float: left; width: 40%; font-size: 12px;">Developed by : <a target="_blank" href="http://apsissolutions.com/">apsissolutions.com</a></div>
        <div style="float: left; width: 30%; font-size: 14px; font-weight: bold">@<?php echo date('Y')?> SR Chemical Limited</div>
        <div style="float: right; width: 20%; font-size: 12px;">Page {PAGENO} of {nb}</div>
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="footera" />