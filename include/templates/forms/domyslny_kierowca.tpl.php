<?php
    if (count($this->Pola[$Nazwa]['opcje']['elementy'])){
?>
        <select name="<?php echo $this->MapaNazw[$Nazwa]; ?>">
            <option value='0'>--- Wybierz ---</option>
            <?php
            foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Idx => $Val){
            ?>
		<option value='<?php echo $Idx; ?>'<?php echo ($Wartosc == $Idx ? ' selected="selected"' : ''); ?>><?php echo $Val; ?></option>
            <?php
		}
            ?>
            </select>
    <?php
    }else{
    ?>
	Brak przypisanych kierowców do przewoźnika
    <?php
    }
    ?>