<div style="width:580px; margin: 0 auto 0 auto; text-align: left;">
	<style>
	
	td.title { border: 1px solid #000; background: #ffcd00; font-style: italic; font-weight: 700; padding: 2px; padding-left: 10px; }

	
	</style>

	<div align="right" style="margin: 5px;"><?php echo STRONA1; ?></div>
	<div align="center" style="margin-top: 0; font-style: italic; font-weight: 700; font-size: 14px;"><?php echo ZLECENIE_TYTUL; ?></div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" >
		<tr>
			<td align="left"><img src="<?php echo SCIEZKA_OGOLNA; ?>images/logo-new.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline; margin-bottom: 20px;"/></td>
			<!--<td align="center" valign="middle">
				<?php
					if($zlecenie['firma_wystaw'] == 1){
				?>
					Critical Cargo and Freight Services sp. z o. o.<br />
					ul. Solidarności 115 lok. 2<br>
					00-140 Warszawa<br />
					<br />
					NIP: 5252581565<br />
				<?php
					}else{
				?>
					Critical Cargo and Freight Services sp. z o. o.<br />
                    ul. Solidarności 115 lok. 2<br>
					00-140 Warszawa<br />
					<br />
					NIP: 5252581565<br />
				<?php
					}
				?>
			</td>-->
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="left" width="60%" style="vertical-align: top;">
				<!--<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
                                            <?php
                                                $DaneOddzialu[1] = array('siedziba' => 'Wrocław', 'tel' => 'tel. +48&nbsp;693233314<br />tel. +48&nbsp;530989969');
                                                $DaneOddzialu[2] = array('siedziba' => 'Warszawa', 'tel' => 'tel. +48&nbsp;22&nbsp;330-81-21<br />fax +48&nbsp;22&nbsp;398-79-07');
                                                $DaneOddzialu[3] = array('siedziba' => 'Poznań', 'tel' => 'tel +48&nbsp;61&nbsp;6417592<br />fax +48&nbsp;61&nbsp;6417594');
                                                $DaneOddzialu[4] = array('siedziba' => 'Gdynia', 'tel' => '');
                                            ?>
						<td style="vertical-align: top;">
                                                    <?php
                                                        echo $DaneOddzialu[$uzytkownik['id_oddzial']]['siedziba'];
                                                    ?>
						</td>
						<td style="vertical-align: top;">
                                                    <?php
                                                        echo $DaneOddzialu[$uzytkownik['id_oddzial']]['tel'];
                                                    ?>
						</td>
					</tr>
				</table>-->
				
				<strong>Critical Cargo and Freight Services sp. z o. o.</strong><br />
					ul. Solidarności 115/2<br>
					00-140 Warszawa, Poland<br />
					
					NIP: PL 525-258-15-65<br />
					<br />
					<br />
					<strong>Adres korespondencyjny</strong><br /><br />
					Critical Cargo and Freight Services sp. z o. o.<br />
					Wrocławska 10D/6<br />
					01-493 Warszawa
			</td>
			<td align="left" width="40%" style="vertical-align: top;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td style="vertical-align: top;">
							Telefon:
						</td>
						<td style="vertical-align: top;">
							+48 669 609 004
						</td>
					</tr>
					<tr>
					<tr>
						<td style="vertical-align: top;">
							Mail:
						</td>
						<td style="vertical-align: top;">
							office@critical-cs.com
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top;">
							WWW:
						</td>
						<td style="vertical-align: top;">
							www.critical-cs.pl
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<!--<br /><br />Informacje na temat terminu płatności możecie Państwo uzyskać wysyłając maila na adres mailowy: termin@mepp.pl<br />--><br /><br /><br /><br />
	<div align="center" style="font-weight: bold; font-size: 14px; font-style: italic;">
		<?php echo UMOWA.($zlecenie['numer_zlecenia']); ?><br />
		<?php echo DNIA.($zlecenie['data_zlecenia']); ?>
	</div>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr><td class="title" align="left" width="270">1. <?php echo ZLECENIOBIORCA; ?></td><td width="40"></td><td class="title" align="left" width="270">2. <?php echo DANE_KIEROWCY; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270">
		<?php
		print $przewoznik['nazwa']."<br />".nl2br($przewoznik['dane_firmy']);
		?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie['kierowca_dane']); ?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td class="title" align="left" width="270">3.<?php echo MIEJSCE_ZALADUNKU; ?></td><td width="40"></td><td class="title" align="left" width="270">4. <?php echo ODBIORCA; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270">
		<?php
		print nl2br($zlecenie['miejsce_zaladunku']);
		$zaladunki = explode('^', $zlecenie['zaladunki']);
		foreach($zaladunki as $id_punktu)
		{
		   if($id_punktu != 0)
		   {
		      $w = mysql_query("SELECT * FROM orderplus_punkty WHERE id = $id_punktu");
		      $dane_punktu = mysql_fetch_object($w);
		      echo "$dane_punktu->nazwa $dane_punktu->kraj $dane_punktu->opis<br /><br />";
		   }
		}

		?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270">
		<?php
		print nl2br($zlecenie['odbiorca']);

		$zaladunki = explode('^', $zlecenie['rozladunki']);
		foreach($zaladunki as $id_punktu)
		{
		   if($id_punktu != 0)
		   {
		      $w = mysql_query("SELECT * FROM orderplus_punkty WHERE id = $id_punktu");
		      $dane_punktu = mysql_fetch_object($w);
		      echo "$dane_punktu->nazwa $dane_punktu->kraj $dane_punktu->opis<br /><br />";
		   }
		}
		?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td class="title" align="left" width="270">5. <?php echo DATA_ZALADUNKU; ?></td><td width="40">&nbsp;</td><td class="title" align="left" width="270">6. <?php echo DATA_ROZLADUNKU; ?></td></tr>
		<tr><td class="table_okno_male" align="left" width="270"><?php print $zlecenie['termin_zaladunku']." ".$zlecenie['godzina_zaladunku']; ?>&nbsp;</td><td width="40"></td><td class="table_okno_male" align="left" width="270"><?php print $zlecenie['termin_rozladunku']." ".$zlecenie['godzina_rozladunku']; ?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td class="title" align="left" width="270">7. <?php echo DOKUMENTY; ?></td><td width="40"></td><td class="title" align="left" width="270">8. <?php echo UWAGI; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie['dokumenty']); ?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie['ladunek_niebezpieczny']);?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td class="title" align="left" width="270">9. <?php echo OPIS_LADUNKU; ?></td><td  width="40"></td><td class="title" width="270">10. <?php echo OSOBA_KONTAKTOWA; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie['opis_ladunku']); ?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie['os_kontaktowa']); ?>&nbsp;</td></tr>
		<tr><td colspan="3" align="left"><br>&nbsp;<br>11. <?php echo FRACHT;
		$cena_format = number_format($zlecenie['stawka_przewoznik'], 2, ',', ' ');
		print ("$cena_format {$zlecenie['waluta']} + VAT");
?> </td></tr>
	</table>
	
	<!-- Nowa strona -->
	<div style="page-break-before: always; margin: 0;">
	<div align="right" style="margin: 5px;"><?php echo STRONA2?></div>
	<div align="center" style="font-weight: bold;"><?php echo WARUNKI; ?></div>
	<div align="left">
		<?php
		echo stripslashes($warunki_szablon['pelny_tekst']);
		?>
		<br />
	</div>
	<br />
	<br />
	<br />
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr>
			<td align="left"><?php echo PODZIEKOWANIA; ?></td>
			<td align="right"><?php print WYSTAWIL.$uzytkownik['imie'].' '.$uzytkownik['nazwisko'] ?></td>
		</tr>
	</table>
	<br />
</div>