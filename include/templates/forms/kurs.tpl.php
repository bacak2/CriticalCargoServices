<input type="text" name="<?php echo $this->MapaNazw[$Nazwa]; ?>" style="width: 130px;" value="<?php echo $Wartosc; ?>">
&nbsp;&nbsp;&nbsp;<input type='button' name='Zapisz' value='Kurs z dnia wystawienia' onclick='ValueChange("OpcjaFormularza", "<?php echo $this->Pola[$Nazwa]['opcje']['kurs_param']; ?>");'  class="form-button" />&nbsp;&nbsp;
<?php 
    if($_POST['OpcjaFormularza'] == $this->Pola[$Nazwa]['opcje']['kurs_param'] && floatval($Wartosc) == 0){
        echo " brak kursu w bazie z dnia załadunku";
    }
?>