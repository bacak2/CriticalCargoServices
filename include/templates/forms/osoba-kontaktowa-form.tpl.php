<tr id="os_kontaktowa_new">
    <td>&nbsp;<?php FormularzSimple::PoleInputText("imie_nazwisko", "", "id='os-kontakt-imie-nazwisko' style='width: 100%;'"); ?>&nbsp;</td>
    <td>&nbsp;<?php FormularzSimple::PoleSelect("stanowisko", array("Logistyka" => "Logistyka", "Księgowość" => "Księgowość"), "", "id='os-kontakt-stanowisko'"); ?>&nbsp;</td>
    <td>&nbsp;<?php FormularzSimple::PoleInputText("telefon", "", "id='os-kontakt-telefon' style='width: 100%;'"); ?>&nbsp;</td>
    <td>&nbsp;<?php FormularzSimple::PoleInputText("mail", "", "id='os-kontakt-mail' style='width: 100%;'"); ?>&nbsp;</td>
    <td style="border: 0; vertical-align: middle;" id="os-kontakt-zapis">
        <button name="zapisz" value="Zapisz" title="Zapisz" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='ZapiszOsKontakowa(<?php echo $_POST['client']; ?>); return false;'>Zapisz</button>&nbsp;&nbsp;
        <button name="anuluj" value="Anuluj" title="Anuluj" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='AnulujOsKontakowa(); return false;'>Anuluj</button>
    </td></tr>