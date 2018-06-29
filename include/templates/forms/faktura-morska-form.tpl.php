<?php
    $Klienci = UsefullBase::GetKlienciAccessOddzial($this->Baza, $this->Uzytkownik);
    $Klienci = Usefull::PolaczDwieTablice(array(0 => ' -- Wybierz --'), $Klienci);
    $Terms = UsefullBase::GetTerms($this->Baza);
    $Terms = Usefull::PolaczDwieTablice(array(0 => ' -- Wybierz -- '), $Terms);

    $Form = new FormularzSimple();
    $Form->FormStart("sea_order", "", "post"); 
    echo '<table class="formularz">';
         echo "<tr>\n";
            echo "<th>NABYWCA</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Faktura[id_klienta]", $Klienci, $Values['id_klienta']);
                echo "<br /><br />";
                $Form->PoleTextarea("Faktura[id_klient_text]", $Values['id_klient_text'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        if($SOI['mode'] == "FCL"){
            echo "<tr id='FCL-spec'>\n";
                echo "<td colspan='2'>\n";
                    include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_zlec_fcl_form.php");
                echo "</td>\n";
            echo "</tr>\n";
        }else{
            echo "<tr id='LCL-spec'>\n";
                echo "<td colspan='2'>\n";
                    include(SCIEZKA_SZABLONOW."forms/zlecenia_morskie_zlec_lcl_form.php"); 
                echo "</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
            echo "<th>NUMER FAKTURY</th>\n";
            echo "<td>\n";
                $Form->PoleInputText("Faktura[numer]", $Values['numer']);
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>DATA WYSTAWIENIA</th>\n";
            echo "<td>\n";
                $Form->PoleData("Faktura[data_wystawienia]", $Values['data_wystawienia'], "data_wystawienia");
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
                echo "<th>MIEJSCE WYSTAWIENIA </th>\n";
                echo "<td>\n";
                    $Form->PoleInputText("Faktura[miejsce_wystawienia]", $Values['miejsce_wystawienia']);
                echo "</td>\n";
            echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>DATA SPRZEDAŻY</th>\n";
            echo "<td>\n";
                $Form->PoleData("Faktura[data_sprzedazy]", $Values['data_sprzedazy'], "data_sprzedazy");
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>TERMIN PŁATNOŚCI</th>\n";
            echo "<td>\n";
                $Form->PoleData("Faktura[termin_platnosci]", $Values['termin_platnosci'], "termin_platnosci");
            echo "</td>\n";
        echo "</tr>\n";
        $FormyPlatnosci = $this->Baza->GetOptions("SELECT id_formy, forma FROM faktury_formy_platnosci");
        echo "<tr>\n";
            echo "<th>FORMA PŁATNOŚCI</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Faktura[id_formy]", $FormyPlatnosci, $Values['id_formy']);
            echo "</td>\n";
        echo "</tr>\n";
        $Waluty = UsefullBase::GetWaluty($this->Baza);
        echo "<tr>\n";
            echo "<th>WALUTA</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Faktura[id_waluty]", $Waluty, $Values['id_waluty']);
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
                echo "<th>POL </th>\n";
                echo "<td>\n";
                    echo $SOI['pol'];
                echo "</td>\n";
            echo "</tr>\n";
        echo "<tr>\n";
                echo "<th>POD </th>\n";
                echo "<td>\n";
                    echo $SOI['pod'];
                echo "</td>\n";
            echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>ODBIORCA</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Faktura[id_klient_odbiorca]", $Klienci, $Values['id_klient_odbiorca']);
                echo "<br /><br />";
                $Form->PoleTextarea("Faktura[odbiorca]", $Values['odbiorca'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
         echo "<tr>\n";
            echo "<th>ZAŁADOWCA</th>\n";
            echo "<td>\n";
                $Form->PoleSelect("Faktura[id_klient_shipper]", $Klienci, $Values['id_klient_shipper']);
                echo "<br /><br />";
                $Form->PoleTextarea("Faktura[shipper]", $Values['shipper'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>WARUNKI DOSTAWY</th>\n";
             echo "<td>\n";
                $Form->PoleSelect("Faktura[terms_id]", $Terms, $Values['terms_id']);
                echo "<br /><br />";
                $Form->PoleTextarea("Faktura[terms_text]", $Values['terms_text'], "style='width: 400px; height: 40px;'");
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>B/L NUMER</th>\n";
             echo "<td>\n";
                echo $SOI['bl_no'];
            echo "</td>\n";
        echo "</tr>\n";
        echo "<tr>\n";
            echo "<th>STATEK</th>\n";
             echo "<td>\n";
                echo $SOI['vessel'];
            echo "</td>\n";
        echo "</tr>\n";
   ?>
<tr>
    <td colspan="2">
        <br /><b><u>POZYCJE:</u></b><br /><br />
        <table border="0" cellpadding="4" cellspacing="0" id="Positions-Table" style="border-collapse: collapse;">
    <tr>
        <td style="border-bottom: 2px solid #000;">LP</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">NAZWA USŁUGI</td>
        <td style="border-bottom: 2px solid #000;">ILOŚĆ</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">CENA JEDN. NETTO</td>
        <td style="border-bottom: 2px solid #000;">WART. NETTO </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">VAT</td>
        <td style="border-bottom: 2px solid #000;">KWOTA VAT</td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">BRUTTO JEDN.</td>
        <td style="border-bottom: 2px solid #000;">WART. BRUTTO </td>
        <td style="border-bottom: 2px solid #000; background-color: #F0F0F0;">&nbsp;</td>
    </tr>
<?php
    $Idx = 1;
    foreach($Values['Pos'] as $FCL){
?>
    <tr id="<?php echo "position-row-$Idx"; ?>">
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][lp]", $FCL['lp'], "style='width: 40px;'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][opis]", $FCL['opis']); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][ilosc]", $FCL['ilosc'], "style='width: 40px' id='ilosc$Idx' onchange=\"Oblicz('ilosc$Idx', 'netto_jednostki$Idx', 'netto$Idx'); Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][netto_jednostki]", $FCL['netto_jednostki'], "style='width: 80px;' id='netto_jednostki$Idx' onchange=\"Oblicz('ilosc$Idx', 'netto_jednostki$Idx', 'netto$Idx'); Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?></td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][netto]", $FCL['netto'], "style='width: 80px;' id='netto$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][vat]", $FCL['vat'], "style='width: 40px;' id='vat$Idx' onchange=\"Oblicz2('vat$Idx', 'netto$Idx', 'brutto$Idx', 'netto_jednostki$Idx', 'brutto_jednostki$Idx', 'kwota_vat$Idx')\""); ?> %</td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][kwota_vat]", $FCL['kwota_vat'], "style='width: 80px;' id='kwota_vat$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][brutto_jednostki]", $FCL['brutto_jednostki'], "style='width: 80px;' id='brutto_jednostki$Idx' readonly='readonly'"); ?> %</td>
        <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Faktura[Pos][$Idx][brutto]", $FCL['brutto'], "style='width: 80px;' id='brutto$Idx' readonly='readonly'"); ?></td>
        <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usuń", "onclick='RemovePosition($Idx);'"); ?></td>
    </tr>
<?php
        $Idx++;
    }
?>
</table>
<?php
    $Form->PoleHidden("Positions", $Idx, "id='Positions'");
    $Form->PoleButton("AddNewPosition", "Dodaj kolejną pozycję", "onclick='AddPosition();' style='margin: 20px;'");
    echo "</td>";
    echo "</tr>\n";
echo "<tr>\n";
    echo "<th>KURS<br /><small>kurs po jakim zostanie przeliczona<br />faktura w tabeli rozliczeń - jeżeli waluta != PLN</small> </th>\n";
    echo "<td>\n";
        $Form->PoleInputText("Faktura[kurs]", $Values['kurs'], "style='width: 100px;'");
    echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
    echo "<th>DOTYCHCZAS WPŁACONO </th>\n";
    echo "<td>\n";
        $Form->PoleInputText("Faktura[wplacono]", $Values['wplacono'], "style='width: 100px;'");
    echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
    echo "<th>STATUS </th>\n";
    echo "<td>\n";
        $Form->PoleRadio("Faktura[status]", 1, $Values['status']);
        echo "faktura opłacona&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $Form->PoleRadio("Faktura[status]", 0, $Values['status']);
        echo "faktura nieopłacona&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
    echo "<th>INFORMACJA DODATKOWA</th>\n";
    echo "<td>\n";
        $Form->PoleTextarea("Faktura[uwagi]", $Values['uwagi'], "style='width: 400px; height: 40px;'");
    echo "</td>\n";
echo "</tr>\n";
if($this->WykonywanaAkcja == "edycja"){
    echo "<tr>\n";
        echo "<th>SZABLON FAKTURY</th>\n";
        echo "<td>\n";
            $Form->PoleSelect("Faktura[szablon_faktura]", array("PL" => "PL", "ENG" => "ENG"), $Values['szablon_faktura']);
        echo "</td>\n";
    echo "</tr>\n";
}
echo "<tr>\n";
    echo "<th>&nbsp; </th>\n";
    echo "<td>\n";
        $Form->PoleSubmitImage("OK", "Zapisz", "images/ok.gif", "style='border: 0;'");
        echo "&nbsp;&nbsp;";
        echo "<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" border=\"0\"></a>";
    echo "</td>\n";
echo "</tr>\n";
   echo "</table>";
   $Form->FormEnd();
?>