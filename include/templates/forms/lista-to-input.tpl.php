<div id="<?php echo $this->MapaNazw[$Nazwa]; ?>-select-box">
    <select id="<?php echo $this->MapaNazw[$Nazwa]; ?>-select" name="<?php echo $this->MapaNazw[$Nazwa]; ?>[id]"<?php echo $AtrybutyDodatkowe; ?> onchange='ChangeSelectToInput("<?php echo $this->MapaNazw[$Nazwa]; ?>")'>
        <?php
            if(isset($this->Pola[$Nazwa]['opcje']['wybierz']) && $this->Pola[$Nazwa]['opcje']['wybierz']){
                $Domyslna = (isset($this->Pola[$Nazwa]['opcje']['domyslna']) ? $this->Pola[$Nazwa]['opcje']['domyslna'] : " -- wybierz --");
                echo("<option value='0'".(0 == $Wartosc ? " selected='selected'" : "")."> $Domyslna </option>");
            }
            foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Idx => $Cli){
        ?>
            <option value='<?php echo $Idx; ?>'<?php echo ($Wartosc == $Idx ? ' selected="selected"' : ''); ?>><?php echo $Cli; ?></option>
        <?php
        }
        ?>
            <option value='last'> -- dodaj -- </option>
    </select>
</div>
<div id="<?php echo $this->MapaNazw[$Nazwa]; ?>-input" style="display: none;">
    <input type="text" name="<?php echo $this->MapaNazw[$Nazwa]; ?>[new]" value="" /> <button name="wróc" value="wróć" title="wróć" class="form-button" onclick='ChangeInputToSelect("<?php echo $this->MapaNazw[$Nazwa]; ?>"); return false;'>Wróć</button>
</div>