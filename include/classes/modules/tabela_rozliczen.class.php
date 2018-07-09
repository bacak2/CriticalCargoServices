<?php
/**
 * Moduł tabela rozliczen
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class TabelaRozliczen extends ModulBazowy {
    public $Przewoznicy;
    public $Klienci;
    public $Kierowcy;
    public $KodyKrajow;
    public $Emaile;
    public $PunktyPrzeladunku = false;
    public $ZleceniaDoFaktury;
    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        $this->Tabela = 'orderplus_zlecenie';
        $this->PoleID = 'id_zlecenie';
        $this->PoleNazwy = 'numer_zlecenia';
        $this->Nazwa = 'Zlecenie';
        $this->CzySaOpcjeWarunkowe = true;
        $this->Klienci = UsefullBase::GetKlienci($this->Baza);
        $this->Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
        //$this->Kierowcy = UsefullBase::GetKierowcy($this->Baza);
        $this->KodyKrajow = UsefullBase::GetCountryCodes($this->Baza);
        $this->Filtry[] = array("opis" => "NIP", "nazwa" => "nip_search", "typ" => "tekst");
        $this->Filtry[] = array("opis" => "Znajdź trasę", "nazwa" => "trasa", "typ" => "trasa", "opcje" => $this->KodyKrajow, 'domyslna' => 'dowolne');
        if($_GET['dev'] == "dev"){
            $kwota = number_format(2100.5*1.0000,2,'.','');
            $kwota_vat = number_format(round((23 / 100) * $kwota, 2),2,'.','');
            $kwota_brutto = number_format($kwota + $kwota_vat,2,'.','');
        }
    }

    function &GenerujFormularz($Wartosci, $Mapuj = false) {
        $Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
        $Formularz->DodajPole('numer_zlecenia_krotki', 'tekst', 'Numer', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('id_przewoznik', 'lista_przewoznik', 'Przewoźnik', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'elementy' => UsefullBase::GetPrzewoznicyWithClass($this->Baza), 'klasy' => PrzewoznicyKlasy::GetClasses($this->Baza), 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza","przewoznik_zmiana")')));
        $Formularz->DodajPole('kolor_zlecenia', 'checkbox', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'opis_dodatkowy_za' => ' Wysłana FV dla klienta'));
        $Formularz->DodajPole('kierowca_dane', 'tekst_dlugi', 'Kierowca', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'height: 70px;'), 'opis_dodatkowy_przed' => 'Dane kierowcy:<br />'));
        $Formularz->DodajPole('kierowca_dane_nr_rejestracyjny', 'tekst', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'opis_dodatkowy_przed' => '<br />Nr rejestracyjny:<br />', 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('os_kontaktowa', 'tekst_dlugi', 'Osoba kontaktowa', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('data_zlecenia', 'tekst_data', 'Data Zlecenia (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('kod_kraju_zaladunku', 'lista', 'Kod kraju załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
        $Formularz->DodajPole('adres_zaladunku', 'tekst', 'Adres załadunku (do faktury)', array('tabelka' => Usefull::GetFormStandardRow()));
        $Formularz->DodajPole('miejsce_zaladunku', 'tekst_dlugi', 'Załadowca i miejsce załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('kod_kraju_rozladunku', 'lista', 'Kod kraju rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
        $Formularz->DodajPole('adres_rozladunku', 'tekst', 'Adres rozładunku (do faktury)', array('tabelka' => Usefull::GetFormStandardRow()));
        $Formularz->DodajPole('odbiorca', 'tekst_dlugi', 'Odbiorca i miejsce rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('id_klient', 'lista_klient', 'Zlecający', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetKlienciWithTermin($this->Baza)));
        $Formularz->DodajPole('kurs', 'kurs', 'Kurs waluty ', array('tabelka' => Usefull::GetFormStandardRow(), 'decimal' => true));
        $Formularz->DodajPole('kurs_przewoznik', 'kurs', 'Kurs waluty ', array('tabelka' => Usefull::GetFormStandardRow(), 'decimal' => true, 'kurs_param' => 'pobierz_kurs_przewoznik'));
        $Formularz->DodajPole('stawka_przewoznik', 'tekst', 'Stawka dla przewoźnika', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('ilosc_km', 'tekst', 'Ilość km', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('termin_zaladunku', 'tekst_data', 'Termin załadunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('godzina_zaladunku', 'tekst', 'Godzina załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('termin_rozladunku', 'tekst_data', 'Termin rozładunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('godzina_rozladunku', 'tekst', 'Godzina rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('dokumenty', 'tekst_dlugi', 'Załączone dokumenty', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('typ_serwisu', 'lista', 'Typ serwisu', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetTypySerwisu($this->Baza), 'wybierz' => true));
        $Formularz->DodajPole('ladunek_niebezpieczny', 'tekst_dlugi', 'Uwagi', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('opis_ladunku', 'tekst_dlugi', 'Opis ładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
        $Formularz->DodajPole('stawka_klient', 'tekst', 'Stawka dla klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('nr_zlecenia_klienta', 'tekst', 'Numer zlecenia klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('id_szablon', 'lista', 'Szablon zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetSzablony($this->Baza)));
        $Formularz->DodajPole('termin_platnosci_dni', 'tekst', 'Termin płatności', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;'), 'id' => 'terminek', 'opis_dodatkowy_za' => ' dni'));
        $Formularz->DodajPole('platnosci_status_klient', 'lista', 'Status - przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::StatusyPlatnosci()));
        $Formularz->DodajPole('waluta', 'hidden', null);
        if(is_array($Wartosci)){
            $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
            $Formularz->UstawOpisPola('kurs', "Kurs waluty {$Values['waluta']} (klient)", false);
            $Formularz->UstawOpisPola('kurs_przewoznik', "Kurs waluty {$Values['waluta']} (przewoźnik)", false);
            $Formularz->UstawOpcjePola('stawka_klient', "opis_dodatkowy_za", " ".$Values['waluta'], false);
            $Formularz->UstawOpcjePola('stawka_przewoznik', "opis_dodatkowy_za", " ".$Values['waluta'], false);
        }
        if($this->WykonywanaAkcja != "szczegoly"){
            $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        }
        return $Formularz;
    }

    function &GenerujFormularzFaktura($Wartosci, $Mapuj = false) {
        $this->PrzyciskiFormularza['zapisz']['src'] = "dalej.gif";
        $Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
        $Formularz->DodajPole('zlecenia_faktura', 'podzbiór_checkbox_1n', 'Faktura na zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->ZleceniaDoFaktury));
        $Formularz->DodajPole('firma_wystaw', 'lista', 'Sprzedawca', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(2 => 'MEPP Sp. z o.o.')));
        $Formularz->DodajPole('data_wystawienia', 'tekst_data', 'Data wystawienia (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('miejsce_wystawienia', 'tekst', 'Miejsce wystawienia', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 150px;')));
        $Formularz->DodajPole('data_sprzedazy', 'tekst_data', 'Data sprzedaży (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('termin_platnosci', 'tekst_data', 'Termin płatności (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('vat', 'tekst', 'Stawka VAT (%)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('waluta', 'lista', 'Waluta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetWaluty(), 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza","pobierz_kurs_faktura");')));
        $Formularz->DodajPole('kurs', 'tekst', 'Kurs', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('szablon_faktura', 'lista', 'Szablon faktury', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array('PL' => 'PL', 'ENG' => 'ENG')));
        if($this->WykonywanaAkcja != "szczegoly"){
            $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        }
        return $Formularz;
    }

    function &GenerujFormularzFakturaZbiorcza($Wartosci, $Mapuj = false) {
        $this->PrzyciskiFormularza['zapisz']['src'] = "dalej.gif";
        $Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, $this->Parametr);
        $Formularz->DodajPole('zlecenia_faktura', 'podzbiór_checkbox_1n', 'Faktura na zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->ZleceniaDoFaktury));
        $Formularz->DodajPole('firma_wystaw', 'lista', 'Sprzedawca', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(2 => 'MEPP Sp. z o.o.')));
        $Formularz->DodajPole('data_wystawienia', 'tekst_data', 'Data wystawienia (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('miejsce_wystawienia', 'tekst', 'Miejsce wystawienia', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 150px;')));
        $Formularz->DodajPole('data_sprzedazy', 'tekst_data', 'Data sprzedaży (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('termin_platnosci', 'tekst_data', 'Termin płatności (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('vat', 'tekst', 'Stawka VAT (%)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('waluta', 'lista', 'Waluta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetWaluty(), 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza","pobierz_kurs_faktura");')));
        $Formularz->DodajPole('kurs', 'tekst', 'Kurs', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('szablon_faktura', 'lista', 'Szablon faktury', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array('PL' => 'PL', 'ENG' => 'ENG')));
        if($this->WykonywanaAkcja != "szczegoly"){
            $Formularz->DodajPole('zapisz', 'zapisz_anuluj', 'goToSecondStep', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        }
        return $Formularz;
    }

    function GenerujWarunki($AliasTabeli = null) {
        $Where = $this->DomyslnyWarunek();
        if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
            for ($i = 0; $i < count($this->Filtry); $i++) {
                $Pole = $this->Filtry[$i]['nazwa'];
                if (isset($_SESSION['Filtry'][$Pole])) {
                    $Wartosc = $_SESSION['Filtry'][$Pole];
                    if($this->Filtry[$i]['typ'] == "lista"){
                        if($Pole == "id_faktury"){
                            if($Wartosc == "yes"){
                                $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole > 0";
                            }else{
                                $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = 0";
                            }
                        }else{
                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                        }
                    }else if($this->Filtry[$i]['typ'] == "trasa"){
                        if($Wartosc['kod_kraju_zaladunku'] > 0){
                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."kod_kraju_zaladunku = '{$Wartosc['kod_kraju_zaladunku']}'";
                        }
                        if($Wartosc['kod_kraju_rozladunku'] > 0){
                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."kod_kraju_rozladunku = '{$Wartosc['kod_kraju_rozladunku']}'";
                        }
                    }else{
                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                    }
                }
            }
        }
        return ($Where != '' ? "WHERE $Where" : '');
    }

    function DomyslnyWarunek(){
        return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31'".(!$this->Uzytkownik->IsAdmin() ? " AND id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).")" : "");
    }

    function GenerujSortowanie(){
        if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'])){
            $NowySort = "";
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'] as $Pole => $Wartosc){
                if($Wartosc != ""){
                    $NowySort .= ($NowySort != "" ? "," : "")." $Pole $Wartosc";
                }
            }
            if($NowySort != ""){
                return $NowySort;
            }
        }
        return "data_zlecenia ASC, numer_zlecenia_krotki ASC";
    }

    function PobierzListeElementow($Filtry = array()) {
        $Wynik = array(
            "termin_zaladunku" => array("naglowek" => "Data i godzina załadunku"),
            "termin_rozladunku" => array("naglowek" => "Data i godzina rozładunku"),
        );
        if($this->Uzytkownik->IsAdmin()){
            $Wynik['kolor_zlecenia'] = array("naglowek" => "&nbsp;");
        }
        $Wynik['numer_zlecenia'] = array("naglowek" => "Numer zlecenia");
        if($this->Uzytkownik->IsAdmin()){
            $Wynik['kolor_k'] = array("naglowek" => "&nbsp;");
        }
        $Wynik['id_kierowca'] = array("naglowek" => "Dane kierowcy i numer rejestracyjny", 'elementy' => UsefullBase::GetDaneKierowcy($this->Baza));
        $Wynik['nr_zlecenia_klienta'] = array("naglowek" => "Numer zlecenia klienta");
        $Wynik['miejsce_zaladunku'] = array("naglowek" => "Załadowca i miejsce załadunku");
        $Wynik['odbiorca'] = array("naglowek" => "Odbiorca i miejsce rozładunku");
        $Wynik['ilosc_km'] = array("naglowek" => "Ilość km");
        $Wynik['opis_ladunku'] = array("naglowek" => "Opis ładunku");
        $Wynik['typ_serwisu'] = array("naglowek" => "Typ serwisu", 'elementy' => UsefullBase::GetTypySerwisu($this->Baza));
        if($this->Uzytkownik->MarzaAccess() || $this->Parametr == "tabela_rozliczen_moja"){
            $Wynik['stawka_klient'] = array("naglowek" => "Stawka dla klienta");
            $Wynik['stawka_przewoznik'] = array("naglowek" => "Stawka dla przewoznika");
        }
        $Wynik['id_klient'] = array("naglowek" => "Zleceniodawca", 'elementy' => $this->Klienci);
        $Wynik['id_przewoznik'] = array("naglowek" => "Przewoźnik", 'elementy' => $this->Przewoznicy);
        if($this->Uzytkownik->MarzaAccess() || $this->Parametr == "tabela_rozliczen_moja"){
            $Wynik['marza'] = array("naglowek" => "Marża");
        }
        $Wynik['id_uzytkownik'] = array("naglowek" => "Zlecenie wystawił", 'elementy' => UsefullBase::GetUsers($this->Baza));
        $Wynik['numer_zlecenia2'] = array("naglowek" => "Numer zlecenia");
        foreach($Wynik as $Key => $Val){
            $Wynik[$Key]['td_styl'] = "vertical-align: top;";
        }
        $Where = $this->GenerujWarunki();
        $Sort = $this->GenerujSortowanie();

        $this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY $Sort");
        return $Wynik;
    }

    function PobierzAkcjeNaLiscie($Dane = array()){
        $Akcje = array();
        if(count($Dane) > 0 && $Dane['ost_korekta'] != 2 && $Dane['sea_order_id'] == 0 && $Dane['air_order_id'] == 0){
            if($Dane['id_faktury'] > 0){
                $Akcje[] = array('img' => "faktura_drukuj_button", 'title' => "Drukuj fakturę", "akcja_link" => "drukuj_fakture.php?id={$Dane['id_faktury']}");
            }elseif ($Dane['id_klient']) {
                $Akcje[] = array('img' => "faktura_button", 'title' => "Fakturuj", "akcja" => "faktura");
            }else{
                $Akcje[] = array('img' => "faktura_button_grey");
            }
        }else{
            if(count($Dane) == 0){
                $Akcje[] = array('img' => "faktura_drukuj_button");
            }else{
                $Akcje[] = array('img' => "faktura_button_grey");
            }
        }
        $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_href" => "podglad.php?");
        $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+3 days"));
        if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
            $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "popraw");
        }else{
            $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
        }
        return $Akcje;
    }

    function AkcjeNiestandardowe($ID){
        switch($this->WykonywanaAkcja){
            case "faktura":
                $this->AkcjaFaktura($ID);
                break;
            case "faktura_zbiorcza":
                $this->AkcjaFakturaZbiorcza();
                break;
            case "specyfikacja":
                $this->AkcjaSpecyfikacja();
                break;
            case "popraw":
                $this->AkcjaEdycja($ID);
                break;
        }
    }

    function MozeBycOperacja($ID){
        $Result = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        $TerminEdycji = date("Y-m-d", strtotime($Result['termin_rozladunku']."+3 days"));
        if($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
            return true;
        }
        return false;
    }

    function ShowRecord($Element, $Nazwa, $Styl){
        if($Nazwa == "numer_zlecenia2"){
            $Element[$Nazwa] = $Element['numer_zlecenia'];
        }
        if($Nazwa == "termin_zaladunku"){
            $Element[$Nazwa] = $Element['termin_zaladunku']." ".$Element['godzina_zaladunku'];
        }
        if($Nazwa == "termin_rozladunku"){
            $Element[$Nazwa] = $Element['termin_rozladunku']." ".$Element['godzina_rozladunku'];
        }
        if($Nazwa == "stawka_klient" || $Nazwa == "stawka_przewoznik" || $Nazwa == 'stawka_przewoznik_brutto' || $Nazwa == 'stawka_klient_brutto'){
            if($Element['waluta'] == "PLN"){
                $StawkaPLN = $Element[$Nazwa];
            }else{
                $KursPrzelicz = ($Nazwa == "stawka_przewoznik" || $Nazwa == 'stawka_przewoznik_brutto' ? $Element['kurs_przewoznik'] : $Element['kurs']);
                $StawkaPLN = $Element[$Nazwa] * $KursPrzelicz;
            }
            $this->Sumowanie[$Nazwa] += $StawkaPLN;
            print ("<td$Styl><nobr>" . number_format($Element[$Nazwa], 2, ',', ' ') . " {$Element['waluta']}</nobr>");
            if ($Element['waluta'] != "PLN") {
                if ($KursPrzelicz > 0) {
                    echo("<br><nobr>" . number_format($StawkaPLN, 2, ',', ' ') . " PLN</nobr>");
                }else {
                    echo("<br>Nie podano kursu waluty {$Element['waluta']}!");
                }
            }
            if($Nazwa == "stawka_klient" || $Nazwa == "stawka_przewoznik"){
                echo "<br /><br /><u>Stawka za km:</u><br />";
                if($Element['ilosc_km'] > 0){
                    echo "<nobr>" . number_format($Element[$Nazwa]/$Element['ilosc_km'], 2, ',', ' ') . " {$Element['waluta']}";
                    if ($Element['waluta'] != "PLN") {
                        echo("<br><nobr>" . number_format($StawkaPLN/$Element['ilosc_km'], 2, ',', ' ') . " PLN</nobr>");
                    }
                }else{
                    echo "Brak ilości km";
                }
            }
            print ("</td>");
        }else if($Nazwa == "marza"){
            if ($Element['waluta'] == "PLN") {
                $marza = $Element['stawka_klient'] - $Element['stawka_przewoznik'];
            }else{
                $marza = ($Element['stawka_klient'] * $Element['kurs']) - ($Element['stawka_przewoznik'] * $Element['kurs_przewoznik']);
            }
            $this->Sumowanie['marza'] += $marza;
            echo("<td$Styl><nobr>". number_format($marza, 2, ',', ' ') . " PLN</nobr></td>");
        }else if($Nazwa == "id_uzytkownik"){
            print "<td$Styl>".stripslashes(nl2br($Element[$Nazwa]));
            if($Element['edytowali'] != ""){
                echo "<br /><br />edytował:<br />";
                $Edytowali = explode("#", $Element['edytowali']);
                foreach($Edytowali as $Edit){
                    if($Edit != ""){
                        echo $Edit."<br />";
                    }
                }
            }
            echo("</td>");
        }else if($Nazwa == "kolor_zlecenia" || $Nazwa == "kolor_k"){
            echo("<td$Styl><input type='checkbox' name='{$Nazwa}[]' value='{$Element[$this->PoleID]}'></td>");
        }else if($Nazwa == "numer_zlecenia" || $Nazwa == "numer_zlecenia2"){
            echo("<td$Styl".($Element['kolor_zlecenia'] == 1 ? "bgcolor=\"red\"" : "").">".stripslashes($Element[$Nazwa])."</td>");
        }else{
            echo("<td$Styl>".stripslashes(nl2br($Element[$Nazwa]))."</td>");
        }
    }

    function AkcjaEdycja($ID){
        if($this->MozeBycOperacja($ID)){
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Formularz = $this->GenerujFormularz($_POST, true);
                $PolaWymagane = $Formularz->ZwrocPolaWymagane();
                $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
                $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                if ($OpcjaFormularza == 'zapisz') {
                    echo "<div style='clear: both;'></div>\n";
                    if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
                        if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow(), $ID)) {
                            $this->ShowOK();
                            return;
                        }
                        else {
                            Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                        }
                    }else{
                        Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
                    }
                }
                $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz, $ID);
                foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                    $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                }
                foreach($this->PolaZdublowane as $NazwaPola){
                    $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
                }
                $this->ShowTitleDiv($ID, $_POST);
                $Formularz->Wyswietl($Wartosci, false);
            }
            else {
                $Dane = $this->PobierzDaneElementu($ID);
                $Formularz = $this->GenerujFormularz($Dane, false);
                $this->ShowTitleDiv($ID, $Dane);
                $Formularz->Wyswietl($Dane, false);
            }
        }else{
            Usefull::ShowKomunikatError("<b>Edycja zablokowana</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
        }
    }

    function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
        $DaneDefault = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        $edytowali = $DaneDefault['edytowali'];
        //if($_SESSION['login'] != "artplusadmin"){
        foreach($Wartosci as $Pole => $Value){
            if($DaneDefault[$Pole] != $Value){
                $Now = date("Y-m-d H:i:s");
                $edytowali .= $_SESSION['nazywasie']." ($Now)#";
                $Wartosci['edytowali'] = $edytowali;
                break;
            }
        }
        //}
        $Zlecenia = new Zlecenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        return $Zlecenia->ZapiszDaneElementu($Formularz, $Wartosci, $PrzeslanePliki, $ID);
    }

    function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
        if($_POST['OpcjaFormularza'] == "pobierz_kurs"){
            $Wartosci['kurs'] = UsefullBase::PobierzKursZDnia($this->Baza, $Wartosci['termin_zaladunku'], $Wartosci['waluta'], $Wartosci['id_klient']);
            if(!$Wartosci['kurs']){
                $Wartosci['kurs'] = "0.0000";
            }

        }
        if($_POST['OpcjaFormularza'] == "pobierz_kurs_przewoznik"){
            $Wartosci['kurs_przewoznik'] = UsefullBase::PobierzKursZDnia($this->Baza, $Wartosci['termin_zaladunku'], $Wartosci['waluta']);
            if(!$Wartosci['kurs_przewoznik']){
                $Wartosci['kurs_przewoznik'] = "0.0000";
            }

        }
        return $Wartosci;
    }

    function ShowSuma($NazwaPola){
        if($NazwaPola == "stawka_przewoznik"){
            return "Ogół kosztów: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
        }else if($NazwaPola == "stawka_klient"){
            return "Ogół sprzedaży: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
        }else if($NazwaPola == "marza"){
            return "Suma marży: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
        }else if($NazwaPola == "stawka_przewoznik_brutto" || $NazwaPola == "stawka_klient_brutto"){
            return "<nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN</nobr>";
        }else{
            return $this->Sumowanie[$NazwaPola];
        }
    }

    function ShowBigButtonActions($ID){
        echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
        if(is_null($ID) && $this->WykonywanaAkcja != "specyfikacja"){
            echo("<div style='float: left;'>");
            echo "<div style='float: left; color: #bcce00; font-weight: bold;'>RAPORTY:<br /><br /></div>";
            echo "<div style='clear: both;'></div>\n";
            if($this->Uzytkownik->DostepDoRaportu('klient')){
                echo "<a href='raporty.php?tryb=klient' target='_blank' class='form-button'>klienci</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('przewoznik')){
                echo "<a href='raporty.php?tryb=przewoznik' target='_blank' class='form-button'>przewoźnicy</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('spedytor')){
                echo "<a href='raporty.php?tryb=spedytor' target='_blank' class='form-button'>spedytorzy</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('klientnaspedytora')){
                echo "<a href='raporty.php?tryb=klientnaspedytora' target='_blank' class='form-button'>klienci na spedytora</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('oddzial')){
                echo "<a href='raporty.php?tryb=oddzial' target='_blank' class='form-button'>oddziały</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('branza')){
                echo "<a href='raporty2.php?tryb=branza' target='_blank' class='form-button'>wg. branży</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('siedziba')){
                echo "<a href='raporty2.php?tryb=siedziba' target='_blank' class='form-button'>wg. siedziby</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('typ_serwisu')){
                echo "<a href='raporty2.php?tryb=typ_serwisu' target='_blank' class='form-button'>wg. typu serwisu</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('trasy')){
                echo "<a href='raporty3.php' target='_blank' class='form-button'>wg. tras</a>";
            }
            if($this->Uzytkownik->DostepDoRaportu('analiza_wynikow')){
                echo "<a href='raporty_analiza_wynikow.php' target='_blank' class='form-button'>analiza wyników</a>";
            }
            echo ("</div>");
        }
        include(SCIEZKA_SZABLONOW."nav.tpl.php");
        echo "</div>\n";
        if($this->WykonywanaAkcja != "dodawanie" && $this->WykonywanaAkcja != "specyfikacja" && is_null($ID)){
            include(SCIEZKA_SZABLONOW."filters.tpl.php");
        }
        echo "<div style='clear: both'></div>\n";
    }

    function ShowOK(){
        $this->LinkPowrotu = "?modul=$this->Parametr&zmieniony={$_GET['id']}#Linia_{$_GET['id']}";
        Usefull::ShowKomunikatOK('<b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>');
    }

    function ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie){
        $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
        if (isset($_GET['zmieniony']) && $Element['id_zlecenie'] == $_GET['zmieniony']){
            $KolorWiersza = "#02aec4";
        }
        echo("<tr style='background-color: $KolorWiersza;'>");
        echo("<td class='licznik'><a name='Linia_{$Element['id_zlecenie']}'></a>$Licznik</td>");
        foreach ($Pola as $Nazwa => $Opis) {
            $Styl = "";
            if(is_array($Opis)){
                $Styl = (isset($Opis['td_styl']) ? " style='{$Opis['td_styl']}'" : '');
                $Styl .= (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
                if(isset($Opis['elementy'])){
                    $Element[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]];
                }
                if(isset($Opis['type']) && $Opis['type'] == "date"){
                    $Element[$Nazwa] = ($Element[$Nazwa] == "0000-00-00" ? "&nbsp;" : $Element[$Nazwa]);
                }
            }
            $this->ShowRecord($Element, $Nazwa, $Styl);
        }
        if($this->CzySaOpcjeWarunkowe){
            $AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Element);
        }
        $this->ShowActionsList($AkcjeNaLiscie, $Element);
        echo("</tr>");
    }

    function AkcjaSpecyfikacja(){
        $Specyfikacja = new Specyfikacja($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Specyfikacja->AkcjaDodawanie();
    }

    function AkcjaFaktura($ID){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $Formularz = $this->GenerujFormularzFaktura($_POST, true);
            $PolaWymagane = $Formularz->ZwrocPolaWymagane();
            $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
            $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
            if ($OpcjaFormularza == 'zapisz') {
                echo "<div style='clear: both;'></div>\n";
                if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
                    if ($this->ZapiszFakture($Formularz, $Wartosci, $ID)) {
                        Usefull::RedirectLocation("?modul=faktury&akcja=edycja&id=$this->ID&act=afteradd");
                        return;
                    }
                    else {
                        Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    }
                }else{
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
                }
            }
            $Wartosci = $this->AkcjaPrzeladowanieFaktura($Wartosci, $ID);
            $Formularz->UstawOpcjePola('zlecenia_faktura', 'elementy', $this->ZleceniaDoFaktury, false);
            foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            foreach($this->PolaZdublowane as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            $this->ShowTitleDiv($ID, $_POST);
            $Formularz->Wyswietl($Wartosci, false);
        }
        else {
            $Dane = $this->PobierzDaneElementuFaktura($ID);
            $Formularz = $this->GenerujFormularzFaktura($Dane, false);
            $this->ShowTitleDiv($ID, $Dane);
            $Formularz->Wyswietl($Dane, false);
        }
    }
    function AkcjaFakturaZbiorcza(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['OrdersIDs'])) {
            $ID = $_POST['tabela_rozliczen_nowa_pole_1'][0];
            $Formularz = $this->GenerujFormularzFaktura($_POST, true);
            $PolaWymagane = $Formularz->ZwrocPolaWymagane();
            $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
            $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
            if ($OpcjaFormularza == 'zapisz') {
                echo "<div style='clear: both;'></div>\n";
                if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
                    if ($this->ZapiszFakture($Formularz, $Wartosci, $ID)) {
                        Usefull::RedirectLocation("?modul=faktury&akcja=edycja&id=$this->ID&act=afteradd");
                        return;
                    }
                    else {
                        Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    }
                }else{
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
                }
            }
            $Wartosci = $this->AkcjaPrzeladowanieFaktura($Wartosci, $ID);
            $Formularz->UstawOpcjePola('zlecenia_faktura', 'elementy', $this->ZleceniaDoFaktury, false);
            foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            foreach($this->PolaZdublowane as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            $this->ShowTitleDiv($ID, $_POST);
            $Formularz->Wyswietl($Wartosci, false);
        }
        else {
            $Dane = $this->PobierzDaneElementuFakturaZbiorcza($_POST['OrdersIDs']);
            $Formularz = $this->GenerujFormularzFaktura($Dane, false);
            $this->ShowTitleDiv($ID, $Dane);
            $Formularz->Wyswietl($Dane, false);
        }
    }

    function PobierzDaneElementuFaktura($ID){
        $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        $ClientDane = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient']}'");
        $this->ZleceniaDoFaktury = $this->Baza->GetOptions("SELECT z.id_zlecenie, z.numer_zlecenia FROM orderplus_zlecenie z WHERE ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) AND z.id_klient='{$Dane['id_klient']}' AND id_faktury = '0' AND waluta = '{$Dane['waluta']}'");
        $Faktura['data_wystawienia'] = $this->Dzis;
        $Faktura['miejsce_wystawienia'] = "Warszawa";
        $Faktura['data_sprzedazy'] = $Dane['termin_rozladunku'];
        $Faktura['termin_platnosci'] = date('Y-m-d', strtotime($Faktura['data_wystawienia'].'+'.$Dane['termin_platnosci_dni'].' days'));
        $Faktura['zlecenia_faktura'][] = $ID;
        $Faktura['firma_wystaw'] = $Dane['firma_wystaw'];
        $Faktura['vat'] = $this->UstalStawkeVat($ClientDane, $Dane['kod_kraju_zaladunku'], $Dane['kod_kraju_rozladunku']);
        $Faktura['szablon_faktura'] = ($ClientDane['siedziba_id'] == 2 ? "ENG" : "PL");
        $Faktura['waluta'] = ($ClientDane['waluta_fakturowania'] != "" ? $ClientDane['waluta_fakturowania'] : $Dane['waluta']);
        $Faktura['kurs'] = UsefullBase::PobierzKursDoFaktury($this->Baza, $Dane['waluta'], $Faktura['waluta'], $Dane['termin_zaladunku'], $Dane['id_klient']);
        return $Faktura;
    }

    function PobierzDaneElementuFakturaZbiorcza($ordersIDs){
        $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID IN ({$ordersIDs})");
        $ClientDane = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$ordersIDs}'");
        $this->ZleceniaDoFaktury = $this->Baza->GetOptions("SELECT z.id_zlecenie, z.numer_zlecenia FROM orderplus_zlecenie z WHERE ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) AND z.id_klient='{$Dane['id_klient']}' AND id_faktury = '0' AND waluta = '{$Dane['waluta']}'");
        $Faktura['data_wystawienia'] = $this->Dzis;
        $Faktura['miejsce_wystawienia'] = "Warszawa";
        $Faktura['data_sprzedazy'] = $Dane['termin_rozladunku'];
        $Faktura['termin_platnosci'] = date('Y-m-d', strtotime($Faktura['data_wystawienia'].'+'.$Dane['termin_platnosci_dni'].' days'));
        $IDS = explode(',', $_POST['OrdersIDs']);
        foreach($IDS as $ID){
            $Faktura['zlecenia_faktura'][] = $ID;
        }
        $Faktura['firma_wystaw'] = $Dane['firma_wystaw'];
        $Faktura['vat'] = $this->UstalStawkeVat($ClientDane, $Dane['kod_kraju_zaladunku'], $Dane['kod_kraju_rozladunku']);
        $Faktura['szablon_faktura'] = ($ClientDane['siedziba_id'] == 2 ? "ENG" : "PL");
        $Faktura['waluta'] = ($ClientDane['waluta_fakturowania'] != "" ? $ClientDane['waluta_fakturowania'] : $Dane['waluta']);
        $Faktura['kurs'] = UsefullBase::PobierzKursDoFaktury($this->Baza, $Dane['waluta'], $Faktura['waluta'], $Dane['termin_zaladunku'], $Dane['id_klient']);
        return $Faktura;
    }

    function AkcjaPrzeladowanieFaktura($Wartosci, $ID = null){
        $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        $this->ZleceniaDoFaktury = $this->Baza->GetOptions("SELECT z.id_zlecenie, z.numer_zlecenia FROM orderplus_zlecenie z WHERE ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) AND z.id_klient='{$Dane['id_klient']}' AND id_faktury = '0' AND waluta = '{$Dane['waluta']}'");
        if($_POST['OpcjaFormularza'] == "pobierz_kurs_faktura"){
            $Wartosci['kurs'] = UsefullBase::PobierzKursDoFaktury($this->Baza, $Dane['waluta'], $Wartosci['waluta'], $Dane['termin_zaladunku'], $Dane['id_klient']);
        }
        return $Wartosci;
    }

    function ZapiszFakture($Formularz, $Wartosci, $ID){
        $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
        $ClientDane = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient']}'");
        $AktualnyMiesiac = date("m");
        $AktualnyRok = date("Y");
        $vat = $Wartosci['vat'];
        $vatOblicz = (in_array(strtolower($Wartosci['vat']), array("np","zw")) ? 0 : $Wartosci['vat']);
        unset($Wartosci['vat']);
        $Wartosci['kurs'] = str_replace(",",".",$Wartosci['kurs']);
        $Wartosci['id_oddzial'] = $Dane['id_oddzial'];
        $Faktury = new Faktury($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
        $Wartosci = $Faktury->GenerujNumer($Wartosci);
        $Waluty = UsefullBase::GetWaluty($this->Baza);
        $MapujBazy = array_flip($Waluty);
        $ZapiszWaluta = $Wartosci['waluta'];
        $Wartosci['id_waluty'] = $MapujBazy[$Wartosci['waluta']];
        unset($Wartosci['waluta']);
        $Wartosci['id_formy'] = 2;
        $Wartosci['id_zlecenia'] = $ID;
        $Wartosci['id_klienta'] = $Dane['id_klient'];
        $Wartosci['termin_zaladunku'] = $Dane['termin_zaladunku'];
        $Zlecenia = $Wartosci['zlecenia_faktura'];
        unset($Wartosci['zlecenia_faktura']);
        $Wartosci['uwagi'] = 'numer zlecenia klienta: '.$Dane['nr_zlecenia_klienta']."\n";
        $KrajZaladunku = $this->Baza->GetValue("SELECT kraj_nazwa".($Wartosci['szablon_faktura'] == "ENG" ? "_en" : "")." orderplus_kody_krajow WHERE kod_kraju_id = '{$Dane['kod_kraju_zaladunku']}'");
        $KrajRozladunku = $this->Baza->GetValue("SELECT kraj_nazwa".($Wartosci['szablon_faktura'] == "ENG" ? "_en" : "")." orderplus_kody_krajow WHERE kod_kraju_id = '{$Dane['kod_kraju_rozladunku']}'");
        $Wartosci['uwagi'] .= "{$Dane['adres_zaladunku']} ".Usefull::ActiveUpperText($KrajZaladunku)." - {$Dane['adres_rozladunku']} ".Usefull::ActiveUpperText($KrajRozladunku).";\n";
        $Wartosci['uwagi'] .= "data zał: {$Dane['termin_zaladunku']};\n";
        $Wartosci['uwagi'] .= "data rozł: {$Dane['termin_rozladunku']};\n";
        $CheckVat = $this->UstalStawkeVat($ClientDane, $Dane['kod_kraju_zaladunku'], $Dane['kod_kraju_rozladunku']);
        $Zapytanie = $this->Baza->PrepareInsert("faktury", $Wartosci);
        if($this->Baza->Query($Zapytanie)){
            $FakID = $this->Baza->GetLastInsertId();
            $this->ID = $FakID;
            foreach($Zlecenia as $ZlecenieID){
                $DaneZlecenie = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE id_zlecenie = '$ZlecenieID'");
                $kwota = number_format($DaneZlecenie['stawka_klient']*$Wartosci['kurs'],2,'.','');
                $kwota_vat = number_format(round(($vatOblicz / 100) * $kwota, 2),2,'.','');
                $kwota_brutto = number_format($kwota + $kwota_vat,2,'.','');
                $z2 = "INSERT INTO faktury_pozycje SET
                                    id_faktury = $FakID,
                                     opis = '".($CheckVat == 23 ? "Wewnątrzwspólnotowa usługa spedycyjna" : "Transport międzynarodowy")."',
                                     ilosc = 1,
                                     jednostka = 'szt.',
                                     netto_jednostki = '$kwota',
                                     netto = '$kwota',
                                     vat = '$vat',
                                     kwota_vat = '$kwota_vat',
                                     brutto = '$kwota_brutto',
                                     brutto_jednostki = '$kwota_brutto'";
                if($this->Baza->Query($z2)){
                    $UpdateZlecenie['id_faktury'] = $FakID;
                    //$UpdateZlecenie['kurs'] = $Wartosci['kurs'];
                    $UpdateZlecenie['termin_wlasny'] = $Wartosci['termin_platnosci'];
                    $UpdateZlecenie['stawka_vat_klient'] = $vat;
                    $UpdateZlecenie['faktura_wlasna'] = $Wartosci['numer'];
                    $ZapytanieZlecenie = $this->Baza->PrepareUpdate($this->Tabela, $UpdateZlecenie, array('id_zlecenie' => $ZlecenieID));
                    $this->Baza->Query($ZapytanieZlecenie);
                    if($DaneZlecenie['waluta'] != $ZapiszWaluta){
                        $KursDane = UsefullBase::PobierzKursDoFaktury($this->Baza, $DaneZlecenie['waluta'], $ZapiszWaluta, $DaneZlecenie['termin_zaladunku'], $DaneZlecenie['id_klient'], true);
                        $Wartosci['uwagi'] .= "koszt: {$DaneZlecenie['stawka_klient']} {$DaneZlecenie['waluta']} x {$Wartosci['kurs']} wg tabeli ".($KursDane['bank'] == "KOM" ? "kursów walut BPH z dnia {$KursDane['data']}" : "nr {$KursDane['tabela']} z dnia {$KursDane['data']}").";";
                    }
                }
            }
            if($Dane['waluta'] != $Wartosci['waluta']){
                $this->Baza->Query("UPDATE faktury SET uwagi = '{$Wartosci['uwagi']}' WHERE id_faktury = '$FakID'");
            }
            return true;
        }
        return false;
    }

    function DodatkoweFiltryDoKolumn($Pola, $Elementy, $AkcjeNaLiscie){
        $Users = UsefullBase::GetUsers($this->Baza);
        foreach($Elementy as $Dane){
            $NumeryZlecen[$Dane['numer_zlecenia']] = $Dane['numer_zlecenia'];
            //$Kierowcy[$Dane['id_kierowca']] = $this->Kierowcy[$Dane['id_kierowca']];
            $NumeryZlecenKlienta[$Dane['nr_zlecenia_klienta']] = $Dane['nr_zlecenia_klienta'];
            $Klienci[$Dane['id_klient']] = $this->Klienci[$Dane['id_klient']];
            $Przewoznicy[$Dane['id_przewoznik']] = $this->Przewoznicy[$Dane['id_przewoznik']];
            $Usersi[$Dane['id_uzytkownik']] = $Users[$Dane['id_uzytkownik']];
        }
        $FiltrJest = false;
        if(isset($_POST['filtry'])){
            foreach($_POST['filtry'] as $Pole => $Wartosc){
                if($Wartosc != ""){
                    foreach($Elementy as $Idx => $Dane){
                        if($Dane[$Pole] != $Wartosc){
                            unset($Elementy[$Idx]);
                        }
                    }
                    $FiltrJest = true;
                    break;
                }
            }
        }
        if(!$FiltrJest && isset($_POST['filtry_id'])){
            foreach($_POST['filtry_id'] as $Pole => $Wartosc){
                if(intval($Wartosc) > 0){
                    foreach($Elementy as $Idx => $Dane){
                        if($Dane[$Pole] != $Wartosc){
                            unset($Elementy[$Idx]);
                        }
                    }
                    break;
                }
            }
        }
        asort($NumeryZlecen);
        asort($Kierowcy);
        asort($NumeryZlecenKlienta);
        asort($Klienci);
        asort($Przewoznicy);
        asort($Usersi);
        $Filtry['termin_zaladunku'] = array("type" => "sort");
        $Filtry['termin_rozladunku'] = array("type" => "sort");
        $Filtry['numer_zlecenia'] = array("type" => "filtr", 'elementy' => $NumeryZlecen);
        //$Filtry['id_kierowca'] = array("type" => "filtr_id", 'elementy' => $Kierowcy);
        $Filtry['nr_zlecenia_klienta'] = array("type" => "filtr", 'elementy' => $NumeryZlecenKlienta);
        $Filtry['miejsce_zaladunku'] = array("type" => "sort");
        $Filtry['odbiorca'] = array("type" => "sort");
        $Filtry['id_klient'] = array("type" => "filtr_id", 'elementy' => $Klienci);
        $Filtry['id_przewoznik'] = array("type" => "filtr_id", 'elementy' => $Przewoznicy);
        $Filtry['id_uzytkownik'] = array("type" => "filtr_id", 'elementy' => $Usersi);
        include(SCIEZKA_SZABLONOW."filtry-kolumn.tpl.php");
        return $Elementy;
    }

    function SprawdzDane($Wartosci){
        if($this->WykonywanaAkcja == "faktura"){
            if(isset($Wartosci['zlecenia_faktura'])){
                foreach($Wartosci['zlecenia_faktura'] as $Idx => $ZlecenieID){
                    if($this->Baza->GetValue("SELECT id_faktury FROM $this->Tabela WHERE id_zlecenie = '$ZlecenieID'") > 0){
                        unset($Wartosci['zlecenia_faktura'][$Idx]);
                    }
                }
            }
            if(!isset($Wartosci['zlecenia_faktura']) || count($Wartosci['zlecenia_faktura']) == 0){
                $this->Error = "Musisz wybrać przynajmniej jedno zlecenie do którego jest wystawiana faktura";
                return false;
            }

        }
        return true;
    }

    /**
     * Ustala stawkę vat na podstawie klienta oraz miejsca załadunku i rozładunku
     * @param array $ClientDane
     * @param int $KodKrajuZaladunku
     * @param int $KodKrajuRozladunku
     * @return string $StawkaVat
     */
    function UstalStawkeVat($ClientDane, $KodKrajuZaladunku, $KodKrajuRozladunku){

        if($ClientDane['siedziba_id'] == 2){
            return "np";
        }else{
            $KrajZaladunku = $this->Baza->GetValue("SELECT is_ue FROM orderplus_kody_krajow WHERE kod_kraju_id = '$KodKrajuZaladunku'");
            if($KrajZaladunku == 0){
                return "0";
            }
            $KrajRozladunku = $this->Baza->GetValue("SELECT is_ue FROM orderplus_kody_krajow WHERE kod_kraju_id = '$KodKrajuRozladunku'");
            if($KrajRozladunku == 0){
                return "0";
            }
        }
        return "23";
    }
}
?>
