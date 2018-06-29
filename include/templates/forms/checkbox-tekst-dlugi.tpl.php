<?php
    FormularzSimple::PoleCheckbox("{$this->MapaNazw[$Nazwa]}[check]", 1, $Wartosc['check'], "", "onclick='Notice(this, \"{$this->MapaNazw[$Nazwa]}_div\");'");
?>
<br />
<div id="<?php echo "{$this->MapaNazw[$Nazwa]}_div"; ?>"<?php echo ($Wartosc['check'] == 1 ? "" : "style='display: none;'"); ?>>
    <?php
    FormularzSimple::PoleTextarea("{$this->MapaNazw[$Nazwa]}[value]", $Wartosc['value']);
    ?>
</div>