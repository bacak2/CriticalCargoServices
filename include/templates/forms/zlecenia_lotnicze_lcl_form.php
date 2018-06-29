<table border="0" cellpadding="10" cellspacing="0" id="LCL-Table" style="border-collapse: collapse;">
    <tr>
        <td style="border-bottom: 2px solid #000;">PCS<br />(ilosc opakowan)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">TYPE<br />(rodzaj opakowan) </td>
        <td style="border-bottom: 2px solid #000;">DESCRIPTION<br />(opis towaru) </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">WEIGHT<br />(waga)</td>
        <td style="border-bottom: 2px solid #000;">VOLUME<br />(objetosc)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CHARGEABLE WEIGHT</td>
        <td style="border-bottom: 2px solid #000;">DGR<br />(lad niebez.)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CLASS<br />(klasa ladunku niebez.) </td>
        <td style="border-bottom: 2px solid #000;">UN<br />(rodzaj lad niebez)</td>
    </tr>
<?php
    $Idx = 0;
    foreach($Values['LCL'] as $LCL){
?>
    <tr id="<?php echo "fcl-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_pcs]", $LCL['cont_pcs'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_type]", $LCL['cont_type']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_description]", $LCL['cont_description']); ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_weight]", $LCL['cont_weight'], "style='width: 40px'"); echo "&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_volume]", $LCL['cont_volume'], "style='width: 40px'"); echo "&nbsp;CBM"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[LCL][$Idx][chargeable_weight]", $LCL['chargeable_weight'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("Air[LCL][$Idx][cont_dgr]", $TakNie, $LCL['cont_dgr'], "onchange='ChangeDGR(this, \"lcl\", $Idx);'") ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_class]", $LCL['cont_class'], "id='lcl-class-$Idx' ".($LCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[LCL][$Idx][cont_un]", $LCL['cont_un'], "id='lcl-un-$Idx' ".($LCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
    </tr>
<?php
        $Idx++;
    }
?>
</table>