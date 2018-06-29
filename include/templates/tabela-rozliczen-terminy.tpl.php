<td<?php echo $Styl; ?>>
    <a href='javascript:ShowTerminy(<?php echo $Element[$this->PoleID]; ?>)' id='href_<?php echo $Element[$this->PoleID]; ?>'><b>Podgląd</b></a>
    <div id='div_platnosci_<?php echo $Element[$this->PoleID]; ?>' style="display: none; width: 260px;"> 
        Data sprzedaży: <?php echo $Element['data_sprzedazy']; ?><br />
        Termin płatnośći klient: <?php echo $Element['termin_wlasny']; ?><br />
        Rzeczywista zapłata klient: <?php echo $Element['rzecz_zaplata_klienta']; ?><br />
        Opóźnienie Klient: <?php echo $Element['opoznienie_klient']; ?><br />
        Data wpływu faktury przewoźnik: <?php echo $Element['data_wplywu']; ?><br />
        Termin Płatności przewoźnik: <?php echo $Element['termin_przewoznika']; ?><br />
        Planowana zapłata przewoźnik: <?php echo $Element['planowana_zaplata_przew']; ?><br />
        Rzeczywista zapłata przewoźnik: <?php echo $Element['rzecz_zaplata_przew']; ?><br />
        Opóźnienie przewoźnik: <?php echo $Element['opoznienie_przewoznik']; ?><br />
        FIFO: <?php echo $Element['fifo']; ?><br />
        <br /><br /><a href='javascript:CloseTerminy(<?php echo $Element[$this->PoleID]; ?>)'><b>Zamknij</b></a>
    </div>
</td>