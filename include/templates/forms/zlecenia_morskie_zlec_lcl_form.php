<table border="0" cellpadding="10" cellspacing="0" id="LCL-Table" style=" border-collapse: collapse;">
    <tr>
        <td style="border-bottom: 2px solid #000;">&nbsp;</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">NO.<br />(numer kontenera)</td>
        <td style="border-bottom: 2px solid #000;">PCS<br />(ilosc opakowan)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">TYPE<br />(rodzaj opakowan) </td>
        <td style="border-bottom: 2px solid #000;">DESCRIPTION<br />(opis towaru) </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">WEIGHT<br />(waga)</td>
        <td style="border-bottom: 2px solid #000;">VOLUME<br />(objetosc)</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">DGR<br />(lad niebez.)</td>
        <td style="border-bottom: 2px solid #000;">CLASS<br />(klasa ladunku niebez.) </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">UN<br />(rodzaj lad niebez)</td>
    </tr>
<?php
    $Idx = 0;
    foreach($Values['LCL'] as $LCL){
?>
    <tr id="<?php echo "fcl-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;">
            <?php
                if(!in_array($LCL['cont_no'], $Wykorzystane)){
                    if($this->Parametr == "faktury_morskie" || $this->Parametr == "faktury"){
                        $Form->PoleCheckbox("Faktura[cont_number][]", $LCL['cont_no'], $Values['cont_number']);
                    }else{
                        $Form->PoleCheckbox("SeaZlec[cont_number][]", $FCL['cont_no'], $Values['cont_number']);
                    }
                }else{
                    echo "&nbsp;";
                }
             ?>
        </td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $LCL['cont_no']; ?></td>
        <td style="border-bottom: 1px solid #888;"><?php echo $LCL['cont_pcs']; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $LCL['cont_type']; ?></td>
        <td style="border-bottom: 1px solid #888;"><?php echo $LCL['cont_description']; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo "{$LCL['cont_weight']}&nbsp;KG"; ?></td>
        <td style="white-space: nowrap; border-bottom: 1px solid #888;"><?php echo "{$LCL['cont_volume']}&nbsp;CBM"; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $LCL['cont_dgr']; ?></td>
        <td style="border-bottom: 1px solid #888;"><?php echo $LCL['cont_class']; ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php echo $LCL['cont_un']; ?></td>
    </tr>
<?php
        $Idx++;
    }
?>
</table>