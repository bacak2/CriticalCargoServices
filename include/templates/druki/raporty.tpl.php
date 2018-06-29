<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>


<table  width="720" align="center"  border="0" style="border: 1px solid #888888" cellspacing="1" cellpadding="2">
<tr>
			<td colspan="4" style="text-align: left">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td colspan="3" style="font-size: 14pt; text-align: center; width: 300px">
				<b><br />Raport

<?php

if($tryb == 'klient' || $tryb == "klientmojatabela")      echo 'wg klientów';
if($tryb == 'przewoznik')  echo 'wg przewoźników';
if($tryb == 'spedytor')    echo 'wg spedytorów';
if($tryb == 'oddzial')     echo 'wg oddziałów';
if($tryb == 'klientnaspedytora')    echo 'wg klientów na spedytora';

?>
</b><br /><br />
<form action="" method="post">

<?php
$Form = new FormularzSimple();
echo "<div style=\"font-size: 11px\">";
$warunek = "";
if($tryb == 'klientnaspedytora')
{
   echo "spedytor:";
   $Spedytorzy = $this->Baza->GetOptions("SELECT id_uzytkownik, CONCAT(login,' (',imie,' ',nazwisko,')') as nazwa FROM orderplus_uzytkownik".($this->Uzytkownik->IsAdmin() ? "" : " WHERE id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") ")." ORDER BY login ASC");
   $SpedID = (isset($_POST['spedid']) ? $_POST['spedid'] :  Usefull::GetFirstKey($Spedytorzy));
   $Form->PoleSelect("spedid", $Spedytorzy, $SpedID, "style='font-size: 11px;'");
   echo "<br /><br />\n";
   $warunek = "id_uzytkownik = '$SpedID' AND ";
}
if($tryb == 'klientmojatabela'){ 
	$InniUserzy = array($_SESSION['id_uzytkownik']);
	$Userzy = array();
	$UserzyFiltr = array();
	$this->Baza->Query("SELECT id_uzytkownik, imie, nazwisko, blokada FROM orderplus_uzytkownik WHERE id_oddzial = '{$_SESSION['id_oddzial']}'");
	while($InniUserzyW = $this->Baza->GetRow()){
		$InniUserzy[] = $InniUserzyW['id_uzytkownik'];
		$Userzy[$InniUserzyW['id_uzytkownik']] = $InniUserzyW['imie']." ".$InniUserzyW['nazwisko'];
		if($InniUserzyW['blokada'] == 0){
			$UserzyFiltr[$InniUserzyW['id_uzytkownik']] = $InniUserzyW['imie']." ".$InniUserzyW['nazwisko'];
		}
	}
   $warunek = "id_uzytkownik IN(".(implode(",",$InniUserzy)).") AND ";
}
UsefullBase::ShowWyborPrzedzialu($this->Baza);
echo "</div>";
?>

</form>
				</td>
				</tr>
 <tr>
	 <td valign="middle" bgcolor="#CECECE" align="center"><b>Lp.</b></td>
	 <td valign="middle" bgcolor="#CECECE" align="center"><b>
<?php

if($tryb == 'klient' || $tryb == 'klientmojatabela')      echo 'Klient';
if($tryb == 'przewoznik')  echo 'Przewoźnik';
if($tryb == 'spedytor')    echo 'Spedytor';
if($tryb == 'oddzial')     echo 'Oddział';
if($tryb == 'klientnaspedytora')     echo 'Klient';
if($_GET['dev'] == "dev"){
    $Spedytorzy = $this->Baza->GetOptions("SELECT id_uzytkownik, CONCAT(login,' (',imie,' ',nazwisko,')') as nazwa FROM orderplus_uzytkownik".($this->Uzytkownik->IsAdmin() ? "" : " WHERE id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") ")." ORDER BY login ASC");
}
?>
</b></td>
     <td valign="middle" width="30" bgcolor="#CECECE" align="center"><b>Ilość zleceń</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Sprzedaż</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Koszty</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Marża</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Marża %</b></td>
     <?php
        if($_GET['dev'] == "dev"){
          foreach($Spedytorzy as $UserID => $UserName){
              ?><td valign="middle" width="120" bgcolor="#CECECE" align="center"><b><?php echo $UserName; ?></b></td><?php
          }
        }
     ?>
 </tr>



<?php

$lp = 1;
$kolor = 'white';

$Elementy[0] = "nie przypisano";
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
 	$Key = (isset($Elementy[$zleconko[$PoleID]]) ? $zleconko[$PoleID] : 0);
 	if(is_null($Key)){
 		$Key = 0;
 	}
 	if(!key_exists($Key,$DaneDoRaportu)){
 		$DaneDoRaportu[$Key]["ilosc_zlecen"] = 0;
                $DaneDoRaportu[$Key]["ilosc_zlecen_by_spedytor"] = array();
 		$DaneDoRaportu[$Key]["suma_klient"] = 0;
 		$DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
 		$DaneDoRaportu[$Key]["marza"] = 0;
 	}
 	$DaneDoRaportu[$Key]["ilosc_zlecen"]++;
        $DaneDoRaportu[$Key]["ilosc_zlecen_by_spedytor"][$zleconko['id_uzytkownik']]++;
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
 $zm2 = "SELECT * FROM orderplus_sea_orders WHERE $warunek ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_morski";
$wm2 = mysql_query($zm2);
$TrybKlient = false;
while($zleconko_morskie = mysql_fetch_array($wm2)){
    if($tryb == 'klient' || $tryb == "klientmojatabela" || $tryb == 'klientnaspedytora'){
        $TrybKlient = true;
    }
          $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$zleconko_morskie['id_zlecenie']}'", "id_faktury");
          $FakKlienci = array();
          $PosMany = 0;
          foreach($Faktury as $FID => $DaneFak){
            if($tryb == 'klient' || $tryb == "klientmojatabela" || $tryb == 'klientnaspedytora'){
                if(!in_array($DaneFak['id_klienta'], $FakKlienci)){
                    $Key = Usefull::SetKey($Elementy, $DaneFak['id_klienta']);
                    $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
                    $FakKlienci[] = $DaneFak['id_klienta'];
                    $DaneDoRaportu[$DaneFak['id_klienta']]['ilosc_zlecen_by_spedytor'][$zleconko_morskie['id_uzytkownik']]++;
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
                $Key = Usefull::SetKey($Elementy, $zleconko_morskie[$PoleMorskieID]);
                $DaneDoRaportu = $this->AddToDoRaportu($DaneDoRaportu, $Key);
          }
          if($tryb == 'klient' || $tryb == "klientmojatabela" || $tryb == 'klientnaspedytora'){
                $FakKlienci = array_unique($FakKlienci);
                $IDDo = $FakKlienci[0];
          }else{
              $IDDo = $zleconko_morskie[$PoleMorskieID];
          }
            if(($TrybKlient && count($FakKlienci) == 1) || !$TrybKlient){
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

$SumaObrotow = 0;
$SumaKosztow = 0;
$SumaMarzy = 0;
$SumaZlecen = 0;
$SumaZlecenByUser = array();
foreach($DaneDoRaportu as $Dane){
 	$SumaObrotow += $Dane["suma_klient"];
 	$SumaKosztow += $Dane["suma_przewoznik"];
 	$SumaMarzy += $Dane["marza"];
 	$SumaZlecen += $Dane["ilosc_zlecen"];
}
 foreach($Elementy as $ID => $Etykieta){
 	if(key_exists($ID, $DaneDoRaportu) && $DaneDoRaportu[$ID]["ilosc_zlecen"] > 0){
      echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". $lp ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">$Etykieta</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">{$DaneDoRaportu[$ID]["ilosc_zlecen"]}</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["suma_klient"], 2, ',', ' ')  ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["suma_przewoznik"], 2, ',', ' ') ."</td>";
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]["marza"], 2, ',', ' ')  ."</td>";
      $Dzielnik = ($DaneDoRaportu[$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$ID]["marza"]*100)/$DaneDoRaportu[$ID]["suma_klient"]);
      echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($Dzielnik, 2, ',', ' ')  ." %</td>";
      if($_GET['dev'] == "dev"){
          foreach($Spedytorzy as $UserID => $UserName){
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($DaneDoRaportu[$ID]['ilosc_zlecen_by_spedytor'][$UserID], 0, ',', ' ')."</td>";
                $SumaZlecenByUser[$UserID] += $DaneDoRaportu[$ID]['ilosc_zlecen_by_spedytor'][$UserID];
          }
      }
      echo "</tr>";
      $lp++;
 	}
 }

$kolor = '#cccccc';
echo "<tr><td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>$SumaZlecen</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaObrotow, 2, ',', ' ')  ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaKosztow, 2, ',', ' ') ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\"><b>". number_format($SumaMarzy, 2, ',', ' ')  ."</b></td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
if($_GET['dev'] == "dev"){
          foreach($Spedytorzy as $UserID => $UserName){
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">". number_format($SumaZlecenByUser[$UserID], 0, ',', ' ')."</td>";
          }
      }
?>
</tr>
</table>
</td>
</tr>
</table>