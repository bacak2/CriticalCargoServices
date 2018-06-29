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
			<th class="small bordered raport_klient">SHIPPER</th>
			<th class="small bordered raport_klient">CONSIGNEE</th>
			<th class="small bordered raport_klient">CARRIER</th>
			<th class="small bordered raport_klient">POL</th>
			<th class="small bordered raport_klient">ETD</th>
			<th class="small bordered raport_klient">POD</th>
			<th class="small bordered raport_klient">ETA</th>
			<th class="small bordered raport_klient">TERMS</th>
                        <th class="small bordered raport_klient">MAWB</th>
			<th class="small bordered raport_klient">HAWB</th>
                        <th class="small bordered raport_klient">INSURANCE</th>
			<th class="small bordered raport_klient">PSC</th> 
                        <th class="small bordered raport_klient">TYPE</th>
                        <th class="small bordered raport_klient">WEIGHT</th>
                        <th class="small bordered raport_klient">DGR</th>
                        <th class="small bordered raport_klient">AKTUALNY STATUS</th>
		</tr>
		<?php
			foreach($Zlecenia as $Dane){
                                $FCL = $this->Baza->GetRows("SELECT * FROM orderplus_air_orders_fcl WHERE id_zlecenie = '{$Dane['id_zlecenie']}'");
                                $LCL = $this->Baza->GetRows("SELECT * FROM orderplus_air_orders_lcl WHERE id_zlecenie = '{$Dane['id_zlecenie']}'");
                                if($FCL == false){
                                    $FCL = array();
                                }
                                if($LCL == false){
                                    $LCL = array();
                                }
                                $Kontenery = Usefull::PolaczDwieTablice($FCL, $LCL);
                                $IleContow = count($Kontenery);
                                if($IleContow == 0){
                                    $Kontenery = array(0 => array());
                                }
				echo "<tr>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">";
                                        if($Dane['id_klient_shipper'] > 0){
                                            $Klient = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient_shipper']}'");
                                                echo $Klient.($zlecenie['shipper'] != "" ? "<br />" : "");
                                        }
                                        echo ($zlecenie['shipper'] != "" ? $zlecenie['shipper']."<br />" : "");
                                    echo "</td>";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">";
                                        if($Dane['id_klient_consignee'] > 0){
                                            $Klient = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient_consignee']}'");
                                                echo $Klient.($zlecenie['consignee'] != "" ? "<br />" : "");
                                        }
                                        echo ($zlecenie['consignee'] != "" ? $zlecenie['consignee']."<br />" : "");
                                    echo "</td>";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['carrier']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['pol']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['etd']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['pod']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['eta']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">".$Terms[$Dane['terms_id']].($Dane['terms_text'] != "" ? " {$Dane['terms_text']}" : "")."</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['mawb']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">{$Dane['hawb']}</td>\n";
                                    echo "<td class='small bordered white'".($IleContow > 1 ? " rowspan='$IleContow'" : "").">".strtoupper($Dane['insurence'])."</td>\n";
                                    $Lp = 1;
                                    foreach($Kontenery as $Cont){
                                        if($Lp > 1){
                                            ?><tr><?php
                                        }
                                        echo "<td class='small bordered white'><nobr>{$Cont['cont_pcs']}</nobr></td>";
                                        echo "<td class='small bordered white'><nobr>{$Cont['cont_type']}</nobr></td>";
                                        echo "<td class='small bordered white'>".number_format($Cont['cont_weight'],2,",",".")." KGS</td>";
                                        echo "<td class='small bordered white'>".$Cont['cont_dgr'].($Cont['cont_dgr'] == "Yes" ? " ".$Cont['cont_class']." ".$Cont['cont_un'] : "")."</td>";
                                        echo "<td class='small bordered white'>".($Lp == 1 ? $Dane['zlecenie_status'] : "&nbsp;")."</td>";
                                         ?></tr><?php
                                         $Lp++;
                                    }
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