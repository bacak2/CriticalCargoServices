<?php
    $data = date("Y-m-d");
    if (isset($_POST['nowy'])) {
        $Fak = $_POST['Faktury'];
        $Costs = $_POST['Koszty'];
    }
    else {
        foreach($Faktury as $Faktura){
            $Fak[$Faktura['id_faktury']] = $Faktura;
        }
        foreach($Koszty as $Koszt){
            $Costs[$Koszt['id_koszt']] = $Koszt;
        }
    }
    $nr_zlecenia = $zlecenie['numer_zlecenia'];
    $Form = new FormularzSimple();

?>
    <?php
        $Form->FormStart();
        $Form->PoleHidden("nowy", "stary", "id='nowy'");
    ?>
<script type="text/javascript" src="js/koszty.js"></script>
<table class="formularz">
    <tr><th>Zlecenie nr.</th><td><?php echo $nr_zlecenia; ?></td></tr>
	<tr>
            <td colspan="2">
                <br /><b><u>FAKTURY:</u></b><br /><br />
                <table border="0" cellpadding="7" cellspacing="0" id="Positions-Table" style="margin: 15px; border-collapse: collapse;">
                    <tr>
                        <th style="border-bottom: 2px solid #000;">NUMER FAKTURY</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">PLANOWANA ZAPŁATA</th>
                        <th style="border-bottom: 2px solid #000;">RZECZYWISTA ZAPŁATA KLIENTA</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">TERMIN PŁATNOŚCI</th>
                        <th style="border-bottom: 2px solid #000;">STATUS</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">KOMENTARZ</th>
                    </tr>
                <?php
                    foreach($Faktury as $Faktura){
                ?>
                    <tr>
                        <td style="border-bottom: 1px solid #888;"><?php echo $Faktura['numer']; ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleData("Faktury[{$Faktura['id_faktury']}][planowana_zaplata]", $Fak[$Faktura['id_faktury']]['planowana_zaplata'], "planowana_{$Faktura['id_faktury']}"); ?></td>
                        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleData("Faktury[{$Faktura['id_faktury']}][rzeczywista_zaplata]", $Fak[$Faktura['id_faktury']]['rzeczywista_zaplata'], "rzeczywista_{$Faktura['id_faktury']}"); ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleData("Faktury[{$Faktura['id_faktury']}][termin_platnosci]", $Fak[$Faktura['id_faktury']]['termin_platnosci'], "termin_platnosci_{$Faktura['id_faktury']}"); ?></td>
                        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("Faktury[{$Faktura['id_faktury']}][platnosci_status]", $StatusyKlient, $Fak[$Faktura['id_faktury']]['platnosci_status'], "rel='{$Faktura['id_faktury']}'"); ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleTextarea("Faktury[{$Faktura['id_faktury']}][platnosci_komentarz]", $Fak[$Faktura['id_faktury']]['platnosci_komentarz'], "style='height: 40px; width: 100px;'"); ?></td>                        
                    </tr>
                <?php
                    }
                ?>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br /><b><u>KOSZTY:</u></b><br /><br />
                <table border="0" cellpadding="7" cellspacing="0" id="Positions-Table" style="border-collapse: collapse; margin: 15px;">
                    <tr>
                        <th style="border-bottom: 2px solid #000;">DOSTAWCA</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">OPIS</th>
                        <th style="border-bottom: 2px solid #000;">KOSZT</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">NUMER FAKTURY</th>
                        <th style="border-bottom: 2px solid #000;">TERMIN PŁATNOŚCI</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">PLANOWANA ZAPŁATA</th>
                        <th style="border-bottom: 2px solid #000;">RZECZYWISTA ZAPŁATA</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">KWOTA NA FAKTURZE</th>
                        <th style="border-bottom: 2px solid #000;">STAWKA VAT</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">KURS</th>
                        <th style="border-bottom: 2px solid #000;">STATUS</th>
                        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">KOMENTARZ</th>
                    </tr>
                <?php
                    foreach($Koszty as $Koszt){
                ?>
                    <tr>
                        <td style="border-bottom: 1px solid #888;"><?php echo $this->Przewoznicy[$Koszt['id_przewoznik']]; ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;">&nbsp;<?php echo $Koszt['opis']; ?>&nbsp;</td>
                        <td style="border-bottom: 1px solid #888;"><?php echo $Koszt['koszt']." ".$this->Waluty[$Koszt['waluta']]; ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Koszty[{$Koszt['id_koszt']}][nr_faktury]", $Costs[$Koszt['id_koszt']]['nr_faktury'], "style='width: 120px;' id='faktura_check_{$Koszt['id_koszt']}'"); ?></td>
                        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleData("Koszty[{$Koszt['id_koszt']}][termin_platnosci]", $Costs[$Koszt['id_koszt']]['termin_platnosci'], "termin_{$Koszt['id_koszt']}"); ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleData("Koszty[{$Koszt['id_koszt']}][planowana_zaplata_przew]", $Costs[$Koszt['id_koszt']]['planowana_zaplata_przew'], "planowana_zaplata_przew_{$Koszt['id_koszt']}"); ?></td>
                        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleData("Koszty[{$Koszt['id_koszt']}][rzeczywista_zaplata]", $Costs[$Koszt['id_koszt']]['rzeczywista_zaplata'], "rzeczywista_koszt_{$Koszt['id_koszt']}"); ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;">
                                <?php $Form->PoleInputText("Koszty[{$Koszt['id_koszt']}][koszt_kwota_1]", $Costs[$Koszt['id_koszt']]['koszt_kwota_1'], "style='width: 70px;'"); ?><br />
                                <?php $Form->PoleInputText("Koszty[{$Koszt['id_koszt']}][koszt_kwota_2]", $Costs[$Koszt['id_koszt']]['koszt_kwota_2'], "style='width: 70px;'"); ?><br />
                        </td>
                        <td style="border-bottom: 1px solid #888;">
                                <?php $Form->PoleSelect("Koszty[{$Koszt['id_koszt']}][stawka_vat]", $StawkiVat, $Costs[$Koszt['id_koszt']]['stawka_vat']); ?><br />
                                <?php $Form->PoleSelect("Koszty[{$Koszt['id_koszt']}][stawka_vat_2]", $StawkiVat, $Costs[$Koszt['id_koszt']]['stawka_vat_2']); ?>
                        </td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Koszty[{$Koszt['id_koszt']}][kurs]", $Costs[$Koszt['id_koszt']]['kurs'], "style='width: 60px;'"); ?></td>
                        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("Koszty[{$Koszt['id_koszt']}][platnosci_status]", $Statusy, $Costs[$Koszt['id_koszt']]['platnosci_status'], "rel='{$Koszt['id_koszt']}' class='check_status'"); ?></td>
                        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleTextarea("Koszty[{$Koszt['id_koszt']}][platnosci_komentarz]", $Costs[$Koszt['id_koszt']]['platnosci_komentarz'], "style='height: 40px; width: 100px;'"); ?></td>
                    </tr>
                <?php
                    }
                ?>
                </table>
            </td>
        </tr>
        <tr><td colspan="2" style="text-align: center;"><input type="image" onclick="SaveKoszty(); return false;" src="images/ok.gif" border="0"> <a href="<?php echo $this->LinkPowrotu; ?>"><img src="images/anuluj.gif" border="0"></a></td></tr>
        <?php
            $Form->FormEnd();
        ?>
</table>