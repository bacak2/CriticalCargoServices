<table cellpadding='0' cellspacing='5' border='0'>
<?php
    foreach($Wartosc as $ID => $Dane){
?>
        <tr><td style="border: 0;"><?php echo $Dane['numer']; ?></td><?php  echo ($this->Pola[$Nazwa]['opcje']['no_status'] == true ? "" : "<td style='border: 0;'>aktualny status: <input type='text' name='{$this->MapaNazw[$Nazwa]}[$ID]' value='{$Dane['status']}' /></td>"); ?><td style="border: 0;">Zaznacz do usunięcia: <input type='checkbox' name='UsunZlecenia[]' value='<?php echo $ID; ?>' /></td></tr>
<?php
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