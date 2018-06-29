<?php
    echo "<b>{$this->Pola[$Nazwa]['opcje']['elementy'][$Wartosc['check']]}</b>";
?>
<br />
<div id="<?php echo "{$this->MapaNazw[$Nazwa]}_div"; ?>"<?php echo ($Wartosc['check'] == -1 ? "" : "style='display: none;'"); ?>>
    <?php
        echo $Wartosc['value'];
    ?>
</div>