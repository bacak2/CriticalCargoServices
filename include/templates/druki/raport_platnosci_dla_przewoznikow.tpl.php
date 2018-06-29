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
                 <td bgcolor="#CECECE" class="report-td"><b>Lp.</b></td>
                 <?php
                    if($person_type == "przewoznik"){
                 ?>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Numer Zlecenia</b></td>
                 <?php
                    }
                 ?>
                 <td width="200" bgcolor="#CECECE" class="report-td"><b>Klient</b></td>
                 <?php
                    if($person_type == "przewoznik"){
                 ?>
                 <td width="200" bgcolor="#CECECE" class="report-td"><b>Przewoźnik</b></td>
                 <?php
                    }
                 ?>
                 <td width="140" bgcolor="#CECECE" class="report-td"><b>Nr faktury <?php echo ($person_type == "przewoznik" ? "przewoźnika" : "klienta"); ?></b></td>
                 <td width="130" bgcolor="#CECECE" class="report-td"><b>Kwota brutto<br />dla <?php echo ($person_type == "przewoznik" ? "przewoźnika" : "klienta"); ?> (waluta)</b></td>
                 <td width="130" bgcolor="#CECECE" class="report-td"><b>Kwota brutto<br />dla <?php echo ($person_type == "przewoznik" ? "przewoźnika" : "klienta"); ?> (PLN)</b></td>
                 <?php
                    if($person_type == "klient"){
                 ?>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Pozostało EUR</b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Pozostało PLN</b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Data wystawienia<br />faktury</b></td>
                 <?php
                    }
                 ?>                 
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Termin płatności<br /><?php echo ($person_type == "przewoznik" ? "przewoźnik" : "klient"); ?></b></td>                 
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Planowana zapłata<br />dla <?php echo ($person_type == "przewoznik" ? "przewoźnika" : "klienta"); ?></b></td>
                 <td width="100" bgcolor="#CECECE" class="report-td"><b>Rzeczywista zapłata<br />dla <?php echo ($person_type == "przewoznik" ? "przewoźnika" : "klienta"); ?></b></td>
                 <td width="70" bgcolor="#CECECE" class="report-td"><b>Opóźnienie</b></td>
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
    
    $pole_termin = ($person_type == "przewoznik" ? "termin_przewoznika" : "termin_wlasny");
    $pole_planowana = ($person_type == "przewoznik" ? "planowana_zaplata_przew" : "planowana_zaplata_klient");
    $pole_rzeczywista = ($person_type == "przewoznik" ? "rzecz_zaplata_przew" : "rzecz_zaplata_klienta");
    $pole_komentarz = ($person_type == "przewoznik" ? "platnosci_komentarz" : "platnosci_komentarz_klient");
    $pole_status = ($person_type == "przewoznik" ? "platnosci_status" : "platnosci_status_klient");
    $pole_opoznienie = ($person_type == "przewoznik" ? "opoznienie_przewoznik" : "opoznienie_klient");
    $pole_faktura = ($person_type == "przewoznik" ? "faktura_przewoznika" : "faktura_wlasna");
    $pole_stawka = ($person_type == "przewoznik" ? "stawka_przewoznik" : "suma_pozycji");
    $pole_vat = ($person_type == "przewoznik" ? "stawka_vat_przewoznik" : "stawka_vat_klient");
    $pole_kurs = ($person_type == "przewoznik" ? "kurs_przewoznik" : "kurs");
    $pole_pozostalo = ($person_type == "przewoznik" ? "pozostalo_przewoznik" : "wplacono");
    $pole_waluta = ($person_type == "przewoznik" ? "waluta_faktura_przewoznik" : "waluta_klient");
    $pole_id = ($person_type == "przewoznik" ? "id_przewoznik" : "id_klient");
    if($person_type == "przewoznik"){
        $Statusy = Usefull::StatusyPlatnosci();
        $StatusyKolejnosc = array(3,4,5,2,1,0);
    }else{
        $Statusy = Usefull::StatusyPlatnosciKlient();
        $StatusyKolejnosc = array(6,7,9,8,3,4,5,2,1,0);
    }
    if(isset($_POST['ZmienTermin']) || isset($_POST['ZapiszAll'])){
        if($_POST['nowa_data'] != ""){
            $jakie_pole = ($_POST['JakiTermin'] == "rzeczywisty" ? $pole_rzeczywista : $pole_planowana);
            foreach($_POST['Platnosc'] as $idk){
                $where = ($person_type == "przewoznik" ? "id_zlecenie = '$idk'" : "id_faktury = '$idk'");
                mysql_query("UPDATE orderplus_zlecenie SET $jakie_pole = '{$_POST['nowa_data']}' WHERE $where");
            }
        }
    }

    if(isset($_POST['DopiszKomentarz']) || (isset($_POST['ZapiszAll']) && $_POST['nowy_komentarz'] != "")){
        foreach($_POST['Platnosc'] as $idk){
            $where = ($person_type == "przewoznik" ? "id_zlecenie = '$idk'" : "id_faktury = '$idk'");
            mysql_query("UPDATE orderplus_zlecenie SET $pole_komentarz = CONCAT($pole_komentarz,IF($pole_komentarz != '','\n',''),'{$_POST['nowy_komentarz']}') WHERE $where");
        }
    }
    
    if(isset($_POST['NadpiszKomentarz'])){
        foreach($_POST['Platnosc'] as $idk){
            $where = ($person_type == "przewoznik" ? "id_zlecenie = '$idk'" : "id_faktury = '$idk'");
            mysql_query("UPDATE orderplus_zlecenie SET $pole_komentarz = '{$_POST['nowy_komentarz']}' WHERE $where");
        }
    }

    if(isset($_POST['ZmienStatus']) || (isset($_POST['ZapiszAll']) && $_POST['nowy_status'] > 0)){
        foreach($_POST['Platnosc'] as $idk){
            $where = ($person_type == "przewoznik" ? "id_zlecenie = '$idk'" : "id_faktury = '$idk'");
            mysql_query("UPDATE orderplus_zlecenie SET $pole_status = '{$_POST['nowy_status']}' WHERE $where");
        }
    }
    
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
        $filtr_datowy = "$pole_termin >= '$start' AND $pole_termin <= '$stop' AND $pole_rzeczywista = '0000-00-00'";
        $pole_sort = $pole_termin;
    }else if($raport_type == "rzeczywista"){
        $filtr_datowy = "$pole_rzeczywista >= '$start' AND $pole_rzeczywista <= '$stop'";
        $pole_sort = $pole_rzeczywista;
    }else{
        $filtr_datowy = "$pole_planowana >= '$start' AND $pole_planowana <= '$stop' AND $pole_rzeczywista = '0000-00-00'";
        $pole_sort = $pole_planowana;
    }

    if(isset($_POST['oddzial']) && $_POST['oddzial'] > -1){
        $filtr_datowy .= " AND t.id_oddzial = '{$_POST['oddzial']}'";
    }

    if($person_type == "przewoznik" && isset($_POST['id_przewoznik']) && $_POST['id_przewoznik'] > -1){
        $filtr_datowy .= " AND t.id_przewoznik = '{$_POST['id_przewoznik']}'";
    }

    if($person_type == "klient" && isset($_POST['id_klient']) && $_POST['id_klient'] > -1){
        $filtr_datowy .= " AND t.id_klient = '{$_POST['id_klient']}'";
    }

    if(isset($_POST['status']) && $_POST['status'] > -1){
        $filtr_datowy .= " AND t.$pole_status = '{$_POST['status']}'";
    }

    if($this->Uzytkownik->IsAdmin() == false){
            $warunek .= "t.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
    }

    $DaneDoRaportu = array();
    $this->Baza->Query("SELECT t.*, DATEDIFF(NOW(), $pole_termin) as opoznienie ".($person_type == "klient" ? ", (SELECT SUM(brutto) FROM faktury_pozycje WHERE id_faktury = f.id_faktury) as suma_pozycji, f.wplacono, f.data_wystawienia, IF(f.id_waluty = 1,'PLN','EUR') as waluta_klient " : "")."
                            FROM $this->Tabela t
                            LEFT JOIN orderplus_klient k ON(k.id_klient = t.id_klient)
                            LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = t.id_przewoznik)
                            ".($person_type == "klient" ? "LEFT JOIN faktury f ON(f.id_faktury = t.id_faktury)" : "")."
                            WHERE $warunek ((t.ost_korekta = 1) OR (t.ost_korekta = 0 AND t.korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy ORDER BY ".($person_type == "przewoznik" ? "p.nazwa ASC" : "k.nazwa ASC")." ,$pole_sort ASC");
//    echo "SELECT t.*, DATEDIFF(NOW(), $pole_termin) as opoznienie ".($person_type == "klient" ? ", (SELECT SUM(brutto) FROM faktury_pozycje WHERE id_faktury = f.id_faktury) as suma_pozycji, f.wplacono " : "")."
//                            FROM $this->Tabela t
//                            LEFT JOIN orderplus_klient k ON(k.id_klient = t.id_klient)
//                            LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = t.id_przewoznik)
//                            ".($person_type == "klient" ? "LEFT JOIN faktury f ON(f.numer = t.$pole_faktura)" : "")."
//                            WHERE $warunek ((t.ost_korekta = 1) OR (t.ost_korekta = 0 AND t.korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy ORDER BY ".($person_type == "przewoznik" ? "p.nazwa ASC" : "k.nazwa ASC")." ,$pole_sort ASC";
//    var_dump($this->Baza->GetLastError());
    $Zlecenia = array();
    //$OddzialyZlecenia = array();
    $PrzewoznicyIDs = array();
    $KlienciIDs = array();
    while($zleconko = $this->Baza->GetRow()){
        if($person_type == "przewoznik"){
            $Zlecenia["all"][$zleconko[$pole_status]][] = $zleconko;
        }else{
            $Zlecenia["all"][$zleconko[$pole_status]][$zleconko['id_faktury']] = $zleconko;
        }
        //$OddzialyZlecenia[] = $zleconko['id_oddzial'];
        $KlienciIDs[] = $zleconko['id_klient'];
        $PrzewoznicyIDs[] = $zleconko['id_przewoznik'];
    }   
?>
<form name="formularzy" action="" method="post">
<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>
<table  width="900"  align="center"  border="0" style="" cellspacing="1" cellpadding="2" style="border-collapse: collapse;">
<tr>
    <td colspan="3" style="text-align: left; border: 1px solid #888888; border-right: 0;">
            <img src="images/logo-new.png" alt="Logo" />
    </td>
    <td colspan="<?php echo ($person_type == "przewoznik" ? 10 : 11); ?>" style="font-size: 14pt; text-align: center; width: 300px;border: 1px solid #888888; border-left: 0;">
            <b><br />Raport płatności dla <?php echo ($person_type == "przewoznik" ? "przewoźników" : "klientów"); ?></b><br /><br /> 
    <div style="font-size: 11px;">
        od: <input type='text' style='width: 80px; font-size: 11px;' name='start' value="<?php echo $start; ?>" /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='showKal(document.formularzy.start);' src='images/kalendarz.png'>&nbsp;&nbsp;
        do: <input type='text' style='width: 80px; font-size: 11px;' name='stop' value="<?php echo $stop; ?>" /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='showKal(document.formularzy.stop);' src='images/kalendarz.png'>&nbsp;&nbsp;
        <?php
        //UsefullBase::ShowWyborPrzedzialu($this->Baza, false, false, $stop);
        echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"button\" value=\"Wybierz\" onclick='this.form.action=\"\"; this.form.submit();' />";
        echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"button\" value=\"Exportuj do XLS\" onclick='this.form.action=\"raporty_platnosci_dla_przewoznikow_xls.php\"; this.form.submit();' />";
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
//$OddzialyZlecenia = array_unique($OddzialyZlecenia);

$lp = 1;
$Sumy = array();
$SumyPLN = 0;
//foreach($Oddzialy as $oddzial_id => $oddzial_nazwa){
$oddzial_id = "all";
//      if(!in_array($oddzial_id, $OddzialyZlecenia)){
//          continue;
//      }
      $SumyOddzial = array();
      $SumyOddzialPLN = 0;
      //echo "<tr><td colspan='13' bgcolor='#7E7E7E' style='font-size: 17px; font-weight: bold; color: #FFF'>$oddzial_nazwa</td></tr>";
      $pierwszy_status = true;
      foreach($StatusyKolejnosc as $status_id){
          $SumaStatus = array();
          $SumaStatusPLN = 0;
          if(count($Zlecenia[$oddzial_id][$status_id]) == 0){
              continue;
          }
            if($pierwszy_status == false){
                echo "<tr><td colspan='".($person_type == "przewoznik" ? 13 : 14)."' style='height: 20px; border: 1px solid #888888;'>&nbsp;</td></tr>";
            }
            $pierwszy_status = false;
          echo "<tr><td colspan='".($person_type == "przewoznik" ? 13 : 14)."' bgcolor='#AEAEAE' style='font-size: 15px; font-weight: bold; border: 1px solid #888888;'>{$Statusy[$status_id]}</td></tr>";
          ShowNaglowek($person_type);
          $kolor = "#FFFFFF";
          $LastClient = 0;
          foreach($Zlecenia[$oddzial_id][$status_id] as $zleconko){
              if($zleconko[$pole_id] != $LastClient){
                  $kolor = ($kolor == "#FFFFFF" ? "#E6F0FF" : "#FFFFFF");
              }
              $LastClient = $zleconko[$pole_id];
              echo "<tr><td bgcolor=\"$kolor\" class='report-td'>". $lp ."</td>";
              if($person_type == "przewoznik"){
                echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko['numer_zlecenia']}</nobr></td>";
              }
              echo "<td bgcolor=\"$kolor\" class='report-td'>{$Klienci[$zleconko['id_klient']]}</td>";
              if($person_type == "przewoznik"){
                echo "<td bgcolor=\"$kolor\" class='report-td'>{$Przewoznicy[$zleconko['id_przewoznik']]}</td>";
              }
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko[$pole_faktura]}</nobr></td>";
              if($person_type == "przewoznik"){
                    $StawkaVatPrzewoznik = (in_array(strtolower($zleconko[$pole_vat]), array("np","zw")) ? 0 :  $zleconko[$pole_vat]);
                    $StawkaPrzewoznik = $zleconko[$pole_stawka]*(1+$StawkaVatPrzewoznik/100);
                    $Kurs = ($zleconko[$pole_waluta] != 'PLN' ? $zleconko[$pole_kurs] : 1);
                    if($zleconko[$pole_pozostalo] != ""){
                        $StawkaPrzewoznik = $zleconko[$pole_pozostalo]/$Kurs;
                    }
              }else{
                  $StawkaPrzewoznik = $zleconko[$pole_stawka];
                  $Kurs = ($zleconko[$pole_waluta] != 'PLN' ? $zleconko[$pole_kurs] : 1);
              }
                $Sumy[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                $SumyOddzial[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                $SumaStatus[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                $StawkaPrzewoznikPLN = $StawkaPrzewoznik * $Kurs;
                $SumyPLN += $StawkaPrzewoznikPLN;
                $SumyOddzialPLN += $StawkaPrzewoznikPLN;
                $SumaStatusPLN += $StawkaPrzewoznikPLN;
              echo "<td bgcolor=\"$kolor\" class='report-td'>";
                if( $zleconko[$pole_waluta] != "PLN"){
                        echo number_format($StawkaPrzewoznik, 2, ',', ' ')  ." {$zleconko[$pole_waluta]}";
                }else{
                    echo "&nbsp;";
                }
              echo "</td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>". number_format($StawkaPrzewoznikPLN, 2, ',', ' ')  ." PLN</nobr></td>";
              if($person_type == "klient"){
                  echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>".($zleconko[$pole_waluta] != "PLN" && $zleconko[$pole_pozostalo] > 0 ? ($StawkaPrzewoznik - $zleconko[$pole_pozostalo]) : "")."</nobr></td>";
                  echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>".($zleconko[$pole_waluta] == "PLN" && $zleconko[$pole_pozostalo] > 0 ? ($StawkaPrzewoznik - $zleconko[$pole_pozostalo]) : "")."</nobr></td>";
                  echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko['data_wystawienia']}</nobr></td>";
              }
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko[$pole_termin]}</nobr></td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko[$pole_planowana]}</nobr></td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>{$zleconko[$pole_rzeczywista]}</nobr></td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><nobr>";
              if($zleconko[$pole_rzeczywista] == "0000-00-00"){ 
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
              echo "<td bgcolor=\"$kolor\" class='report-td'>".nl2br($zleconko[$pole_komentarz])."</td>";
              echo "<td bgcolor=\"$kolor\" class='report-td'><input type='checkbox' name='Platnosc[]' value='".($person_type == "przewoznik" ? $zleconko['id_zlecenie'] : $zleconko['id_faktury'])."' class='check_platnosci' /></td>";
              $lp++;
              echo "</tr>";
          }
            echo "<tr><td valign=\"middle\" bgcolor='#AEAEAE' style='font-size: 15px; font-weight: bold;' align=\"right\" colspan='".($person_type == "przewoznik" ? 5 : 3)."'><b>SUMA  - {$Statusy[$status_id]}</b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"center\"><b>";
                    foreach($SumaStatus as $Wal => $Kwota){
                        echo "<nobr>".number_format($Kwota, 2, ",", " ")." $Wal</nobr><br />";
                    }
                echo "</b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"center\"><b>";
                    echo "<nobr>".number_format($SumaStatusPLN, 2, ",", " ")." PLN</nobr>";
                echo "</b></td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"right\">&nbsp;</td>";
                echo "<td valign=\"middle\" bgcolor='#AEAEAE' align=\"left\" colspan='".($person_type == "przewoznik" ? 5 : 8)."'>&nbsp;</td>";
            echo "</tr>";

      }
//        echo "<tr><td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF; font-size: 17px; font-weight: bold;' align=\"right\" colspan='5'><b>SUMA - $oddzial_nazwa</b></td>";
//            echo "<td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF' align=\"center\"><b>";
//                foreach($SumyOddzial as $Wal => $Kwota){
//                    echo "<nobr>".number_format($Kwota, 2, ",", " ")." $Wal</nobr><br />";
//                }
//            echo "</b></td>";
//            echo "<td valign=\"middle\" bgcolor='#7E7E7E'  style='color: #FFF' align=\"center\"><b>";
//                echo "<nobr>".number_format($SumyOddzialPLN, 2, ",", " ")." PLN</nobr>";
//            echo "</b></td>";
//            echo "<td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF' align=\"right\">&nbsp;</td>";
//            echo "<td valign=\"middle\" bgcolor='#7E7E7E' style='color: #FFF' align=\"left\" colspan='5'>&nbsp;</td>";
//        echo "</tr>";
        echo "<tr><td colspan='".($person_type == "przewoznik" ? 13 : 14)."' style='height: 40px; border-left: 0; border-right: 0;'>&nbsp;</td></tr>";

//   }
$kolor = '#cccccc';
echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" colspan='".($person_type == "przewoznik" ? 5 : 3)."'><b>SUMA WSZYSTKICH</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b>";
    foreach($Sumy as $Wal => $Kwota){
        echo "<nobr>".number_format($Kwota, 2, ",", " ")." $Wal</nobr><br />";
    }
echo "</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b>";
    echo "<nobr>".number_format($SumyPLN, 2, ",", " ")." PLN</nobr>";
echo "</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" colspan='".($person_type == "przewoznik" ? 1 : 4)."'>&nbsp;</td>"; 
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"left\" colspan='5'>"; 
?>
    <right><a href="javascript:check_all()" style="color: #000; text-decoration: none; float: right;">zaznacz/odznacz wszystkie</a></right><br /><br />
    <b>Dla zaznaczonych:</b><br />
        <select name="JakiTermin" >
            <option value="planowany">Termin planowany</option>            
            <option value="rzeczywisty">Termin rzeczywisty</option>
        </select>
        <input name="ZmienTermin" style="font-size: 11px;" type="submit" value="Zmień termin" onclick='this.form.action=""; this.form.submit();' /><br /><br />
        Nowa data: <input type='text' style='width: 80px;' name='nowa_data' /><img style='margin-left: 10px; cursor: pointer; vertical-align: middle;' onclick='show_calc();' src='images/kalendarz.png'><br /><br />
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