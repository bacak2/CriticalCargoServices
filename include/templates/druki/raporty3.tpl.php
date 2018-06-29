<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>


<table  width="720" align="center"  border="0" style="border: 1px solid #888888" cellspacing="1" cellpadding="2">
<tr>
			<td colspan="3" style="text-align: left">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td colspan="8" style="font-size: 14pt; text-align: center; width: 300px">
				<b><br />Raport wg. trasy</b><br /><br />
<form action="" method="post">

<?php
$Form = new FormularzSimple();
$Przewoznicy = Usefull::PolaczDwieTablice(array(0 => " -- Wszyscy --"), UsefullBase::GetPrzewoznicy($this->Baza));
echo "<div style=\"font-size: 11px\">";
	$Kraje = Usefull::PolaczDwieTablice(array(0 => " -- Wszystkie --"), UsefullBase::GetCountryCodes($this->Baza));
	$From = (isset($_POST['from']) ? $_POST['from'] : 0);
	$To = (isset($_POST['to']) ? $_POST['to'] : 0);
	$Prze = (isset($_POST['przewoznik']) ? $_POST['przewoznik'] : 0);
	echo "<nobr>Trasa: ";
	$Form->PoleSelect("from", $Kraje, $From, "onchange = 'this.form.submit();'");
	echo " ==> ";
	$Form->PoleSelect("to", $Kraje, $To, "onchange = 'this.form.submit();'");
	echo "</nobr><br /><br />\n";
	echo "<nobr>Przewoźnik: ";
	$Form->PoleSelect("przewoznik", $Przewoznicy, $Prze, "onchange = 'this.form.submit();'");
	echo "</nobr><br /><br />\n";
	UsefullBase::ShowWyborPrzedzialu($this->Baza);
echo "</div>";
$Klienci = UsefullBase::GetKlienci($this->Baza);
$Userzy = UsefullBase::GetUsers($this->Baza);
$TypSerwisu = UsefullBase::GetTypySerwisu($this->Baza);
?>

</form>
				</td>
				</tr>
<?php
$lp = 1;
$kolor = 'white';
$totalna_suma_marzy = 0;
$totalna_suma_klienta = 0;
$totalna_suma_przewoznika = 0;
$totalna_suma_zlecen = 0;
$Trasa = "";
if($_POST['from'] > 0 || $_POST['to'] > 0){
	if($_POST['from'] > 0){
		$Trasa .= " AND kod_kraju_zaladunku = '{$_POST['from']}'";
	}
	if($_POST['to'] > 0){
		$Trasa .= " AND kod_kraju_rozladunku = '{$_POST['to']}'";
	}
}
if($_POST['przewoznik'] > 0){
	$Trasa .= " AND id_przewoznik = '{$_POST['przewoznik']}'";
}
if(!isset($_POST['start'])){
    $filtr_datowy = "termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31'";
}else{
      $filtr_datowy = "termin_zaladunku >= '{$_POST['start']}' AND termin_zaladunku <= '{$_POST['stop']}'";
   }

$DaneDoRaportu = array();
$z2 = "SELECT * FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' $warunek $Trasa AND $filtr_datowy ORDER BY termin_zaladunku ASC";
//var_dump($z2);

$w2 = mysql_query($z2);
 while($zleconko = mysql_fetch_array($w2)){
 	$DaneDoRaportu[] = $zleconko;
 }
?>
<tr>
	<td valign="middle" width="40" bgcolor="#CECECE" align="center"><b>LP</b></td>
	<td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Nr zlecenia</b></td>
	 <td valign="middle" width="200" bgcolor="#CECECE" align="center"><b>Klient</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Przewoźnik</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Kod załad.</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Adres załadunku</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Kod rozład.</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Adres rozładunku</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Typ serwisu</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Opis ładunku</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Koszt</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Stawka za km</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Użytkownik</b></td>
 </tr>
<?php
foreach($DaneDoRaportu as $Dane){
      echo "<tr>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">$lp</td>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Dane['numer_zlecenia']}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Klienci[$Dane['id_klient']]}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Przewoznicy[$Dane['id_przewoznik']]}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Kraje[$Dane['kod_kraju_zaladunku']]}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Dane['miejsce_zaladunku']}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Kraje[$Dane['kod_kraju_rozladunku']]}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Dane['odbiorca']}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$TypSerwisu[$Dane['typ_serwisu']]}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Dane['opis_ladunku']}</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><nobr>". number_format($Dane['stawka_przewoznik'], 2, ',', ' ')  ." {$Dane["waluta"]}</nobr></td>";
	      if($Dane['waluta'] == "PLN"){
	      		$Stawka = $Dane['stawka_przewoznik'];
	      }else{
	      		$Stawka = $Dane['stawka_przewoznik']*$Dane['kurs_przewoznik'];
	      }
	      $totalna_suma_przewoznika += $Stawka;
              $StawkaZaKm = ($Dane['ilosc_km'] > 0 ? "<nobr>".number_format($Dane['stawka_przewoznik']/$Dane['ilosc_km'], 2, ',', ' ')." {$Dane["waluta"]}</nobr>" : "brak ilości km");
              echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">$StawkaZaKm</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$Userzy[$Dane["id_uzytkownik"]]}</td>";
      echo "</tr>\n";
     $lp++;
}
$kolor = '#cccccc';
echo "<tr>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><nobr><b>". number_format($totalna_suma_przewoznika, 2, ',', ' ') ."</nobr></b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td></tr>";

?>


</table>
</td>
</tr>
</table>