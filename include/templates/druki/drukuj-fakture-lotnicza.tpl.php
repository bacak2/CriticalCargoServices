<div class="layout">


<table style="margin: 15px 0 12px 0" width="100%" cellpadding="5" cellspacing="0">
   <tr id="print">
			<td style="font-size: 12px; text-align:left;padding-right: 7px">
			<a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
			</td>
	</tr>
   <tr>
      <td width="28%" valign="top">
          <h1><u><?php echo $Lang['FAKTURA_VAT'] ?></u></h1>
          <h2><?php echo $Lang['NR']." ". $faktura['numer'] ?></h2>
          <h3 style="text-align: left;">
          <?php
            echo $Lang['ORYGINA'];
          ?>
          </h3>
      </td>
      <td>
          <table cellpadding="3" cellspacing="0" border="0">
              <tr>
                  <td><b><?php echo $Lang['DATA_WYSTAWIENIA']; ?>:</b></td>
                  <td><?php echo $faktura['data_wystawienia']; ?></td>
              </tr>
              <tr>
                  <td><b><?php echo $Lang['MIEJSCE_WYSTAWIENIA']; ?>:</b></td>
                  <td><?php echo $faktura['miejsce_wystawienia']; ?></td>
              </tr>
              <tr>
                  <td><b><?php echo $Lang['DATA_SPRZEDAZY']; ?>:</b></td>
                  <td><?php echo $faktura['data_sprzedazy']; ?></td>
              </tr>
              <tr>
                  <td><b><?php echo $Lang['TERMIN_PLATNOSCI']; ?>:</b></td>
                  <td><?php echo $faktura['termin_platnosci']; ?></td>
              </tr>
              <tr>
                  <td><b><?php echo $Lang['FORMA_PLATNOSCI']; ?>:</b></td>
                  <td><?php echo $FormyPlatnosci[$faktura['id_formy']]; ?></td>
              </tr>
          </table>
      </td>
      <td width="34%" valign="top">
          <img src="images/logo-faktury.jpg" style="margin-left: 5px;" />
      </td>
   </tr>
   <tr><td colspan="3" style="font-size: 8px; height: 3px; border-bottom: 1px solid black">&nbsp;</td></tr>
