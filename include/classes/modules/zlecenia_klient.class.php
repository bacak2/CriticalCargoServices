<?php
/**
 * Moduł zlecenia od klientów
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class ZleceniaKlient extends ModulBazowy {
        public $Klienci;
        public $KodyKrajow;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_zlecenie_klient';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlec_klienta';
            $this->Nazwa = 'Zlecenie';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->KodyKrajow = UsefullBase::GetCountryCodes($this->Baza);
            $this->Filtry[] = array("opis" => "Filtruj wg klienta", "nazwa" => "id_klient", "typ" => "lista", "opcje" => $this->Klienci, 'domyslna' => '---- wszyscy klienci ----');
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('data_zlecenia', 'tekst_data', 'Data Zlecenia (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('kod_kraju_zaladunku', 'lista', 'Kod kraju załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
            $Formularz->DodajPole('miejsce_zaladunku', 'tekst_dlugi', 'Załadowca i miejsce załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('kod_kraju_rozladunku', 'lista', 'Kod kraju rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
            $Formularz->DodajPole('odbiorca', 'tekst_dlugi', 'Odbiorca i miejsce rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('termin_zaladunku', 'tekst_data', 'Termin załadunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('typ_godz_zaladunku', 'lista', 'Godzina załadunku', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 50px;'), 'opis_dodatkowy_za' => ":<br />", 'elementy' => Usefull::GetTypyGodzin()));
            $Formularz->DodajPole('godzina_zaladunku', 'tekst', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('termin_rozladunku', 'tekst_data', 'Termin rozładunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('typ_godz_rozladunku', 'lista', 'Godzina rozładunku', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 50px;'), 'opis_dodatkowy_za' => ":<br />", 'elementy' => Usefull::GetTypyGodzin()));
            $Formularz->DodajPole('godzina_rozladunku', 'tekst', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('opis_ladunku', 'tekst_dlugi', 'Opis ładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('stawka_klient', 'tekst', 'Stawka dla klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('waluta', 'lista', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'elementy' => Usefull::GetWaluty()));
            $Formularz->DodajPole('dodatki', 'zlecenie_klient_dodatki', '', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function DomyslnyWarunek(){
            return ($this->Uzytkownik->CheckNoOddzial() ? "id_oddzial = '{$_SESSION['id_oddzial']}'" : "");
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"numer_zlec_klienta" => 'Numer zlecenia',
                        "data_zlecenia" => 'Data Zlecenia',
                        "id_klient" => array('naglowek' => 'Klient', 'elementy' => $this->Klienci),
                        "zlecenie_status" => array('naglowek' => "Status", 'elementy' => array(0 => "anulowane", 1 => "oczekujące na akceptacje", 2 => "zaakceptowane"))
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja" => "szczegoly");
                $Akcje[] = array('img' => "add_button", 'title' => "Wystaw zlecenie", "akcja_link" => "?modul=zlecenia&akcja=dodawanie&idzk={$Dane[$this->PoleID]}");
                if(in_array($_SESSION["uprawnienia_id"], array(1,2,4))){
                    $Akcje[] = array('img' => "cancel_button", 'title' => "Anulowanie", "akcja" => "anulowanie");
                }else{
                    $Akcje[] = array('img' => "cancel_button_grey", 'title' => "Anulowanie");
                }
		return $Akcje;
	}

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case "anulowanie":
                    $this->AkcjaAnulowanie($ID);
                    break;
            }
	}

        function AkcjaAnulowanie($ID) {
            $this->PobierzNazweElementu($ID);
            if($this->MozeBycOperacja($ID)){
		if (!isset($_GET['del']) || $_GET['del'] != 'ok') {
			Usefull::ShowKomunikatOstrzezenie("<b>Czy na pewno chcesz anulować <span style='color: #006c67;'>$this->NazwaElementu</span> ?</b><br/><br/><br/><a href=\"{$_SERVER['REQUEST_URI']}&del=ok\"><img src=\"images/ok.gif\" style='display: inline; vertical-align: middle;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"$this->LinkPowrotu\"><img src=\"images/anuluj.gif\" style='display: inline; vertical-align: middle;'></a><br /><br />");
		}
		else {
                    if ($this->Anuluj($ID)) {
                            Usefull::ShowKomunikatOK("<b>Zlecenie anulowane.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
                    }
                    else {
                            Usefull::ShowKomunikatError("<b>Wystąpił problem. Zlecenie nie zostało anulowane.</b><br/><br/>".$this->Baza->GetLastErrorDescription()."<br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
                    }
		}
            }else{
                Usefull::ShowKomunikatError("<b>Nie możesz anulować tego zlecenia.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
            }
	}

        function Anuluj($ID){
            return $this->Baza->Query("UPDATE $this->Tabela SET zlecenie_status = '0' WHERE $this->PoleID = '$ID'");
        }

        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            if($Typ == "zlecenie_klient_dodatki"){
                $Dane[$Pole]['dodatkowe_ubezpieczenie'] = $Dane['dodatkowe_ubezpieczenie'];
                $Dane[$Pole]['dodatkowe_raporty'] = $Dane['dodatkowe_raporty'];
                $Dane[$Pole]['dodatkowe_raporty_godziny'] = $Dane['dodatkowe_raporty_godziny'];
            }
            return $Dane;
        }
}
?>
