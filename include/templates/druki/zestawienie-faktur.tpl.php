z<?php
if(isset($_POST['start']))
{
   $warunek_post = "data_wystawienia >= '{$_POST['start']}' AND data_wystawienia <= '{$_POST['stop']}'";
}
else
{
   $warunek_post = "data_wystawienia >= '{$_SESSION['okresStart']}-01' AND data_wystawienia <= '{$_SESSION['okresEnd']}-31'";
}
$zapyt = mysql_query("SELECT * FROM faktury WHERE $warunek_post ORDER BY autonumer ASC");

while($faktura = mysql_fetch_array($zapyt)){
    $faktura['typek'] = "normal";
    $Faktury[] = $faktura;
    $Numerki[] = $faktura['autonumer'];
}

$zapyt = mysql_query("SELECT * FROM orderplus_sea_orders_faktury WHERE $warunek_post ORDER BY autonumer ASC");
while($faktura = mysql_fetch_array($zapyt)){
    $faktura['typek'] = "morska";
    $Faktury[] = $faktura;
    $Numerki[] = $faktura['autonumer'];
}

$zapyt = mysql_query("SELECT * FROM orderplus_air_orders_faktury WHERE $warunek_post ORDER BY autonumer ASC");
while($faktura = mysql_fetch_array($zapyt)){
    $faktura['typek'] = "lotnicza";
    $Faktury[] = $faktura;
    $Numerki[] = $faktura['autonumer'];
}
array_multisort($Numerki, SORT_ASC, $Faktury);


$Miesiac = intval(substr($_SESSION['okresStart'], 5, 2));
$Rok = intval(substr($_SESSION['okresStart'], 0, 4));
$Miesiace = array (
1 => 'Styczeń',
2 => 'Luty',
3 => 'Marzec',
4 => 'Kwiecień',
5 => 'Maj',
6 => 'Czerwiec',
7 => 'Lipiec',
8 => 'Sierpień',
9 => 'Wrzesień',
10 => 'Październik',
11 => 'Listopad',
12 => 'Grudzień'
);
?>
<div style="width: 19cm; margin: 1cm auto 1cm auto; text-align: left;">
	<table>
		<tr>
			<td width="16cm">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td style="font-size: 14pt; text-align: center;">
				<b>Zestawienie faktur VAT</b><br />
				<br />
				<form action="" method="post">
				<div style="font-size: 11px; ">
<?php


$start = mysql_result(mysql_query("SELECT data_wystawienia FROM faktury ORDER BY data_wystawienia ASC"), 0, 0);
$stop = mysql_result(mysql_query("SELECT data_wystawienia FROM faktury ORDER BY data_wystawienia DESC"), 0, 0);
echo "od:<select style=\"font-size: 11px;\" name=\"start\">";
$temp = $start;
do
{
   if(isset($_POST['start']))
   {
      $_POST['start'] == $temp ? $sel = 'selected' : $sel = '';
   }
   else
   {
   "{$_SESSION['okresStart']}-01" == $temp  ? $sel = 'selected' : $sel = '';
   }
   echo "<option value=\"$temp\" $sel>$temp</option>";
   $temp = date('Y-m-d', strtotime("$temp +1 day"));
}
while($temp <= $stop);
echo "</select>";


echo "&nbsp;&nbsp;do:<select style=\"font-size: 11px;\" name=\"stop\">";
$temp = $start;
do
{
   if(isset($_POST['stop']))
   {
      $_POST['stop'] == $temp ? $sel = 'selected' : $sel = '';
   }
   else
   {
      $temp == $stop ? $sel = 'selected' : $sel = '';
      /*"{$_SESSION['okres']}-30" == $temp  ? $sel = 'selected' : $sel = '';
      "{$_SESSION['okres']}-31" == $temp  ? $sel = 'selected' : $sel = '';*/
   }
   echo "<option value=\"$temp\" $sel>$temp</option>";
   $temp = date('Y-m-d', strtotime("$temp +1 day"));
}
while($temp <= $stop);
echo "</select>";
echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"submit\" value=\"Wybierz\" />";


?>
</div>
</form>
<br />
			</td>
		</tr>
	</table>
	<br />
	<table class="ramka">
	<thead>
		<tr>
			<th>Lp.</th>
			<th>Numer</th>
			<th>Data</th>
			<th>Klient</th>
			<th align="right">Kwota&nbsp;netto</th>
			<th align="right">VAT</th>
			<th align="right">Kwota&nbsp;VAT</th>
			<th align="right">Kwota&nbsp;brutto</th>
			<th>Termin płatności</th>
		</tr>
	</thead>
	<tbody>
