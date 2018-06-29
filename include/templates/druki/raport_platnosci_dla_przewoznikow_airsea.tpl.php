<script type="text/javascript" src="js/kalendarz.js"></script>
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
    function show_calc(){
        x = x - 184;
        y = y - 200;
        javascript:showKal(document.formularzy.nowa_data);
    }
    
    function check_all(){
        var is_checked = $(".check_platnosci:first").attr("checked");
        $('.check_platnosci').each(function(){
            $(this).attr("checked", (is_checked ? false : true));
        });
    }
</script>
<?php
    function ShowNaglowek($person_type = "przewoznik"){
        ?>
        <tr>
                 <td valign="middle" bgcolor="#CECECE" class="report-td"><b>Lp.</b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Numer Zlecenia</b></td>
                 <td width="140" bgcolor="#CECECE" class="report-td"><b>Nr faktury <?php echo ($person_type == "przewoznik" ? "dostawcy" : "klienta"); ?></b></td>
                 <td width="200" bgcolor="#CECECE" class="report-td"><b><?php echo ($person_type == "przewoznik" ? "Dostawca" : "Klient"); ?></b></td>
                 <td width="80" bgcolor="#CECECE" class="report-td"><b>PLN<br /></b></td>
                 <td width="80" bgcolor="#CECECE" class="report-td"><b>USD<br /></b></td>
                 <td width="80" bgcolor="#CECECE" class="report-td"><b>EUR<br /></b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Termin płatności</b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Termin płatności planowany</b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Termin płatności rzeczywisty</b></td>
                 <td width="60" bgcolor="#CECECE" class="report-td"><b>Opóźnienie</b></td>
                 <td width="150" bgcolor="#CECECE" class="report-td"><b>Komentarz</b></td>
                 <td width="150" bgcolor="#CECECE" class="report-td"><b>&nbsp;</b></td>
         </tr>
         <?php
    }

    if(isset($_POST['person-type'])){
        $person_type = $_POST['person-type'];
    }else{
        $person_type = "przewoznik";
    }
    
    $pole_planowana = ($person_type == "przewoznik" ? "planowana_zaplata_przew" : "planowana_zaplata");
    $pole_faktura = ($person_type == "przewoznik" ? "nr_faktury" : "numer");
    $pole_id = ($person_type == "przewoznik" ? "id_przewoznik" : "id_klienta");
    if($person_type == "przewoznik"){
        $Statusy = $Statusy = Usefull::StatusyPlatnosciAirSea();
        $StatusyKolejnosc = array(3,4,5,2,1,0);
    }else{
        $Statusy = Usefull::StatusyPlatnosciKlient();
        $StatusyKolejnosc = array(6,7,9,8,3,4,5,2,1,0);
    }
	
    if(isset($_POST['ZmienTermin']) || isset($_POST['ZapiszAll'])){
        if($_POST['nowa_data'] != ""){
            $jakie_pole = ($_POST['JakiTermin'] == "rzeczywisty" ? "rzeczywista_zaplata" : $pole_planowana);
//			echo var_dump($_POST);
            foreach($_POST['Platnosc'] as $type => $zlecki){
			if($person_type == "przewoznik"){
                $table = ($type == "sea" ? "orderplus_sea_orders_koszty" : "orderplus_air_orders_koszty");
				$poleid = 'id_koszt';
			}else{
				$table = ($type == "sea" ? "orderplus_sea_orders_faktury" : "orderplus_air_orders_faktury");
				$poleid = 'id_faktury';
			}
                foreach($zlecki as $idk){
//					echo "UPDATE $table SET $jakie_pole = '{$_POST['nowa_data']}' WHERE id_zlecenie = '$idk'<br/>";
                    mysql_query("UPDATE $table SET $jakie_pole = '{$_POST['nowa_data']}' WHERE $poleid  = '$idk'");
//					echo mysql_error();
                }
            }
        }
    }
     
    if(isset($_POST['DopiszKomentarz']) || (isset($_POST['ZapiszAll']) && $_POST['nowy_komentarz'] != "")){
        foreach($_POST['Platnosc'] as $type => $zlecki){
			if($person_type == "przewoznik"){
                $table = ($type == "sea" ? "orderplus_sea_orders_koszty" : "orderplus_air_orders_koszty");
				$poleid = 'id_koszt';
			}else{
				$table = ($type == "sea" ? "orderplus_sea_orders_faktury" : "orderplus_air_orders_faktury");
				$poleid = 'id_faktury';
			}
            foreach($zlecki as $idk){
                mysql_query("UPDATE $table SET platnosci_komentarz = CONCAT(platnosci_komentarz,IF(platnosci_komentarz != '','\n',''),'{$_POST['nowy_komentarz']}') WHERE $poleid  = '$idk'");
            }
        }
    }
    
    if(isset($_POST['NadpiszKomentarz']) || (isset($_POST['ZapiszAll']) && $_POST['nowy_status'] > 0)){
        foreach($_POST['Platnosc'] as $type => $zlecki){
			if($person_type == "przewoznik"){
                $table = ($type == "sea" ? "orderplus_sea_orders_koszty" : "orderplus_air_orders_koszty");
				$poleid = 'id_koszt';
			}else{
				$table = ($type == "sea" ? "orderplus_sea_orders_faktury" : "orderplus_air_orders_faktury");
				$poleid = 'id_faktury';
			}
            foreach($zlecki as $idk){
                mysql_query("UPDATE $table SET platnosci_komentarz = '{$_POST['nowy_komentarz']}' WHERE $poleid  = '$idk'");
            }
        }
    }
    
    $Error = false;
    $BrakiFaktur = array();
    $ZleStatusy = array();
    if(isset($_POST['ZmienStatus'])){
        foreach($_POST['Platnosc'] as $type => $zlecki){
			if($person_type == "przewoznik"){
                $table = ($type == "sea" ? "orderplus_sea_orders_koszty" : "orderplus_air_orders_koszty");
				$poleid = 'id_koszt';
			}else{
				$table = ($type == "sea" ? "orderplus_sea_orders_faktury" : "orderplus_air_orders_faktury");
				$poleid = 'id_faktury';
			}
            foreach($zlecki as $idk){
                $DaneZmienianego = $this->Baza->GetData("SELECT * FROM $table WHERE $poleid = '$idk'");
                if($_POST['nowy_status'] == 1 && $DaneZmienianego['nr_faktury'] != ""){
                    $ZleStatusy[] = $idk;
                }else if($_POST['nowy_status'] != 1 && $DaneZmienianego['nr_faktury'] == ""){
                    $BrakiFaktur[] = $idk;
                }else{
                    mysql_query("UPDATE $table SET platnosci_status = '{$_POST['nowy_status']}' WHERE $poleid  = '$idk'");
                }
            }
        }
    }
    if(count($BrakiFaktur) > 0 || count($ZleStatusy) > 0){
        $Error = true;
    }
