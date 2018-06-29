<?php
$this->Form->PoleSelectMulti("{$this->MapaNazw[$Nazwa]}[]", $this->Pola[$Nazwa]['opcje']['elementy'], -1);
?>
<br /><br /><input type='button' name='Zapisz' value='Dodaj zlecenia' onclick='ValueChange("OpcjaFormularza", "dodaj_nowe");' class="form-button" /><br />