<?php
$Licznik = 1;
$suma_netto[1] = 0;
$suma_netto[2] = 0;
$suma_netto[3] = 0;
$suma_brutto[1] = 0;
$suma_brutto[2] = 0;
$suma_brutto[3] = 0;
$suma_vat[1] = 0;
$suma_vat[2] = 0;
$suma_vat[3] = 0;
$Waluty = UsefullBase::GetWaluty($this->Baza);
$Klienci = UsefullBase::GetKlienci($this->Baza);
foreach($Faktury as $faktura)
{
    $TabPozycje = ($faktura['typek'] == "morska" ? "orderplus_sea_orders_faktury_pozycje" : ($faktura['typek'] == "lotnicza" ? "orderplus_air_orders_faktury_pozycje" : "faktury_pozycje"));
   $zap3= "SELECT *,
           SUM(netto) as suma_netto,
           SUM(kwota_vat) as suma_kwot_vat,
           SUM(brutto) as suma_brutto
           FROM $TabPozycje WHERE id_faktury = {$faktura['id_faktury']} GROUP BY vat DESC";
   $w1 = mysql_query($zap3);
   $lp = 1;
   $suma_pozycji_netto = 0;
   $suma_pozycji_brutto = 0;
   $suma_pozycji_kwot_vat = 0;
   while($pozycje = mysql_fetch_object($w1))
   {
      $suma_pozycji_brutto += $pozycje->suma_brutto;
      $suma_pozycji_netto += $pozycje->suma_netto;
      $suma_pozycji_kwot_vat += $pozycje->suma_kwot_vat;
      $kwocinka = $pozycje->vat;
   }

   $kwota_brutto_tekst = Usefull::ZmienFormatKwoty($suma_pozycji_brutto);
   $kwota_netto_tekst = Usefull::ZmienFormatKwoty($suma_pozycji_netto);
   $kwota_vat_tekst = Usefull::ZmienFormatKwoty($suma_pozycji_kwot_vat);

   $faktura['termin_platnosci'] < date('Y-m-d') ? $terminek = '<span style="color: #740000">'.$faktura['termin_platnosci'].'</span>' : $terminek = $faktura['termin_platnosci'];
   echo "<tr>
			<td>". $Licznik++ ."</td>
			<td>{$faktura['numer']}</td>
			<td>{$faktura['data_wystawienia']}</td>
			<td>{$Klienci[$faktura['id_klienta']]}</td>
			<td align=\"right\"><nobr>$kwota_netto_tekst&nbsp;{$Waluty[$faktura['id_waluty']]}</nobr></td>
			<td align=\"right\"><nobr>$kwocinka&nbsp;".(in_array(strtolower($kwocinka),array("np","zw")) ? "" : "%")."</nobr></td>
			<td align=\"right\"><nobr>$kwota_vat_tekst&nbsp;{$Waluty[$faktura['id_waluty']]}</nobr></td>
			<td align=\"right\"><nobr>$kwota_brutto_tekst&nbsp;{$Waluty[$faktura['id_waluty']]}</nobr></td>
			<td>$terminek</td>
		</tr>";
   $suma_netto[$faktura['id_waluty']] += $suma_pozycji_netto;
   $suma_brutto[$faktura['id_waluty']] += $suma_pozycji_brutto;
   $suma_vat[$faktura['id_waluty']] += $suma_pozycji_kwot_vat;
}

for($i = 1; $i <= 3; $i++){
    $suma_netto_tekst[$i] = number_format($suma_netto[$i], 2, ',', ' ');
    $suma_vat_tekst[$i]= number_format($suma_vat[$i], 2, ',', ' ');
    $suma_brutto_tekst[$i] = number_format($suma_brutto[$i], 2, ',', ' ');
}
?>
		<tr>
			<th colspan="4">&nbsp;</th>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th colspan="4">RAZEM</th>
			<td align="right"><nobr><?php echo($suma_netto_tekst[1]); ?>&nbsp;zł</nobr><br /><nobr><?php echo($suma_netto_tekst[2]); ?>&nbsp;usd</nobr><br /><nobr><?php echo($suma_netto_tekst[3]); ?>&nbsp;eur</nobr></td>
			<td>&nbsp;</td>
			<td align="right"><nobr><?php echo($suma_vat_tekst[1]); ?>&nbsp;zł</nobr><br /><nobr><?php echo($suma_vat_tekst[2]); ?>&nbsp;usd</nobr><br /><nobr><?php echo($suma_vat_tekst[3]); ?>&nbsp;eur</nobr></td>
			<td align="right"><nobr><?php echo($suma_brutto_tekst[1]); ?>&nbsp;zł</nobr><br /><nobr><?php echo($suma_brutto_tekst[2]); ?>&nbsp;usd</nobr><br /><nobr><?php echo($suma_brutto_tekst[3]); ?>&nbsp;eur</nobr></td>
		</tr>
	</tbody>
	</table>
</div>