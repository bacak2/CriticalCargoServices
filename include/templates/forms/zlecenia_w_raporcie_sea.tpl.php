<table cellpadding='0' cellspacing='5' border='0'>
<?php
    $i = 0;
    foreach($Wartosc as $ID => $Dane){
?>
        <tr style="background-color: <?php echo ($i % 2 == 0 ? "#FFF;" : "#C6E0FF;");  ?>">
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; white-space: nowrap"><br /><?php echo $Dane['numer']; ?></td>
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; width: 150px; white-space: nowrap;"><br />Podjęcie: <input type='text' name='SeaOrder[<?php echo $ID; ?>][podjecie]' value='<?php echo $Dane['podjecie']; ?>' id="podjecie_<?php echo $ID; ?>" style="width: 80px;" /><img src='images/kalendarz.png' onclick='wstecz = 0; showKal(document.getElementById("podjecie_<?php echo $ID; ?>"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; width: 110px; white-space: nowrap;"><br />ETD: <input type='text' name='SeaOrder[<?php echo $ID; ?>][etd]' value='<?php echo $Dane['etd']; ?>' style="width: 80px;" id="etd_<?php echo $ID; ?>" /><img src='images/kalendarz.png' onclick='wstecz = 0; showKal(document.getElementById("etd_<?php echo $ID; ?>"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; width: 110px; white-space: nowrap;"><br />RTD: <input type='text' name='SeaOrder[<?php echo $ID; ?>][rtd]' value='<?php echo $Dane['rtd']; ?>' style="width: 80px;" id="rtd_<?php echo $ID; ?>" /><img src='images/kalendarz.png' onclick='wstecz = 0; showKal(document.getElementById("rtd_<?php echo $ID; ?>"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; width: 110px; white-space: nowrap;"><br />ETA: <input type='text' name='SeaOrder[<?php echo $ID; ?>][eta]' value='<?php echo $Dane['eta']; ?>' style="width: 80px;" id="eta_<?php echo $ID; ?>" /><img src='images/kalendarz.png' onclick='wstecz = 0; showKal(document.getElementById("eta_<?php echo $ID; ?>"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>
            <td style="border-width: 1px 0px 0px 0px; font-weight: bold; width: 110px; white-space: nowrap;"><br />RTA: <input type='text' name='SeaOrder[<?php echo $ID; ?>][rta]' value='<?php echo $Dane['rta']; ?>' style="width: 80px;" id="rta_<?php echo $ID; ?>" /><img src='images/kalendarz.png' onclick='wstecz = 0; showKal(document.getElementById("rta_<?php echo $ID; ?>"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'>
            <td style="border-width: 1px 0px 0px 0px; white-space: nowrap;"><br />Zaznacz do usunięcia: <input type='checkbox' name='UsunZlecenia[]' value='<?php echo $ID; ?>' /></td>
        </tr>
        <?php
            foreach($Dane['kontenery'] as $contenery){
                ?><tr style="background-color: <?php echo ($i % 2 == 0 ? "#FFF;" : "#C6E0FF;");  ?>"><td style="border: 0; text-align: left;"><?php echo $contenery['cont_no']; ?></td><td style='border: 0;' colspan="2"><?php  echo ($this->Pola[$Nazwa]['opcje']['no_status'] == true ? "" : "aktualny status: <input type='text' name='{$this->MapaNazw[$Nazwa]}[$ID][{$contenery['order_fcl_id']}]' value='{$contenery['cont_status']}' />"); ?></td><td colspan="5" style="border: 0; ">&nbsp;</td></tr><?php
            }
        ?>
<?php
        $i++;
    }
?>
</table>
<?php
    if(count($Wartosc) > 0){
?>
    <br /><input type='submit' name='Zapisz' value='Zapisz zmiany' onclick='ValueChange("OpcjaFormularza", "zapisz_zmiany");'  class="form-button" />
<?php
    }
?>