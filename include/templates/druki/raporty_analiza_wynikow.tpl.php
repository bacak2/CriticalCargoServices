<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>


<table  width="720" align="center"  border="0" style="border: 1px solid #888888" cellspacing="1" cellpadding="2">
<tr>
			<td colspan="3" style="text-align: left">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td colspan="3" style="font-size: 14pt; text-align: center; width: 300px">
				<b><br />Analiza wyników</b><br /><br />
<form action="" method="post">

<?php
$Form = new FormularzSimple();
$Rodzaj = (isset($_POST['rodzaj']) ? $_POST['rodzaj'] : 0);
echo "<div style=\"font-size: 11px\">";
	echo "Rodzaj: ";
	$Rodzaje = array(0 => "po kliencie", 1 => "po Oddziale");
	if($_SESSION['uprawnienia_id'] == 1){
		$Rodzaje[2] = "po wynikach całej firmy";
	}
	$Form->PoleSelect("rodzaj", $Rodzaje, $Rodzaj, "onchange='this.form.submit();'");
	if($Rodzaj == 0){
		echo "<br /><br />Klient: ";
		$Klienci = UsefullBase::GetKlienciActive($this->Baza);
		$KlientId = (isset($_POST["klient"]) ? $_POST["klient"] : Usefull::GetFirstKey($Klienci));
		$Form->PoleSelect("klient", $Klienci, $KlientId, "style='width: 300px;' onchange='this.form.submit();'");
		$warunek = "id_klient = '$KlientId' AND ";
                $warunek_morski = "";
                $warunek_lotniczy = "";
	}else if($Rodzaj == 1){
		if($_SESSION['uprawnienia_id'] == 1){
			echo "<br /><br />Oddział: ";
			$Oddzialy = UsefullBase::GetOddzialy($this->Baza);
			$OddzialID = (isset($_POST['oddzial']) ? $_POST['oddzial'] : 2);
                        unset($Oddzialy[9]);
                        unset($Oddzialy[4]);
                        $Oddzialy[9] = "WAW AIR";
                        $Oddzialy[4] = "GDY SEA";
                        $Oddzialy[-4] = "GDY AIR";
			$Form->PoleSelect("oddzial", $Oddzialy, $OddzialID, "onchange='this.form.submit();'");
		}else{
			$OddzialID = $_SESSION['id_oddzial'];
		}
		$warunek = "id_oddzial = '$OddzialID' AND ";
                $warunek_morski = "id_oddzial = '$OddzialID' AND ";
                $warunek_lotniczy = "id_oddzial = '".($OddzialID * -1)."' AND ";
	}else if($Rodzaj == 2){
		$warunek = "";
                $warunek_morski = "";
                $warunek_lotniczy = "";
	}
        echo "<br /><br />Od: ";
        $Termin = (isset($_POST['termin']) ? $_POST['termin'] : date("Y-m", strtotime("-12 months")));
        $Terminy = array();
        $StartDate = "2007-09-01";
        $EndDate = date("Y-m-d");
        for($Date = $EndDate; $Date >= $StartDate; $Date = $NewDate){
            $Option = date("Y-m", strtotime($Date));
            $Terminy[$Option] = $Option;
            $NewDate = date("Y-m-d", strtotime($Date."-1 month"));
        }
        $Form->PoleSelect("termin", $Terminy, $Termin, "onchange='this.form.submit();'");
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

$Start = $Termin;
$Start .= "-01";
$filtr_datowy = "data_zlecenia >= '$Start'";
$filtr_datowy_morski = "data_zlecenia >= '$Start'";

$DaneDoRaportu = array();
$z2 = "SELECT * FROM orderplus_zlecenie WHERE $warunek ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy ORDER BY data_zlecenia ASC";
$w2 = mysql_query($z2);
 while($zleconko = mysql_fetch_array($w2)){
 	$Key = date("Y-m", strtotime($zleconko["data_zlecenia"]));
 	if(!key_exists($Key,$DaneDoRaportu)){
 		$DaneDoRaportu[$Key]["ilosc_zlecen"] = 0;
 		$DaneDoRaportu[$Key]["suma_klient"] = 0;
 		$DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
 		$DaneDoRaportu[$Key]["marza"] = 0;
 	}
 	$DaneDoRaportu[$Key]["ilosc_zlecen"]++;
	if($zleconko["waluta"] == "PLN"){
 		$DaneDoRaportu[$Key]["suma_klient"] += $zleconko["stawka_klient"];
 		$DaneDoRaportu[$Key]["suma_przewoznik"] += $zleconko["stawka_przewoznik"];
 		$marza = $zleconko["stawka_klient"] - $zleconko["stawka_przewoznik"];
 		$DaneDoRaportu[$Key]["marza"] += $marza;
	}
	else{
 		$DaneDoRaportu[$Key]["suma_klient"] += $zleconko["stawka_klient"]*$zleconko["kurs"];
 		$DaneDoRaportu[$Key]["suma_przewoznik"] += $zleconko["stawka_przewoznik"]*$zleconko["kurs_przewoznik"];
                $marza = ($zleconko["stawka_klient"]*$zleconko["kurs"]) - ($zleconko["stawka_przewoznik"]*$zleconko["kurs_przewoznik"]);
 		$DaneDoRaportu[$Key]["marza"] += $marza;
	}
 }
 #### Zlecenia morskie ####
 $zm2 = "SELECT * FROM orderplus_sea_orders WHERE $warunek_morski ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_morski ORDER BY data_zlecenia";
