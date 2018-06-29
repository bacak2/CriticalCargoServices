<div style="width:1280px; margin: 0 auto 0 auto; text-align: left;">
	<style>
	body { background: #eae8e9 !important; } 
	th { background: #ffcd00 !important; font-style: italic; color: #43443f;}
	td.white { background: #fff; }
	</style>
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="width: 40%;" rowspan="2"><img src="../images/logo-new.png" alt="Logo" /></td>
			<td style="font-weight: bold; font-size: 13px; text-align: right; font-style: italic;"><br /><br /><div class='inline' style='width: 100px;'>Date & Time:</div> <div class='data inline'><?php echo str_replace(" ", " / ", date("d.m.Y H:i", strtotime($Raport['raport_date']))).' h'; ?></div></td>
		</tr>
		<tr>
			<td style="width: 60%; font-weight: bold; font-size: 16px;"><br /><br /><i>Shipment location report for</i> <span style="font-size: 18px;"><?php echo $Client; ?></span></td>
		</tr>
	</table>
	<br /><br />
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%; border-collapse: collapse;">
		<tr>
			<th class="small bordered raport_klient">NR ZLECENIA</th>
			<th class="small bordered raport_klient" colspan="2">FCL/LCL</th>
			<th class="small bordered raport_klient">KIERUNEK</th>
			<th class="small bordered raport_klient">ILOŚĆ</th>
			<th class="small bordered raport_klient">WAGA (kg)</th>
			<th class="small bordered raport_klient">CBM</th>
			<th class="small bordered raport_klient">NR KONTENERA</th>
			<th class="small bordered raport_klient">NR BL</th>
                        <th class="small bordered raport_klient">PODJĘCIE</th>
			<th class="small bordered raport_klient">ETD (PLAN.)</th>
                        <th class="small bordered raport_klient">REAL TIME OF DEPARTURE</th>
			<th class="small bordered raport_klient">ETA (PLAN.)</th> 
                        <th class="small bordered raport_klient">REAL TIME OF ARRIVAL</th>
                        <th class="small bordered raport_klient">POD</th>
                        <th class="small bordered raport_klient">AKTUALNY STATUS</th>
		</tr>
		<?php
                        $i = 0;
			foreach($Zlecenia as $Dane){
                                $Kontenery = $Dane['kontenery'];
                                $IleContow = count($Dane['kontenery']);
                                if($IleContow == 0){
                                    $Kontenery = array(0 => array());
                                }
				echo "<tr style='background-color: ".($i % 2 == 0 ? "#FFF;" : "#C6E0FF;")."'>\n";
                                    echo "<td class='small bordered'".($IleContow > 1 ? " rowspan='$IleContow'" : "")."><nobr>{$Dane['numer_zlecenia']}</nobr></td>";
                                    $Lp = 1;
                                    foreach($Kontenery as $Cont){
                                        if($Lp > 1){
                                            ?><tr style='background-color: <?php echo ($i % 2 == 0 ? "#FFF;" : "#C6E0FF;");  ?>'><?php
                                        }
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? $Dane['mode'] : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'><nobr>{$Size[$Cont['cont_dim_size']]} {$Types[$Cont['cont_dim_type']]}</nobr></td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? $Dane['pod'] : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'><nobr>{$Cont['cont_pcs']} {$Cont['cont_type']}</nobr></td>";
                                        echo "<td class='small bordered white'>".number_format($Cont['cont_weight'],2,",",".")."</td>";
                                        echo "<td class='small bordered white'>".number_format($Cont['cont_volume'],2,",",".")."</td>";
                                        echo "<td class='small bordered white'>{$Cont['cont_no']}</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? $Dane['bl_no'] : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? nl2br($Dane['podjecie']) : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? Usefull::ShowDate($Dane['etd']) : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? Usefull::ShowDate($Dane['rtd']) : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? Usefull::ShowDate($Dane['eta']) : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? Usefull::ShowDate($Dane['rta']) : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".($Lp >= 1 ? $Dane['pod'] : "&nbsp;")."</td>";
                                        echo "<td class='small bordered white'>".$Cont['cont_status']."</td>"; 
                                         ?></tr><?php
                                         $Lp++;
                                    }
                              $i++;
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