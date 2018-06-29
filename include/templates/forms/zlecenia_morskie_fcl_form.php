<table border="0" cellpadding="4" cellspacing="0" id="FCL-Table" style="border-collapse: collapse;">
    <tr>
        <td style="border-bottom: 2px solid #000;">CONT. DIM<br />(rozmiar/typ kontenera)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">NO.<br />(numer kontenera)</td>
        <td style="border-bottom: 2px solid #000;">PCS<br />(ilosc opakowan)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">TYPE<br />(rodzaj opakowan) </td>
        <td style="border-bottom: 2px solid #000;">DESCRIPTION<br />(opis towaru) </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">WEIGHT<br />(waga)</td>
        <td style="border-bottom: 2px solid #000;">VOLUME<br />(objetosc)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">SEAL<br />(plomba)</td>
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
        <td style="border-bottom: 1px solid #888;"><?php
            $Form->PoleSelect("Sea[FCL][$Idx][cont_dim_size]", $Size, $FCL['cont_dim_size'], "style='margin-bottom: 10px;'");
            echo "<br />";
            $Form->PoleSelect("Sea[FCL][$Idx][cont_dim_type]", $Types, $FCL['cont_dim_type']);
            ?>
        </td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleHidden("Sea[FCL][$Idx][order_fcl_id]", $FCL['order_fcl_id']); ?><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_no]", $FCL['cont_no']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_pcs]", $FCL['cont_pcs'], "style='width: 40px'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_type]", $FCL['cont_type']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_description]", $FCL['cont_description']); ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_weight]", $FCL['cont_weight'], "style='width: 40px'"); echo "&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_volume]", $FCL['cont_volume'], "style='width: 40px'"); echo "&nbsp;CBM"; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_seal]", $FCL['cont_seal']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("Sea[FCL][$Idx][cont_dgr]", $TakNie, $FCL['cont_dgr'], "onchange='ChangeDGR(this, \"fcl\", $Idx);'") ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_class]", $FCL['cont_class'], "id='fcl-class-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_un]", $FCL['cont_un'], "id='fcl-un-$Idx' ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemoveContainer($Idx);'"); ?></td>
    </tr>
<?php
        $Idx++; 
    }
?>
</table>
<?php
    $Form->PoleHidden("Containers", $Idx, "id='Containers'");
    $Form->PoleButton("AddNewContainer", "Dodaj kontener", "onclick='AddContainer();' style='margin: 20px;'");
?>