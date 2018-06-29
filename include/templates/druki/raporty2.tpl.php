<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>


<table  width="720" align="center"  border="0" style="border: 1px solid #888888" cellspacing="1" cellpadding="2">
<tr>
			<td colspan="3" style="text-align: left">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td colspan="4" style="font-size: 14pt; text-align: center; width: 300px">
				<b><br />Raport <?php echo $Naglowek; ?></b><br /><br />
<form action="" method="post">

<?php
echo "<div style=\"font-size: 11px\">";
UsefullBase::ShowWyborPrzedzialu($this->Baza);
echo "</div>";
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

if(!isset($_POST['start'])){
	$filtr_datowy = "termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31'";
        $filtr_datowy_morski = "data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31'";
}else{
	$filtr_datowy = "termin_zaladunku >= '{$_POST['start']}' AND termin_zaladunku <= '{$_POST['stop']}'";
        $filtr_datowy_morski = "data_zlecenia >= '{$_POST['start']}' AND data_zlecenia <= '{$_POST['stop']}'";
}
if($this->Uzytkownik->IsAdmin() == false){
	$warunek .= "id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
}

$DaneDoRaportu = array();
$z2 = "SELECT * FROM orderplus_zlecenie WHERE $warunek ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy";
$w2 = mysql_query($z2);
 while($zleconko = mysql_fetch_array($w2)){
 	$Key = ($Pole == "id_klient" ? $Klienci[$zleconko[$Pole]] : ($zleconko['sea_order_id'] > 0 ? "LCL" : $zleconko[$Pole]));
 	if(is_null($Key)){
 		$Key = 0;
 	}
 	if(!key_exists($Key,$DaneDoRaportu)){
 		$DaneDoRaportu[$Key]["ilosc_zlecen"] = 0;
 		$DaneDoRaportu[$Key]["suma_klient"] = 0;
 		#$DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
 		$DaneDoRaportu[$Key]["marza"] = 0;
 	}
 	$DaneDoRaportu[$Key]["ilosc_zlecen"]++;
	if($zleconko["waluta"] == "PLN"){
 		$DaneDoRaportu[$Key]["suma_klient"] += $zleconko["stawka_klient"];
 		#$DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
 		$marza = $zleconko["stawka_klient"] - $zleconko["stawka_przewoznik"];
 		$DaneDoRaportu[$Key]["marza"] += $marza;
	}
	else{
 		$DaneDoRaportu[$Key]["suma_klient"] += $zleconko["stawka_klient"]*$zleconko["kurs"];
 		#$DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
                $marza = ($zleconko["stawka_klient"]*$zleconko["kurs"]) - ($zleconko["stawka_przewoznik"]*$zleconko["kurs_przewoznik"]);
 		$DaneDoRaportu[$Key]["marza"] += $marza;
	}
 }
 #### Zlecenia morskie ####
 $zm2 = "SELECT * FROM orderplus_sea_orders WHERE $warunek ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_morski";
$wm2 = mysql_query($zm2);
$TrybKlient = false;
while($zleconko_morskie = mysql_fetch_array($wm2)){
    if($Pole == "id_klient"){
        $TrybKlient = true;
    }
          $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$zleconko_morskie['id_zlecenie']}'", "id_faktury");
          $FakKlienci = array();
          $PosMany = 0;
          foreach($Faktury as $FID => $DaneFak){
            if($Pole == "id_klient"){
                if(!in_array($DaneFak['id_klienta'], $FakKlienci)){
                    $Key = Usefull::SetKey($Elementy, $DaneFak['id_klienta']);
                    $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
                    $FakKlienci[] = $DaneFak['id_klienta'];
                }
            }
            $Pozycje = mysql_query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$FID'");
            while($Pos = mysql_fetch_array($Pozycje)){
                if($Waluty[$DaneFak['id_waluty']] == "PLN"){
                    $PosMany += $Pos['netto'];
                }else{
                    $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                }
            }
          }
          if(!$TrybKlient){
                $Key = Usefull::SetKey($Elementy, $zleconko_morskie['mode']);
                $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
          }
          if($Pole == "id_klient"){
                $FakKlienci = array_unique($FakKlienci);
                $IDDo = $FakKlienci[0];
          }else{
              $IDDo = $zleconko_morskie['mode'];
          }
            if(($TrybKlient && count($FakKlienci) == 1) || !$TrybKlient){
                      $Koszty = mysql_query("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$zleconko_morskie['id_zlecenie']}'");
                      $Kwota = 0;
                      while($KosztyRes = mysql_fetch_array($Koszty)){
                         $Kwota += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                      }
                    $DaneDoRaportu[$IDDo]["suma_klient"] += $PosMany;
                    #$DaneDoRaportu[$IDDo]["suma_przewoznik"] += $Kwota;
                    $marza = $PosMany - $Kwota;
                    $DaneDoRaportu[$IDDo]["marza"] += $marza;
            }
}
 $SumaObrotow = 0;
 $SumaMarzy = 0;
 $SumaZlecen = 0;
 foreach($DaneDoRaportu as $Dane){
 	$SumaObrotow += $Dane["suma_klient"];
 	$SumaMarzy += $Dane["marza"];
 	$SumaZlecen += $Dane["ilosc_zlecen"];
 }
?>
<tr>
	 <td valign="middle" bgcolor="#CECECE" align="center"><b>Lp.</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Ilość zleceń</b></td>
	 <td valign="middle" width="200" bgcolor="#CECECE" align="center"><b><?php echo $Kolumna; ?></b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Sprzedaż</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Marża</b></td>
     <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>% Marża</b></td>
     <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>% udział</b></td>
 </tr>
<?php
 $Elementy[0] = "nieprzypisane";
 foreach($Elementy as $ID => $Etykieta){
      echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". $lp ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["ilosc_zlecen"], 0)  ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">$Etykieta</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["suma_klient"], 2, ',', ' ')  ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["marza"], 2, ',', ' ') ."</td>";
      $Dzielnik = ($DaneDoRaportu[$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$ID]["marza"]*100)/$DaneDoRaportu[$ID]["suma_klient"]);
      $Dzielnik2 = ($SumaMarzy == 0 ? 0 : ($DaneDoRaportu[$ID]["marza"]*100)/$SumaMarzy);
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($Dzielnik, 2, ',', ' ')  ." %</td>";
	  echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($Dzielnik2, 2, ',', ' ')  ." %</td></tr>";
      $lp++;
   }
$kolor = '#cccccc';
echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">".number_format($SumaZlecen, 0)."</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaObrotow, 2, ',', ' ')  ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaMarzy, 2, ',', ' ') ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td></tr>";

?>


</table>
</td>
</tr>
</table>