<table align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr>
<td>


<table  align="center"  border="0" style="width: 1240px;" cellspacing="0" cellpadding="2">
<tr>
			<td style="width: 50%; text-align: left">
				<img src="images/logo-new.png" alt="Logo" />
			</td>
			<td style="width: 50%; font-size: 14pt; text-align: center; width: 300px"> 
				<b><br />Analiza wyników</b><br /><br />
<form name="analiza_wynikow" action="" method="post">

<?php
$Form = new FormularzSimple();
$Rodzaj = (isset($_POST['rodzaj']) ? $_POST['rodzaj'] : 0);
echo "<div style=\"font-size: 11px\">";
	echo "Rodzaj: ";
        $Rodzaje = array();
        $Rodzaje[0] = "wszyscy klienci";
        $Rodzaje[1] = "klienci wg. oddziału";
        $Rodzaje[2] = "klienci wg. spedytora";
	$Form->PoleSelect("rodzaj", $Rodzaje, $Rodzaj, "onchange='this.form.submit();'");
	if($Rodzaj == 1){
            echo "<br /><br />Oddział: ";
            $Form->PoleSelect("oddzial", $Oddzialy, $OddzialID);
	}else if($Rodzaj == 2){
           echo "<br /><br />spedytor:";
           $Form->PoleSelect("spedid", $Spedytorzy, $SpedID, "style='font-size: 11px;'");
           echo "<br /><br />\n";
	}
        echo "<br /><br />Okres: ";
        $Terminy['miesieczny'] = "Miesięczny";
        $Terminy['tygodniowy'] = "Tygodniowy";
        $Terminy['dzienny'] = "Dzienny";
        $Form->PoleSelect("rodzaj_termin", $Terminy, $Termin, "id='rodzaj_termin' onchange='this.form.submit();'");
        ?>
            <div id="miesiac_od" style='display: <?php echo ($Termin == "miesieczny" || $Termin == "dzienny" ? "inline" : "none"); ?>'>
                <?php echo ($Termin == "miesieczny" ? "od" : "w miesiącu")." "; $Form->PoleSelect("miesiac_1", $Miesiace, $MiesiacOd); ?>
            </div>
            <div id="tydzien_od" style='display: <?php echo ($Termin == "tygodniowy" ? "inline" : "none"); ?>'>
                od <?php $Form->PoleSelect("tydzien_1", $Tygodnie, $TydzienOd); ?>
            </div>
            <div id="rok_od" style='display: inline'>
                <?php $Form->PoleSelect("rok_1", $Lata, $RokOd); ?>
            </div>
            <div id="miesiac_do" style='display: <?php echo ($Termin == "miesieczny" ? "inline" : "none"); ?>'>
                do <?php $Form->PoleSelect("miesiac_2", $Miesiace, $MiesiacDo); ?> 
            </div>
            <div id="tydzien_do" style='display: <?php echo ($Termin == "tygodniowy" ? "inline" : "none"); ?>'>
                do <?php $Form->PoleSelect("tydzien_2", $Tygodnie, $TydzienDo); ?>
            </div>
            <div id="rok_do" style='display: <?php echo ($Termin == "miesieczny" || $Termin == "tygodniowy" ? "inline" : "none"); ?>'>
                <?php $Form->PoleSelect("rok_2", $Lata, $RokDo); ?>
            </div>
    </div>
    <br /><input type="button" name="generuj_raport" value="Generuj" onclick="SubmitForm(document.analiza_wynikow, '/raporty_analiza_wynikow<?php echo (isset($AirSea) && $AirSea ? "_airsea" : "") ?>.php');" />&nbsp;<input type="button" name="generuj_raport_xls" value="Pobierz w formie XLS" onclick="SubmitForm(document.analiza_wynikow, '/raporty_analiza_wynikow<?php echo (isset($AirSea) && $AirSea ? "_airsea" : "") ?>_xls.php');" /><br />

</form>
        </td>
    </tr>
</table>
    <table align="center"  border="0" style="border: 1px solid #888888" cellspacing="1" cellpadding="8">
<?php 
$lp = 1;
$kolor = 'white';
 ?>
 <tr>
    <td valign="middle" bgcolor="#CECECE" align="center" style="width: 30px;">&nbsp;</td>
    <td valign="middle" bgcolor="#CECECE" align="center" style="width: 250px !important;">&nbsp;</td>
 <?php
 $tablica_key = array();
 if($Termin == "miesieczny"){
    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
        $month_key = date("m", strtotime($date_check));
        $rok_key = date("Y", strtotime($date_check));
        ?><td valign="middle" colspan="5" bgcolor="#CECECE" align="center" style="border-left: 1px solid #000; border-right: 1px solid #000;"><?php echo $Miesiace[$month_key]." $rok_key"; ?></td><?php
        $new_date_check = date("Y-m-d", strtotime($date_check." +1 months"));
        $tablica_key[] = "$rok_key-$month_key";
    }
 }
 if($Termin == "tygodniowy"){
    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
        $yearday = date("z", strtotime($date_check));
        $week_key = ceil(($yearday+1) / 7);
        $rok_key = date("Y", strtotime($date_check));
        ?><td valign="middle" colspan="5" bgcolor="#CECECE" align="center" style="border-left: 1px solid #000; border-right: 1px solid #000;"><?php echo $week_key; ?> tydzień <?php echo $rok_key; ?></td><?php
        $new_date_check = date("Y-m-d", strtotime($date_check." +7 days"));
        $tablica_key[] = "$rok_key-$week_key";
    }
 }
 if($Termin == "dzienny"){
    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
        ?><td valign="middle" colspan="5" bgcolor="#CECECE" align="center" style="border-left: 1px solid #000; border-right: 1px solid #000;"><?php echo $date_check; ?></td><?php
        $new_date_check = date("Y-m-d", strtotime($date_check." +1 days"));
        $tablica_key[] = "$date_check";
    }
 }
