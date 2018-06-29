<div style="width: 19cm; margin: 1cm auto 1cm auto; text-align: left;">
	<span style='font-size: 11pt; font-weight: bold;'><?php echo $Wartosci['tytul'] ?></span>
	<br />
	<br />
	<table class="ramka">
	<thead>
		<tr>
			<th>Lp.</th>
			<th style="width: 130px;">Data załadunku</th>
			<th style="width: 130px;">Data rozładunku</th>
                        <th>Osoba zlecająca</th>
			<th>Załadowca i miejsce załadunku</th>
			<th>Odbiorca i miejsce rozładunku</th>
                        <th>Opis ładunku</th>
			<th>Koszt</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$Wartosci['nowe_zlecenia'][] = 0;
			$Licznik = 1;
			$Zlecenia = $this->Baza->GetResultAsArray("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie IN(".implode(",",$Wartosci['nowe_zlecenia']).")", "id_zlecenie");
			$Sumuj = 0;
			foreach($Zlecenia as $ZleID => $Zle){
				echo "<tr>\n";
					echo "<td>$Licznik</td>\n";
					echo "<td>{$Zle['termin_zaladunku']} {$Zle['godzina_zaladunku']}</td>\n";
					echo "<td>{$Zle['termin_rozladunku']} {$Zle['godzina_rozladunku']}</td>\n";
                                        echo "<td>".nl2br($Zle['nr_zlecenia_klienta'])."</td>\n";
					echo "<td>".nl2br($Zle['miejsce_zaladunku'])."</td>\n";
					echo "<td>".nl2br($Zle['odbiorca'])."</td>\n";
                                        echo "<td>".nl2br($Zle['opis_ladunku'])."</td>\n";
					echo "<td style='vertical-align: bottom;'>";
						if($Zle['waluta'] == "PLN"){
							echo $Zle['stawka_klient']."&nbsp;PLN;";
							$StawkaPLN = $Zle['stawka_klient'];
						}else{
							echo $Zle['stawka_klient']."&nbsp;{$Zle['waluta']};<br />";
							$StawkaPLN = $Zle['stawka_klient']*$Zle['kurs'];
							echo number_format($StawkaPLN,2,".","")."&nbsp;PLN;";
						}
					echo "</td>\n";
				echo "</tr>\n";
				$Licznik++;
				$Sumuj += round($StawkaPLN,2);
			}
			$VAT = $Sumuj * ($Wartosci['stawka_vat']/100);
			$Brutto = $Sumuj + $VAT;
		?>
		<tr><td colspan="8" class='przerwa'>&nbsp;
		</td></tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>Kwota netto</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($Sumuj,2,".","")."&nbsp;zł"; ?></td>
		</tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>Kwota vat</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($VAT,2,".","")."&nbsp;zł"; ?></td>
		</tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>Kwota brutto</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($Brutto,2,".","")."&nbsp;zł"; ?></td>
		</tr>
        </table>
</div>