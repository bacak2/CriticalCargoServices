<div class="layout">
<style>
	h4 { background: #eee; }
</style>

<table style="margin: 15px 0 12px 0" width="100%" cellpadding="0" cellspacing="0">
   <tr id="print">
			<td style="font-size: 12px; text-align:left;padding-right: 7px">
			<a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
			</td>
	</tr>
   <tr>
      <td width="50%" valign="middle">
<?php
$w = mysql_query("SELECT * FROM faktury_wystawiajacy");
if($wystaw = mysql_fetch_object($w))
{
   echo "<img src=\"images/logo-new-bw.png\" style=\"margin: 0 0 20px 10px;\"/>";
   if($faktura['firma_wystaw'] == 1){
      echo '<br />'.$Lang['TEL'].' +48 22 127 85 28';
	  echo '<br />fax: +48 22 127 85 28';
	  echo '<br />e-mail: office@critical-cs.com';
   }else{
      echo '<br />'.$Lang['TEL'].' +48 22 127 85 28';
	  echo '<br />fax: +48 22 127 85 28';
	  echo '<br />e-mail: office@critical-cs.com';
   }
   echo "<br />www.critical-cs.com";
}
?>
      </td>
      <td width="50%" valign="middle">
      <h1><u><?php echo $Lang['FAKTURA_VAT'] ?></u></h1>
      <h2><?php echo $Lang['NR']." ". $faktura['numer'] ?></h2>
      <h3>
      <?php

      echo $Lang['ORYGINA'];
      /*if($_GET['t'] == 'o')
      {
         echo 'ORYGINAŁ';
      }
      if($_GET['t'] == 'k')
      {
         echo 'KOPIA';
      }
      if($_GET['t'] == 'ko')
      {
         echo 'ORYGINAŁ / KOPIA';
      }
      if($_GET['t'] == 'p')
      {
         echo 'pro-forma';
      }
      if($_GET['t'] == 'd')
      {
         echo 'DUPLIKAT';
      }*/
      ?>
      </h3>
      </td>
   </tr>
   <tr><td colspan="2" style="font-size: 8px; height: 3px; border-bottom: 1px solid black">&nbsp;</td></tr>
</table>



<?php

echo "
<table style=\"margin: 0px 0 10px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"center\">". $Lang['DATA_WYSTAWIENIA'] ."</td>
      <td align=\"center\">". $Lang['MIEJSCE_WYSTAWIENIA'] ."</td>
      <td align=\"center\">". $Lang['DATA_SPRZEDAZY'] ."</td>
      <td align=\"center\">". $Lang['TERMIN_PLATNOSCI'] ."</td>
      <td align=\"center\">". $Lang['FORMA_PLATNOSCI'] ."</td>
   </tr>
   <tr>
      <td align=\"center\"><b>{$faktura['data_wystawienia']}</b></td>
      <td align=\"center\"><b>".($faktura['szablon_faktura'] == 'ENG' ? str_replace("Warszawa", "Warsaw", $faktura['miejsce_wystawienia']) : $faktura['miejsce_wystawienia'])."</b></td>
      <td align=\"center\"><b>{$faktura['data_sprzedazy']}</b></td>
      <td align=\"center\"><b>{$faktura['termin_platnosci']}</b></td>
      <td align=\"center\"><b>";
      if($faktura['szablon_faktura'] == 'ENG')
      echo $faktura['forma_en'];
      else
      echo $faktura['forma'];
      echo
      "</b></td>
   </tr>
</table>";

?>

<table style="margin: 27px 0 30px 0" width="100%" cellpadding="0" cellspacing="0">
   <tr>
      <td width="50%" valign="top">
         <h4><?php echo $Lang['SPRZEDAWCA'] ?></h4>
         <div class="dane_nabywcy">
         		<?php
					if($faktura['firma_wystaw'] == 1){
				?>
					Critical Cargo and Freight Services Sp. z o.o.<br /> 
al. Solidarności 115/2<br />
00-140 Warszawa, Poland<br />
NIP: PL 525-258-15-65
				<?php
					}else{
				?>
					Critical Cargo and Freight Services Sp. z o.o.<br />
al. Solidarności 115/2<br />
00-140 Warszawa, Poland<br />
NIP: PL 525-258-15-65
				<?php
					}
				?>
				<b>
						<?php
						
						
							echo "<br /><br />";
                                                        echo "IBAN PL";
                                                        echo "<br />";
                                                        echo "PLN: PL25 1020 1042 0000 8802 0310 0443<br />";
                                                        echo "EUR: PL73 1020 1042 0000 8902 0310 0500<br />";
							echo "SWIFT: BPKOPLPW<br />";
						
						
						
							/*echo "<br />".$Lang['NUMER_BANKU']."<br />";
							echo "Bank BPH S.A.<br />";
							echo "ul. Targowa 41 03-728 Warszawa<br />";
							if($faktura['szablon_faktura'] == 'ENG'){
								if($faktura['firma_wystaw'] == 1){
									echo "SWIFT: BPHKPLPK<br />";
									echo "IBAN PL: PL32106000760000330000640183<br>";
								}else{
                                                                    echo "<br />IBAN PL<br />";
                                                                    if($faktura['waluta'] == "USD"){
                                                                        echo "USD: PL54106000760000330000699442<br />";
                                                                    }else{
                                                                        echo "PLN: PL58106000760000320001361929<br />";
                                                                        echo "EUR: PL49106000760000330000656852<br />";
                                                                    }
                                                                    echo "SWIFT: BPHKPLPK<br />";
								}
							}else{
								if($faktura['firma_wystaw'] == 1){
									echo "04 1060 0076 0000 3260 0148 5014<br />";
                                                                        if($faktura['waluta'] == "EUR"){
                                                                            echo "Konto EUR:<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                            echo "IBAN PL: PL32106000760000330000640183<br>";
                                                                        }else if($faktura['waluta'] == "USD"){

                                                                        }
								}else{
                                                                        echo "Konto PLN: 58 1060 0076 0000 3200 0136 1929<br />";
                                                                        if($faktura['waluta'] == "EUR" || $faktura['waluta'] == "PLN"){
                                                                            echo "Konto EUR: PL49106000760000330000656852<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                        }else if($faktura['waluta'] == "USD"){
                                                                            echo "<br />";
                                                                            echo "Konto USD: PL54106000760000330000699442<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                        }
								}
							}*/
						?>
				</b>
         </div>
      </td>
      <td width="50%" valign="top">
         <h4><?php echo $Lang['NABYWCA']; ?></h4>
         <div class="dane_nabywcy">
<?php
$klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klienta']}'");
echo($klient['nazwa']); ?><br />
				<?php echo($klient['adres']); ?>,<br /> <?php echo($klient['kod_pocztowy']); ?> <?php echo($klient['miejscowosc']); ?><br />
				<?php echo $Lang['NIP']; ?> <?php echo ($klient['nip']); ?><br>
				<br />
         </div>
      </td>
   </tr>
   <tr>
   <td colspan="2"><br />
   <?php
      $uwagi = stripslashes($faktura['uwagi']);
      if($faktura['szablon_faktura'] == 'ENG')
      {
         $uwagi = str_replace('numer zlecenia klienta', 'Order No.', $uwagi);
      }
    echo $uwagi;
    ?> </td></tr>
</table>



<?php

echo "
<table style=\"margin: 10px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"center\" style=\"height: 30px; border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['LP'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\"><b>". $Lang['NAZWA'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['PKWIU_PKOB'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['ILOSC'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['JEDNOSTKA_MIARY'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['CENA_JEDNOSTKOWA'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>".$Lang['WARTOSC_SPRZEDAZY'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['VAT'] ."</b></td>
   </tr>";



$Pozycje = $this->Baza->GetRows($Zap);
$lp = 1;
foreach($Pozycje as $Pos)
{
   echo "
   <tr>
   <td align=\"center\" style=\"height: 20px;\">$lp</td>
   <td align=\"center\">";
   if($faktura['szablon_faktura'] == 'ENG')
   {
      echo str_replace('Wewnątrzwspólnotowa usługa spedycyjna', 'Intraeuropean Forwarding Service', $Pos['opis']);
   }
   else
   {
      echo $Pos['opis'];
   }
   echo "</td>
   <td align=\"center\">{$Pos['pkwiu']}</td>
   <td align=\"center\">{$Pos['ilosc']}</td>
   <td align=\"center\">".($faktura['szablon_faktura'] == 'ENG' ? str_replace("szt", "pcs", $Pos['jednostka']) : $Pos['jednostka'])."</td>
   <td align=\"center\">". Usefull::ZmienFormatKwoty($Pos['netto_jednostki']) ." <small>{$faktura['waluta']}</small></td>
   <td align=\"center\">".  Usefull::ZmienFormatKwoty($Pos['netto']) ."  <small>{$faktura['waluta']}</small></td>
   <td align=\"center\">{$Pos['vat']}".(!in_array(strtolower($Pos['vat']), array("np", "zw")) ? "%" : "")."</td>
   </tr>";
   $lp++;
}
echo "</table>";



echo "
<table style=\"margin: 30px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"right\" style=\"height: 30px;\">". $Lang['OGOLEM'] ."</td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['WARTOSC_NETTO']."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"130\"><b>". $Lang['KWOTA_VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['WARTOSC_BRUTTO'] ."</b></td>
   </tr>";


$z1 = "SELECT *,
       SUM(netto) as suma_netto,
       SUM(kwota_vat) as suma_kwot_vat,
       SUM(brutto) as suma_brutto
       FROM faktury_pozycje WHERE id_faktury = $ID GROUP BY vat DESC"; 
$w1 = mysql_query($z1);
$lp = 1;
$suma_netto = 0;
$suma_brutto = 0;
$suma_kwot_vat = 0;
while($pozycje = mysql_fetch_object($w1))
{
   $suma_brutto += $pozycje->suma_brutto;
   $suma_netto += $pozycje->suma_netto;
   $suma_kwot_vat += $pozycje->suma_kwot_vat;
   echo "
   <tr>
   <td align=\"center\" style=\"height: 20px;\"></td>
   <td align=\"center\">".  Usefull::ZmienFormatKwoty($pozycje->suma_netto) ." <small>{$faktura['waluta']}</small></td>
   <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : "")."</td>
   <td align=\"center\">".  Usefull::ZmienFormatKwoty($pozycje->suma_kwot_vat) ." <small>{$faktura['waluta']}</small></td>
   <td align=\"center\">".  Usefull::ZmienFormatKwoty($pozycje->suma_brutto) ." <small>{$faktura['waluta']}</small></td>
   </tr>";
}


echo "<tr>
   <td align=\"right\" style=\"height: 20px;\"><b>".$Lang['RAZEM'] ." </b></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">". Usefull::ZmienFormatKwoty($suma_netto)." <small>{$faktura['waluta']}</small></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">&nbsp;</td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">".  Usefull::ZmienFormatKwoty($suma_kwot_vat) ." <small>{$faktura['waluta']}</small></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">".  Usefull::ZmienFormatKwoty($suma_brutto) ." <small>{$faktura['waluta']}</small></td>
   </tr>";
echo "</table>";





echo "<table style=\"font-size: 16px; margin: 22px 0 20px 0; width: auto; float: right;\" cellpadding=\"0\" cellspacing=\"0\">";
if($faktura['status'] == 0){
   echo "<tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['DO_ZAPLATY'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($suma_brutto) ." <small>{$faktura['waluta']}</small></b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['SLOWNIE'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>";
   echo Usefull::KwotaSlownie($suma_brutto, $faktura['waluta'], $faktura['szablon_faktura']);
   echo "</b></td>
   </tr>";
if($faktura['wplacono'] > 0)
{
   $pozostalo = $suma_brutto - $faktura['wplacono'];
   echo "<tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />Wpłacono:</td>
      <td align=\"left\" style=\"font-size: 14px;\"><br /><br />&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($faktura['wplacono']) ." <small>{$faktura['waluta']}</small></b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\">Pozostało:</td>
      <td align=\"left\" style=\"font-size: 14px;\">&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($pozostalo) ." {$faktura['waluta']}</b>
      &nbsp;&nbsp;&nbsp;". Usefull::KwotaSlownie($pozostalo, $faktura['waluta'], $faktura['szablon_faktura']) ."</td>
   </tr>";
}
}else{
    echo "<tr>
       <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />&nbsp;</td>
      <td align=\"left\" style=\"font-size: 14px; height: 30px; font-weight: bold;\">".$Lang['OPLACONA']."</b></td>
   </tr>";
}
?>
</table>


<table  style="margin: 190px 0 0 0" width="100%" cellpadding="3" cellspacing="10">
		<tr>
			<td width="50%" style="border-bottom: 1px dotted black;">
			&nbsp;
			</td>
			<td>
			</td>
			<td width="50%" style="border-bottom: 1px dotted black;">
			&nbsp;
			</td>
		</tr>
		<?php
         $podpis_wystawcy = $this->Baza->GetValue("SELECT osoba FROM faktury_wystawiajacy LIMIT 1");
         if($podpis_wystawcy != '')
         {
            echo '<tr height="20" style="text-align: center; font-size: 12px;">
			         <td><b>'. $podpis_wystawcy .'
				        </b></td>
			            <td>
			            </td>
			         <td>
				  &nbsp;
			   </td>
		    </tr>';

         }
		?>
		<tr style="text-align: center; font-size: 8pt;">
			<td>
				<?php echo $Lang['WYSTAWIENIE_FAKTURY'] ?>
			</td>
			<td>
			</td>
			<td>
				<?php echo $Lang['ODBIOR_FAKTURY'] ?>
			</td>
		</tr>
	</table>
	<?php
	if($faktura['szablon_faktura'] == "PL"){
		echo "<br /><br /><br />
		<span style='font-size: 8pt;'>
		Zgodnie z art. 7 ustawy o terminach zapłaty w transakcjach handlowych,
		jeżeli dłużnik w określonym w umowie nie dokona zapłaty na rzecz
		wierzyciela, zobowiązany jest on do zapłaty wierzycielowi, bez odrębnego
		wezwania, odsetek w wysokości odsetek od zaległości
		podatkowych.<br />VAT NP - Reverse Charge  |  0 % - Art 83 ust. 1.3 ustawy o podatku od  
towarów i usług <br />
		Ta faktura jest także wezwaniem do zapłaty w rozumieniu art. 476 KC.</span>";
	}
?>

</div>