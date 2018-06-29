<div id="print" style="width:793px; margin: 0 auto 0 auto; font-size: 12px; text-align:left; padding-right: 7px">
    <a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a> | <a href="javascript:FontResize('down');">zmniejsz czcionkę</a> | <a href="javascript:FontResize('up');">powiększ czcionkę</a>
</div>
<div style="width:793px; height: 1122px; margin: 0 auto 0 auto; text-align: left; background: url('images/bl-tlo.jpg') no-repeat;">
    <table cellpadding="10" cellspacing="0" style="width: 673px; margin-left: 92px; margin-top: 45px;">

        <tr>
            <td style="width: 358px; height: 107px; padding-top: 17px;" colspan="2">
                <?php
                    if($zlecenie['id_klient_shipper'] > 0){
                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_shipper']}'");
                        echo $Klient['nazwa']."<br />";
                        echo $Klient['adres']."<br />";
                        echo $Klient['kod_pocztowy']." ".$Klient['miejscowosc']."<br />";
                    }
                    echo ($zlecenie['shipper'] != "" ? $zlecenie['shipper'] : "");
                ?>
            </td>
            <td style="padding-left: 160px;"><?php echo $zlecenie['numer']; ?></td>
        </tr>
        <tr>
            <td style="width: 358px; height: 95px;" colspan="2">
                <?php
                    if($zlecenie['id_klient_consignee'] > 0){
                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_consignee']}'");
                        echo $Klient['nazwa']."<br />";
                        echo $Klient['adres']."<br />";
                        echo $Klient['kod_pocztowy']." ".$Klient['miejscowosc']."<br />";
                    }
                    echo ($zlecenie['consignee'] != "" ? $zlecenie['consignee'] : "");
                ?>
            </td>
            <td rowspan="3" style="width: 290px;"> 
                <img src="images/logo-new-trans.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/><br /><br />
                Critical Cargo and Freight Services sp. z o. o.<br />
				ul. Solidarności 115 lok. 2<br />
				00-140 Warszawa<br />
				NIP 5252581565<br /><br />

            </td>
        </tr>
          <tr>
            <td style="width: 358px; height: 85px;" colspan="2">
                <?php
                    echo nl2br($zlecenie['notify_adress']);
                ?>
            </td>
        </tr>
         <tr>
            <td style="width: 358px; height: 30px; text-align: right;" colspan="2">
                <span style="padding-right: 40px;"><?php echo $zlecenie['place_of_receipt'];?></span>
            </td>
        </tr>
         <tr> 
            <td style="width: 165px; height: 30px;"><?php echo $zlecenie['vessel'];?></td>
            <td style="width: 173px; height: 30px; text-align: right;">
                <span style="padding-right: 40px;"><?php echo $zlecenie['pol'];?></span>
            </td>
            <td rowspan="2" style="width: 290px;">
                REF. NO <?php echo $SOI['numer_zlecenia']; ?>
            </td>
        </tr>
        <tr>
            <td style="width: 150px; height: 30px;"><?php echo $zlecenie['pod'];?></td>
            <td style="width: 158px; height: 30px; text-align: right;">
                <span style="padding-right: 40px;"><?php echo $zlecenie['place_of_delivery'];?></span>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 390px; padding: 0;"> 
                <br />
                <table cellpadding="3" cellspacing="0" style="border: 0; width: 100%;">
                    <?php
                        foreach($Containers as $Cont){
                    ?>
                        <tr>
                            <td style="width: 180px;"><?php echo $Size[$Cont['cont_dim_size']]." ".$Types[$Cont['cont_dim_type']]." ".$Cont['cont_no']; ?></td>
                            <td style="width: 155px;"><?php echo $Cont['cont_pcs']." ".$Cont['cont_type']; ?></td>
                            <td style="width: 135px;"><?php echo $Cont['cont_description']; ?></td>
                            <td style="width: 100px;"><?php echo number_format($Cont['cont_weight'],2,",","."); ?> KGS</td>
                            <td><?php echo number_format($Cont['cont_volume'],2,",","."); ?> CBM</td>
                        </tr>
                    <?php
                        }
                    ?>
                </table>
                <br /><br />
                <?php
                    echo nl2br($zlecenie['info']);
                ?>
            </td>
        </tr>
         <tr>
            <td style="width: 358px; padding-left: 0;" colspan="2">
                <?php echo $zlecenie['declaration_of_interest']; ?>
            </td>
            <td style="width: 290px; padding-left: 94px;">
               <?php echo $zlecenie['declared_value']; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 240px; padding: 0;">
                <table cellpadding="3" cellspacing="0" style="border: 0; width: 100%; margin-top: 112px;">
                        <tr>
                            <td style="width: 255px; height: 38px;"><?php echo $zlecenie['freight_amount']; ?></td>
                            <td style="width: 162px; height: 38px;"><?php echo $zlecenie['freight_payable']; ?></td>
                            <td style="height: 38px;"><?php echo $zlecenie['place_and_date_issue']; ?></td>
                        </tr>
                         <tr>
                            <td style="width: 255px; height: 38px;">&nbsp;</td>
                            <td style="width: 162px; height: 38px;"><?php echo $zlecenie['number_fbl']; ?></td>
                            <td style="height: 38px;">&nbsp;</td>
                        </tr>
                         <tr>
                            <td colspan="2">
                                <?php
                                    if($zlecenie['id_przewoznik_agent'] > 0){ 
                                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_przewoznik WHERE id_przewoznik = '{$zlecenie['id_przewoznik_agent']}'");
                                        echo $Klient['nazwa']."<br />";
                                    }
                                    echo ($zlecenie['agent'] != "" ? $zlecenie['agent']."<br />" : "");
                                ?>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                </table>
            </td>
        </tr>
    </table>
</div>