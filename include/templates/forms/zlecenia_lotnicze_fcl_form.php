<table border="0" cellpadding="4" cellspacing="0" id="FCL-Table" style="border-collapse: collapse;">
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
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">&nbsp;</td>
    </tr>
<?php
    $Size = UsefullBase::GetSizes($this->Baza);
    $Types = UsefullBase::GetTypes($this->Baza);
    $Idx = 0;
    foreach($Values['FCL'] as $FCL){
?>
    <tr id="<?php echo "fcl-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_pcs]", $FCL['cont_pcs'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_type]", $FCL['cont_type']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_description]", $FCL['cont_description']); ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_weight]", $FCL['cont_weight'], "style='width: 40px'"); echo "&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_volume]", $FCL['cont_volume'], "style='width: 40px'"); echo "&nbsp;CBM"; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[FCL][$Idx][chargeable_weight]", $FCL['chargeable_weight'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("Air[FCL][$Idx][cont_dgr]", $TakNie, $FCL['cont_dgr'], "onchange='ChangeDGR(this, \"fcl\", $Idx);'") ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_class]", $FCL['cont_class'], "id='fcl-class-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Air[FCL][$Idx][cont_un]", $FCL['cont_un'], "id='fcl-un-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemoveContainer($Idx);'"); ?><?php $Form->PoleHidden("Air[FCL][$Idx][order_fcl_id]", $FCL['order_fcl_id']); ?></td>
    </tr>
<?php
        $Idx++; 
    }
?>
</table>
<?php
    $Form->PoleHidden("Containers", $Idx, "id='Containers'");
    $Form->PoleButton("AddNewContainer", "Dodaj ładunek", "onclick='AddContainerAir();' style='margin: 20px;'"); 
?>