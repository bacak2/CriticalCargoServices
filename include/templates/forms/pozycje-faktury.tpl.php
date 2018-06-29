<table border="0" cellpadding="4" cellspacing="0" id="Positions-Table" style="border-collapse: collapse;">
    <tr>
        <th style="border-bottom: 2px solid #000;">LP</th>
        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">NAZWA USŁUGI</th>
        <th style="border-bottom: 2px solid #000;">ILOŚĆ</th>
        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CENA JEDN. NETTO</th>
        <th style="border-bottom: 2px solid #000;">WART. NETTO </th>
        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">VAT</th>
        <th style="border-bottom: 2px solid #000;">KWOTA VAT</th>
        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">BRUTTO JEDN.</th>
        <th style="border-bottom: 2px solid #000;">WART. BRUTTO </th>
        <th style="border-bottom: 2px solid #000; background-color: #F0F0F0;">&nbsp;</th>
    </tr>
<?php
    $Idx = 1;
    $Form = new FormularzSimple();
    foreach($Wartosc as $FCL){
?>
    <tr id="<?php echo "position-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][lp]", $Idx, "style='width: 40px;'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][opis]", $FCL['opis'], "style='width: 100%;'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][ilosc]", $FCL['ilosc'], "style='width: 40px' id='ilosc$Idx' onchange=\"Oblicz('ilosc$Idx', 'netto_jednostki$Idx', 'netto$Idx'); Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][netto_jednostki]", $FCL['netto_jednostki'], "style='width: 80px;' id='netto_jednostki$Idx' onchange=\"Oblicz('ilosc$Idx', 'netto_jednostki$Idx', 'netto$Idx'); Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][netto]", $FCL['netto'], "style='width: 80px;' id='netto$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][vat]", $FCL['vat'], "style='width: 40px;' id='vat$Idx' onchange=\"Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?> %</td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][kwota_vat]", $FCL['kwota_vat'], "style='width: 80px;' id='kwota_vat$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][brutto_jednostki]", $FCL['brutto_jednostki'], "style='width: 80px;' id='brutto_jednostki$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][brutto]", $FCL['brutto'], "style='width: 80px;' id='brutto$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemovePosition($Idx);'"); ?></td>
    </tr>
<?php
        $Idx++;
    }
?>
</table>
<?php
    $Form->PoleHidden("Positions", $Idx, "id='Positions'");
    $Form->PoleButton("AddNewPosition", "Dodaj kolejną pozycję", "onclick='AddPosition();' style='margin: 20px;'");
?>