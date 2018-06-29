<?php
    include(SCIEZKA_SZABLONOW."forms/sea-dane.php");
    $Mode = array("FCL" => 'FCL', "LCL" => "LCL");
    $Waluty = UsefullBase::GetWaluty($this->Baza);
    $Wstecz = intval(date("Y")) - 2011;
    $Form = new FormularzSimple();
    $Form->FormStart("sea_order", "", "post"); 
    echo '<table class="formularz">';
        echo "<tr>\n";
            echo "<th>SHIPPER (załadowca)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[shipper]", $Values['shipper'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                $Form->PoleSelect("Sea[id_klient_shipper]", $Klienci, $Values['id_klient_shipper']);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>CONSIGNEE (odbiorca)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[consignee]", $Values['consignee'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                $Form->PoleSelect("Sea[id_klient_consignee]", $Klienci, $Values['id_klient_consignee']);
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>AGENT (partner w POL)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[agent]", $Values['agent'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                echo ("<select name=\"Sea[id_przewoznik_agent]\">");
                    foreach($Przewoznicy as $PID => $PDane){
                        echo("<option value='$PID'".($Values['id_przewoznik_agent'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                    }
		echo("</select>");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>VESSEL (statek)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[vessel]", $Values['vessel'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        if($Type == "I"){
            echo "<tr>\n";
                echo "<th>VOYAGE NO</th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("Sea[voyage_no]", $Values['voyage_no'], "style='width: 200px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }else{
            echo "<tr>\n";
                echo "<th>FEEDER (statek feederowy)</th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("Sea[feeder]", $Values['feeder'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>POL (port załadunku)</th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Sea[pol]", $Values['pol'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ETD (spodziewana data wypłynięcia)</th>\n";
            echo "<td>\n";
                $Form->PoleData("Sea[etd]", $Values['etd'], "etd", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>RTD (data wypłynięcia)</th>\n";
            echo "<td>\n";
                $Form->PoleData("Sea[rtd]", $Values['rtd'], "rtd", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>POD (port rozładunku) </th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Sea[pod]", $Values['pod'], "style='width: 200px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ETA (spodziewana data przypłynięcia) </th>\n";
            echo "<td>\n";
                $Form->PoleData("Sea[eta]", $Values['eta'], "eta", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>RTA (data przypłynięcia) </th>\n";
            echo "<td>\n";
                $Form->PoleData("Sea[rta]", $Values['rta'], "rta", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>TERMS (warunki dostawy - incoterms)</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Sea[terms_id]", $Terms, $Values['terms_id']);
                echo "<br /><br />";
                $Form->PoleTextarea("Sea[terms_text]", $Values['terms_text'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>B/L NO (nr listu przewozowego) </th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Sea[bl_no]", $Values['bl_no'], "style='width: 200px;'");
//                if($Type == "E"){
//                    echo ($Values['bl_no'] == "" ? "Nie wystawiono jeszcze listu przewozowego" : $Values['bl_no']);
//                }else{
//                    $Form->PoleInputText("Sea[bl_no]", $Values['bl_no'], "style='width: 200px;'");
//                }
            echo "</td>\n";
        echo "</tr>\n";
        if($Type == "E"){
             echo "<tr>\n";
                echo "<th>BOOKING NO  </th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("Sea[booking_no]", $Values['booking_no'], "style='width: 200px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>INSURANCE (ubezpiecznie) </th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Sea[insurence]", $TakNie, $Values['insurence']);
            echo "</td>\n";
        echo "</tr>\n";
        if($Type == "E"){
            echo "<tr>\n";
                echo "<th>DEPOT </th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("Sea[depot]", $Values['depot'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>MODE of TRANSPORT </th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Sea[mode]", $Mode, $Values['mode'], "onchange='ChangeSpecification(this);'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr id='FCL-spec'".($Values['mode'] == "LCL" ? " style='display: none;'" : "").">\n";
            echo "<td colspan='2'>\n";
                include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_fcl_form.php");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr id='LCL-spec'".($Values['mode'] == "FCL" ? " style='display: none;'" : "").">\n";
            echo "<td colspan='2'>\n";
                include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_lcl_form.php");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>CUSTOMS CLEARANCE (odprawa celna) </th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[customs_clearence]", $Values['customs_clearence'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>OCEAN CARRIER (armator oceaniczny)</th>\n";
            echo "<td>\n";
               $Form->PoleSelect("Sea[ocean_carrier_id]", $Carriers, $Values['ocean_carrier_id'], "onchange='OpenText(this, 37, \"ocean_carrier\");'");
               echo "<br /><br />";
               $Form->PoleTextarea("Sea[ocean_carrier_text]", $Values['ocean_carrier_text'], "style='width: 400px; height: 40px;".($Values['ocean_carrier_id'] != 37 ? " display: none;" : "")."' id='ocean_carrier'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>INLAND CARRIER (przewoznik) </th>\n";
            echo "<td>\n";
               $PrzewoznicyNew = Usefull::PolaczDwieTablice(array(0 => array('nazwa' => ' -- Wybierz -- '), -1 => array('nazwa' => 'inny')), $PrzewoznicyArr);
               echo ("<select name=\"Sea[inland_carrier_id]\" onchange='OpenText(this, -1, \"inland_carrier\");'>");
                    foreach($PrzewoznicyNew as $PID => $PDane){
                        echo("<option value='$PID'".($Values['inland_carrier_id'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                    }
		echo("</select>");
               echo "<br /><br />";
               echo "<div id='inland_carrier'".($Values['inland_carrier_id'] != -1 ? " style='display: none;'" : "").">\n";
                    echo "Nazwa:<br />";
                    $Form->PoleInputText("NewPrzewoznik[nazwa]", $_POST['NewPrzewoznik']['nazwa']);
                    echo "<br />Identyfikator:<br />";
                    $Form->PoleInputText("NewPrzewoznik[identyfikator]", $_POST['NewPrzewoznik']['identyfikator']);
                    echo "<br />Dane firmy:<br />";
                    $Form->PoleTextarea("NewPrzewoznik[dane_firmy]", $_POST['NewPrzewoznik']['dane_firmy'], "style='width: 400px; height: 60px;'");
                    echo "<br />NIP:<br />";
                    $Form->PoleInputText("NewPrzewoznik[nip]", $_POST['NewPrzewoznik']['nip']);
               echo "</div>\n";
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>TERMINAL </th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("Sea[terminal]", $Values['terminal'], "style='width: 400px; height: 40px;'");
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
        if($Type == "I"){
            echo "<tr>\n";
                echo "<th>DEPOT </th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("Sea[depot]", $Values['depot'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>PODJĘCIE </th>\n";
            echo "<td>\n";
                $Form->PoleData("Sea[podjecie]", $Values['podjecie'], "podjecie", "sea_order", 0);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>NABYWCA </th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Sea[nabywca_id]", $Klienci, $Values['nabywca_id']);
            echo "</td>\n";
        echo "</tr>\n";
        if($this->Parametr == "tabela_rozliczen_morskie"){
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