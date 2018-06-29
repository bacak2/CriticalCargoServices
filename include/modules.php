<?php
        if(isset($_POST['program'])){
            $_SESSION['Aplikacja'] = $_POST['program'];
        }
        if(!isset($_SESSION['Aplikacja'])){
            $_SESSION['Aplikacja'] = "ORDER";
        }
        if($_SESSION['uprawnienia_id'] == 6){
            $_SESSION['Aplikacja'] = "CRM";
        }
        $Panel->UstawAplikacje("ORDER");
        $Panel->DodajModul('ZleceniaKlient', 'zlecenia_klient', 'Zlecenia od klientów');
        //$Panel->DodajModul(null, 'spedycja_morska', 'Spedycja morska');
        $Panel->DodajModul('SeaOrders', 'zlecenia_morskie', 'Sea Orders', 'spedycja_morska');
        $Panel->DodajModul('ZleceniaMorskie', 'zlecenia_morskie_zlec', 'Zlecenia', 'spedycja_morska');
        //$Panel->DodajModul(null, 'spedycja_lotnicza', 'Spedycja lotnicza');
        $Panel->DodajModul('AirOrders', 'zlecenia_lotnicze', 'Air Orders', 'spedycja_lotnicza');
        $Panel->DodajModul('ZleceniaLotnicze', 'zlecenia_lotnicze_zlec', 'Zlecenia', 'spedycja_lotnicza');
        $Panel->DodajModul('Przewoznicy', 'przewoznicy', 'Przewoźnicy');
        //$Panel->DodajModul('Kierowcy', 'kierowcy', 'Kierowcy');
        //$Panel->DodajModul('Punkty', 'punkty', 'Punkty przeładunku');
        $Panel->DodajModul(null, 'baza_klientow', 'Klienci');
        if($_SESSION['Aplikacja'] == "ORDER"){
            $Panel->DodajModul('Klienci', 'klienci', 'Klienci', 'baza_klientow');
        }
        $Panel->DodajModul('KlienciRaporty', 'klienci_raporty', 'Raporty', 'baza_klientow');
        $Panel->DodajModul('KlienciPotwierdzenia', 'klienci_potwierdzenia', 'Potwierdzenia', 'baza_klientow');
        if($_SESSION['Aplikacja'] == "ORDER"){
            $Panel->DodajModul('Uzytkownicy', 'uzytkownicy', 'Użytkownicy');
        }
        $Panel->DodajModul('Szablon', 'szablon', 'Szablon');
        $Panel->DodajModul(null, 'rozliczenia', 'Tabela rozliczeń');
        //$Panel->DodajModul('TabelaRozliczen', 'tabela_rozliczen', 'Tabela rozliczeń', 'rozliczenia');
        $Panel->DodajModul('TabelaRozliczenNowa', 'tabela_rozliczen_nowa', 'Tabela rozliczeń - nowa', 'rozliczenia');
        $Panel->DodajModul('KlienciRaporty', 'klienci_raporty', 'Raporty', 'rozliczenia');
        $Panel->DodajModul('KlienciPotwierdzenia', 'klienci_potwierdzenia', 'Potwierdzenia', 'rozliczenia');
        //$Panel->DodajModul(null, 'rozliczenia_morskie', 'Tabela rozliczeń - morskie');
        $Panel->DodajModul('TabelaRozliczenMorskie', 'tabela_rozliczen_morskie', 'Tabela rozliczeń', 'rozliczenia_morskie');
         $Panel->DodajModul('KlienciRaportyMorskie', 'klienci_raporty_morskie', 'Raporty', 'rozliczenia_morskie');
         //$Panel->DodajModul(null, 'rozliczenia_lotnicze', 'Tabela rozliczeń - lotnicze');
        $Panel->DodajModul('TabelaRozliczenLotnicze', 'tabela_rozliczen_lotnicze', 'Tabela rozliczeń', 'rozliczenia_lotnicze');
        $Panel->DodajModul('KlienciRaportyLotnicze', 'klienci_raporty_lotnicze', 'Raporty', 'rozliczenia_lotnicze');
        //$Panel->DodajModul('MojaTabelaRozliczen', 'tabela_rozliczen_moja', 'Moja tabela rozliczeń');
        $Panel->DodajModul('Faktury', 'faktury_nowe', null, 'tabela_rozliczen_nowa', true);
        $Panel->DodajModul('Platnosci', 'platnosci_nowe', null, 'tabela_rozliczen_nowa', true);
        $Panel->DodajModul('Zlecenia', 'zlecenia', 'Zlecenia', 'tabela_rozliczen_nowa', true);
        $Panel->DodajModul('Faktury', 'faktury', 'Faktury');
        $Panel->DodajModul('FakturyMorskie', 'faktury_morskie', null, 'faktury', true);
        $Panel->DodajModul('FakturyLotnicze', 'faktury_lotnicze', null, 'faktury', true);
        $Panel->DodajModul('NotyObciazeniowe', 'noty', 'Noty obciążeniowe');
        //$Panel->DodajModul('Platnosci', 'platnosci', 'Płatności');
        //$Panel->DodajModul('PlatnosciMorskie', 'platnosci_morskie', 'Płatności morskie');
        //$Panel->DodajModul('PlatnosciLotnicze', 'platnosci_lotnicze', 'Płatności lotnicze');
        $Panel->UstawAplikacje("CRM");
        $Panel->DodajModul('Zdarzenia', 'zdarzenia', 'Zdarzenia');
        $Panel->DodajModul('Zalaczniki', 'zalaczniki', 'Załączniki', 'zdarzenia', true);
        if($_SESSION['Aplikacja'] == "CRM"){
            $Panel->DodajModul('Klienci', 'klienci', 'Klienci');
        }
        $Panel->DodajModul('Kontakty', 'kontakty', 'Kontakty', 'klienci', true); 
        $Panel->DodajModul('Oddzialy', 'oddzialy', 'Oddziały');
        if($_SESSION['Aplikacja'] == "CRM"){
            $Panel->DodajModul('Uzytkownicy', 'uzytkownicy', 'Użytkownicy');
        }
        $Panel->DodajModul('RaportyCRM', 'raporty', 'Raporty');
        $Panel->DodajModul('Logowania', 'logowania', 'Lista logowań', 'raporty', true);
        $Panel->DodajModul('RaportyCRMDzienny', 'day_raport', 'Raport dzienny', 'raporty', true);
        $Panel->DodajModul('RaportyCRMClient', 'client_raport', 'Raport klientów', 'raporty', true);
?>
