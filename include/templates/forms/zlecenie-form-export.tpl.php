<?php
    include(SCIEZKA_SZABLONOW."forms/sea-dane.php");
    $Wstecz = intval(date("Y")) - 2011;
    $Szablony = $this->Baza->GetOptions("SELECT id_szablon, CONCAT(tytul,' ',jezyk) as name FROM orderplus_szablon WHERE status = 1 ORDER BY lp ASC");
    $Form = new FormularzSimple();
    $Form->FormStart("sea_order", "", "post"); 
    echo '<table class="formularz">';
         echo "<tr>\n";
            echo "<th>TO (do)</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("SeaZlec[zlecenie_to]", $Values['zlecenie_to'], "style='width: 400px; height: 40px;'");
                echo "<br /><br />";
                echo ("<select name=\"SeaZlec[id_przewoznik_to]\">");
                    foreach($Przewoznicy as $PID => $PDane){
                        echo("<option value='$PID'".($Values['id_przewoznik_to'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                    }
		echo("</select>");
            echo "</td>\n";
        echo "</tr>\n";
        if($SOI['mode'] == "FCL"){
            echo "<tr id='FCL-spec'>\n";
                echo "<td colspan='2'>\n";
                    include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_zlec_fcl_form.php");
                echo "</td>\n";
            echo "</tr>\n";
        }else{
            echo "<tr id='LCL-spec'>\n";
                echo "<td colspan='2'>\n";
                    include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_zlec_lcl_form.php");
                echo "</td>\n";
            echo "</tr>\n";
        }
        if($SOI['sea_order_type'] == "I"){
            echo "<tr>\n";
                echo "<th>DATA PODJECIA PELNEGO KONTENERA </th>\n";
                echo "<td>\n";
                    $Form->PoleData("SeaZlec[data_podjecia]", $Values['data_podjecia'], "data_podjecia");
                echo "</td>\n";
            echo "</tr>\n";
        }
         echo "<tr>\n";
            echo "<th>".($SOI['sea_order_type'] != "I" ? "DEPOT/" : "")."MIEJSCE PODJECIA </th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("SeaZlec[miejsce_podjecia]", $Values['miejsce_podjecia'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ARMATOR".($SOI['sea_order_type'] == "I" ? "/ZWOLNIENIE" : "")."</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("SeaZlec[ocean_carrier_id]", $Carriers, $Values['ocean_carrier_id'], "onchange='OpenText(this, 37, \"ocean_carrier\");'");
                echo "<br /><br />";
                $Form->PoleTextarea("SeaZlec[ocean_carrier_text]", $Values['ocean_carrier_text'], "style='width: 400px; height: 40px;".($Values['ocean_carrier_id'] != 37 ? " display: none;" : "")."' id='ocean_carrier'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ODPRAWA CELNA </th>\n";
            echo "<td>\n";
                 $Form->PoleTextarea("SeaZlec[customs_clearence]", $Values['customs_clearence'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";

            echo "<tr>\n";
                echo "<th>MIEJSCE ZAŁADUNKU</th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("SeaZlec[shipper]", $Values['shipper'], "style='width: 400px; height: 40px;'");
                    echo "<br /><br />";
                    $Form->PoleSelect("SeaZlec[id_klient_shipper]", $Klienci, $Values['id_klient_shipper']);
                echo "</td>\n";
            echo "</tr>\n";

            echo "<tr>\n";
                echo "<th>DATA ZAŁADUNKU </th>\n";
                echo "<td>\n";
                    $Form->PoleData("SeaZlec[data_zaladunku]", $Values['data_zaladunku'], "data_zaladunku");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>TERMINAL/DOSTAWA PEŁNY </th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("SeaZlec[terminal]", $Values['terminal'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>NR BOOKINGU </th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("SeaZlec[nr_booking]", $Values['nr_booking'], "style='width: 200px;'");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>VESSEL</th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("SeaZlec[vessel]", $Values['vessel'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>FEEDER</th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("SeaZlec[feeder]", $Values['feeder'], "style='width: 400px; height: 40px;'");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>CUT OFF </th>\n";
                echo "<td>\n";
                    $Form->PoleData("SeaZlec[cut_off]", $Values['cut_off'], "cut_off");
                    echo "&nbsp;&nbsp;&nbsp;godz.";
                    $Form->PoleInputText("SeaZlec[cut_off_hour]", $Values['cut_off_hour'], "style='width: 70px;'");
                echo "</td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<th>POD</th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("SeaZlec[pod]", $Values['pod'], "style='width: 200px;'");
                echo "</td>\n";
            echo "</tr>\n";

        echo "<tr>\n";
            echo "<th>INFORMACJE DODATKOWE</th>\n";
            echo "<td>\n";
                $Form->PoleTextarea("SeaZlec[informacje_dodatkowe]", $Values['informacje_dodatkowe'], "style='width: 400px; height: 60px;'");
            echo "</td>\n";
        echo "</tr>\n";
        if($SOI['sea_order_type'] == "I"){
             echo "<tr>\n";
                echo "<th>INSTRUKCJE</th>\n";
                echo "<td>\n";
                    $Form->PoleTextarea("SeaZlec[instrukcje]", $Values['instrukcje'], "style='width: 400px; height: 150px;'");
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>SZABLON WARUNKÓW</th>\n";
            echo "<td>\n";
                 $Form->PoleSelect("SeaZlec[id_szablon]", $Szablony, $Values['id_szablon']);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>&nbsp; </th>\n";
            echo "<td>\n";
                $Form->PoleSubmitImage("OK", "Zapisz", "images/ok.gif", "style='border: 0;'");
                echo "&nbsp;&nbsp;";
                echo "<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" border=\"0\"></a>";
            echo "</td>\n";
        echo "</tr>\n";
        echo '</table>';
    $Form->FormEnd();
?>