$wm2 = mysql_query($zm2);
$TrybKlient = false;
while($zleconko_morskie = mysql_fetch_array($wm2)){
    $Key = date("Y-m", strtotime($zleconko_morskie["data_zlecenia"]));
    if($Rodzaj == 0){
        $TrybKlient = true;
    }
          $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$zleconko_morskie['id_zlecenie']}'", "id_faktury");
          $FakKlienci = array();
          $PosMany = 0;
          foreach($Faktury as $FID => $DaneFak){
            if($Rodzaj == 0){
                if(!in_array($DaneFak['id_klienta'], $FakKlienci)){
                    if($DaneFak['id_klienta'] == $KlientId){
                        $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
                    }
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
                $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
          }
          if($Rodzaj == 0){
                $FakKlienci = array_unique($FakKlienci);
          }
          $IDDo = $Key;
            if(($TrybKlient && count($FakKlienci) == 1 && $FakKlienci[0] == $KlientId) || !$TrybKlient){
                  $Koszty = mysql_query("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$zleconko_morskie['id_zlecenie']}'");
                  $Kwota = 0;
                  while($KosztyRes = mysql_fetch_array($Koszty)){
                     $Kwota += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                  }
                $DaneDoRaportu[$IDDo]["suma_klient"] += $PosMany;
                $DaneDoRaportu[$IDDo]["suma_przewoznik"] += $Kwota;
                $marza = $PosMany - $Kwota;
                $DaneDoRaportu[$IDDo]["marza"] += $marza;
            }
}

#### Zlecenia lotnicze ####
 $zm2 = "SELECT * FROM orderplus_air_orders WHERE $warunek_lotniczy ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_morski ORDER BY data_zlecenia";
$wm2 = mysql_query($zm2);
$TrybKlient = false;
while($zleconko_lotnicze = mysql_fetch_array($wm2)){
    $Key = date("Y-m", strtotime($zleconko_lotnicze["data_zlecenia"]));
    if($Rodzaj == 0){
        $TrybKlient = true;
    }
          $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_air_orders_faktury WHERE id_zlecenia = '{$zleconko_lotnicze['id_zlecenie']}'", "id_faktury");
          $FakKlienci = array();
          $PosMany = 0;
          foreach($Faktury as $FID => $DaneFak){
            if($Rodzaj == 0){
                if(!in_array($DaneFak['id_klienta'], $FakKlienci)){
                    if($DaneFak['id_klienta'] == $KlientId){
                        $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
                    }
                    $FakKlienci[] = $DaneFak['id_klienta'];
                }
            }
            $Pozycje = mysql_query("SELECT * FROM orderplus_air_orders_faktury_pozycje WHERE id_faktury = '$FID'");
            while($Pos = mysql_fetch_array($Pozycje)){
                if($Waluty[$DaneFak['id_waluty']] == "PLN"){
                    $PosMany += $Pos['netto'];
                }else{
                    $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                }
            }
          }
          if(!$TrybKlient){
                $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
          }
          if($Rodzaj == 0){
                $FakKlienci = array_unique($FakKlienci);
          }
          $IDDo = $Key;
            if(($TrybKlient && count($FakKlienci) == 1 && $FakKlienci[0] == $KlientId) || !$TrybKlient){
                  $Koszty = mysql_query("SELECT * FROM orderplus_air_orders_koszty WHERE id_zlecenie = '{$zleconko_lotnicze['id_zlecenie']}'");
                  $Kwota = 0;
                  while($KosztyRes = mysql_fetch_array($Koszty)){
                     $Kwota += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                  }
                $DaneDoRaportu[$IDDo]["suma_klient"] += $PosMany;
                $DaneDoRaportu[$IDDo]["suma_przewoznik"] += $Kwota;
                $marza = $PosMany - $Kwota;
                $DaneDoRaportu[$IDDo]["marza"] += $marza;
            }
}
 $SumaObrotow = 0;
 $SumaPrzewoznik = 0;
 $SumaMarzy = 0;
 $SumaIlosciZlecen = 0;
 foreach($DaneDoRaportu as $Dane){
 	$SumaIlosciZlecen += $Dane["ilosc_zlecen"];
 	$SumaObrotow += $Dane["suma_klient"];
 	$SumaPrzewoznik += $Dane["suma_przewoznik"];
 	$SumaMarzy += $Dane["marza"];
 }
?>
<tr>
	 <td valign="middle" width="200" bgcolor="#CECECE" align="center"><b>Miesiąc</b></td>
	 <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>Ilość zleceń</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Sprzedaż</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Koszt</b></td>
     <td valign="middle" width="160" bgcolor="#CECECE" align="center"><b>Marża</b></td>
     <td valign="middle" width="100" bgcolor="#CECECE" align="center"><b>% Marża</b></td>
 </tr>
<?php
for($DataCheck = $Start; $DataCheck < $EndDate; $DataCheck = $NewDataCheck){
	  $ID = date("Y-m", strtotime($DataCheck));
	  $Etykieta = date("m.Y", strtotime($DataCheck));
      echo "<tr>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">$Etykieta</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["ilosc_zlecen"], 0, ","," ")  ."</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["suma_klient"], 2, ',', ' ')  ."</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["suma_przewoznik"], 2, ',', ' ')  ."</td>";
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["marza"], 2, ',', ' ') ."</td>";
	      $Dzielnik = ($DaneDoRaportu[$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$ID]["marza"]*100)/$DaneDoRaportu[$ID]["suma_klient"]);
	      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($Dzielnik, 2, ',', ' ')  ." %</td>";
      echo "</tr>\n";
      $NewDataCheck = date("Y-m-d", strtotime($DataCheck."+1 month"));
}
$kolor = '#cccccc';
echo "<tr>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaIlosciZlecen, 0, ","," ")  ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaObrotow, 2, ',', ' ')  ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaPrzewoznik, 2, ',', ' ') ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaMarzy, 2, ',', ' ') ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td></tr>";

?>


</table>
</td>
</tr>
</table>