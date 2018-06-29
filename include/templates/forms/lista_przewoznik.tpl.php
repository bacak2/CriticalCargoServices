<select name="<?php echo $this->MapaNazw[$Nazwa]; ?>"<?php echo $AtrybutyDodatkowe; ?>>
<option value="0"> Wybierz przewoźnika </option>
    <?php
        foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Idx => $Val){

    ?>
            <option value='<?php echo $Idx; ?>'<?php echo ($Wartosc == $Idx ? ' selected="selected"' : '').($Val['klasa_id'] > 0 ? " style='background-color: {$this->Pola[$Nazwa]['opcje']['klasy'][$Val['klasa_id']]['klasa_color']};'" : "").">{$Val['nazwa']} ({$this->Pola[$Nazwa]['opcje']['klasy'][$Val['klasa_id']]['klasa_nazwa']})</option>"; ?>
            <?php
            }
            ?>
    </select>