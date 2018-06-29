<?php
    $Check = (isset($Wartosc['check']) && $Wartosc['check'] == 1 ? true : false);
?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $this->MapaNazw[$Nazwa]; ?>[check]" value="1" onclick='ChcePrzypomnienie(this);'<?php echo ($Check ? " checked" : "");  ?> /> przypomnienie mailowe
&nbsp;<span id="godzina_przyp" style="<?php echo $Check ? "visibility: visible;" : "visibility: hidden;" ?>">o godz. <?php $this->Form->PoleInputText("{$this->MapaNazw[$Nazwa]}[godzina]", $Wartosc['godzina'], "style='width: 80px;'") ?> <small>format GG:mm</small></span>