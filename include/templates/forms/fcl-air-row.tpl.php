<?php
    $Idx = $_POST['next'];
    $PoleName = (isset($_POST['name']) ? $_POST['name'] : "Air");
    $FCL = array();
    $Size = UsefullBase::GetSizes($this->Baza);
    $Types = UsefullBase::GetTypes($this->Baza);
    $TakNie = Usefull::GetTakNie2();
    $Form = new FormularzSimple();
 ?>
    <tr id="<?php echo "fcl-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_pcs]", $FCL['cont_pcs'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_type]", $FCL['cont_type']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_description]", $FCL['cont_description']); ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_weight]", $FCL['cont_weight'], "style='width: 40px'"); echo "&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_volume]", $FCL['cont_volume'], "style='width: 40px'"); echo "&nbsp;CBM"; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][chargeable_weight]", $FCL['chargeable_weight'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("{$PoleName}[FCL][$Idx][cont_dgr]", $TakNie, $FCL['cont_dgr'], "onchange='ChangeDGR(this, \"fcl\", $Idx);'") ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_class]", $FCL['cont_class'], "id='fcl-class-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("{$PoleName}[FCL][$Idx][cont_un]", $FCL['cont_un'], "id='fcl-un-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usun", "onclick='RemoveContainer($Idx);'"); ?></td>
    </tr>