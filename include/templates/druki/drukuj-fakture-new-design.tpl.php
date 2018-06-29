<div style="page-break-after: always;">
<div class="layout_new_design" style="<?php echo ($_GET['bg'] == "no" ? "" : "background-image: url('images/faktura-new-design/".($faktura['szablon_faktura'] == 'ENG' ? "bottom-en" : "bottom-pl").($faktura['id_faktury'] >= 16318 ? "-09-2013" : "").($koniec_faktury == false ? "-no-signature" : "").".png');"); ?> background-repeat: no-repeat; background-position: bottom left;">
    <table style="margin: 0;" width="100%" cellpadding="0" cellspacing="0">
        <tr id="print">
            <td style="font-size: 12px; text-align:left;padding-right: 7px" colspan="2">
                <a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
            </td>
        </tr>
        <tr> 
          <td style="width: 288px; vertical-align: top; height: 567px; <?php echo ($_GET['bg'] == "no" ? "" : "background-image: url('images/faktura-new-design/top1.png'); background-repeat: no-repeat;"); ?>">
              <table cellpadding="0" cellspacing="0" style="width: 100%;">
                  <tr>
                      <td style="height: 194px; vertical-align: top;">
                          <div style="margin: 158px 0px 0px 19px; font-size: 25px; font-weight: bold;"><?php echo $Lang['SPRZEDAWCA'] ?></div>
                      </td>
                  </tr>
                  <tr>
                      <td style="height: 117px; vertical-align: top;">
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
                            <div style="margin: 9px 0px 0px 19px; font-size: 25px; font-weight: bold;"><?php echo $Lang['NABYWCA']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 205px; vertical-align: top;">
                            <div style="margin: 19px 0px 0px 17px; font-size: 13px; font-weight: bold; font-family: 'Trebuchet MS';">
                                <?php
                                    $klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klienta']}'");
                                ?>
                                <?php echo $klient['nazwa']; ?><br />
				<?php echo $klient['adres']; ?><br /><?php echo($klient['kod_pocztowy']); ?> <?php echo($klient['miejscowosc']); ?><br />
				<?php echo $Lang['NIP']; ?> <?php echo ($klient['nip']); ?><br />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 621px; vertical-align: top; height: 567px; <?php echo ($_GET['bg'] == "no" ? "" : "background-image: url('images/faktura-new-design/top2.png');"); ?>">
                <div style="position: relative; height: 188px;"> 
                    <div style="text-align: right; top: 100px; position: absolute; width: 605px; font-size: 40px; color: #FFF; font-weight: bold; "><?php echo ($ID > 17152 ? ($ID <= 18149 ? $Lang['FAKTURA_VAT_UPPER_POPRAWIONE'] : $Lang['FAKTURA_VAT_UPPER_BEZ_VAT']) : $Lang['FAKTURA_VAT_UPPER']) ?></div>
                    <div style="text-align: left; position: absolute; top: 154px; left: 14px; width: 607px; font-size: 16px; color: #FFF; font-weight: bold; font-family: 'Trebuchet MS';"><?php echo $Lang['NR_UPPER']." ". $faktura['numer']; ?></div>
                    <div style="text-align: right; position: absolute; top: 154px; width: 603px; font-size: 16px; color: #FFF; font-weight: bold; "><?php echo ($ID <= 18149 ? $Lang['ORYGINA'] : ""); ?></div>
                </div>
                <div style="position: relative; height: 122px;">
                    <table cellpadding="0" cellspacing="0">
                        <tr> 
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['DATA_WYSTAWIENIA_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo $Lang['MIEJSCE_WYSTAWIENIA_NEW']; ?></td>
                            <td style="width: 5px;"></td>
                            <td style="height: 74px; width: 118px; vertical-align: middle; text-align: center; color: #000; font-size: 16px; font-weight: bold;"><?php echo ($faktura['id_faktury'] > 73 ? $Lang['DATA_WYKONANIA_USLUGI'] : $Lang['DATA_SPRZEDAZY_NEW']); ?></td>
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
                            <td style="height: 48px; width: 118px; vertical-align: middle; text-align: center; color: #231f20; font-size: 16px; font-weight: bold;"><?php echo ($faktura['szablon_faktura'] == 'ENG' ? $faktura['forma_en'] : $faktura['forma']); ?></td>
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
                                <?php
                                    echo implode("<br />", $dane_konta['bank']);
                                    ?>
                            </td>
                            <td style="width: 24px;"></td> 
                            <td style="height: 84px; width: 279px; vertical-align: top; text-align: left; color: #231f20; font-size: 14px; font-weight: bold; line-height: 140%;">
                                <?php
                                    if($faktura['szablon_faktura'] == 'ENG'){
                                        ?>
                                        IBAN PL<br />
                                            <?php
                                            if($faktura['waluta'] == "USD"){
                                            ?>
                                                USD: <?php echo $dane_konta['usd']; ?><br />
                                            <?php
                                            }else{
                                            ?>
                                                PLN: <?php echo $dane_konta['pln']; ?><br />
                                                EUR: <?php echo $dane_konta['eur']; ?><br />
                                            <?php
                                            }
                                            ?>
                                            SWIFT: <?php echo $dane_konta['swift']; ?>
                                         <?php
                                    }else{
                                       ?>
                                            PLN: <?php echo str_replace("PL", "", $dane_konta['pln']); ?><br />
                                            <?php
                                            if($faktura['waluta'] == "EUR" || $faktura['waluta'] == "PLN"){
                                            ?>
                                                EUR: <?php echo $dane_konta['eur']; ?><br />
                                                SWIFT: <?php echo $dane_konta['swift']; ?><br />
                                            <?php
                                            }else if($faktura['waluta'] == "USD"){
                                            ?>
                                                USD: <?php echo $dane_konta['usd']; ?><br />
                                                SWIFT: <?php echo $dane_konta['swift']; ?><br />
                                            <?php
                                            }
                                    }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="position: relative; height: 106px; margin-top: 8px; left: 93px; text-align: right; font-size: 14px; font-weight: bold; width: 520px;">
                    <?php
                    $uwagi = stripslashes($faktura['uwagi']);
                    if($faktura['szablon_faktura'] == 'ENG'){
                        $pl_version = array('numer zlecenia klienta', 'data zał.', 'data rozł.', 'data zał:', 'data rozł:', 'data załadunku', 'data rozładunku');
                        $en_version = array('Order No.', 'loading date', 'day of delivery', 'loading date:', 'day of delivery:', 'loading date', 'day of delivery');
                        $uwagi = str_replace($pl_version, $en_version, $uwagi);
                    }

                    echo nl2br($uwagi);
                   ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="2" cellspacing="0" style="width: 100%;" id="pozycje-table"> 
                    <tr style="height: 24px;">
                        <th style="width: 62px;"><?php echo $Lang['LP']; ?></th>
                        <th style="width: 270px;"><?php echo $Lang['NAZWA']; ?></th>
                        <th style="width: 100px;"><?php echo $Lang['ILOSC']; ?></th>
                        <th style="width: 100px;"><?php echo $Lang['JEDNOSTKA_MIARY']; ?></th>
                        <th style="width: 130px;"><?php echo $Lang['CENA_JEDNOSTKOWA']; ?></th>
                        <th style="width: 130px;"><?php echo $Lang['WARTOSC_SPRZEDAZY']; ?></th>
                        <th><?php echo $Lang['VAT']; ?></th>
                    </tr>
                        <?php
                            foreach($Pozycje as $Pos){ 
                                ?>
                                <tr>
                                    <td><?php echo $lp; ?></td>
                                    <td>
                                        <?php
                                        if($faktura['szablon_faktura'] == 'ENG'){
                                            echo str_replace(array('Wewnątrzwspólnotowa usługa spedycyjna', 'Transport międzynarodowy'), 'Intraeuropean Forwarding Service', $Pos['opis']);
                                        }else{
                                            echo $Pos['opis'];
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $Pos['ilosc']; ?></td>
                                    <td>&nbsp;<?php echo ($faktura['szablon_faktura'] == 'ENG' ? str_replace("szt", "pcs", $Pos['jednostka']) : $Pos['jednostka']); ?>&nbsp;</td>
                                    <td><?php echo Usefull::WyswietlFormatWaluty($Pos['netto_jednostki'], $faktura['waluta']); ?></td>
                                    <td><?php echo Usefull::WyswietlFormatWaluty($Pos['netto'], $faktura['waluta']); ?></td>
                                    <td><?php echo "{$Pos['vat']}".(!in_array(strtolower($Pos['vat']), array("np", "zw")) ? "%" : ""); ?></td>
                                </tr>
                            <?php
                            $lp++;
                        }
                        if(count($Pozycje) < 4){
                            $DodajWiersze = 4 - count($Pozycje);
                            for($j = 0; $j < $DodajWiersze; $j++){
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
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
                        if($koniec_faktury == true){
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: right; border-top: 1px solid #929494;"><span style="padding-right: 8px;"><?php echo $Lang['OGOLEM']; ?></span></td>
                        <th><?php echo $Lang['WARTOSC_NETTO']; ?></th>
                        <th><?php echo $Lang['VAT']; ?></th>
                        <th><?php echo $Lang['KWOTA_VAT']; ?></th>
                        <th><?php echo $Lang['WARTOSC_BRUTTO']; ?></th>
                    </tr>
                    <?php
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
                    while($pozycje = mysql_fetch_object($w1)){
                        $suma_brutto += $pozycje->suma_brutto;
                        $suma_netto += $pozycje->suma_netto;
                        $suma_kwot_vat += $pozycje->suma_kwot_vat;
                        ?>
                        <tr>
                            <td colspan="3" style="border: 0;">&nbsp;</td>
                            <td style="border-left: 1px solid #dfdddd;"><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_netto, $faktura['waluta']); ?></td>
                            <td><?php echo $pozycje->vat.(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : ""); ?></td>
                            <td><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_kwot_vat, $faktura['waluta']); ?></td>
                            <td><?php echo Usefull::WyswietlFormatWaluty($pozycje->suma_brutto, $faktura['waluta']); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                        <tr>
                            <td colspan="2"></td>
                            <td style="border: 0; text-align: right; color: #FFF; <?php echo ($_GET['bg'] == "no" ? "" : "background-image: url('images/faktura-new-design/total_bg.png'); background-repeat: no-repeat; background-position: top right;"); ?>"><span style="padding-right: 8px;"><?php echo $Lang['RAZEM']; ?></span></td>
                            <td style="border-left: 1px solid #dfdddd; border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_netto, $faktura['waluta']); ?></td>
                            <td style="border-bottom: 1px solid #929494;">&nbsp;</td>
                            <td style="border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_kwot_vat, $faktura['waluta']); ?></td>
                            <td style="border-bottom: 1px solid #929494;"><?php echo Usefull::WyswietlFormatWaluty($suma_brutto, $faktura['waluta']); ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                </table>
            </td>
        </tr>
        <?php
            if($koniec_faktury == true){
                if($faktura['status'] == 0){
        ?>
        <tr>
            <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 26px; font-weight: bold; padding-top: 10px;"><?php echo $Lang['DO_ZAPLATY']." ".Usefull::WyswietlFormatWaluty($suma_brutto, $faktura['waluta']); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo $Lang['SLOWNIE']." ".Usefull::KwotaSlownie($suma_brutto, $faktura['waluta'], $faktura['szablon_faktura']); ?></td>
        </tr>
            <?php
            if($faktura['wplacono'] > 0){
                $pozostalo = $suma_brutto - $faktura['wplacono'];
            ?>
            <tr>
                <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo "Wpłacono: ".Usefull::WyswietlFormatWaluty($faktura['wplacono'], $faktura['waluta']); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; color: #3d3e3d; font-size: 23px; font-weight: bold; padding-top: 8px;"><?php echo "Pozostało: ".Usefull::WyswietlFormatWaluty($pozostalo, $faktura['waluta']); ?></td>
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
            }
        ?>
    </table>
</div>
</div>