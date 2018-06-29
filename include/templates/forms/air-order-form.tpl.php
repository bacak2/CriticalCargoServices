<?php
    include(SCIEZKA_SZABLONOW."forms/sea-dane.php");
    //$Mode = array("FCL" => 'FCL', "LCL" => "LCL");
    $Waluty = UsefullBase::GetWaluty($this->Baza);
    $Wstecz = intval(date("Y")) - 2011;
    $Form = new FormularzSimple();
    $Form->FormStart("sea_order", "", "post");
    echo '<table class="formularz">';
        echo "<tr>\n";
            echo "<th>SHIPPER (załadowca)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[shipper]", $Values['shipper'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                $Form->PoleSelect("Air[id_klient_shipper]", $Klienci, $Values['id_klient_shipper']);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>CONSIGNEE (odbiorca)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[consignee]", $Values['consignee'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                $Form->PoleSelect("Air[id_klient_consignee]", $Klienci, $Values['id_klient_consignee']);
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>AGENT (partner w POL)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[agent]", $Values['agent'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                echo ("<select name=\"Air[id_przewoznik_agent]\">");
                    foreach($Przewoznicy as $PID => $PDane){
                        echo("<option value='$PID'".($Values['id_przewoznik_agent'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                    }
		echo("</select>");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>CARRIER</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[carrier]", $Values['carrier'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        if($Type == "I"){
            echo "<tr>\n";
                echo "<th>FLIGHT NO</th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("Air[flight_no]", $Values['flight_no'], "style='width: 200px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>POL (port załadunku)</th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Air[pol]", $Values['pol'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ETD (spodziewana data wypłynięcia)</th>\n";
            echo "<td>\n";
                $Form->PoleData("Air[etd]", $Values['etd'], "etd", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>POD (port rozładunku) </th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Air[pod]", $Values['pod'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ETA (spodziewana data przypłynięcia) </th>\n";
            echo "<td>\n";
                $Form->PoleData("Air[eta]", $Values['eta'], "eta", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>TERMS (warunki dostawy - incoterms)</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Air[terms_id]", $Terms, $Values['terms_id']);
                echo "<br /><br />";
                $Form->PoleTextarea("Air[terms_text]", $Values['terms_text'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>MAWB </th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Air[mawb]", $Values['mawb'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>HAWB </th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Air[hawb]", $Values['hawb'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>INSURANCE (ubezpiecznie) </th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Air[insurence]", $TakNie, $Values['insurence']);
            echo "</td>\n";
        echo "</tr>\n";
       
        echo "<tr id='FCL-spec'>\n";
            echo "<td colspan='2'>\n";
                include(SCIEZKA_SZABLONOW."forms/zlecenia_lotnicze_fcl_form.php"); 
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>CUSTOMS CLEARANCE (odprawa celna) </th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[customs_clearence]", $Values['customs_clearence'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>DIMENSIONS</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Air[dimensions]", $Values['dimensions'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        if($this->WystawiamyDoIstniejacegoZlecenia()){
            echo "<tr>\n";
                echo "<th>Dołącz zlecenia do tego SO:</th>\n";
                echo "<td>\n";
                    if($MoznaPodpiac){
                        foreach($MoznaPodpiac as $ZIDo => $ZIDnumb){
                            $Form->PoleCheckbox("DoPodpiecia[]", $ZIDo, null, "", "id='do_podpiecia_$ZIDo'");
                            echo $ZIDnumb."<br />";
                        }
                    }else{
                        echo "brak zleceń bez SO";
                    }
                echo "</td>\n";
            echo "</tr>\n";
        }
        if($this->Parametr == "tabela_rozliczen_lotnicze"){
            ?>
<tr>
    <th>KOSZTY </th>
    <td>
        <table border="0" cellpadding="4" cellspacing="0" id="Positions-Table" style="border-collapse: collapse">
            <tr>
                <td style="border-bottom: 2px solid #000; font-weight: bold;">DOSTAWCA</td>
                <td style="border-bottom: 2px solid #000; font-weight: bold; background-color: #F0F0F0;">OPIS</td>
                <td style="border-bottom: 2px solid #000; font-weight: bold;">KOSZT</td>
                <td style="border-bottom: 2px solid #000; font-weight: bold; background-color: #F0F0F0;">WALUTA</td>
                <td style="border-bottom: 2px solid #000; font-weight: bold;">KURS</td>
                <td style="border-bottom: 2px solid #000; font-weight: bold; background-color: #F0F0F0;">&nbsp;</td>
            </tr>
<?php
    $Idx = 0;
    foreach($Values['Koszty'] as $FCL){
?>
    <tr id="<?php echo "position-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;">
            <?php
                    echo ("<select name=\"Koszty[$Idx][id_przewoznik]\" id='koszty_$Idx'>");
                    foreach($Przewoznicy as $PID => $PDane){
                        echo("<option value='$PID'".($FCL['id_przewoznik'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                    }
		echo("</select>");
            ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Koszty[$Idx][opis]", $FCL['opis'], "style='width: 200px;'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Koszty[$Idx][koszt]", $FCL['koszt'], "style='width: 70px;'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleSelect("Koszty[$Idx][waluta]", $Waluty, $FCL['waluta']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Koszty[$Idx][kurs]", $FCL['kurs'], "style='width: 80px;'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleHidden("Koszty[$Idx][id_koszt]", $FCL['id_koszt']); ?><?php $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemovePosition($Idx);'"); ?></td>
    </tr>
<?php
        $Idx++;
    }
?>
</table>
        <div id="loading" style="width: 100%; display: none;"></div>
<?php
    $Form->PoleHidden("Positions", $Idx, "id='Positions'");
    $Form->PoleButton("AddNewKoszt", "Dodaj koszt", "onclick='AddKoszt();' style='margin: 20px;'");
    echo "</td>";
    echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>&nbsp; </th>\n";
            echo "<td>\n";
                $Form->PoleSubmitImage("OK", "Zapisz", "images/ok.gif", "style='border: 0;'");
                echo "&nbsp;&nbsp;";
                echo "<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" border=\"0\"></a>";
//                if($_SESSION['login'] == "artplusadmin"){
//                    echo " <input type='button' value='Sprawdź' onclick='javascript:SessionTime()'> {$_SESSION['czas_odswiezenia']} <span id='session_time'></span>\n";
//                }
            echo "</td>\n";
        echo "</tr>\n";
    $Form->FormEnd();
echo '</table>';
?>