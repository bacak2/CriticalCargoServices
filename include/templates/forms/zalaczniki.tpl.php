<?php
    
        ?>
        <table class="osoby_kontaktowe_tabelka" id="zalacznik_tabelka"><tbody>
                 <tr class="first">
                    <td style="border: 0"></td><td style="border: 0"></td>
                </tr>
        <?php
        if(count($Wartosc) > 0){
            foreach($Wartosc as $Zalacznik){
                include(SCIEZKA_SZABLONOW."forms/zalacznik-row.tpl.php");
            }
        }
        ?>
        </table>
        <button name="dodaj" value="dodaj dokument" title="dodaj dokument" class="form-button" style="margin: 8px 0px 8px 10px;" onclick='DodajDokument(<?php echo (isset($_GET['id']) ? $_GET['id'] : 0); ?>); return false;'>Dodaj dokument</button>