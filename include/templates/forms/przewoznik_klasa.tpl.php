<?php
    if($Wartosc['klasa_id'] != 4 || in_array($_SESSION["uprawnienia_id"], array(1,4))){
?>
        <select name='<?php echo $this->MapaNazw[$Nazwa]; ?>[klasa_id]' onchange='PodajPowod(this);'>
            <option value='0'<?php echo ($Wartosc['klasa_id'] == 0 ? " selected" : ""); ?>> -- wybierz -- </option>
            <?php
            foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $KlasaID => $Klasa){
            ?>
                <option value='<?php echo $KlasaID; ?>'<?php echo ($Wartosc['klasa_id'] == $KlasaID ? " selected" : ""); ?>><?php echo $Klasa['klasa_nazwa']; ?></option>
            <?php
            }
            ?>
        </select>
    <?php
    }else{
    ?>
        <b><?php echo $Klasy[4]['klasa_nazwa']; ?></b>
    <?php
    }
    ?>
    <div id='powod_zakazu'<?php echo ($Wartosc['klasa_id'] != 4 ? " style='display: none;'" : ""); ?>>
        <br />Powód zakazu współpracy:<br /><textarea  name='<?php echo $this->MapaNazw[$Nazwa]; ?>[powod_zakazu]' style="width:400px; height:100px;"><?php echo $Wartosc['powod_zakazu']; ?></textarea>
    </div>