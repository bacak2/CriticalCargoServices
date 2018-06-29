<?php
    echo "<b>".($Wartosc['check'] == 1 ? "TAK" : "NIE")."</b>";
?>
<br />
<div id="<?php echo "{$this->MapaNazw[$Nazwa]}_div"; ?>"<?php echo ($Wartosc['check'] == 1 ? "" : "style='display: none;'"); ?>>
    <?php
        echo $Wartosc['value'];
    ?>
</div>