<?php
$TakNie = Usefull::GetTakNie2();
?>
<table border="0" cellpadding="4" cellspacing="0" id="FCL-Table" style="width: 100%; border-collapse: collapse;">
    <tr>
         <?php
            if($AOID > 0){
            ?>
            <td style="border-bottom: 2px solid #000;">&nbsp;</td>
            <?php
        }
        ?>
        <td style="border-bottom: 2px solid #000;">PCS<br />(ilosc opakowan)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">TYPE<br />(rodzaj opakowan) </td>
        <td style="border-bottom: 2px solid #000;">DESCRIPTION<br />(opis towaru) </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">WEIGHT<br />(waga)</td>
        <td style="border-bottom: 2px solid #000;">VOLUME<br />(objetosc)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CHARGEABLE WEIGHT<br />(waga płatna)</td>
        <td style="border-bottom: 2px solid #000;">DGR<br />(lad niebez.)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CLASS<br />(klasa ladunku niebez.) </td>
        <td style="border-bottom: 2px solid #000;">UN<br />(rodzaj lad niebez)</td>
    </tr>
<?php
    $Size = UsefullBase::GetSizes($this->Baza);
    $Types = UsefullBase::GetTypes($this->Baza);
    $Idx = 0;
    if($AOID > 0){
        foreach($Values['FCL'] as $FCL){
?>
    <tr id="<?php echo "fcl-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;">
        <?php
            if(!in_array($FCL['order_fcl_id'], $Wykorzystane)){
                if($this->Parametr == "faktury_lotnicze" || $this->Parametr == "faktury"){
                    $Form->PoleCheckbox("Faktura[order_fcl_id][]", $FCL['order_fcl_id'], (in_array($FCL['order_fcl_id'], $Values['order_fcl_id']) ? $FCL['order_fcl_id'] : 0));
                }
            }else{
                echo "&nbsp;";
            }
        ?>
        </td>
        <td style="border-bottom: 1px solid #888;"><?php echo $FCL['cont_pcs']; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $FCL['cont_type']; ?></td>
        <td style="border-bottom: 1px solid #888;"><?php echo $FCL['cont_description']; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo "{$FCL['cont_weight']}&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php echo "{$FCL['cont_volume']}&nbsp;CBM"; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $FCL['chargeable_weight']; ?></td>
        <td style="border-bottom: 1px solid #888;"><?php echo $FCL['cont_dgr']; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"> <?php echo $FCL['cont_class']; ?></td>
        <td style="border-bottom: 1px solid #888;"> <?php echo $FCL['cont_un']; ?></td>
    </tr>
<?php
            $Idx++;
        }
    }else{
        $Idx = 0;
        foreach($Values['FCL'] as $FCL){
        ?>
        <tr id="<?php echo "fcl-row-$Idx"; ?>">
            <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_pcs]", $FCL['cont_pcs'], "style='width: 40px'"); ?></td>
            <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_type]", $FCL['cont_type']); ?></td>
            <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_description]", $FCL['cont_description']); ?></td>
            <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_weight]", $FCL['cont_weight'], "style='width: 40px'"); echo "&nbsp;KG"; ?></td>
            <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_volume]", $FCL['cont_volume'], "style='width: 40px'"); echo "&nbsp;CBM"; ?></td>
            <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][chargeable_weight]", $FCL['chargeable_weight'], "style='width: 40px'"); ?></td>
            <td style="border-bottom: 1px solid #888;"><?php $Form->PoleSelect("AirZlec[FCL][$Idx][cont_dgr]", $TakNie, $FCL['cont_dgr'], "onchange='ChangeDGR(this, \"fcl\", $Idx);'") ?></td>
            <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Sea[FCL][$Idx][cont_class]", $FCL['cont_class'], "id='fcl-class-$Idx'; ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
            <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("AirZlec[FCL][$Idx][cont_un]", $FCL['cont_un'], "id='fcl-un-$Idx'; ".($FCL['cont_dgr'] == "No" ? "disabled=disabled" : "")) ?></td>
            <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;">
                <?php
                    if($Idx > 0){
                        $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemoveContainer($Idx);'");
                    }else{
                        echo "&nbsp;";
                    }
                ?></td>
        </tr>
        <?
        $Idx++;
        }
    }
?>
</table>
 <?php
    if($AOID == 0){
       $Form->PoleHidden("Containers", $Idx, "id='Containers'"); 
        $Form->PoleButton("AddNewContainer", "Dodaj ładunek", "onclick='AddContainerAirZlec();' style='margin: 20px;'"); 
    }
?>