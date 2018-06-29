<?php
    if(!isset($Wartosc['wybor'])){
        $Wartosc['wybor'] = 'lista';
    }
    if($this->Pola[$Nazwa]['opcje']['przewoznik'] > 0){
        if (count($this->Pola[$Nazwa]['opcje']['elementy']) > 0) {
?>
            <input type='radio' name='<?php echo $this->MapaNazw[$Nazwa]; ?>[wybor]' value='lista'<?php echo ($Wartosc['wybor'] == 'lista' ? ' checked="checked"' : '').(count($this->Pola[$Nazwa]['opcje']['elementy']) == 0 ? ' disabled="disabled"' : ''); ?>>wybierz z listy:
            <select name="<?php echo $this->MapaNazw[$Nazwa]; ?>[kierowca]"<?php echo (count($this->Pola[$Nazwa]['opcje']['elementy']) == 0 ? ' disabled="disabled"' : ''); ?>>
                <option value='0'>--- brak danych ---</option>
                <?php
                foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Idx => $Val){
                ?>
                    <option value='<?php echo $Idx; ?>'<?php echo ($Wartosc['kierowca'] == $Idx ? ' selected="selected"' : ''); ?>><?php echo $Val; ?></option>
                <?php
                }
                ?>
            </select>
            <br />lub<br />
  <?php
        }else{
             echo("&nbsp;&nbsp;<b>Brak kierowców</b><br /><br />");
             $Wartosc['wybor'] = 'nowy';
        }
  ?>
      <input type='radio' name='<?php echo $this->MapaNazw[$Nazwa]; ?>[wybor]' value='nowy'<?php echo ($Wartosc['wybor'] == 'nowy' ? ' checked="checked"' : ''); ?>>wprowadź nowego kierowcę:<br /><br />
      <div style='margin-left: 20px'>
        Imię i Nazwisko: <input type="text" name="<?php echo $this->MapaNazw[$Nazwa]; ?>[nowy][imie_nazwisko]" size="30" value="<?php echo $Wartosc['nowy']['imie_nazwisko']; ?>" style="width: 200px; margin-bottom: 8px;"><br />
        Rejestracja: <input type="text" name="<?php echo $this->MapaNazw[$Nazwa]; ?>[nowy][rejestracja]" size="30" value="<?php echo $Wartosc['nowy']['rejestracja']; ?>" style="width: 200px; margin-bottom: 8px;"><br />
        Dane nowego kierowcy: <br /><textarea name="<?php echo $this->MapaNazw[$Nazwa]; ?>[nowy][dane_kierowcy]" style="width:300px; height:100px; margin-bottom: 8px;"><?php echo $Wartosc['nowy']['dane_kierowcy']; ?></textarea>
      </div>
<?php
    }else{
?>
    Wybierz przewoźnika
<?php
    }
?>