?>
</tr>
<tr>
     <td valign="middle" bgcolor="#CECECE" align="center" style="width: 30px"><b>Lp.</b></td>
     <td valign="middle" bgcolor="#CECECE" align="center" style="width: 250px !important;"><b>Nazwa klienta</b></td>
     <?php
        foreach($tablica_key as $keys){
     ?>
     <td valign="middle" width="70" bgcolor="#CECECE" align="center" style="border-left: 1px solid #000; "><b>Ilość zleceń</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Sprzedaż</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Koszt</b></td>
     <td valign="middle" width="120" bgcolor="#CECECE" align="center"><b>Marża</b></td>
     <td valign="middle" width="80" bgcolor="#CECECE" align="center" style="border-right: 1px solid #000;"><b>% Marża</b></td>
     <?php
        }
     ?>
 </tr>
<?php
$lp = 1;
foreach($klienci as $klient_id => $klient_name){
      echo "<tr>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\" style='width: 30px;'> $lp </td>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\" style='width: 250px; display: block;'> <div style='display: block; min-width: 200px;'>$klient_name</div></td>";
          foreach($tablica_key as $ID){
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" style='border-left: 1px solid #000;'>". number_format($DaneDoRaportu[$klient_id][$ID]["ilosc_zlecen"], 0, ","," ")  ."</td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($DaneDoRaportu[$klient_id][$ID]["suma_klient"], 2, ',', ' ')  ."</nobr></td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($DaneDoRaportu[$klient_id][$ID]["suma_przewoznik"], 2, ',', ' ')  ."</nobr></td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($DaneDoRaportu[$klient_id][$ID]["marza"], 2, ',', ' ') ."</nobr></td>";
                $Dzielnik = ($DaneDoRaportu[$klient_id][$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$klient_id][$ID]["marza"]*100)/$DaneDoRaportu[$klient_id][$ID]["suma_klient"]);
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" style='border-right: 1px solid #000;'><nobr>". number_format($Dzielnik, 2, ',', ' ')  ." %</nobr></td>";
          }
      echo "</tr>\n";
      $lp++;
}
if(count($UsunieciKlienci) > 0){
echo "<tr>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\" style='width: 30px;'> $lp </td>";
          echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\" style='width: 250px; display: block;'> <div style='display: block; min-width: 200px;'>Usunięci klienci</div></td>";
          foreach($tablica_key as $ID){
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" style='border-left: 1px solid #000;'>". number_format($UsunieciKlienci[$ID]["ilosc_zlecen"], 0, ","," ")  ."</td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($UsunieciKlienci[$ID]["suma_klient"], 2, ',', ' ')  ."</nobr></td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($UsunieciKlienci[$ID]["suma_przewoznik"], 2, ',', ' ')  ."</nobr></td>";
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><nobr>". number_format($UsunieciKlienci[$ID]["marza"], 2, ',', ' ') ."</nobr></td>";
                $Dzielnik = ($UsunieciKlienci[$ID]["suma_klient"] == 0 ? 0 : ($UsunieciKlienci[$ID]["marza"]*100)/$UsunieciKlienci[$ID]["suma_klient"]);
                echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\" style='border-right: 1px solid #000;'><nobr>". number_format($Dzielnik, 2, ',', ' ')  ." %</nobr></td>";
          }
      echo "</tr>\n";
      $lp++;
}
$kolor = '#cccccc';
echo "<tr>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
foreach($tablica_key as $ID){
    echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b><nobr>". number_format($SumaIlosciZlecen[$ID], 0, ","," ")  ."</b></nobr></td>";
    echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b><nobr>". number_format($SumaObrotow[$ID], 2, ',', ' ')  ."</b></nobr></td>";
    echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b><nobr>". number_format($SumaPrzewoznik[$ID], 2, ',', ' ') ."</b></nobr></td>";
    echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"right\"><b><nobr>". number_format($SumaMarzy[$ID], 2, ',', ' ') ."</b></nobr></td>";
    echo "<td valign=\"middle\" bgcolor=\"$kolor\" align=\"center\">&nbsp;</td>";
}

?>
    </tr>


</table>
</td>
</tr>
</table>