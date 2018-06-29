<?php
    
        ?>
        <table class="osoby_kontaktowe_tabelka" id="osoby_kontaktowe_tabelka"><tbody>
                <tr class="first">
                    <th>Imię i nazwisko</th><th>Stanowisko</th><th>Telefon</th><th>E-mail</th><td style="border: 0"></td>
                </tr>
        <?php
        if(count($Wartosc) > 0){
            foreach($Wartosc as $Person){
                include(SCIEZKA_SZABLONOW."forms/osoba-kontaktowa-row.tpl.php");
            }
        }
        ?>
        </table>
        <button name="dodaj" value="dodaj osobę" title="dodaj osobę" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='DodajOsKontakowa(<?php echo (isset($_GET['id']) ? $_GET['id'] : 0); ?>); return false;'>Dodaj osobę</button>