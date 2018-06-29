<div style="width:750px; min-height: 1000px; margin: 0 auto 0 auto; text-align: left;">
    <table cellpadding="10" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 50%; font-size: 10pt;"><br /><br /><br /><b>ZLECENIE <?php echo ($SeaOrder['sea_order_type'] == "I" ? "IMPORT" : "EXPORT"); ?> FCL <br /><?php echo $zlecenie['numer_zlecenia']; ?></b></td>
            <td style="width: 50%;"><img src="/images/logo-new.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/></td>
        </tr>
        <tr>
            <td style="width: 50%;">
                <b>TO:</b>
                <?php
                    echo $przewoznik_to['nazwa']."<br />".nl2br($przewoznik_to['dane_firmy']).($zlecenie['zlecenie_to'] != "" ? "<br />".nl2br($zlecenie['zlecenie_to']) : "");
                ?>
            </td>
            <td style="width: 50%;">
                Critical Cargo and Freight Services sp. z o. o.<br />
				ul. Solidarności 115/2<br /><br />
				00-140 Warszawa<br /><br />
            POLAND, NIP 5252581565<br />
                <?php
                    $DaneOddzialu[1] = array('siedziba' => 'Wrocław', 'tel' => 'tel. +48&nbsp;693233314<br />tel. +48&nbsp;530989969');
                    $DaneOddzialu[2] = array('siedziba' => 'Warszawa', 'tel' => 'tel. +48&nbsp;22&nbsp;330-81-21<br />fax +48&nbsp;22&nbsp;398-79-07');
                    $DaneOddzialu[3] = array('siedziba' => 'Poznań', 'tel' => 'tel +48&nbsp;61&nbsp;6417592<br />fax +48&nbsp;61&nbsp;6417594');
                    $DaneOddzialu[4] = array('siedziba' => 'Gdynia', 'tel' => '');
                    echo $uzytkownik['imie']." ".$uzytkownik['nazwisko']."<br />";
                    if($uzytkownik['id_oddzial'] > 0){
                        echo $DaneOddzialu[$uzytkownik['id_oddzial']]['siedziba'];
                        echo "<br />";
                        echo $DaneOddzialu[$uzytkownik['id_oddzial']]['tel'];
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <br /><br/>
                   <b>--------------------------------------------------- FCL CONT. SPEC. ---------------------------------------------------</b>
                <br /><br />
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <table cellpadding="3" cellspacing="0" style="border: 0; width: 100%;">
                    <tr>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">CONT. DIM.</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">NO.</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">PCS</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">TYPE</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">DESCRIPTION</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">WEIGHT</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">VOLUME</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">DANGEROUS<br />GOODS</td>
                    </tr>
                    <?php
                        $Plomby = array();
                        foreach($Containers as $Cont){
                            if(in_array($Cont['cont_no'], $Conty)){
                    ?>
                    <tr>
                        <td><?php echo $Size[$Cont['cont_dim_size']]." ".$Types[$Cont['cont_dim_type']]; ?></td>
                        <td><?php echo $Cont['cont_no']; ?></td>
                        <td><?php echo $Cont['cont_pcs']; ?></td>
                        <td><?php echo $Cont['cont_type']; ?></td>
                        <td><?php echo $Cont['cont_description']; ?></td>
                        <td style="text-align: right;"><?php echo number_format($Cont['cont_weight'],2,",","."); ?> KGS</td>
                        <td style="text-align: right;"><?php echo number_format($Cont['cont_volume'],2,",","."); ?> CBM</td>
                        <td style="text-align: center;"><?php echo $Cont['cont_dgr'].($Cont['cont_dgr'] == "Yes" ? " ".$Cont['cont_class']." ".$Cont['cont_un'] : ""); ?></td>
                    </tr>
                    <?php
                            $Plomby[] = $Cont['cont_seal'];
                         }
                        }
                    ?>
                </table>
            </td>
        </tr>
        <?php
            if($SeaOrder['sea_order_type'] == "I"){
        ?>
        <tr>
            <td colspan="2">
                <b>DATA PODJĘCIA PEŁNEGO KONT.:</b> <?php echo $zlecenie['data_podjecia']; ?>
            </td>
        </tr>
        <?php
            }
        ?>
        <tr>
            <td colspan="2">
                <b>MIEJSCE PODJECIA:</b> <?php echo $zlecenie['miejsce_podjecia']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>NUMBER PLOMBY:</b> <?php echo implode(", ", $Plomby); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>ARMATOR/ZWOLNIENIE:</b> <?php echo ($zlecenie['ocean_carrier_id'] != 37 ? $Carriers[$zlecenie['ocean_carrier_id']]." " : "").($zlecenie['ocean_carrier_text'] != "" ? "{$zlecenie['ocean_carrier_text']}" : ""); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>ODPRAWA CELNA:</b> <?php echo $zlecenie['customs_clearence']; ?>
            </td>
        </tr>
        <?php
            if($SeaOrder['sea_order_type'] == "I"){
        ?>
            <tr>
                <td colspan="2">
                    <b>MIEJSCE ROZŁADUNKU:</b>
                    <?php
                        echo nl2br($zlecenie['consignee']);
                        if($zlecenie['id_klient_consignee'] > 0){
                            $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_consignee']}'");
                            echo "{$consignee['nazwa']}, {$consignee['adres']}, {$consignee['kod_pocztowy']} {$consignee['miejscowosc']}";
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>DATA ROZŁADUNKU:</b> <?php echo $zlecenie['data_rozladunku']; ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>ZWROT PUSTEGO KONTENERA:</b> <?php echo $zlecenie['zwrot_pustego']; ?>
                </td>
            </tr>
        <?php
            }else{
        ?>
            <tr>
                <td colspan="2">
                    <b>MIEJSCE ZAŁADUNKU:</b>
                    <?php
                        echo $zlecenie['shipper'];
                        if($zlecenie['id_klient_shipper'] > 0){
                            $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_shipper']}'");
                            echo "{$consignee['nazwa']}, {$consignee['adres']}, {$consignee['kod_pocztowy']} {$consignee['miejscowosc']}";
                        }
                    ?>
                </td>
                </tr>
            <tr>
                <td colspan="2">
                    <b>DATA ZAŁADUNKU:</b> <?php echo $zlecenie['data_zaladunku']; ?>
                </td>
            </tr>
                <tr>
                <td colspan="2">
                    <b>TERMINAL/DOSTAWA PEŁNY:</b> <?php echo $zlecenie['terminal']; ?>
                </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <b>NR BOOKINGU:</b> <?php echo $zlecenie['nr_booking']; ?>
                </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <b>VESSEL:</b> <?php echo $zlecenie['vessel']; ?>
                </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <b>FEEDER:</b> <?php echo $zlecenie['feeder']; ?>
                </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <b>CUT OFF:</b> <?php echo $zlecenie['cut_off']." ".$zlecenie['cut_off_hour']; ?>
                </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <b>POD:</b> <?php echo $zlecenie['pod']; ?>
                </td>
            </tr>
        <?php
            }
           ?>
        <tr>
            <td colspan="2" style="text-align: center;">
                <br /><br/>
                   <b>------------------------------------------------------------------------------------------------------------------------</b>
                <br /><br />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>INFORMACJE DODATKOWE:</b><br />
                <?php echo nl2br(stripslashes($zlecenie['informacje_dodatkowe'])); ?>
                <br /><br />
            </td>
        </tr>
        <?php
            if($SeaOrder['sea_order_type'] == "I"){
        ?>
        <tr>
            <td colspan="2">
                <b>INSTRUKCJE:</b><br />
                <?php echo nl2br(stripslashes($zlecenie['instrukcje'])); ?>
                <br /><br />
            </td>
        </tr>
         <?php
            }
        ?>
    </table>
    <!-- Nowa strona -->
	<div style="page-break-before: always; margin-top: 10px;">
            <div align="center" style="font-weight: bold;"><?php echo WARUNKI; ?></div>
            <div align="left">
                    <?php
                    echo stripslashes($warunki_szablon['pelny_tekst']);
                    ?>
            </div>
        </div>
</div>