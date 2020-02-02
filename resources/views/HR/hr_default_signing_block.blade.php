@if(isset($signatures))
    <?php $max = ceil(count($signatures)/4); ?>
    <table width="100%" id="sign_table" class="mt-20 no-borders">
        <tr>
            @for($i=0; $i<($max*4); $i++)
                <td valign="top" style="text-align: center">
                    @if(!empty($signatures[$i]))
                        ..........................
                        <p>{{isset($signatures[$i])?$signatures[$i]:''}}</p>
                    @endif
                </td>
            @endfor
        </tr>
    </table>
    @else
    <table width="100%" id="sign_table">
        <tr>
            <td style="text-align: center">.........................<br>Prepared by</td>
            <td style="text-align: center">..........................<br>Employee Sign</td>
            <td style="text-align: center">..........................<br>Manager-Hr</td>
        </tr>
    </table>
@endif