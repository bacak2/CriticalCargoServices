<tr id="zalacznik_new">
    <td>&nbsp;
        <?php
            FormularzSimple::PoleFile("zalacznik_add", "");
            echo "<br />Opis pliku: ";
            FormularzSimple::PoleInputText("zalacznik_add_opis", "", "style='width: 100%;'");
        ?>
    &nbsp;</td>
    <td style="border: 0; vertical-align: middle;" id="os-kontakt-zapis">
        <button name="zapisz" value="Zapisz" title="Zapisz" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='ZapiszDokument(); return false;'>Zapisz</button>&nbsp;&nbsp;
        <button name="anuluj" value="Anuluj" title="Anuluj" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='AnulujDokument(); return false;'>Anuluj</button>
    </td>
</tr>