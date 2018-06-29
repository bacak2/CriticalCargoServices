<div style="width:800px; margin: 0 auto 0 auto; text-align: left;">
	<style>
	body { background: #eae8e9 !important; } 
	th { background: #ffcd00 !important; font-style: italic; color: #43443f;}
	td.white { background: #fff; }
	td.col-1 { white-space: pre;}
	</style>
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="width: 40%;" rowspan="2"><img src="../images/logo-new.png" alt="Logo" /></td>
			<td style="width: 60%; font-weight: bold; font-size: 13px; text-align: right; font-style: italic;"><br /><br /><div class='inline' style='width: 100px;'>Date & Time:</div> <div class='data inline'><?php echo str_replace(" ", " / ", date("d.m.Y H:i", strtotime($Raport['raport_date']))).' h'; ?></div></td>
		</tr>
		<tr>
			<td style="width: 100%; font-size: 14px;"><br /><br /><i>Shipment location report for:</i> <span style="font-size: 16px; font-weight: 700;"><?php echo $Client; ?></span></td>
		</tr>
	</table>
	<br /><br />
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%; border-collapse: collapse;">
		<tr>
			<th class="small bordered raport_klient">No.</th>
			<th class="small bordered raport_klient">Order No.</th>
			<th class="small bordered raport_klient">Truck No.</th>
			<th class="small bordered raport_klient">Loading place</th>
			<th class="small bordered raport_klient">Delivery place</th>
			<th class="small bordered raport_klient">Loading date</th>
			<th class="small bordered raport_klient">Unloading date</th>
			<th class="small bordered raport_klient">Shipment</th>
			<th class="small bordered raport_klient">Type of service</th>
			<th class="small bordered raport_klient">Actually status</th>
		</tr>
		<?php
			$Lp = 1;
			foreach($Zlecenia as $Dane){
				echo "<tr>\n";
					$TruckNr = $this->Baza->GetValue("SELECT rejestracja FROM orderplus_kierowca WHERE id_kierowca = '{$Dane['id_kierowca']}'");
					echo "<td class='small bordered white'>$Lp</td>";
					echo "<td class='small bordered white'>{$Dane['numer_zlecenia_krotki']}</td>";
					/*echo "<td class='small bordered white'><nobr>{$Dane['kierowca_dane_nr_rejestracyjny']}</nobr></td>";*/
					echo "<td class='small bordered white col-1'>";
					echo str_replace(";",";<br />",$Dane['kierowca_dane_nr_rejestracyjny']);
					echo "</td>";
					echo "<td class='small bordered white'>{$Dane['miejsce_zaladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['odbiorca']}</td>";
					echo "<td class='small bordered white'>{$Dane['termin_zaladunku']} {$Dane['godzina_zaladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['termin_rozladunku']} {$Dane['godzina_rozladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['opis_ladunku']}</td>";
					echo "<td class='small bordered white'>{$Typy[$Dane['typ_serwisu']]}</td>";
					echo "<td class='small bordered white'>{$Dane['zlecenie_status']}</td>";
				echo "</tr>\n";
				$Lp++;
			}
		?>
	</table>
	<!--<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="font-size: 10px;" colspan="2">
				<br /><br />
                                We are working under the conditions of <a href="http://mepp.pl/index.php?option=com_wrapper&view=wrapper&Itemid=417&lang=pl" target="_blank">"Regulations of Forwarding Services MEPP European Freight Solutions Sp. z o.o."</a> dated 1.11.2011.<br />
                                Pracujemy w oparciu o <a href="http://mepp.pl/index.php?option=com_wrapper&view=wrapper&Itemid=417&lang=pl" target="_blank">"Regulamin Świadczenia Usług Spedycyjnych MEPP European Freight Solutions Sp. z o.o."</a> z dnia 1.11.2011.<br /><br /><br />
				This information is sent from our Freight Operational System to the following e-mail adress: <?php echo $Raport['send_email']; ?>
			</td>
		</tr>
	</table>-->
	<br /><br /><br />
</div>