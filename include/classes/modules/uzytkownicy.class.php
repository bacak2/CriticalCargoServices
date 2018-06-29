<?php
/**
 * Moduł użytkownicy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2008 Asrael
 * @package		Panelplus
 * @version		1.0
 */

/**
 * Obsługa bazy użytkowników
 *
 */
class Uzytkownicy extends ModulBazowy {
        private $Privilages;
        private $Oddzial;
        private $TablicaZakladek = array();
        private $DostepDoKolumn = array();
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->ZablokowaneElementyIDs = array(1,12);
            if(isset($_GET['id']) && $this->Uzytkownik->IsAdmin() == false){
                $Upr = $this->Baza->GetValue("SELECT uprawnienia_id FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$_GET['id']}'"); 
                if($Upr < 3){
                    $this->ZablokowaneElementyIDs[] = $_GET['id'];
                }
            }
            $this->Tabela = 'orderplus_uzytkownik';
            $this->PoleID = 'id_uzytkownik';
            $this->PoleNazwy = 'login';
            $this->Nazwa = 'Użytkownik';
            $this->PoleVisible = 'blokada';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Oddzial = $this->Baza->GetOptions("SELECT id_oddzial, nazwa FROM orderplus_oddzial ORDER BY nazwa ASC");
            $Privilages = $this->Baza->GetOptions("SELECT uprawnienia_id, uprawnienia_nazwa FROM orderplus_uprawnienia ORDER BY uprawnienia_lp");
            $this->Privilages = Usefull::PolaczDwieTablice(array(0 => 'brak przypisania'), $Privilages);
            $this->DostepDoKolumn = Usefull::GetTabelaRozliczenKolumny();
            $this->OdpalZmiany();
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('login', 'tekst', 'Login', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('pass', 'password', 'Nowe hasło<br />(zostaw puste jeśli nie zmieniasz hasła)', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('imie', 'tekst', 'Imię', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('nazwisko', 'tekst', 'Nazwisko', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('email', 'tekst', 'Email', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('id_oddzial', 'lista', 'Oddział:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->Oddzial));
            if($this->Uzytkownik->IsAdmin() == false){
                unset($this->Privilages[1]);
                unset($this->Privilages[2]);
            }
            $Formularz->DodajPole('uprawnienia_id', 'lista', 'Poziom uprawnień:<br /><small>Uwaga! Zmiana poziomu uprawnień spowoduje ustawienie domyślnych uprawnień dla wybranego poziomu</small>', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->Privilages, 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza", "change-role")')));
            $Formularz->DodajPole('aplikacja_dostep', 'podzbiór_my_checkbox_1n', 'Dostęp do aplikacji:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => array("ORDER" => "ORDER",  "CRM" => "CRM")));
            $Formularz->DodajPole('raporty_dostep', 'podzbiór_my_checkbox_1n', 'Dostęp do raportów:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->GetRaporty()));
            $Formularz->DodajPole('oddzialy_dostep', 'podzbiór_my_checkbox_1n', 'Dostęp do oddziałów:<br /><small>dotyczy widoczności w tabeli rozliczeń, raportów systemu, raportów, potwierdzeń</small>', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->Oddzial));
            $Formularz->DodajPole('uprawnienia', 'uprawnienia', 'Uprawnienia:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->TablicaZakladek));
            $Formularz->DodajPole('uprawnienia_kolumn', 'uprawnienia_kolumn', 'Tabela rozliczeń - uprawnienia do kolumn:', array("tabelka" => Usefull::GetFormStandardRow(), 'elementy' => $this->DostepDoKolumn));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

	function &GenerujFormularzZmianaHasla() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('haslo', 'password', 'Hasło', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('powtorz_haslo', 'password', 'Powtórz hasło', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case 'zmiana_hasla': $this->AkcjaZmianaHasla($ID); break;
                default: $this->AkcjaLista();
            }
	}
	
	function PobierzListeElementow($Filtry = array()) {
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY $this->PoleNazwy");
		$Wynik = array(
			"login" => 'Login',
                        "uprawnienia_id" => array('naglowek' => 'Poziom uprawnień', 'elementy' => $this->Privilages),
                        "id_oddzial" => array('naglowek' => 'Oddział', 'elementy' => $this->Oddzial)
		);
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
                if($this->Uzytkownik->IsAdmin() == false && $Dane['uprawnienia_id'] < 3){
                    $this->ZablokowaneElementyIDs[] = $Dane['id_uzytkownik'];
                }
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                if($Dane['blokada'] == 0){
                    $Akcje[] = array('img' => "unlock", 'title' => "Zablokuj użytkownika", "akcja" => "blokowanie");
                }else{
                    $Akcje[] = array('img' => "lock", 'title' => "Odblokuj użytkownika", "akcja" => "blokowanie");
                }
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
                    $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
		}
                $Akcje[] = array('img' => "desc_button", 'title' => "Szczegóły", "akcja" => "szczegoly");
		return $Akcje;
	}

        function SprawdzDane($TabelaWartosci, $ID = 0){
            if(isset($TabelaWartosci['haslo']) && !$this->Uzytkownik->SprawdzPowtorzoneHaslo($TabelaWartosci['haslo'], $TabelaWartosci['powtorz_haslo'])){
                $this->Error = "Nie wpisano hasła lub hasło zostało błędnie powtórzone.";
                return false;
            }
            if(!$this->Uzytkownik->CzyNieZdublowanoLoginu($TabelaWartosci['login'], $ID)){
                $this->Error = "Login już istnieje w bazie!";
                return false;
            }
            return true;
        }

        function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
                if($ID){
                    if($this->Uzytkownik->Edytuj($ID, $Wartosci)){
                        return true;
                    }
                }else{
                    if($this->Uzytkownik->Dodaj($Wartosci)){
                        return true;
                    }
		}
		return false;
	}
	
	function AkcjaKasowanie($ID) {
		if($ID != 1){
                    if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
                            Usefull::ShowKomunikatOstrzezenie("Skasować <b>".$this->PobierzNazweElementu($ID)."</b> ?<br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/bin.gif\" style='display: inline; vertical-align: middle;'> Skasuj</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/cancel.gif\" style='display: inline; vertical-align: middle;'> Anuluj</a><br/><br/><br/><b>UWAGA! Dane zostaną utracone bezpowrotnie!</b>");
                    }
                    else {
                            if ($this->UsunElement($ID)) {
                                    Usefull::ShowKomunikatOK("<b>Pozycja skasowana.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                            }
                            else {
                                   Usefull::ShowKomunikatError("<b>Wystąpił problem. Dane nie zostały skasowane.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
                            }
                    }
		}else{
			Usefull::ShowKomunikatError("<b>Nie możesz skasować konta administratora głównego!</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót</a>");
		}
	}

        function GenerujTabliceZakladek($Zakladki){
		$this->TablicaZakladek = $Zakladki;
	}

        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            switch($Typ){
                case 'uprawnienia':
                    $Dane[$Pole] = explode(",", trim($Dane[$Pole]));
                    break;
                case 'uprawnienia_kolumn':
                    $Kolumny = $this->Baza->GetRows("SELECT * FROM orderplus_uzytkownik_tabela_rozliczen WHERE $this->PoleID = '{$Dane[$this->PoleID]}'");
                    foreach($Kolumny as $Upr){
                        $Dane[$Pole][] = $Upr['tabela_widok'];
                        $Dane[$Pole][] = $Upr['tabela_kolumna'];
                    }
                    $Dane[$Pole] = array_unique($Dane[$Pole]);
                    break;
            }
            return $Dane;
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Aplikacje = unserialize($Dane['aplikacja_dostep']);
            unset($Dane['aplikacja_dostep']);
            $Dane['aplikacja_dostep'] = $Aplikacje;
            $Raporty = unserialize($Dane['raporty_dostep']);
            unset($Dane['raporty_dostep']);
            $Dane['raporty_dostep'] = $Raporty;
            $Oddzialy = unserialize($Dane['oddzialy_dostep']);
            unset($Dane['oddzialy_dostep']);
            $Dane['oddzialy_dostep'] = $Oddzialy;
            return $Dane;
        }

        function OdpalZmiany(){
            $this->Baza->EnableLog();
            if($_GET['dev'] == "set_aplikacje"){
                $All = serialize(array('ORDER', 'CRM'));
                $CRM = serialize(array('CRM'));
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET aplikacja_dostep = '$All' WHERE uprawnienia_id != '6'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET aplikacja_dostep = '$CRM' WHERE uprawnienia_id = '6'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET aplikacja_dostep = '$All' WHERE uprawnienia_id != '6'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET aplikacja_dostep = '$CRM' WHERE uprawnienia_id = '6'");
            }
            if($_GET['dev'] == "set_widoki"){
                $Widoki['all'] = array('widok', 'id_klient', 'id_przewoznik', 'id_oddzial', 'id_uzytkownik', 'stawka_klient', 'stawka_przewoznik', 'nr_zlecenia_klienta', 'numer_zlecenia', 'terminy');
                $Widoki['admin'] = array('widok', 'stawka_klient_brutto', 'stawka_przewoznik_brutto', 'id_faktury', 'faktura_przewoznika');
                $Widoki['operacja'] = array('widok', 'termin_zaladunku', 'termin_rozladunku', 'id_kierowca', 'miejsce_zaladunku', 'odbiorca', 'opis_ladunku', 'ilosc_km', 'typ_serwisu', 'marza');
                $Widoki['platnosci'] = array('widok', 'data_sprzedazy', 'termin_wlasny', 'rzecz_zaplata_klienta', 'opoznienie_klient', 'data_wplywu', 'termin_przewoznika', 'planowana_zaplata_przew', 'rzecz_zaplata_przew', 'opoznienie_przewoznik', 'fifo');
                $Admin = serialize($Widoki);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '1'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '2'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '5'");
                $TPSArray = array('all' => $Widoki['all'], 'operacja' => $Widoki['operacja']);
                unset($TPSArray['all'][9]);
                unset($TPSArray['operacja'][9]);
                $TPS = serialize($TPSArray);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$TPS' WHERE uprawnienia_id = '3'");
                $KAArray = array('all' => $Widoki['all'], 'admin' => $Widoki['admin'], 'operacja' => $Widoki['operacja']);
                unset($KAArray['operacja'][3]);
                unset($KAArray['operacja'][7]);
                unset($KAArray['operacja'][8]);
                unset($KAArray['operacja'][9]);
                $KA = serialize($KAArray);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$KA' WHERE uprawnienia_id = '4'");
                $Pusta = serialize(array());
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Pusta' WHERE uprawnienia_id = '6'");
            }
            if($_GET['dev'] == "set_widoki"){
                $Widoki['all'] = array('widok', 'id_klient', 'id_przewoznik', 'id_oddzial', 'id_uzytkownik', 'stawka_klient', 'stawka_przewoznik', 'nr_zlecenia_klienta', 'numer_zlecenia', 'terminy');
                $Widoki['admin'] = array('widok', 'stawka_klient_brutto', 'stawka_przewoznik_brutto', 'id_faktury', 'faktura_przewoznika');
                $Widoki['operacja'] = array('widok', 'termin_zaladunku', 'termin_rozladunku', 'id_kierowca', 'miejsce_zaladunku', 'odbiorca', 'opis_ladunku', 'ilosc_km', 'typ_serwisu', 'marza');
                $Widoki['platnosci'] = array('widok', 'data_sprzedazy', 'termin_wlasny', 'rzecz_zaplata_klienta', 'opoznienie_klient', 'data_wplywu', 'termin_przewoznika', 'planowana_zaplata_przew', 'rzecz_zaplata_przew', 'opoznienie_przewoznik', 'fifo');
                $Admin = serialize($Widoki);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '1'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '2'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Admin' WHERE uprawnienia_id = '5'");
                $TPSArray = array('all' => $Widoki['all'], 'operacja' => $Widoki['operacja']);
                unset($TPSArray['all'][9]);
                unset($TPSArray['operacja'][9]);
                $TPS = serialize($TPSArray);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$TPS' WHERE uprawnienia_id = '3'");
                $KAArray = array('all' => $Widoki['all'], 'admin' => $Widoki['admin'], 'operacja' => $Widoki['operacja']);
                unset($KAArray['operacja'][3]);
                unset($KAArray['operacja'][7]);
                unset($KAArray['operacja'][8]);
                unset($KAArray['operacja'][9]);
                $KA = serialize($KAArray);
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$KA' WHERE uprawnienia_id = '4'");
                $Pusta = serialize(array());
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET widok_kolumny = '$Pusta' WHERE uprawnienia_id = '6'");
            }
            if($_GET['dev'] == "set_raporty"){
                $RaportyArray = $this->GetRaporty();
                $Raporty = array_keys($RaportyArray);
                $Raports = serialize($Raporty);
                $Pusta = serialize(array());
                $TPS = serialize(array('trasy'));
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET raporty_dostep = '$Raports' WHERE uprawnienia_id != '6'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET raporty_dostep = '$Raports' WHERE uprawnienia_id != '6'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET raporty_dostep = '$TPS' WHERE uprawnienia_id = '3'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET raporty_dostep = '$TPS' WHERE uprawnienia_id = '3'");
                unset($RaportyArray['analiza_wynikow']);
                $Raports2 = serialize(array_keys($RaportyArray));
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET raporty_dostep = '$Raports2' WHERE uprawnienia_id > '3'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET raporty_dostep = '$Raports2' WHERE uprawnienia_id > '3'");
                $this->Baza->Query("UPDATE orderplus_uprawnienia SET raporty_dostep = '$Pusta' WHERE uprawnienia_id = '6'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET raporty_dostep = '$Pusta' WHERE uprawnienia_id = '6'");
            }
            if($_GET['dev'] == "set_moduly"){
                $Dane = $this->Baza->GetOptions("SELECT uprawnienia_id, uprawnienia FROM orderplus_uprawnienia");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',{$Dane[2]}') WHERE uprawnienia_id = '2'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',{$Dane[3]}') WHERE uprawnienia_id = '3'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',{$Dane[4]}') WHERE uprawnienia_id = '4'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',{$Dane[5]}') WHERE uprawnienia_id = '5'");
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',{$Dane[6]}') WHERE uprawnienia_id = '6'");
            }
            if($_GET['dev'] == "set_oddzialy"){
                $Dane = $this->Baza->GetOptions("SELECT id_uzytkownik, id_oddzial FROM orderplus_uzytkownik");
                foreach($Dane as $Uid => $Oid){
                    $Zapis = serialize(array($Oid));
                    $this->Baza->Query("UPDATE orderplus_uzytkownik SET oddzialy_dostep = '$Zapis' WHERE id_uzytkownik = '$Uid'");
                }
                $KKO = serialize(array(1,2,3,5));
                $this->Baza->Query("UPDATE orderplus_uzytkownik SET oddzialy_dostep = '$KKO' WHERE uprawnienia_id = '2'");
            }
            $this->Baza->EnableLog(false);
        }

        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
            if($_POST['OpcjaFormularza'] == "change-role"){
                $UprawnieniaBazowe = $this->Uzytkownik->GetUprawnieniaBazowe($Wartosci['uprawnienia_id']);
                $Wartosci['aplikacja_dostep'] = unserialize($UprawnieniaBazowe['aplikacja_dostep']);
                $Wartosci['raporty_dostep'] = unserialize($UprawnieniaBazowe['raporty_dostep']);
                $Wartosci['uprawnienia'] = explode(",", $UprawnieniaBazowe['uprawnienia']);
                $Wartosci['uprawnienia_kolumn'] = array();
                $Kolumny = unserialize($UprawnieniaBazowe['widok_kolumny']);
                foreach($Kolumny as $Group => $Columns){
                    foreach($Columns as $Kolumna){
                        if($Kolumna == "widok"){
                            $Wartosci['uprawnienia_kolumn'][] = $Group;
                        }else{
                            $Wartosci['uprawnienia_kolumn'][] = $Kolumna;
                        }
                    }
                }
                if($Wartosci['uprawnienie_id'] == 2){
                    $Wartosci['oddzialy_dostep'] = array(1,2,3,5);
                }else{
                    $Wartosci['oddzialy_dostep'] = array();
                }
            }
            return $Wartosci;
        }

        function GetRaporty(){
            $Raporty['klient'] = "klienci";
            $Raporty['przewoznik'] = "przewoźnicy";
            $Raporty['spedytor'] = "spedytorzy";
            $Raporty['klientnaspedytora'] = "klienci na spedytora";
            $Raporty['oddzial'] = "oddziały";
            $Raporty['branza'] = "wg. branży";
            $Raporty['siedziba'] = "wg. siedziby";
            $Raporty['typ_serwisu'] = "wg. typu serwisu";
            $Raporty['trasy'] = "wg. tras";
            $Raporty['analiza_wynikow'] = "analiza wyników 1";
            $Raporty['analiza_wynikow_stara'] = "analiza wyników 2";
            $Raporty['zestawienie_faktur'] = "zestawienie faktur";
            $Raporty['platnosci_dla_przewoznikow'] = "płatności dla przewoźników";
            return $Raporty;
        }

}
?>
