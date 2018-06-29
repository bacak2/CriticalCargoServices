<br /><a href="?modul=zlecenia&akcja=dodaj" class="form-button">wyślij zlecenie</a>
<div style="clear: both;"></div><br />
<?php

$wiersze2 = mysql_query("SELECT * FROM orderplus_zlecenie WHERE id_klient = '{$_SESSION['zalogowany_id']}' AND ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))  ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");
$Dzis = date("Y-m-d");
$Statusy = array(0 => "anulowane", 1 => "oczekujące na akceptacje", 2 => "zaakceptowane");

?>
<br /><span style="font-weight: bold; color: #BCCE00;">ZLECENIA WYSTAWIONE</span><br /><br />
    <table class="lista">
        <tr>
            <th class='licznik'>Lp</th>
            <th>Numer Zlecenia</th>
            <th>Własny Numer Zlecenia</th>
            <th>Data załadunku</th>
            <th>Data rozładunku</th>
            <th>Miejsce załadunku</th>
            <th>Miejsce rozładunku</th>
            <th>Status</th>
            <th class='ikona'><img src="images/buttons/podglad_button_grey.png" alt="Podgląd" /></th>
            <th class='ikona'><img src="images/buttons/copy_button_grey.png" alt="Duplikuj" /></th>
            <th class='ikona'><img src="images/buttons/faktura_drukuj_button_grey.png" alt="Podgląd faktury" /></th>
            <th class='ikona'><img src="images/buttons/podglad_potwierdzenie_button_grey.png" alt="Podgląd potwierdzenia" /></th>
            <th class='ikona'><img src="images/buttons/podglad_raport_button_grey.png" alt="Podgląd raportu" /></th>
        </tr>
<?php
$Licznik = 1; 
while ($wiersz = mysql_fetch_object($wiersze2)) {
   $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
   echo("<tr style='background-color: $KolorWiersza;'>");
   print ("<td class='licznik'>$Licznik</td>");
   print ("<td>".OrderNumberForClient($wiersz->numer_zlecenia)."</td>");
   print ("<td>$wiersz->numer_zlec_klienta</td>");
   print ("<td>$wiersz->termin_zaladunku</td>");
   print ("<td>$wiersz->termin_rozladunku</td>");
   print ("<td>".nl2br($wiersz->miejsce_zaladunku)."</td>");
   print ("<td>".nl2br($wiersz->odbiorca)."</td>");
   $Status = ($wiersz->korekta == 3 ? 0 : 2);
    print ("<td>{$Statusy[$Status]}</td>");
   print ("<td class='ikona'> <a href=\"podglad.php?real_id=$wiersz->id_zlecenie\" class=\"akcja\" target='_blank'><img src=\"images/buttons/podglad_button.png\" onmouseover='this.src=\"images/buttons/podglad_button_hover.png\"' onmouseout='this.src=\"images/buttons/podglad_button.png\"'></a></td>");
   print ("<td class='ikona'> <a href=\"?modul=zlecenia&akcja=dodaj&real_idzk=$wiersz->id_zlecenie\" class=\"akcja\"><img src=\"images/buttons/copy_button.png\" onmouseover='this.src=\"images/buttons/copy_button_hover.png\"' onmouseout='this.src=\"images/buttons/copy_button.png\"'></a></td>");
   print ("<td class='ikona'> ");
        if($wiersz->id_faktury > 0){
           echo "<a href=\"drukuj_fakture_new.php?id=$wiersz->id_faktury\" target=\"_blank\" class=\"akcja\"><img src=\"images/buttons/faktura_drukuj_button.png\" onmouseover='this.src=\"images/buttons/faktura_drukuj_button_hover.png\"' onmouseout='this.src=\"images/buttons/faktura_drukuj_button.png\"'></a>";
        }else{
            echo "-";
        }
   echo ("</td>");
  print ("<td class='ikona'>");
    if(GetValue("SELECT count(*) FROM orderplus_klient_potwierdzenie_zlecenie WHERE zlecenie_id = '$wiersz->id_zlecenie'") > 0){
            echo "<a href=\"?modul=klient_potwierdzenia&filtr_zlecenie=$wiersz->id_zlecenie\" class=\"akcja\"><img src=\"images/buttons/podglad_potwierdzenie_button.png\" onmouseover='this.src=\"images/buttons/podglad_potwierdzenie_button_hover.png\"' onmouseout='this.src=\"images/buttons/podglad_potwierdzenie_button.png\"'></a>";
    }else{
        echo "-";
    }
   echo ("</td>");
   print ("<td class='ikona'>");
    if(GetValue("SELECT count(*) FROM orderplus_klient_raport_zlecenie WHERE zlecenie_id = '$wiersz->id_zlecenie'") > 0){
            echo "<a href=\"?modul=klient_raporty&filtr_zlecenie_r=$wiersz->id_zlecenie\" class=\"akcja\"><img src=\"images/buttons/podglad_raport_button.png\" onmouseover='this.src=\"images/buttons/podglad_raport_button_hover.png\"' onmouseout='this.src=\"images/buttons/podglad_raport_button.png\"'></a>";
    }else{
        echo "-";
    }

   echo ("</td>");
   print ("</tr>");
   $Licznik++;
}

?>
</table>
<?php
$wiersze = mysql_query("SELECT * FROM orderplus_zlecenie_klient
                        WHERE id_klient = '{$_SESSION['zalogowany_id']}'
                        ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");

?>
<br /><br /><br /><span style="font-weight: bold; color: #BCCE00;">ZLECENIA NIE WYSTAWIONE</span><br /><br />
    <table class="lista">
        <tr>
            <th class='licznik'>Lp</th>
            <th>Własny Numer Zlecenia</th>
            <th>Data załadunku</th>
            <th>Data rozładunku</th>
            <th>Miejsce załadunku</th>
            <th>Miejsce rozładunku</th>
            <th>Status</th>
            <th class='ikona'><img src="images/buttons/podglad_button_grey.png" alt="Podgląd" /></th>
            <th class='ikona'><img src="images/buttons/copy_button_grey.png" alt="Duplikuj" /></th>
        </tr>
<?php
$Licznik = 1;
while ($wiersz = mysql_fetch_object($wiersze)) {
   $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
   echo("<tr style='background-color: $KolorWiersza;'>");
   print ("<td class='licznik'>$Licznik</td>");
   print ("<td>$wiersz->numer_zlec_klienta</td>");
   print ("<td>$wiersz->termin_zaladunku</td>");
   print ("<td>$wiersz->termin_rozladunku</td>");
   print ("<td>".nl2br($wiersz->miejsce_zaladunku)."</td>");
   print ("<td>".nl2br($wiersz->odbiorca)."</td>");
    print ("<td>{$Statusy[$wiersz->zlecenie_status]}</td>");
   print ("<td class='ikona'> <a href=\"podglad.php?id=$wiersz->id_zlecenie\" class=\"akcja\" target='_blank'><img src=\"images/buttons/podglad_button.png\" onmouseover='this.src=\"images/buttons/podglad_button_hover.png\"' onmouseout='this.src=\"images/buttons/podglad_button.png\"'></a></td>");
   print ("<td class='ikona'> <a href=\"?modul=zlecenia&akcja=dodaj&idzk=$wiersz->id_zlecenie\" class=\"akcja\"><img src=\"images/buttons/copy_button.png\" onmouseover='this.src=\"images/buttons/copy_button_hover.png\"' onmouseout='this.src=\"images/buttons/copy_button.png\"'></a></td>");
   print ("</tr>");
   $Licznik++;
}

?>
</table>