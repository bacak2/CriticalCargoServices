<br><br><br>
<center>
    <p>
        <b>Wyślij e-mail na adres z bazy:</b>
        <br><br>
        <select name='<?php echo $this->MapaNazw[$Nazwa]; ?>[lista][]' size='10' multiple='multiple'>
            <?php
                foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Adres){
                    $Adres = trim($Adres);
            ?>
                    <option value='<?php echo $Adres; ?>'><?php echo $Adres; ?></option>
            <?php
		}
            ?>
	</select>
        <br><br>lub wpisz adres: <input type='text' name='<?php echo $this->MapaNazw[$Nazwa]; ?>[email_dodatkowy]' value='' style="width: 200px;" />
</center>