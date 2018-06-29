<div style="width: 19cm; height: 10cm; margin: 0 auto;">
    <table style="text-align: left; font-size: 16px; font-family: tahoma; width: 100%; height: 100%">
        <tr>
            <td width="40%" align="left" style="padding: 7cm 0 0 40px;">
                <img src="images/mepp_logo_koperta.jpg" alt="Logo" />
            </td>
            <td align="left" style="padding: 7cm 0 0 40px; font-size: 16px; ">
                <?php
                    echo $Dane['nazwa'];
                ?>
                <br /><br />
                <?php
                    echo $Dane['adres']."<br />". $Dane['kod_pocztowy'][0].$Dane['kod_pocztowy'][1]. "-". $Dane['kod_pocztowy'][2].$Dane['kod_pocztowy'][3].$Dane['kod_pocztowy'][4]. " {$Dane['miejscowosc']}";
                ?>
            </td>
        </tr>
    </table>
</div>