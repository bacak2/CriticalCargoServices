<script type="text/javascript" src="js/funkcje.js"></script>
<script type="text/javascript">
<!--
function SprawdzFormularz(formularz) {
   if (formularz.zlecenie_przewoznik.value == 0) {
      alert('Nie wybrano przewoźnika!');
      formularz.zlecenie_przewoznik.focus();
      return false;
   }
   if (!SprawdzDate(formularz.zlecenie_data_zlecenia.value)) {
      alert('Błędna data zlecenia!');
      formularz.zlecenie_data_zlecenia.focus();
      return false;
   }
   if ((formularz.wybor_kierowcy.value == 'nowy') && (formularz.zlecenie_kierowca_nazwisko.value == '')) {
      alert('Wprowadź dane nowego kierowcy!');
      formularz.zlecenie_kierowca_nazwisko.focus();
      return false;
   }
   return true;
}
//-->
</script>
<?php
$rozladunki = substr($rozladunki, 0, -1);

if (isset($_POST['nowy']) && $_POST['nowy'] == 'nowy') {
    $data_zlecenia = $_POST['zlecenie_data_zlecenia'];
   $id_klient = $_SESSION['zalogowany_id'];
   $id_oddzial = mysql_result(mysql_query("SELECT id_oddzial FROM orderplus_klient WHERE id_klient = '$id_klient'"),0,0);
   $stawka_przewoznik = 0;
   $zaladunek = $_POST['zlecenie_zaladunek'];
   $rozladunek = $_POST['zlecenie_rozladunek'];
   $termin_zaladunku = $_POST['zlecenie_termin_zaladunku'];
   $godzina_zaladunku = $_POST['zlecenie_godzina_zaladunku'];
   $termin_rozladunku = $_POST['zlecenie_termin_rozladunku'];
   $godzina_rozladunku = $_POST['zlecenie_godzina_rozladunku'];
   $niebezpieczny = $_POST['zlecenie_niebezpieczny'];
   $opis_ladunku = $_POST['zlecenie_opis_ladunku'];
   $stawka_klient = 0;
   $waluta = 'PLN';
   $KodKrajuR = $_POST['zlecenie_kod_kraju_r'];
   $KodKrajuZ = $_POST['zlecenie_kod_kraju_z'];
   $AktualnyMiesiac = date("m");
   $AktualnyRok = date("Y");
   $numer_zlecenia_krotki = intval(mysql_result(mysql_query("SELECT max(numer_zlecenia_krotki) FROM orderplus_zlecenie_klient"), 0, 0)) + 1;
   $numer_zlecenia = "$numer_zlecenia_krotki/$AktualnyMiesiac/$AktualnyRok";
   $WalutaSmall = strtolower($waluta);
  $TypGodzinyZaladunku = $_POST['zlecenie_typ_godz_zaladunku'];
  $TypGodzinyRozladunku = $_POST['zlecenie_typ_godz_rozladunku'];
  $dod_ubezpieczenie = isset($_POST['zlecenie_dodatkowe_ubezpieczenie']) ? 1 : 0;
  $dod_raporty = isset($_POST['zlecenie_dodatkowe_raporty']) ? 1 : 0;
  $dod_raporty_godz = $_POST['zlecenie_dodatkowe_raporty_godziny'];
  $numer_zlecenia_klienta = $_POST['zlecenie_numer_zlecenia_klienta'];
  $stawka_klient = str_replace(",", ".", $_POST['zlecenie_stawka_klient']);
  $waluta = $_POST['zlecenie_waluta'];

   if(mysql_query("INSERT INTO orderplus_zlecenie_klient SET id_oddzial = '$id_oddzial',
                   numer_zlecenia_krotki='$numer_zlecenia_krotki', numer_zlecenia='$numer_zlecenia', data_zlecenia='$data_zlecenia', 
                   id_klient = '$id_klient', miejsce_zaladunku='$zaladunek', odbiorca='$rozladunek', termin_zaladunku='$termin_zaladunku', godzina_zaladunku='$godzina_zaladunku',
                   termin_rozladunku='$termin_rozladunku', godzina_rozladunku='$godzina_rozladunku', ladunek_niebezpieczny='$niebezpieczny', 
                   opis_ladunku='$opis_ladunku', stawka_klient='$stawka_klient', waluta='$waluta', data_wprowadzenia = now(),
                    kod_kraju_rozladunku = '$KodKrajuR', kod_kraju_zaladunku = '$KodKrajuZ', typ_godz_zaladunku = '$TypGodzinyZaladunku', typ_godz_rozladunku = '$TypGodzinyRozladunku',
                    dodatkowe_ubezpieczenie = '$dod_ubezpieczenie', dodatkowe_raporty = '$dod_raporty', dodatkowe_raporty_godziny = '$dod_raporty_godz', numer_zlec_klienta = '$numer_zlecenia_klienta'")){
                        $id = mysql_insert_id();
                        echo("<tr><td width=\"100%\" bgcolor=\"#FFFFFF\" align=\"center\"><br><br><br><b>Zlecenie zostało wysłane</b><br><br><a href=\"?modul=$modul\"><img src=\"images/ok.gif\" border=\"0\"></a></td></tr>");
                   }
                   else {
                      echo("<tr><td width=\"100%\" bgcolor=\"#FFFFFF\" align=\"center\"><br><br><br><b>Błąd.</b><br><br>".  mysql_error()."<br><br><a href=\"?modul=$modul\"><img src=\"images/ok.gif\" border=\"0\"></a></td></tr>");
                   }
}
else {
   $Waluty = array('PLN', 'EUR', 'USD');
   if (isset($_POST['nowy'])){
      $data_zlecenia = $_POST['zlecenie_data_zlecenia'];
      $zaladunek = $_POST['zlecenie_zaladunek'];
      $rozladunek = $_POST['zlecenie_rozladunek'];
      $termin_zaladunku = $_POST['zlecenie_termin_zaladunku'];
      $godzina_zaladunku = $_POST['zlecenie_godzina_zaladunku'];
      $termin_rozladunku = $_POST['zlecenie_termin_rozladunku'];
      $godzina_rozladunku = $_POST['zlecenie_godzina_rozladunku'];
      $niebezpieczny = $_POST['zlecenie_niebezpieczny'];
      $opis_ladunku = $_POST['zlecenie_opis_ladunku'];
      $KodKrajuR = $_POST['zlecenie_kod_kraju_r'];
      $KodKrajuZ = $_POST['zlecenie_kod_kraju_z'];
      $TypGodzinyZaladunku = $_POST['zlecenie_typ_godz_zaladunku'];
      $TypGodzinyRozladunku = $_POST['zlecenie_typ_godz_rozladunku'];
      $dod_ubezpieczenie = isset($_POST['zlecenie_dodatkowe_ubezpieczenie']) ? 1 : 0;
      $dod_raporty = isset($_POST['zlecenie_dodatkowe_raporty']) ? 1 : 0;
      $dod_raporty_godz = $_POST['zlecenie_dodatkowe_raporty_godziny'];
      $numer_zlecenia_klienta = $_POST['zlecenie_numer_zlecenia_klienta'];
      $stawka_klient = $_POST['zlecenie_stawka_klient'];
      $waluta = $_POST['zlecenie_waluta'];
   }
   else {
      $data_zlecenia = date('Y-m-d');
      $zaladunek = '';
      $rozladunek = '';
      $termin_zaladunku = '';
      $godzina_zaladunku = '';
      $termin_rozladunku = '';
      $godzina_rozladunku = '';
      $niebezpieczny = '';
      $opis_ladunku = '';
      $KodKrajuR = 34;
      $KodKrajuZ = 34;
      $TypGodzinyZaladunku = 0;
      $TypGodzinyRozladunku = 0;
      $dod_ubezpieczenie = 0;
      $dod_raporty = 0;
      $dod_raporty_godz = "";
      $numer_zlecenia_klienta = "";
      $stawka_klient = 0.00;
      $waluta = "PLN";
      if(isset($_GET['idzk']) || isset($_GET['real_idzk'])){
          if(isset($_GET['idzk'])){
            $Query = mysql_query("SELECT * FROM orderplus_zlecenie_klient WHERE id_zlecenie = '{$_GET['idzk']}'");
          }else{
              $Query = mysql_query("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie = '{$_GET['real_idzk']}'");
          }
          $zlecenie = mysql_fetch_object($Query);
          $zaladunek = $zlecenie->miejsce_zaladunku;
          $rozladunek = $zlecenie->odbiorca;
          $termin_zaladunku = $zlecenie->termin_zaladunku;
          $godzina_zaladunku = $zlecenie->godzina_zaladunku;
          $termin_rozladunku = $zlecenie->termin_rozladunku;
          $godzina_rozladunku = $zlecenie->godzina_rozladunku;
          $niebezpieczny = $zlecenie->ladunek_niebezpieczny;
          $opis_ladunku = $zlecenie->opis_ladunku;
          $KodKrajuR = $zlecenie->kod_kraju_rozladunku;
          $KodKrajuZ = $zlecenie->kod_kraju_zaladunku;
          $TypGodzinyZaladunku = $zlecenie->typ_godz_zaladunku;
          $TypGodzinyRozladunku = $zlecenie->typ_godz_rozladunku;
          $dod_ubezpieczenie = $zlecenie->dodatkowe_ubezpieczenie;
          $dod_raporty = $zlecenie->dodatkowe_raporty;
          $dod_raporty_godz = $zlecenie->dodatkowe_raporty_godziny;
          $nr_zlecenia_klienta = $zlecenie->numer_zlec_klienta;
          $stawka_klient = $zlecenie->stawka_klient;
          $waluta = $zlecenie->waluta;
          if(isset($_GET['real_idzk'])){
              $zlecenieKlient = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_zlecenie_klient WHERE real_id = '{$_GET['real_idzk']}'"));
              $niebezpieczny = $zlecenieKlient->ladunek_niebezpieczny;
              $dod_ubezpieczenie = $zlecenieKlient->dodatkowe_ubezpieczenie;
              $dod_raporty = $zlecenieKlient->dodatkowe_raporty;
              $dod_raporty_godz = $zlecenieKlient->dodatkowe_raporty_godziny;
          }
      }
   }
   echo("<form name='formularz' action='' onsubmit=\"return SprawdzFormularz(this);\" method='post'>");
   echo("<input type=\"hidden\" name=\"nowy\" value=\"nowy\">");
   echo "<table class='formularz'>";
   echo("<tr><th>Własny numer zlecenia</td><td><input type=\"text\" name=\"zlecenie_numer_zlecenia_klienta\" style='width: 300px;'  value=\"$numer_zlecenia_klienta\"></td></tr>");
   echo("<tr><th>Data Zlecenia (RRRR-MM-DD)</td><td><input type=\"text\" id=\"zlecenie_data_zlecenia\" name=\"zlecenie_data_zlecenia\" style='width: 100px;' value=\"$data_zlecenia\" class='pole_wymagane' readonly='readonly'></td></tr>");
   $Kody = GetCountryCodes();
   echo("<tr><th>Kod kraju załadunku</td><td>");
   echo("<select name=\"zlecenie_kod_kraju_z\">");
   echo("<option value=\"0\"".(0 == $KodKrajuZ ? ' selected' : '').">-- wybierz --</option>");
   
   foreach($Kody as $KodID => $Kod){
      echo("<option value=\"$KodID\"".($KodID == $KodKrajuZ ? ' selected' : '').">$Kod</option>");
   }
   echo("</select></td></tr>");
   echo("<tr><th>Załadowca i miejsce załadunku</td><td><textarea name=\"zlecenie_zaladunek\" style=\"width:250px;height:100px;\">$zaladunek</textarea></td></tr>");
   echo("<tr><th>Kod kraju rozładunku</td><td>");
   echo("<select name=\"zlecenie_kod_kraju_r\">");
   echo("<option value=\"0\"".(0 == $KodKrajuR ? ' selected' : '').">-- wybierz --</option>");
   
   foreach($Kody as $KodID => $Kod){
      echo("<option value=\"$KodID\"".($KodID == $KodKrajuR ? ' selected' : '').">$Kod</option>");
   }
   echo("</select></td></tr>");
   echo("<tr><th>Odbiorca i miejsce rozładunku</td><td><textarea name=\"zlecenie_rozladunek\" style=\"width:250px;height:100px;\">$rozladunek</textarea></td></tr>");
  $TypyGodzin = GetTypyGodzin();
   echo("<tr><th>Termin załadunku (RRRR-MM-DD)</td><td><input type=\"text\" name=\"zlecenie_termin_zaladunku\" style='width: 100px;'  value=\"$termin_zaladunku\" readonly=\"readonly\">&nbsp;&nbsp;<img src='images/kalendarz.png' onclick='javascript:showKal(document.formularz.zlecenie_termin_zaladunku);' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'></td></tr>");
   //-- godzina zal--//
   echo("<tr><th>Godzina załadunku</td><td>");
        echo "<select name='zlecenie_typ_godz_zaladunku'>\n";
            foreach($TypyGodzin as $Idx => $Typ){
                echo "<option value='$Idx'".($Idx == $TypGodzinyZaladunku ? " selected" : "").">$Typ załadunku</option>\n";
            }
        echo "</select><br />\n";
   echo ("<input type=\"text\" name=\"zlecenie_godzina_zaladunku\" size=\"10\" style=\"margin-top: 10px; width:50px;\" value=\"$godzina_zaladunku\"></td></tr>");
   //-- godzina zal--//
   echo("<tr><th>Termin rozładunku (RRRR-MM-DD)</td><td><input type=\"text\" name=\"zlecenie_termin_rozladunku\" style='width: 100px;'  value=\"$termin_rozladunku\" readonly=\"readonly\">&nbsp;&nbsp;<img src='images/kalendarz.png' onclick='javascript:showKal(document.formularz.zlecenie_termin_rozladunku);' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'></td></tr>");
   //-- godzina roz--//
   echo("<tr><th>Godzina rozładunku</td><td>");
        echo "<select name='zlecenie_typ_godz_rozladunku'>\n";
            foreach($TypyGodzin as $Idx => $Typ){
                echo "<option value='$Idx'".($Idx == $TypGodzinyRozladunku ? " selected" : "").">$Typ rozładunku</option>\n";
            }
        echo "</select><br />\n";
    echo("<input type=\"text\" name=\"zlecenie_godzina_rozladunku\" size=\"10\" style=\"margin-top: 10px; width:50px;\" value=\"$godzina_rozladunku\"></td></tr>");
    echo("<tr><th>Towar</td><td><textarea name=\"zlecenie_opis_ladunku\" style=\"width:250px;height:100px;\">$opis_ladunku</textarea></td></tr>");
    echo("<tr><th>Cena netto</td><td><input type=\"text\" name=\"zlecenie_stawka_klient\" style=\"width:90px;\" value=\"$stawka_klient\">");
     echo("&nbsp;&nbsp;<select name=\"zlecenie_waluta\">");
           foreach ($Waluty as $Waluta) {
              echo("<option value=\"$Waluta\"".($waluta == $Waluta ? ' selected' : '').">$Waluta</option>");
           }
   echo("</select></td></tr>");
   echo("<tr><th>Uwagi&nbsp;&nbsp<a href='#' class='tip_trigger2'><img src='images/information.gif' alt='info'  /></a><span class='tip2'>w uwagach mogą Państwo zamieścić informacje dodatkowe o zlecenie np. wartość towaru, specjalne wymagania dotyczące zabezpieczenia towaru, numery załadunku itp.</span></td><td><textarea name=\"zlecenie_niebezpieczny\" style=\"width:250px;height:100px;\">$niebezpieczny</textarea></td></tr>");
   echo("<tr><th>&nbsp;</td><td>");
        echo "<input type='checkbox' name='zlecenie_dodatkowe_ubezpieczenie' value='1'".($dod_ubezpieczenie == 1 ? " checked" : "")." /> dodatkowe ubezpieczenie cargo<br />\n";
        echo "<input type='checkbox' name='zlecenie_dodatkowe_raporty' value='1'".($dod_raporty == 1 ? " checked" : "")." onclick='TypeHours(this.checked)' /> dodatkowe raporty o statusie przesyłki&nbsp;&nbsp<a href='#' class=\"tip_trigger\"><img src='images/information.gif' alt='info' /></a><span class=\"tip\">standardowo otrzymujecie od nas Państwo codziennie do godziny 10.00  raport z informację o aktualnym statusie przesyłki,<br /> jeśli chcieliby Państwo otrzymać dodatkowe raporty, proszę o wpisanie godzin po przecinku (raporty po godzinie 16.00, wysyłane są sms-em)</span><br />\n";
        echo "<div id='raporty_godziny' style='margin: 10px 0px;".($dod_raporty == 0 ? " display: none;" : "")."'>\n";
            echo "Wpisz godziny po przecinku:<br />";
            echo "<textarea name='zlecenie_dodatkowe_raporty_godziny'>$dod_raporty_godziny</textarea>\n";
        echo "</div>\n";
        echo "<input type='checkbox' name='akceptacja' value='1' id='akceptacja-regulaminu' /> akceptuje <a href='http://mepp.pl/index.php?option=com_wrapper&view=wrapper&Itemid=417&lang=pl' target='_blank' />regulamin</a> świadczenia usług spedycyjnych <br />\n";
   echo ("</td></tr>");
   
   echo("<tr><th>&nbsp;</td><td><a href=\"javascript:WyslijZlecenie()\"><img src=\"images/ok.gif\" border=\"0\"></a> <a href=\"?modul=$modul\"><img src=\"images/anuluj.gif\" border=\"0\"></a></td></tr>");
   echo("</form>");
   echo "</table>";
}
?>