?>
<form name="formularzy" action="" method="post">
<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>
<table  width="900"  align="center"  border="0" style="" cellspacing="1" cellpadding="2">
<tr>
			<td colspan="3" style="text-align: left; border: 1px solid #888888; border-right: 0;">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td colspan="10" style="font-size: 14pt; text-align: center; width: 300px;border: 1px solid #888888; border-left: 0;">
				<b><br />Raport płatności dla <?php echo ($person_type == "przewoznik" ? "przewoźników" : "klientów"); ?></b><br /><br /> 

<?php
echo "<div style=\"font-size: 11px\">";
        if(isset($_POST['start'])){
          $start = $_POST['start'];
        }else{
          $start = date("Y-m-d");
        }
        if(isset($_POST['stop'])){
            $stop = $_POST['stop'];
        }else{
            $stop = date("Y-m-d");
        }
        if(isset($_POST['raport-type'])){
            $raport_type = $_POST['raport-type'];
        }else{
            $raport_type = "planowana";
        }
        
        $lp = 1;
        $kolor = 'white';
        $totalna_suma_marzy = 0;
        $totalna_suma_klienta = 0;
        $totalna_suma_przewoznika = 0;
        $totalna_suma_zlecen = 0;

        if($raport_type == "termin-platnosci"){
            $filtr_datowy = "(termin_platnosci >= '$start' AND termin_platnosci <= '$stop') AND rzeczywista_zaplata = '0000-00-00'";
            $pole_sort = "termin_platnosci";
        }else if($raport_type == "rzeczywista"){
            $filtr_datowy = "(rzeczywista_zaplata >= '$start' AND rzeczywista_zaplata <= '$stop')";
            $pole_sort = "rzeczywista_zaplata";
        }else{
            $filtr_datowy = "($pole_planowana >= '$start' AND $pole_planowana <= '$stop') AND rzeczywista_zaplata = '0000-00-00'";
            $pole_sort = $pole_planowana;
        }

        if(isset($_POST['status']) && $_POST['status'] > -1){
            $filtr_datowy .= " AND platnosci_status = '{$_POST['status']}'";
        }

        if($this->Uzytkownik->IsAdmin() == false){
                $warunek_morski .= "so.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
                $warunek_lotniczy .= "ao.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
        }

        if($person_type == "przewoznik" && isset($_POST['id_przewoznik']) && $_POST['id_przewoznik'] > -1){
            $warunek_morski .= "sok.id_przewoznik = '{$_POST['id_przewoznik']}' AND ";
            $warunek_lotniczy .= "aok.id_przewoznik = '{$_POST['id_przewoznik']}' AND ";
        }

        if($person_type == "klient" && isset($_POST['id_klient']) && $_POST['id_klient'] > -1){
            $warunek_morski .= "sok.id_klienta = '{$_POST['id_klient']}' AND ";
            $warunek_lotniczy .= "aok.id_klienta = '{$_POST['id_klient']}' AND ";
        }

        $DaneDoRaportu = array();
        $OddzialyZlecenia = array('sea', 'air');
        $Zlecenia = array();
        $PrzewoznicyIDs = array();
        $KlienciIDs = array();        
        ### Pobieranie zleceń SEA ###
        if(!isset($_POST['oddzial']) || $_POST['oddzial'] == -1 || $_POST['oddzial'] == "sea"){
            if($person_type == "przewoznik"){
                $this->Baza->Query("SELECT sok.*, so.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_sea_orders_koszty sok
                                    LEFT JOIN orderplus_sea_orders so ON(so.id_zlecenie = sok.id_zlecenie)
                                    LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = sok.id_przewoznik)
                                    WHERE $warunek_morski ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
            }else{
                $this->Baza->Query("SELECT sok.*, so.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_sea_orders_faktury sok
                                    LEFT JOIN orderplus_sea_orders so ON(so.id_zlecenie = sok.id_zlecenia)
                                    LEFT JOIN orderplus_klient p ON(p.id_klient = sok.id_klienta)
                                    WHERE $warunek_morski ((so.ost_korekta = 1) OR (so.ost_korekta = 0 AND so.korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
            }
            while($zleconko = $this->Baza->GetRow()){
                $Zlecenia['sea'][$zleconko['platnosci_status']][] = $zleconko;
                 if($person_type == "przewoznik"){
                     $PrzewoznicyIDs[] = $zleconko['id_przewoznik'];
                 }else{
                     $KlienciIDs[] = $zleconko['id_klienta'];
                 }
            }
        }

        if(!isset($_POST['oddzial']) || $_POST['oddzial'] == -1 || $_POST['oddzial'] == "air"){
            if($person_type == "przewoznik"){
                $this->Baza->Query("SELECT aok.*, ao.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_air_orders_koszty  aok
                                    LEFT JOIN orderplus_air_orders ao ON(ao.id_zlecenie = aok.id_zlecenie)
                                    LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = aok.id_przewoznik)
                                    WHERE $warunek_lotniczy ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
            }else{
                $this->Baza->Query("SELECT aok.*, ao.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_air_orders_faktury  aok
                                    LEFT JOIN orderplus_air_orders ao ON(ao.id_zlecenie = aok.id_zlecenia)
                                    LEFT JOIN orderplus_klient p ON(p.id_klient = aok.id_klienta)
                                    WHERE $warunek_lotniczy ((ao.ost_korekta = 1) OR (ao.ost_korekta = 0 AND ao.korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
            }
            while($zleconko = $this->Baza->GetRow()){
                $Zlecenia['air'][$zleconko['platnosci_status']][] = $zleconko;
                if($person_type == "przewoznik"){
                     $PrzewoznicyIDs[] = $zleconko['id_przewoznik'];
                 }else{
                     $KlienciIDs[] = $zleconko['id_klienta'];
                 }
            }
        }        
        ?>
        od: <input type='text' style='width: 80px; font-size: 11px;' name='start' value="<?php echo $start; ?>" /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='showKal(document.formularzy.start);' src='images/kalendarz.png'>&nbsp;&nbsp;
        do: <input type='text' style='width: 80px; font-size: 11px;' name='stop' value="<?php echo $stop; ?>" /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='showKal(document.formularzy.stop);' src='images/kalendarz.png'>&nbsp;&nbsp;
        <?php
        echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"button\" value=\"Wybierz\" onclick='this.form.action=\"\"; this.form.submit();' />";
        echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"button\" value=\"Exportuj do XLS\" onclick='this.form.action=\"raporty_platnosci_dla_przewoznikow_airsea_xls.php\"; this.form.submit();' />";
        echo "<br /><br />";
        FormularzSimple::PoleSelect("oddzial", Usefull::PolaczDwieTablice(array("-1" => "-- wszystkie oddzialy --"), $Oddzialy), (isset($_POST['oddzial']) ? $_POST['oddzial'] : -1), "style=\"font-size: 11px;\"");
        echo "&nbsp;&nbsp;";
        FormularzSimple::PoleSelect("status", Usefull::PolaczDwieTablice(array("-1" => "-- wszystkie statusy --"), $Statusy), (isset($_POST['status']) ? $_POST['status'] : -1), "style=\"font-size: 11px;\"");
        echo "&nbsp;&nbsp;";
        FormularzSimple::PoleSelect("raport-type", $Rodzaje, $raport_type, "style=\"font-size: 11px;\"");
        echo "<br /><br />";
        FormularzSimple::PoleSelect("person-type", array('przewoznik' => "Raport płatności dla przewoźników", "klient" => "Raport płatności klientów"), $person_type, "style=\"font-size: 11px;\"");        
        echo "<br /><br />Filtruj: ";
        if($person_type == "przewoznik"){
            if(count($PrzewoznicyIDs)){
                $PrzewoznicyToSelect = $this->Baza->GetOptions("SELECT id_przewoznik, nazwa FROM orderplus_przewoznik WHERE id_przewoznik IN(".implode(",",  array_unique($PrzewoznicyIDs)).") ORDER BY nazwa ");
            }else{
                $PrzewoznicyToSelect = array();
            }
            FormularzSimple::PoleSelect("id_przewoznik", Usefull::PolaczDwieTablice(array("-1" => "-- wszyscy przewoźnicy --"), $PrzewoznicyToSelect), (isset($_POST['id_przewoznik']) ? $_POST['id_przewoznik'] : -1), "style=\"font-size: 11px;\"");
        }else{
            if(count($KlienciIDs)){
                $KlienciToSelect = $this->Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient WHERE id_klient IN(".implode(",",  array_unique($KlienciIDs)).") ORDER BY nazwa ");
            }else{
                $KlienciToSelect = array();
            }
            FormularzSimple::PoleSelect("id_klient", Usefull::PolaczDwieTablice(array("-1" => "-- wszyscy klienci --"), $KlienciToSelect), (isset($_POST['id_klient']) ? $_POST['id_klient'] : -1), "style=\"font-size: 11px;\"");
        }
echo "</div>";
?>

				</td>
				</tr>
<?php

if($Error){
    ?>
<tr>
    <td colspan="13" style="font-size: 14pt; text-align: center; border: 1px solid #888888; border-top: 0; border-bottom: 0;">
        <?php 
            $TrescError = "Nie wszystkie zmiany zostały zapisane. Powód:<br />";
            if(count($BrakiFaktur) > 0){
                $TrescError .= "<b>Nie można zmienić statusu na inny niż estymowane gdy nie ma wpisanej faktury</b>";
            }
            if(count($ZleStatusy) > 0){
                $TrescError .= "<b>Nie można zmienić statusu na esymowane gdy jest wpisany numer faktury</b>";
            }
            echo Usefull::ShowKomunikatError($TrescError);
         ?>
    </td>
</tr>
    <?php
}

$lp = 1;
$Sumy = array();
foreach($Oddzialy as $oddzial_id => $oddzial_nazwa){
      if(!in_array($oddzial_id, $OddzialyZlecenia)){
          continue;
      }
      $SumyOddzial = array();
      echo "<tr><td colspan='13' bgcolor='#7E7E7E' style='font-size: 17px; font-weight: bold; color: #FFF'>$oddzial_nazwa</td></tr>";
      $pierwszy_status = true;
      foreach($StatusyKolejnosc as $status_id){
          $SumaStatus = array();
          if(count($Zlecenia[$oddzial_id][$status_id]) == 0){
              continue;
          }
            if($pierwszy_status == false){
                echo "<tr><td colspan='13' style='height: 20px; border-left: 0; border-right: 0;'>&nbsp;</td></tr>";
            }
            $pierwszy_status = false;
          echo "<tr><td colspan='13' bgcolor='#AEAEAE' style='font-size: 15px; font-weight: bold;'>{$Statusy[$status_id]}</td></tr>";
          ShowNaglowek($person_type);
          $kolor = "#FFFFFF";
          $LastClient = 0;          
          foreach($Zlecenia[$oddzial_id][$status_id] as $zleconko){
              if($zleconko[$pole_id] != $LastClient){
                  $kolor = ($kolor == "#FFFFFF" ? "#E6F0FF" : "#FFFFFF");
              }
              $LastClient = $zleconko[$pole_id];
              echo "<tr><td bgcolor=\"$kolor\" class='report-td'>". $lp ."</td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko['numer_zlecenia']}</nobr></td>"; 
              echo "<td bgcolor=\"$kolor\" class='report-td'>{$zleconko[$pole_faktura]}</td>"; 
              echo "<td bgcolor=\"$kolor\" class='report-td'>".($person_type == "przewoznik" ? $Przewoznicy[$zleconko['id_przewoznik']] : $Klienci[$zleconko['id_klienta']])."</td>";
                if($person_type == "przewoznik"){
                    $StawkaVatPrzewoznik_1 = (in_array(strtolower($zleconko['stawka_vat']), array("np","zw")) ? 0 :  $zleconko['stawka_vat']);
                    $StawkaVatPrzewoznik_2 = (in_array(strtolower($zleconko['stawka_vat_2']), array("np","zw")) ? 0 :  $zleconko['stawka_vat_2']);
                    $StawkaPrzewoznik_1 = $zleconko['koszt_kwota_1'] + ($zleconko['koszt_kwota_1'] * $StawkaVatPrzewoznik_1/100);
                    $StawkaPrzewoznik_2 = $zleconko['koszt_kwota_2'] + ($zleconko['koszt_kwota_2'] * $StawkaVatPrzewoznik_2/100);
                    $StawkaPrzewoznik = $StawkaPrzewoznik_1 + $StawkaPrzewoznik_2;
                    $Sumy[$zleconko['waluta']] += $StawkaPrzewoznik;
                    $SumyOddzial[$zleconko['waluta']] += $StawkaPrzewoznik;
                    $SumaStatus[$zleconko['waluta']] += $StawkaPrzewoznik;
                }else{
                    $table_pozycje = ($oddzial_id == "sea" ? "orderplus_sea_orders_faktury_pozycje" : "orderplus_air_orders_faktury_pozycje");
                    $StawkaPrzewoznik = $this->Baza->GetValue("SELECT SUM(brutto) FROM $table_pozycje WHERE id_faktury = '{$zleconko['id_faktury']}'");
                    #echo "SELECT SUM(brutto) FROM $table_pozycje WHERE id_faktury = '{$zleconko['id_faktury']}'";
                    if($StawkaPrzewoznik == false){
                        $StawkaPrzewoznik = 0;
                    }
                    $Sumy[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                    $SumyOddzial[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                    $SumaStatus[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                }
                echo "<td bgcolor=\"$kolor\" class='report-td'>". ($zleconko['waluta'] == 1 || $zleconko['id_waluty'] == 1 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : "")  ."</td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'>". ($zleconko['waluta'] == 2 || $zleconko['id_waluty'] == 2 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : "")  ."</td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'>". ($zleconko['waluta'] == 3 || $zleconko['id_waluty'] == 3 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : "")  ."</td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko['termin_platnosci']}</nobr></td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko[$pole_planowana]}</nobr></td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko['rzeczywista_zaplata']}</nobr></td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>";
                if($zleconko['rzeczywista_zaplata'] == "0000-00-00"){ 
                      $kolor_opoznienia = "#000000";
                      if($zleconko['opoznienie'] > 0){
                          $kolor_opoznienia = "#FF0000";
                      }
                      if($zleconko['opoznienie'] <= 0 && $zleconko['opoznienie'] >= -5){
                          $kolor_opoznienia = "#009900";
                      }
                      echo "<span style='color: $kolor_opoznienia;'>".($zleconko['opoznienie'] * -1)."</span>";
                }else{
                    echo "&nbsp;";
                }
                echo "</nobr></td>";
                echo "<td bgcolor=\"$kolor\" class='report-td'>".nl2br($zleconko['platnosci_komentarz'])."</td>";
                echo "<td bgcolor=\"".(in_array($zleconko['id_koszt'], $BrakiFaktur) || in_array($zleconko['id_koszt'], $ZleStatusy) ? "#FFC0C0" : $kolor)."\" class='report-td'><input type='checkbox' name='Platnosc[$oddzial_id][]' value='".($person_type == "przewoznik" ? $zleconko['id_koszt'] : $zleconko['id_faktury'])."' class='check_platnosci' /></td>";
              $lp++;
              echo "</tr>";
          }
            echo "<tr><td valign=\"middle\" bgcolor='#AEAEAE' style='font-size: 15px; font-weight: bold;' align=\"right\" colspan='4'><b>SUMA - $oddzial_nazwa - {$Statusy[$status_id]}</b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"center\" style='line-height: 15px;'><b><nobr>".number_format($SumaStatus[1], 2, ",", " ")." PLN</nobr></b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"center\" style='line-height: 15px;'><b><nobr>".number_format($SumaStatus[2], 2, ",", " ")." USD</nobr></b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"center\" style='line-height: 15px;'><b><nobr>".number_format($SumaStatus[3], 2, ",", " ")." EUR</nobr></b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"right\">&nbsp;</td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"left\" colspan='5'>&nbsp;</td>";
            echo "</tr>";

      }
        echo "<tr><td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF; font-size: 17px; font-weight: bold;' align=\"right\" colspan='4'><b>SUMA - $oddzial_nazwa</b></td>";
            echo "<td valign=\"middle\" bgcolor='#7E7E7E' align=\"center\" style='line-height: 17px;'><b><nobr>".number_format($SumyOddzial[1], 2, ",", " ")." PLN</nobr></b></td>";
            echo "<td valign=\"middle\" bgcolor='#7E7E7E' align=\"center\" style='line-height: 17px;'><b><nobr>".number_format($SumyOddzial[2], 2, ",", " ")." USD</nobr></b></td>";
            echo "<td valign=\"middle\" bgcolor='#7E7E7E' align=\"center\" style='line-height: 17px;'><b><nobr>".number_format($SumyOddzial[3], 2, ",", " ")." EUR</nobr></b></td>";
            echo "<td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF' align=\"right\">&nbsp;</td>";
            echo "<td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF' align=\"left\" colspan='5'>&nbsp;</td>";
        echo "</tr>";
        echo "<tr><td colspan='13' style='height: 40px; border-left: 0; border-right: 0;'>&nbsp;</td></tr>";

   }
$kolor = '#cccccc';
echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" colspan='4'><b>SUMA WSZYSTKICH</b></td>"; 
echo "<td valign=\"middle\" bgcolor='$kolor' align=\"center\"><b><nobr>".number_format($Sumy[1], 2, ",", " ")." PLN</nobr></b></td>";
echo "<td valign=\"middle\" bgcolor='$kolor' align=\"center\"><b><nobr>".number_format($Sumy[2], 2, ",", " ")." USD</nobr></b></td>";
echo "<td valign=\"middle\" bgcolor='$kolor' align=\"center\"><b><nobr>".number_format($Sumy[3], 2, ",", " ")." EUR</nobr></b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"left\" colspan='4'>";
?>
    <right><a href="javascript:check_all()" style="color: #000; text-decoration: none; float: right;">zaznacz/odznacz wszystkie</a></right><br /><br />
    <b>Dla zaznaczonych:</b><br />
        <select name="JakiTermin" >
            <option value="planowany">Termin planowany</option>
            <option value="rzeczywisty">Termin rzeczywisty</option>
        </select>
        <input name="ZmienTermin" style="font-size: 11px;" type="submit" value="Zmień termin" onclick='this.form.action=""; this.form.submit();' /><br /><br />        Nowa data: <input type='text' style='width: 80px;' name='nowa_data' /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='show_calc();' src='images/kalendarz.png'><br /><br />
        <input name='DopiszKomentarz' style="font-size: 11px;" type="submit" value="Dopisz komentarz" onclick='this.form.action=""; this.form.submit();' />&nbsp;&nbsp;<input name='NadpiszKomentarz' style="font-size: 11px;" type="submit" value="Nadpisz komentarz" onclick='this.form.action=""; this.form.submit();' /><br /><br />
        Nowy komentarz: <textarea name='nowy_komentarz' style="vertical-align: top;"></textarea><br /><br />
        <input name='ZmienStatus' style="font-size: 11px;" type="submit" value="Zmień status" onclick='this.form.action=""; this.form.submit();' /><br /><br />
        Nowy status:
        <select name='nowy_status' />
            <?php
                foreach($Statusy as $status_id => $status_nazwa){
                    ?><option value='<?php echo $status_id; ?>'><?php echo $status_nazwa; ?></option><?php
                }
            ?>
        </select>
        <br /><br />
        <input name='ZapiszAll' style="font-size: 11px;" type="submit" value="Zapisz wszystkie zmiany" onclick='this.form.action=""; this.form.submit();' /><br /><br />
        </td>
    </tr>
</table>
</td>
</tr>
</table>
    </form>