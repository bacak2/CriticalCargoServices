<div style="width:750px; min-height: 1000px; margin: 0 auto 0 auto; text-align: left;">
    <table cellpadding="10" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 50%;" colspan="2"><img src="images/logo-new.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/></td>
            <td style="width: 50%; text-align: right; font-size: 10pt;" colspan="2"><br /><br /><b>AIR ORDER NO <?php echo $zlecenie['numer_zlecenia']; ?></b></td>
        </tr>
        <tr>
            <td style="width: 50%;" colspan="2">
                <b>SHIPPER:</b><br />
                <?php
                    if($zlecenie['id_klient_shipper'] > 0){
                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_shipper']}'");
                        echo $Klient['nazwa']."<br /><br />";
                        echo $Klient['adres']."<br />";
                        echo $Klient['kod_pocztowy']." ".$Klient['miejscowosc']."<br /><br />";
                    }
                    echo ($zlecenie['shipper'] != "" ? $zlecenie['shipper']."<br />" : "");
                ?>
            </td>
            <td style="width: 50%;" colspan="2">
                <b>CONSIGNEE:</b><br />
                <?php
                    if($zlecenie['id_klient_consignee'] > 0){
                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$zlecenie['id_klient_consignee']}'");
                        echo $Klient['nazwa']."<br /><br />";
                        echo $Klient['adres']."<br />";
                        echo $Klient['kod_pocztowy']." ".$Klient['miejscowosc']."<br /><br />";
                    }
                    echo ($zlecenie['consignee'] != "" ? $zlecenie['consignee']."<br />" : "");
                ?>
            </td>

        </tr>
        <tr>
            <td colspan="4">
                <b>AGENT:</b><br />
                <?php
                    if($zlecenie['id_przewoznik_agent'] > 0){
                        $Klient = $this->Baza->GetData("SELECT * FROM orderplus_przewoznik WHERE id_przewoznik = '{$zlecenie['id_przewoznik_agent']}'");
                        echo $Klient['nazwa']."<br /><br />";
                        echo nl2br($Klient['dane_firmy'])."<br />";
                    }
                    echo ($zlecenie['agent'] != "" ? $zlecenie['agent']."<br />" : "");
                ?>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-left: 10px;" colspan="2">
                <b>CARRIER / FLIGHT NO</b><br />
               <?php
                    echo $zlecenie['carrier']."&nbsp;&nbsp;&nbsp;&nbsp;".$zlecenie['flight_no'];
                ?>
            </td>
            <td style="width: 25%; padding-left: 10px; text-align: center;">
                <b>POL</b><br />
                <?php
                    echo $zlecenie['pol'];
                ?>
            </td>
            <td style="width: 25%; padding-left: 10px; text-align: center;">
                <b>POD</b><br />
                <?php
                    echo $zlecenie['pod'];
                ?>
            </td>
        </tr>
        <tr>
            <td style="width: 50%;" colspan="2"><b>TERMS:</b> <?php echo $Terms[$zlecenie['terms_id']].($zlecenie['terms_text'] != "" ? " {$zlecenie['terms_text']}" : ""); ?></td>
            <td style="width: 50%;" colspan="2"><b>MAWB:</b> <?php echo $zlecenie['mawb']; ?></td>
        </tr>
        <tr>
            <td style="width: 50%;" colspan="2"><b>INSURANCE:</b> <?php echo strtoupper($zlecenie['insurence']); ?></td>
            <td style="width: 50%;" colspan="2"><b>HAWB:</b> <?php echo $zlecenie['hawb']; ?></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center;">
                <br /><br/>
                   <b>--------------------------------------------------- CARGO DETAILS ---------------------------------------------------</b>
                <br /><br />
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table cellpadding="3" cellspacing="0" style="border: 0; width: 100%;">
                    <tr>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">PCS</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">TYPE</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold;">DESCRIPTION</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">WEIGHT</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">VOLUME</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">CHARGEABLE<br />WEIGHT</td>
                        <td style="border-bottom: 1px solid #000; font-weight: bold; text-align: center;">DANGEROUS<br />GOODS</td>
                    </tr>
                    <?php
                        foreach($Containers as $Cont){
                    ?>
                        <tr>
                            <td><?php echo $Cont['cont_pcs']; ?></td>
                            <td><?php echo $Cont['cont_type']; ?></td>
                            <td><?php echo $Cont['cont_description']; ?></td>
                            <td style="text-align: right;"><?php echo number_format($Cont['cont_weight'],2,",","."); ?> KGS</td>
                            <td style="text-align: right;"><?php echo number_format($Cont['cont_volume'],2,",","."); ?> CBM</td>
                            <td style="text-align: right;"><?php echo number_format($Cont['chargeable_weight'],2,",","."); ?></td>
                            <td style="text-align: center;"><?php echo $Cont['cont_dgr'].($Cont['cont_dgr'] == "Yes" ? " ".$Cont['cont_class']." ".$Cont['cont_un'] : ""); ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                </table>
                <br /> <br /> <br />
            </td>
        </tr>
         <tr>
            <td colspan="4" style="text-align: center;">
                   <b>------------------------------------------------------ ADD. INFO ------------------------------------------------------</b>
                <br /><br />
            </td>
        </tr>
        <tr>
            <td colspan="4"><b>CUSTOM CLEARANCE:</b> <?php echo $zlecenie['customs_clearence']; ?></td>
        </tr>
        <tr>
            <td colspan="4">
                <b>DIMENSIONS:</b><br />
                <?php
                    echo nl2br($zlecenie['dimensions']);
                ?>
            </td>
        </tr>
    </table>
</div>