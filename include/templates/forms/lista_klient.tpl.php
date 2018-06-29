<script type='text/javascript'>
    var Clients = new Array();
    <?php
        foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $ID => $Cli){
            echo "Clients[$ID] = '{$Cli['termin_platnosci_dni']}';";
        }
    ?>
</script>
<select name="<?php echo $this->MapaNazw[$Nazwa]; ?>"<?php echo $AtrybutyDodatkowe; ?> onchange='document.getElementById("terminek").value = Clients[this.value];'>
    <option value="0"> Wybierz klienta </option>
    <?php
        foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Idx => $Cli){
    ?>
        <option value='<?php echo $Idx; ?>'<?php echo ($Wartosc == $Idx ? ' selected="selected"' : ''); ?>><?php echo $Cli['nazwa']; ?></option>
    <?php
    }
    ?>
</select>