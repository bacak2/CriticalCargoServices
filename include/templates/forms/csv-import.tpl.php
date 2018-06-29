<form id="import_cen" name="ceny" method="post" action="" enctype='multipart/form-data'>
     <table class="formularz">
        <tr>
            <th>
                <div style="position: relative; width: 100%;">
                    Import bazy klientów z pliku CSV
                </div>
            </th>
        </tr>
        <tr>
            <td>
                <div style="padding: 6px;">
                    <b>Wybierz plik CSV</b><br />
                    <?php FormularzSimple::PoleFile("importowane_dane", ""); ?>
                    <br /><br />
                    <b>UWAGA!</b><br />
                    Plik CSV musi mieć odpowiednią strukturę kolumn:<br />
                    Nazwa klienta, Siedziba, Kod Kraju, Kod Pocztowy, Miasto, Adres e-mail, telefon, strona www 
                </div>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" style="display: inline; vertical-align: middle; margin-top: 1px;" alt="Zapisz" title="Zapisz" src="images/ok.gif" onclick="SubmitForm();">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->LinkPowrotu; ?>"><img style="display: inline; vertical-align: middle;" alt="Anuluj" title="Anuluj" src="images/anuluj.gif"> </a>
            </td>
        </tr>
     </table>
</form>