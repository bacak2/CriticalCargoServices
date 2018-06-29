<div class="layout_new_design" style="background-image: url('images/faktura-new-design/<?php echo ($faktura['szablon_faktura'] == 'ENG' ? "bottom-en" : "bottom-pl").($faktura['id_faktury'] >= 1128 ? "-09-2013" : ""); ?>.png'); background-repeat: no-repeat; background-position: bottom left;">
    <table style="margin: 0;" width="100%" cellpadding="0" cellspacing="0">
        <tr id="print">
            <td style="font-size: 12px; text-align:left;padding-right: 7px" colspan="2">
                <a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
            </td>
        </tr>
        <tr> 
          <td style="width: 288px; vertical-align: top; height: 567px; background-image: url('images/faktura-new-design/top1-morska.png'); background-repeat: no-repeat;">
              <table cellpadding="0" cellspacing="0" style="width: 100%;">
                  <tr>
                      <td style="height: 194px; vertical-align: top;">
                          <div style="margin: 158px 0px 0px 19px; font-size: 25px; font-weight: bold;"><?php echo $Lang['SPRZEDAWCA'] ?></div>
                      </td>
                  </tr>
                  <tr>
                      <td style="height: 100px; vertical-align: top;">
                          <div style="margin: 19px 0px 0px 17px; font-size: 13px; font-weight: bold; font-family: 'Trebuchet MS';">
                                Critical Cargo and Freight Services sp. z o. o.<br />
				ul. Solidarności 115/2<br />
				00-140 Warszawa<br />
            POLAND, <?php echo $Lang['NIP']; ?> PL5252581565<br />
                          </div>
                      </td>
                    </tr>
                    <tr>
                        <td style="height: 46px; vertical-align: top;"> 
                            <div style="margin: 12px 0px 0px 19px; font-size: 25px; font-weight: bold;"><?php echo $Lang['NABYWCA']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 100px; vertical-align: top; overflow: hidden;">
                            <div style="margin: 8px 0px 0px 17px; font-size: 13px; font-weight: bold; font-family: 'Trebuchet MS';">
                                <?php
                                    $klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klienta']}'");
                                ?>
                                <?php echo $klient['nazwa']; ?><br />
				<?php echo $klient['adres']; ?> <?php echo($klient['kod_pocztowy']); ?> <?php echo($klient['miejscowosc']); ?><br />
				<?php echo $Lang['NIP']; ?> <?php echo ($klient['nip']); ?><br />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 46px; vertical-align: top; overflow: hidden;">
                            <div style="margin: 12px 0px 0px 13px; font-size: 25px; font-weight: bold;"><?php echo $Lang['ODBIORCA']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 80px; vertical-align: top;">
                            <div style="margin: 8px 0px 0px 17px; font-size: 13px; font-weight: bold; font-family: 'Trebuchet MS';">
                                <?php
                                if($faktura['id_klient_odbiorca'] > 0){
                                    $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klient_odbiorca']}'");
                                    echo "{$consignee['nazwa']}&nbsp;&nbsp;&nbsp;";
                                }
                                echo $faktura['odbiorca'];
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 621px; vertical-align: top; height: 567px; background-image: url('images/faktura-new-design/top2-morska.png'); background-repeat: no-repeat;">
                <div style="position: relative; height: 188px;"> 
                    <div style="text-align: right; top: 100px; position: absolute; width: 605px; font-size: 40px; color: #FFF; font-weight: bold; "><?php echo $Lang['FAKTURA_VAT_UPPER'] ?></div>
                    <div style="text-align: left; position: absolute; top: 154px; left: 14px; width: 607px; font-size: 16px; color: #FFF; font-weight: bold; font-family: 'Trebuchet MS';"><?php echo $Lang['NR_UPPER']." ". $faktura['numer']; ?></div>
                    <div style="text-align: right; position: absolute; top: 154px; width: 603px; font-size: 16px; color: #FFF; font-weight: bold; "><?php echo $Lang['ORYGINA']; ?></div>
                </div>
                <div style="position: relative; height: 122px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr> 
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['DATA_WYSTAWIENIA_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['MIEJSCE_WYSTAWIENIA_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['DATA_SPRZEDAZY_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['TERMIN_PLATNOSCI_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['FORMA_PLATNOSCI_NEW']; ?></td>
                        </tr>
                        <tr> 
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo $faktura['data_wystawienia']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo ($faktura['szablon_faktura'] == 'ENG' ? str_replace("Warszawa", "Warsaw", $faktura['miejsce_wystawienia']) : $faktura['miejsce_wystawienia']); ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo $faktura['data_sprzedazy']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo $faktura['termin_platnosci']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo $FormyPlatnosci[$faktura['id_formy']]; ?></td>
                        </tr>
                    </table>
                </div>
                <div style="position: relative; height: 144px;">
                    <table cellpadding="0" cellspacing="0" style="margin-left: 17px;">
                        <tr>
                            <td style="height: 62px; width: 299px; vertical-align: middle; text-align: left; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['DANE_BANKU']; ?></td>
                            <td style="width: 24px;"></td>
                            <td style="height: 62px; width: 279px; vertical-align: middle; text-align: left; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['DANE_KONTA']; ?></td>
                        </tr>
                        <tr>
                            <td style="height: 84px; width: 299px; vertical-align: top; text-align: left; color: #231f20; font-size: 16px; font-weight: bold; line-height: 140%;">
                                BZ WBK<br />
                            </td>
                            <td style="width: 24px;"></td> 
                            <td style="height: 84px; width: 279px; vertical-align: top; text-align: left; color: #231f20; font-size: 14px; font-weight: bold; line-height: 140%;">
                                <?php
                                    if($faktura['szablon_faktura'] == 'ENG'){
                                        ?>
                                        IBAN PL<br />
                                            <?php
                                            if($Waluty[$faktura['id_waluty']] == "USD"){
                                            ?>
                                                USD: PL40109010430000000118855686<br />
                                            <?php
                                            }else{
                                            ?>
                                                PLN: PL60109010430000000117266466<br />
                                                EUR: PL32109010430000000117266776<br />
                                            <?php
                                            }
                                            ?>
                                            SWIFT: WBKPPLPP
                                         <?php
                                    }else{
                                       ?>
                                            PLN: 60109010430000000117266466<br />
                                            <?php
                                            if($Waluty[$faktura['id_waluty']] == "EUR"){
                                            ?>
                                                EUR: PL32109010430000000117266776<br />
                                                SWIFT: WBKPPLPP<br />
                                            <?php
                                            }else if($Waluty[$faktura['id_waluty']] == "USD"){
                                            ?>
                                                USD: PL40109010430000000118855686<br />
                                                SWIFT: WBKPPLPP<br />
                                            <?php
                                            }
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="position: relative; height: 106px; margin-top: 8px; left: 93px; text-align: right; font-size: 14px; font-weight: bold; width: 520px; line-height: 18px;">
                    <?php echo ($faktura['szablon_faktura'] == 'ENG' ? "order no" : "numer zlecenia").": {$SOI['numer_zlecenia']}";?><br />
                    <?php echo ($faktura['szablon_faktura'] == 'ENG' ? "loading date" : "data zał.").": {$SOI['etd']}";?><br />
                    <?php echo ($faktura['szablon_faktura'] == 'ENG' ? "delivery date" : "data roz.").": {$SOI['eta']}";?><br />
                    <?php echo $SOI['pol']." - ".$SOI['pod']; ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="2" cellspacing="0" style="width: 100%; border-collapse: collapse;" id="morska-info">
                    <tr>
                        <th><?php echo $Lang['ZALADOWCA']; ?>:&nbsp;&nbsp;&nbsp;</th>
                        <td>
                            <?php
                                if($faktura['id_klient_shipper'] > 0){
                                    $consignee = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klient_shipper']}'");
                                    echo "{$consignee['nazwa']}&nbsp;&nbsp;&nbsp;";
                                }
                                echo $faktura['shipper'];
                            ?>
                        </td>
                        <th><?php echo $Lang['WARUNKI_DOSTAWY']; ?>:&nbsp;&nbsp;&nbsp;</th>
                        <td>
                            <?php
                                if($faktura['terms_id'] > 0){
                                    echo $Terms[$faktura['terms_id']];
                                }
                                echo ($faktura['terms_text'] != "" ? "&nbsp;&nbsp;{$faktura['terms_text']}" : "");
                             ?>
                        </td>
                    </tr>
                    <tr style="height: 5px;">
                        <td style="height: 5px; width: 906px;" colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <th><?php echo $Lang['STATEK']; ?>:&nbsp;&nbsp;&nbsp;</th>
                        <td><?php echo $SOI['vessel']." / ".$SOI['voyage_no']; ?></td>
                        <th><?php echo $Lang['BL_NO']; ?>:&nbsp;&nbsp;&nbsp;</th>
                        <td><?php echo $SOI['bl_no']; ?></td>
                    </tr>
                    <tr>
                        <td style="height: 24px; width: 120px; border: 0; text-align: right; font-weight: bold;">INFO:&nbsp;&nbsp;&nbsp;</td>
                        <td style="height: 24px; width: 786px; border: 0;" colspan="3"><?php echo $faktura['uwagi']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="2" cellspacing="0" style="width: 100%;" id="pozycje-table"> 
                    <tr style="height: 24px;">
                        <th style="width: 190px;"><?php echo $Lang['NUMER_ZLECENIA']; ?></th>
                        <th style="width: 66px;"><?php echo $Lang['ILOSC_KONT']; ?></th>
                        <th style="width: 195px;"><?php echo $Lang['TYP']; ?></th>
                        <th style="width: 193px;"><?php echo $Lang['TOWAR']; ?></th>
                        <th style="width: 149px;"><?php echo $Lang['WAGA']; ?></th>
                        <th style="width: 116px;">Volumes</th>
                    </tr>
                        <?php
                             foreach($Conty as $Cont){
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                            if($SOI['mode'] == "FCL"){
                                                echo $Size[$Cont['cont_dim_size']]." ".$Types[$Cont['cont_dim_type']]."&nbsp;&nbsp;&nbsp;";
                                            }
                                            echo $Cont['cont_no'];
                                         ?>
                                    </td>
                                    <td><?php echo $Cont['cont_pcs']; ?></td>
                                    <td>&nbsp;<?php echo $Cont['cont_type']; ?>&nbsp;</td>
                                    <td><?php echo $Cont['cont_description']; ?></td>
                                    <td><?php echo number_format($Cont['cont_weight'],2,",","."); ?> KGS</td>
                                    <td><?php echo number_format($Cont['cont_volume'],2,",","."); ?> CBM</td>
                                </tr>
                            <?php
                        }
                        if(count($Conty) < 3){
                            $DodajWiersze = 3 - count($Conty); 
                            for($j = 0; $j < $DodajWiersze; $j++){
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                </table>
                <table cellpadding="2" cellspacing="0" style="width: 100%;" id="pozycje-table"> 
                    <tr>
                        <td style="width: 430px; text-align: right; border-top: 1px solid #929494;"><span style="padding-right: 8px;"><?php echo $Lang['OGOLEM']; ?></span></td>
                        <th style="width: 120px;"><?php echo $Lang['WARTOSC_NETTO']; ?></th>
                        <th style="width: 120px;"><?php echo $Lang['VAT']; ?></th>
                        <th style="width: 120px;"><?php echo $Lang['KWOTA_VAT']; ?></th>
                        <th style="width: 119px;"><?php echo $Lang['WARTOSC_BRUTTO']; ?></th>
                    </tr>
                    <?php
                   $z1 = "SELECT *,
                           SUM(netto) as suma_netto,
                           SUM(kwota_vat) as suma_kwot_vat,
                           SUM(brutto) as suma_brutto
                           FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = $ID GROUP BY vat DESC";
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
                        ?>
                        <tr>
                            <td style="border: 0;">&nbsp;</td> 
                            <td style="border-left: 1px solid #dfdddd;"><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_netto, $Waluty[$faktura['id_waluty']]); ?></td>
                            <td><?php echo $pozycje->vat.(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : ""); ?></td>
                            <td><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_kwot_vat, $Waluty[$faktura['id_waluty']]); ?></td>
                            <td><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_brutto, $Waluty[$faktura['id_waluty']]); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                        <tr>
                            <td style="border: 0; text-align: right; color: #FFF; background-image: url('images/faktura-new-design/total_bg.png'); background-repeat: no-repeat; background-position: top right;"><span style="padding-right: 8px;"><?php echo $Lang['RAZEM']; ?></span></td>
                            <td style="border-left: 1px solid #dfdddd; border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_netto, $Waluty[$faktura['id_waluty']]); ?></td>
                            <td style="border-bottom: 1px solid #929494;">&nbsp;</td>
                            <td style="border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_kwot_vat, $Waluty[$faktura['id_waluty']]); ?></td>
                            <td style="border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_brutto, $Waluty[$faktura['id_waluty']]); ?></td>
                        </tr>
                </table>
            </td>
        </tr>
        <?php
            if($faktura['status'] == 0){
        ?>
        <tr>
            <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 26px; font-weight: bold; padding-top: 10px;"><?php echo $Lang['DO_ZAPLATY']." ".Usefull::WyswietlFormatWaluty($suma_brutto, $Waluty[$faktura['id_waluty']]); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo $Lang['SLOWNIE']." ".Usefull::KwotaSlownie($suma_brutto, $Waluty[$faktura['id_waluty']], $faktura['szablon_faktura']); ?></td>
        </tr>
            <?php
            if($faktura['wplacono'] > 0){
                $pozostalo = $suma_brutto - $faktura['wplacono'];
            ?>
            <tr>
                <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo "Wpłacono: ".Usefull::WyswietlFormatWaluty($faktura['wplacono'], $Waluty[$faktura['id_waluty']]); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo "Pozostało: ".Usefull::WyswietlFormatWaluty($pozostalo, $Waluty[$faktura['id_waluty']]); ?></td>
            </tr>
            <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 10px;"><?php echo $Lang['OPLACONA']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>