</table>
<table style="margin: 27px 0 5px 0" width="100%" cellpadding="5" cellspacing="0">
   <tr>
      <td width="50%" valign="top">
         <h4><?php echo $Lang['SPRZEDAWCA'] ?></h4>
         <div class="dane_nabywcy">
             Critical Cargo and Freight Services sp. z o. o.<br />
				ul. Solidarności 115/2<br />
				00-140 Warszawa<br />
            POLAND, <?php echo $Lang['NIP']; ?> PL5252581565<br />
            <b>
            <?php
                    if($faktura['id_faktury'] > 230){
                        $Bank = array('name' => 'RAIFFEISEN BANK POLSKA S.A.', 'PLN' => '33 1750 0009 0000 0000 2298 4136', 'EUR' => '42 1750 0009 0000 0000 2298 4168', 'USD' => '14 1750 0009 0000 0000 2298 4187', 'swift' => 'RCBWPLPWXXX');
                    }else{
                        $Bank = array('name' => 'BZ WBK', 'PLN' => '60 1090 1043 0000 0001 1726 6466', 'EUR' => '32 1090 1043 0000 0001 1726 6776', 'USD' => '40 1090 1043 0000 0001 1885 5686', 'swift' => 'WBKPPLPP');
                    }
                    echo "<br />".$Lang['NUMER_BANKU']."<br />";
                    echo "{$Bank['name']}<br />";
                    //echo "ul. Targowa 41 03-728 Warszawa<br />";
                    if($faktura['szablon_faktura'] == 'ENG'){
                        echo "<br />IBAN PL<br />";
                        if($Waluty[$faktura['id_waluty']] == "USD"){
                            echo "Account for payments in USD:</b><br />";
                            echo "{$Bank['USD']}<br /><b>";
                        }else{
                            echo "Account for payments in PLN:</b><br />";
                            echo "{$Bank['PLN']}<br />";
                            echo "<b>Account for payments in EUR:</b><br />";
                            echo "{$Bank['EUR']}<br /><b>";
                        }
                        echo "SWIFT: {$Bank['swift']}<br />";
                    }else{
                        echo "Konto PLN:</b><br /> {$Bank['PLN']}<br /><b>";
                        if($Waluty[$faktura['id_waluty']] == "EUR"){
                            echo "Konto EUR:</b><br /> {$Bank['EUR']}<br /><b>";
                            echo "SWIFT: {$Bank['swift']}<br />";
                        }else if($Waluty[$faktura['id_waluty']] == "USD"){
                            echo "Konto USD:</b><br /> {$Bank['USD']}<br /><b>";
                            echo "SWIFT: {$Bank['swift']}<br />";
                        }
                    }
            ?>
            </b>
            <br /><br />
         </div>
      </td>
       <td width="50%" valign="top">
         <h4><?php echo $Lang['NABYWCA'] ?></h4>
         <div class="dane_nabywcy">
            <?php
            $klient = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klienta']}'"));
            echo($klient->nazwa); ?><br />
            <?php echo($klient->adres); ?>, <?php echo($klient->kod_pocztowy); ?> <?php echo($klient->miejscowosc); ?><br />
            <b><?php echo $Lang['NIP']; ?> <?php echo ($klient->nip); ?></b><br />
            <?php echo ($faktura['id_klient_text'] != "" ? $faktura['id_klient_text']."<br />" : ""); ?>
            <br />
         </div>
      </td>
   </tr>
   <tr>
        <td colspan="2">
            <table cellpadding="3" cellspacing="0" style="border: 0; width: 100%;">
                <tr>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold;"><?php echo $Lang['ILOSC_KONT']; ?></td>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold;"><?php echo $Lang['TYP']; ?></td>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold;"><?php echo $Lang['TOWAR']; ?></td>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold; text-align: center;"><?php echo $Lang['WAGA']; ?></td>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold; text-align: center;">volume</td>
                    <td style="border-top: 1px solid #000; vertical-align: top; font-weight: bold; text-align: center;"><?php echo $Lang['WAGA_PLATNA']; ?></td>
                </tr>
                <?php
                    foreach($Conty as $Cont){

                ?>
                    <tr>
                        <td><?php echo $Cont['cont_pcs']; ?></td>
                        <td><?php echo $Cont['cont_type']; ?></td>
                        <td><?php echo $Cont['cont_description']; ?></td>
                        <td style="text-align: right;"><?php echo number_format($Cont['cont_weight'],2,",","."); ?> KGS</td>
                        <td style="text-align: right;"><?php echo number_format($Cont['cont_volume'],2,",","."); ?> CBM</td>
                        <td style="text-align: right;"><?php echo number_format($Cont['chargeable_weight'],2,",","."); ?> KGS</td>
                    </tr>
                    <?php } ?>
            </table>
        </td>
    </tr>
    <tr>
      <td width="50%" valign="top" align="center">
          <br />
          <b>POL:</b> <?php echo $SOI['pol']; ?>, <?php echo $SOI['etd']; ?>
      </td>
      <td width="50%" valign="top" align="center">
          <br />
          <b>POD:</b> <?php echo $SOI['pod']; ?>, <?php echo $SOI['eta']; ?>
      </td>
   </tr>
   <tr>
      <td colspan="2" valign="top">
          <b><?php echo $Lang['ODBIORCA']; ?>:</b>
          <?php
                if($faktura['id_klient_odbiorca'] > 0){
                    $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klient_odbiorca']}'");
                    echo "{$consignee['nazwa']}&nbsp;&nbsp;&nbsp;";
                }
                echo $faktura['odbiorca'];
            ?>
      </td>
   </tr>
   <tr>
      <td colspan="2" valign="top">
          <b><?php echo $Lang['ZALADOWCA']; ?>:</b>
          <?php
                if($faktura['id_klient_shipper'] > 0){
                    $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klient_shipper']}'");
                    echo "{$consignee['nazwa']}&nbsp;&nbsp;&nbsp;";
                }
                echo $faktura['shipper'];
            ?>
      </td>
   </tr>
   <tr>
      <td width="50%" valign="top">
          <b><?php echo $Lang['WARUNKI_DOSTAWY']; ?>:</b>
          <?php
                if($faktura['terms_id'] > 0){
                    echo $Terms[$faktura['terms_id']];
                }
                echo ($faktura['terms_text'] != "" ? "&nbsp;&nbsp;{$faktura['terms_text']}" : "");
            ?>
      </td>
      <td width="50%" valign="top">
          <b>MAWB:</b>
          <?php echo $SOI['mawb']; ?>
      </td>
   </tr>
   <tr>
      <td width="50%" valign="top">
          <b><?php echo $Lang['SAMOLOT']; ?>:</b>
          <?php echo $SOI['carrier']." / ".$SOI['flight_no']; ?>
      </td>
      <td width="50%" valign="top">
          <b>HAWB:</b>
          <?php echo $SOI['hawb']; ?>
      </td>
   </tr>
   <tr>
      <td colspan="2" valign="top">
          <b>INFO:</b>
          <?php
            echo nl2br($faktura['uwagi']);
          ?>
      </td>
   </tr>
    <tr>
      <td colspan="2" valign="top" style="border-bottom: 1px solid #000;">
          <h3 style="text-align: left; margin-left: 0;">
              <b>NUMER ZLECENIA:</b>
              <?php
                echo $SOI['numer_zlecenia'];
              ?>
          </h3>
      </td>
   </tr>
</table>
<?php

echo "
<table style=\"margin: 0px 0 20px 0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">
   <tr>
      <td align=\"center\" style=\"height: 30px; border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['LP'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\"><b>". $Lang['NAZWA'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>PKWiU 63.40</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['ILOSC'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['CENA_JEDNOSTKOWA'] ."<br /></b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['WARTOSC_NETTO'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['KWOTA_VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>".$Lang['WARTOSC_BRUTTO']."</b></td>
   </tr>";


$Pozycje = mysql_query("SELECT * FROM orderplus_air_orders_faktury_pozycje WHERE id_faktury = '$ID' ORDER BY lp");
while($pozycje = mysql_fetch_object($Pozycje))
{
   echo "
   <tr>
   <td align=\"left\" style=\"height: 20px;\">$pozycje->lp</td>
   <td align=\"left\" colspan='2'>";
    echo $pozycje->opis;
   echo "</td>
   <td align=\"center\">$pozycje->ilosc</td>
   <td align=\"center\" style='white-space: nowrap'>". Usefull::ZmienFormatKwoty($pozycje->netto_jednostki) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\" style='white-space: nowrap'>". Usefull::ZmienFormatKwoty($pozycje->netto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : "")."</td>
   <td align=\"center\" style='white-space: nowrap'>". Usefull::ZmienFormatKwoty($pozycje->kwota_vat) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
    <td align=\"center\" style='white-space: nowrap'>". Usefull::ZmienFormatKwoty($pozycje->brutto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   </tr>";
}
echo "</table>";



echo "
<table style=\"margin: 20px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
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
       FROM orderplus_air_orders_faktury_pozycje WHERE id_faktury = $ID GROUP BY vat DESC"; 
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
   <td align=\"center\">". Usefull::ZmienFormatKwoty($pozycje->suma_netto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : "")."</td>
   <td align=\"center\">". Usefull::ZmienFormatKwoty($pozycje->suma_kwot_vat) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\">". Usefull::ZmienFormatKwoty($pozycje->suma_brutto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   </tr>";
}


echo "<tr>
   <td align=\"right\" style=\"height: 20px;\"><b>".$Lang['RAZEM'] ." </b></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">". Usefull::ZmienFormatKwoty($suma_netto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">&nbsp;</td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">". Usefull::ZmienFormatKwoty($suma_kwot_vat) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">". Usefull::ZmienFormatKwoty($suma_brutto) ."&nbsp;<small>{$Waluty[$faktura['id_waluty']]}</small></td>
   </tr>";
echo "</table>";





echo "<table style=\"font-size: 16px; margin: 22px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">";
if($faktura['status'] == 0){
   echo "<tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['DO_ZAPLATY'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($suma_brutto) ." <small>{$Waluty[$faktura['id_waluty']]}</small></b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['SLOWNIE'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>";
   echo Usefull::KwotaSlownie($suma_brutto, $Waluty[$faktura['id_waluty']], $faktura['szablon_faktura']);
   echo "</b></td>
   </tr>";
if($faktura['wplacono'] > 0)
{
   $pozostalo = $suma_brutto - $faktura['wplacono'];
   echo "<tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />Wpłacono:</td>
      <td align=\"left\" style=\"font-size: 14px;\"><br /><br />&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($faktura['wplacono']) ." <small>{$Waluty[$faktura['id_waluty']]}</small></b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\">Pozostało:</td>
      <td align=\"left\" style=\"font-size: 14px;\">&nbsp;&nbsp;&nbsp;<b>". Usefull::ZmienFormatKwoty($pozostalo) ." {$Waluty[$faktura['id_waluty']]}</b>
      &nbsp;&nbsp;&nbsp;". KwotaSlownie($pozostalo, $Waluty[$faktura['id_waluty']], $faktura['szablon_faktura']) ."</td>
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


<table  style="margin: 30px 0 0 0" width="100%" cellpadding="3" cellspacing="10">
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
                <?php
                    if($faktura['id_faktury'] >= 131){ 
                        ?>
                        <tr style="text-align: left; font-size: 8pt;">
                            <td>
                                    <?php echo $Lang['TEXT_VAT']; ?>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                    }
                ?>
	</table>
	<?php
	if($faktura['szablon_faktura'] == "PL"){
		echo "<br /><br /><br />
		<span style='font-size: 8pt;'>
		Zgodnie z art. 7 ustawy o terminach zapłaty w transakcjach handlowych,
		jeżeli dłużnik w określonym w umowie nie dokona zapłaty na rzecz
		wierzyciela, zobowiązany jest on do zapłaty wierzycielowi, bez odrębnego
		wezwania, odsetek w wysokości odsetek od zaległości
		podatkowych.<br />
		Ta faktura jest także wezwaniem do zapłaty w rozumieniu art. 476 KC.</span>";
	}
?>

</div>