<script type="text/javascript" src="../../../js/jquery.zclip.js"></script>
<?php
/**
 * Moduł zlecenia
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Zlecenia extends ModulBazowy {
        public $Przewoznicy;
        public $Klienci;
        public $Kierowcy;
        public $KodyKrajow;
        public $Emaile;
        public $PunktyPrzeladunku = false;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
      
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->LinkPowrotu = "?modul=tabela_rozliczen_nowa";
            if(isset($_GET['soid'])){
                $this->LinkPowrotu = "?modul=zlecenia_morskie";
            }
            if(isset($_GET['aoid'])){
                $this->LinkPowrotu = "?modul=zlecenia_lotnicze";
            }
            if(isset($_GET['ret']) && $_GET['ret'] == "sea"){
                $this->LinkPowrotu = "?modul=zlecenia_morskie_zlec";
            }
            if(isset($_GET['ret']) && $_GET['ret'] == "air"){
                $this->LinkPowrotu = "?modul=zlecenia_lotnicze_zlec";
            }
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            $this->Tabela = 'orderplus_zlecenie';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Zlecenie';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            //$this->Kierowcy = UsefullBase::GetKierowcyFromOrders($this->Baza);
            $this->KodyKrajow = UsefullBase::GetCountryCodes($this->Baza);
            $this->Filtry[] = array("opis" => "Filtruj wg przewoźnika", "nazwa" => "id_przewoznik", "typ" => "lista", "opcje" => $this->Przewoznicy, 'domyslna' => '---- wszyscy przewoźnicy ----');
            $this->Filtry[] = array("opis" => "Filtruj wg klienta", "nazwa" => "id_klient", "typ" => "lista", "opcje" => $this->Klienci, 'domyslna' => '---- wszyscy klienci ----');
            $this->Filtry[] = array("opis" => "Filtruj wg kierowcy", "nazwa" => "kierowca_dane", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Filtruj wg. wystawienia faktur", "nazwa" => "id_faktury", "typ" => "lista", "opcje" => array('no' => '---- bez faktury ----', 'yes' => '---- z wystawioną fakturą ----'), 'domyslna' => '---- wszystkie zlecenia ----');
	}

	function &GenerujFormularz($Wartosci, $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            if($this->WykonywanaAkcja != "dodawanie" && $this->WykonywanaAkcja != "duplikacja"){
                $Formularz->DodajPole('numer_zlecenia_krotki', 'tekst', 'Numer', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            }
            $Formularz->DodajPole('id_przewoznik', 'lista_przewoznik', 'Przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPrzewoznicyWithClass($this->Baza), 'klasy' => PrzewoznicyKlasy::GetClasses($this->Baza)));
            /** wyłączenie wyboru kierowcy z listy na życzenie klienta 29.03 by MBugański **/
//            if($this->WykonywanaAkcja == "dodawanie"){
//                $Formularz->DodajPole('kierowca', 'kierowca', 'Kierowca', array('tabelka' => Usefull::GetFormStandardRow()));
//            }else{
                $Formularz->DodajPole('kierowca_dane', 'tekst_dlugi', 'Kierowca', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'height: 70px;'), 'opis_dodatkowy_przed' => 'Dane kierowcy:<br />'));
                $Formularz->DodajPole('kierowca_dane_nr_rejestracyjny', 'tekst', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'opis_dodatkowy_przed' => '<br />Nr rejestracyjny:<br />', 'atrybuty' => array('style' => 'width: 100px;')));
//            }
            $Formularz->DodajPole('os_kontaktowa', 'tekst_dlugi', 'Osoba kontaktowa', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('data_zlecenia', 'tekst_data', 'Data Zlecenia (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('id_klient', 'lista_klient', 'Zlecający', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetKlienciWithTermin($this->Baza)));
            $Formularz->DodajPole('stawka_przewoznik', 'tekst', 'Stawka dla przewoźnika', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('kod_kraju_zaladunku', 'lista', 'Kod kraju załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
            $Formularz->DodajPole('adres_zaladunku', 'tekst', 'Adres załadunku (do faktury)', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('miejsce_zaladunku', 'tekst_dlugi', 'Załadowca i miejsce załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            //$Formularz->DodajPole('zaladunki', 'punkty_przeladunku', 'Punkty załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->PunktyPrzeladunku));
            $Formularz->DodajPole('kod_kraju_rozladunku', 'lista', 'Kod kraju rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->KodyKrajow));
            $Formularz->DodajPole('adres_rozladunku', 'tekst', 'Adres rozładunku (do faktury)', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('odbiorca', 'tekst_dlugi', 'Odbiorca i miejsce rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            //$Formularz->DodajPole('rozladunki', 'punkty_przeladunku', 'Punkty rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->PunktyPrzeladunku));
            $Formularz->DodajPole('ilosc_km', 'tekst', 'Ilość km', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('termin_zaladunku', 'tekst_data', 'Termin załadunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('godzina_zaladunku', 'tekst', 'Godzina załadunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('termin_rozladunku', 'tekst_data', 'Termin rozładunku (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('godzina_rozladunku', 'tekst', 'Godzina rozładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('dokumenty', 'tekst_dlugi', 'Załączone dokumenty', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('ladunek_niebezpieczny', 'tekst_dlugi', 'Uwagi', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('typ_serwisu', 'lista', 'Typ serwisu', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetTypySerwisu($this->Baza), 'wybierz' => true));
            $Formularz->DodajPole('opis_ladunku', 'tekst_dlugi', 'Opis ładunku', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('nr_zlecenia_klienta', 'tekst', 'Numer zlecenia klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('stawka_klient', 'tekst', 'Stawka dla klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('waluta', 'lista', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'elementy' => Usefull::GetWaluty()));
            $Formularz->DodajPole('id_szablon', 'lista', 'Szablon zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetSzablony($this->Baza)));
            $Formularz->DodajPole('termin_platnosci_dni', 'tekst', 'Termin płatności', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;'), 'id' => 'terminek', 'opis_dodatkowy_za' => ' dni'));
            $Formularz->DodajPole('platnosci_status_klient', 'lista', 'Status - przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::StatusyPlatnosci()));

//            if(is_array($Wartosci)){
//                $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
//                if($this->WykonywanaAkcja == "dodawanie"){
//                    $Formularz->UstawOpcjePola('kierowca', 'przewoznik', $Values['id_przewoznik'], false);
//                    if($Values['id_przewoznik'] > 0){
//                        $Formularz->UstawOpcjePola('kierowca', 'elementy', UsefullBase::GetDriversByPrzewoznik($this->Baza, $Values['id_przewoznik']), false);
//                    }
//                }
//            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function &GenerujFormularzWyslij() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('email', 'wyslij_raport', null, array('tabelka' => Usefull::GetFormWithoutTHRow(), 'elementy' => $this->Emaile));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
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
                                    }else{
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function DomyslnyWarunek(){
            return "data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31'".($this->Uzytkownik->CheckNoOddzial() ? " AND id_oddzial = '{$_SESSION['id_oddzial']}'" : "");
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"numer_zlecenia" => 'Numer zlecenia',
                        "data_zlecenia" => 'Data Zlecenia',
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");
		return $Wynik;
	}

        function GetEmailePrzewoznika($ID){
            return $this->Baza->GetValue("SELECT emaile FROM orderplus_przewoznik WHERE id_przewoznik = '$ID'");
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $this->Emaile = $this->GetEmailePrzewoznika($Dane['id_przewoznik']);
            if(!$this->Emaile){
                $this->Emaile = array();
            }else{
                $this->Emaile = explode(",", $this->Emaile);
            }
            if($this->WykonywanaAkcja == "duplikacja"){
                $Dane['data_zlecenia'] = $this->Dzis;
                $Dane['termin_platnosci_dni'] = $this->Baza->GetValue("SELECT termin_platnosci_dni FROM orderplus_klient k WHERE k.id_klient = '{$Dane['id_klient']}'");
            }
            return $Dane;
        }

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_href" => "podglad.php?");
                $Akcje[] = array('img' => "copy_button", 'title' => "Duplikuj", "akcja" => "duplikacja");
                $Akcje[] = array('img' => "printer_button", 'title' => "Karta postoju", "akcja_href" => "karta_postoju.php?");
                $Akcje[] = array('img' => "mail_button", 'title' => "E-mail", "akcja" => "email");
                $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+3 days"));
                if ((($Dane['korekta'] == 0 && $Dane['ost_korekta'] == 0) || ($Dane['korekta'] > 0 && $Dane['ost_korekta'] == 1)) &&
   			($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji)){
                            $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                        }else{
                            $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
                        }
                if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                    $Akcje[] = array('img' => "cancel_button", 'title' => "Anulowanie", "akcja" => "anulowanie");
                    $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
                }else{
                    $Akcje[] = array('img' => "cancel_button_grey", 'title' => "Anulowanie");
                    $Akcje[] = array('img' => "delete_button_grey", 'title' => "Kasowanie");
                }
		return $Akcje;
	}

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case "anulowanie":
                    $this->AkcjaAnulowanie($ID);
                    break;
                case "email":
                    $this->AkcjaWyslij($ID);
                    break;
                case "popraw":
                    $this->AkcjaEdycja($ID);
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
            return $this->Baza->Query("UPDATE $this->Tabela SET korekta = '3' WHERE $this->PoleID = '$ID'");
        }

        function MozeBycOperacja($ID){
            $Result = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            $TerminEdycji = date("Y-m-d", strtotime($Result['termin_rozladunku']."+3 days"));
            if($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                return true;
            }
            return false;
        }

        function MozeBycOperacjaEdycja($ID){
                $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+3 days"));
                $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                if ((($Dane['korekta'] == 0 && $Dane['ost_korekta'] == 0) || ($Dane['korekta'] > 0 && $Dane['ost_korekta'] == 1)) &&
                        ($this->Uzytkownik->IsAdmin() || $_SESSION["uprawnienia_id"] == 2 || $this->Dzis < $TerminEdycji)){
                    return true;
                }
                return false;
        }

        function PobierzDaneDomyslne(){
              if(isset($_GET['idzk']) && is_numeric($_GET['idzk'])){
                  $this->Baza->Query("SELECT * FROM orderplus_zlecenie_klient WHERE id_zlecenie = '{$_GET['idzk']}'");
                  $Dane = $this->Baza->GetRow();
              }else{
                $Dane['id_przewoznik'] = (isset($_GET['pid']) ? $_GET['pid'] : 0);
                $Dane['data_zlecenia'] = date('Y-m-d');
                $Dane['stawka_przewoznik'] = '0.00';
                $Dane['stawka_klient'] = '0.00';
                $Dane['waluta'] = 'PLN';
                $Dane['id_szablon'] = '1';
                $Dane['termin_platnosci_dni'] = '14';
                $Dane['typ_serwisu'] = 0;
                $Dane['kod_kraju_rozladunku'] = 34;
                $Dane['kod_kraju_zaladunku'] = 34;
                $Dane['ilosc_km'] = 0;
              }
            return $Dane;
        }

        function ShowRecord($Element, $Nazwa, $Styl){
           if($Nazwa == "numer_zlecenia"){
                if ($Element['korekta'] <> 0) {
                    if ($Element['korekta'] == 3) {
                        print ("<td style='padding-left:5px; color:#d1d3d4;'><strike>".stripslashes($Element[$Nazwa])."</strike></td>");
                    }else{
                        print ("<td style=\"padding-left:5px;color:#0000FF;\">".stripslashes($Element[$Nazwa])."</td>");
                    }
                }
                else {
                    echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
                }
            }else{
                echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
            }
        }

        function AkcjaDrukuj($ID, $Akcja){
            if($this->SprawdzUprawnienie("zlecenia") || isset($_GET['hash'])){
                if($Akcja == "karta_postoju"){
                    $KieroID = $this->Baza->GetValue("SELECT id_kierowca FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                    $Kierowca = $this->Baza->GetData("SELECT * FROM orderplus_kierowca WHERE id_kierowca='$KieroID'");
                    include(SCIEZKA_SZABLONOW."druki/karta_postoju.tpl.php");
                    echo "<br />";
                    include(SCIEZKA_SZABLONOW."druki/karta_postoju.tpl.php");
                }else if($Akcja == "podglad" || $Akcja == "podglad_przewoznik"){
                    if($Akcja == "podglad"){
                        $zlecenie = $this->PobierzDaneElementu($ID);
                    }else{
                        $zlecenie = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE hash = '{$_GET['hash']}'");
                    }
                    $kierowca = $this->Baza->GetData("SELECT * FROM orderplus_kierowca WHERE id_kierowca='{$zlecenie['id_kierowca']}'");
                    $przewoznik = $this->Baza->GetData("SELECT * FROM orderplus_przewoznik WHERE id_przewoznik = '{$zlecenie['id_przewoznik']}'");
                    $uzytkownik = $this->Baza->GetData("SELECT imie, nazwisko, id_oddzial FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$zlecenie['id_uzytkownik']}'");
                    $warunki_szablon = $this->Baza->GetData("SELECT * FROM orderplus_szablon WHERE id_szablon='{$zlecenie['id_szablon']}'");
                    if($zlecenie['id_szablon'] == '2'){
                       include(SCIEZKA_SZABLONOW."zlecenia_lang/eng.php");
                    }
                    else {
                       include(SCIEZKA_SZABLONOW."zlecenia_lang/pl.php");
                    }
                    include(SCIEZKA_SZABLONOW."druki/podglad.tpl.php");
                    if($Akcja == "podglad_przewoznik"){
                        if ($zlecenie['data_podgladu'] == '') {
                            $tresc_maila = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
                            $tresc_maila .= "<html><head>\r\n";
                            $tresc_maila .= "<meta http-equiv=\"content-type\" content=\"text/html;charset=ISO-8859-2\">\r\n";
                            $tresc_maila .= "<title>Potwierdzenie</title>\r\n";
                            $tresc_maila .= "\r\n</head>";

                            $tresc_maila .= "<body bgcolor=\"#ffffff\">\r\n";
                            $tresc_maila .= "<p>Odczytano</p>";
                            $tresc_maila .= "<p>Numer zlecenia: {$zlecenie['numer_zlecenia']}<br>";
                            $tresc_maila .= "<p>Przewoźnik: {$przewoznik['nazwa']}<br>";
                            $tresc_maila .= "<p>Data: ". date ("Y-m-d") . "</p>";

                            $tresc_maila .= "</body></html>\r\n";

                            $adres_email = "office@critical-cs.com";
                            $mail = new MailSMTP();
                            if ($mail->SendEmail("$adres_email", "Potwierdzenie odczytu zlecenia: {$zlecenie['numer_zlecenia']}", "$tresc_maila")){
                                mysql_query("UPDATE orderplus_zlecenie SET data_podgladu = now() WHERE id_zlecenie = '{$zlecenie['id_zlecenie']}'");
                            }
                        }
                    }
                }
            }
        }

        function ShowNaglowekDrukuj($Akcja){
            if($Akcja == "karta_postoju"){
                include(SCIEZKA_SZABLONOW.'naglowek_drukuj_karta_postoju.tpl.php');
            }else{
                include(SCIEZKA_SZABLONOW.'naglowek_drukuj_zlecenie.tpl.php');
            }
        }

        function AkcjaWyslij($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularzWyslij($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
                                echo "<div style='clear: both;'></div>\n";
                                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
				if($this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
					if ($this->WyslijZlecenie($Formularz, $Wartosci, $ID)) {
						Usefull::ShowKomunikatOK('<b>E-mail został wysłany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>');
						return;
					}
					else {
						Usefull::ShowKomunikatError('<b>Błąd. E-mail nie został wysłany.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}
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
			$Formularz = $this->GenerujFormularzWyslij($Dane);
                        $this->ShowTitleDiv($ID, $Dane);
			$Formularz->Wyswietl($Dane, false);
		}
        }

        function WyslijZlecenie($Formularz, $Wartosci, $ID){
            $Hash = $this->Baza->GetValue("SELECT hash FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            $Mail = new MailSMTP($this->Baza);
            $tresc_maila = $this->GetEmailTresc($Hash);
            $SendEmails = "";
            $NoError = true;
            $Mail->SetEmail("Critical CS Order System <office@critical-cs.com>");
            foreach($Wartosci['email']['lista'] as $Email){
                if (!$Mail->SendEmail($Email, $this->GetEmailTitle(), $tresc_maila)){
                        $NoError = false;
                }
            }
            if($Wartosci['email']['email_dodatkowy'] != ""){
                    $Email = $Wartosci['email']['email_dodatkowy'];
                    if (!$Mail->SendEmail($Email, $this->GetEmailTitle(), $tresc_maila)){
                        $NoError = false;
                    }
            }
            if(!$NoError){
                return false;
            }
            return true;
        }

        function GetEmailTitle(){
            return "Wydruk zlecenia";
        }

        function GetEmailTresc($Hash){
           $tresc_maila = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
            $tresc_maila .= "<html><head>\r\n";
            $tresc_maila .= "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">\r\n";
            $tresc_maila .= "<title>Wydruk zlecenia</title>\r\n";
            $tresc_maila .= "\r\n</head>";
            $tresc_maila .= "<body bgcolor=\"#ffffff\">\r\n";
            $tresc_maila .= "<p>Szanowni Państwo</p>";
            $tresc_maila .= "<p>Po kliknięciu w poniższy link możliwe będzie wydrukowanie zlecenia:<br><b><a href=\"http://orderplus.critical-cs.com/zlecenia/podglad.php?hash=$Hash\">Drukuj zlecenie</a></b></p>";
            $tresc_maila .= "<p>Critical Cargo and Freight Services Sp. z o.o.<br />al. Solidarności 115/2
00-140 Warszawa, Poland<br />NIP: PL 525-258-15-65</p>";
            $tresc_maila .= "<p></p>";
            $tresc_maila .= "</body></html>\r\n";
            return $tresc_maila;
        }
       

        function SprawdzDane($Wartosci, $ID){
            if($Wartosci['id_przewoznik'] == 0){
                $this->Error = "Nie wybrano przewoźnika!";
                return false;
            }
            if(!Usefull::CheckDate($Wartosci['data_zlecenia'])){
                $this->Error = "Błędna data zlecenia";
                return false;
            }
            if(!Usefull::CheckDate($Wartosci['termin_zaladunku'])){
                $this->Error = "Błędna data załadunku";
                return false;
            }
            if(!Usefull::CheckDate($Wartosci['termin_rozladunku'])){
                $this->Error = "Błędna data rozładunku";
                return false;
            }
            $DataWstecz = date("Y-m-d", "-1 weeks");
            if($this->WykonywanaAkcja == "dodawanie"){
                if($Wartosci['termin_rozladunku'] < $DataWstecz || $Wartosci['termin_zaladunku'] < $DataWstecz){
                    $this->Error = "Nie możesz wystawić zlecenia z rozładunkiem lub załadunkiem powyżej tygodnia wstecz.";
                    return false;
                }
            }
            if($Wartosci['kierowca']['wybor'] == "nowy" && $Wartosci['kierowca']['nowy']['imie_nazwisko'] == ""){
                $this->Error = "Wprowadź dane nowego kierowcy!";
                return false;
            }
            return true;
        }

        function AkcjaEdycja($ID){
            if($this->MozeBycOperacjaEdycja($ID)){
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
                if($this->WykonywanaAkcja == "dodawanie" || $this->WykonywanaAkcja == "duplikacja"){
                    $Wartosci['id_uzytkownik'] = $_SESSION['id_uzytkownik'];
                    $Wartosci['oddzial'] = $_SESSION['oddzial'];
                    $Wartosci['id_oddzial'] = $_SESSION['id_oddzial'];
                }
                if(isset($Wartosci['kierowca'])){
                    if($Wartosci['kierowca']['wybor'] == "lista"){
                        $Wartosci['id_kierowca'] = $Wartosci['kierowca']['kierowca'];
                    }else{
                        $Wartosci['kierowca']['nowy']['id_przewoznik'] = $Wartosci['id_przewoznik'];
                        $KierowcaZap = $this->Baza->PrepareInsert("orderplus_kierowca", $Wartosci['kierowca']['nowy']);
                        $this->Baza->Query($KierowcaZap);
                        $Wartosci['id_kierowca'] = $this->Baza->GetLastInsertId();
                    }
                }
                unset($Wartosci['kierowca']);
                $Wartosci['stawka_przewoznik'] = number_format(floatval(strtr($Wartosci['stawka_przewoznik'], ',', '.')), 2, '.', '');
                $Wartosci['stawka_klient'] = number_format(floatval(strtr($Wartosci['stawka_klient'], ',', '.')), 2, '.', '');
                $Wartosci['firma_wystaw'] = 2;
                if(!isset($Wartosci['kierowca_dane'])){
                    $Wartosci['kierowca_dane'] = $this->Baza->GetValue("SELECT CONCAT(imie_nazwisko,'\r\n',rejestracja,'\r\n',dane_kierowcy) as kierowca FROM orderplus_kierowca WHERE id_kierowca = '{$Wartosci['id_kierowca']}'");
                    $Wartosci['kierowca_dane_nr_rejestracyjny'] = $this->Baza->GetValue("SELECT rejestracja FROM orderplus_kierowca WHERE id_kierowca = '{$Wartosci['id_kierowca']}'");
                }
                if($Wartosci['termin_zaladunku'] < '2012-01-01'){
                    $Wartosci = $this->OldNumber($Wartosci, $ID);
                }else{
                    $Wartosci = $this->NewNumber($Wartosci, $ID);
                }
                $Wartosci['kurs'] = UsefullBase::PobierzKursZDnia($this->Baza, $Wartosci['termin_zaladunku'], $Wartosci['waluta'], $Wartosci['id_klient']);
                $Wartosci['kurs_przewoznik'] = UsefullBase::PobierzKursZDnia($this->Baza, $Wartosci['termin_zaladunku'], $Wartosci['waluta']);
                $Wartosci['waluta_faktura_przewoznik'] = "PLN";
                if($this->Parametr == "tabela_rozliczen_nowa"){
                    $Wartosci['kolor_zlecenia'] = (isset($Wartosci['kolor_zlecenia']) ? 1 : 0);
                }
		if ($ID) {
                    $this->ID = $ID;
                    if($this->WykonywanaAkcja == "edycja"){
                          $DaneDefalut = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                           $Wartosci['hash'] = md5(date('r').$Wartosci['id_przewoznik']);
                           $Wartosci['ost_korekta'] = 1;
                           $Wartosci['korekta'] = $ID;
                           ### Kopiujemy dane ze zlecenia, te które się nie zmieniają
                           $NoDuplicate = array('id_zlecenie');
                           foreach($DaneDefalut as $key => $value){
                               if(!key_exists($key, $Wartosci) && !in_array($key, $NoDuplicate)){
                                    $Wartosci[$key] = $value;
                               }
                           }
                           $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Wartosci, $_SESSION['id_uzytkownik']);                           
                    }else{
                        $Zapytanie = $this->Baza->PrepareUpdate($this->Tabela, $Wartosci, array($this->PoleID => $ID));
                    }
		}
		else {
                    $Wartosci['sea_order_id'] = (isset($_GET['soid']) ? $_GET['soid'] : 0);
                    $Wartosci['air_order_id'] = (isset($_GET['aoid']) ? $_GET['aoid'] : 0);
                    $Wartosci['data_wprowadzenia'] = date("Y-m-d");
                    $Wartosci['hash'] = md5(date('r').$Wartosci['id_przewoznik']);
                    $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Wartosci);
		}
		if ($this->Baza->Query($Zapytanie)) {
                    if($this->WykonywanaAkcja == "dodawanie" || $this->WykonywanaAkcja == "duplikacja"){
                        $LastID = $this->Baza->GetLastInsertId();
                        $this->ID = $LastID;
                        $this->Baza->Query("UPDATE orderplus_klient SET klient_status = '1' WHERE id_klient = '{$Wartosci['id_klient']}'");
                        if(isset($_GET['idzk'])){
                            $this->Baza->Query("UPDATE orderplus_zlecenie_klient SET zlecenie_status = '2', real_id = '$LastID' WHERE id_zlecenie = '{$_GET['idzk']}'");
                        }
                        $Zdarzenia = new Zdarzenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                        $Zdarzenia->DodajZdarzenieDoCRM($Wartosci['termin_zaladunku'], $Wartosci['id_klient'], $_SESSION['id_uzytkownik']);
                    }
                    if($this->WykonywanaAkcja == "edycja"){
                        $LastID = $this->Baza->GetLastInsertId();
                        $this->ID = $LastID;
                        $Update['ost_korekta'] = (($DaneDefalut['korekta'] == 0) && ($DaneDefalut['ost_korekta'] == 0) ? '2' : '0');
                        $ZapUpd = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $ID));
                        $this->Baza->Query($ZapUpd);
                    }
                    return true;
		}
		else {
                    return false;
		}
	}

        function OldNumber($Wartosci, $ID){
            if(isset($Wartosci['numer_zlecenia_krotki'])){
                if($this->Baza->GetValue("SELECT count(*) FROM $this->Tabela WHERE numer_zlecenia_krotki = '{$Wartosci['numer_zlecenia_krotki']}' AND termin_zaladunku < '2012-01-01'") > 0){
                    $Wartosci['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT numer_zlecenia_krotki FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                }
                $NrZlecenia = $this->Baza->GetValue("SELECT numer_zlecenia FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                $dane_numeru_zlecenia = explode('/', $NrZlecenia);
                $AktualnyMiesiac = $dane_numeru_zlecenia[2];
                $AktualnyRok = $dane_numeru_zlecenia[3];
            }else{
                $AktualnyMiesiac = date("m");
                $AktualnyRok = date("Y");
                $Wartosci['numer_zlecenia_krotki'] = intval($this->Baza->GetValue("SELECT max(numer_zlecenia_krotki) FROM $this->Tabela WHERE termin_zaladunku < '2012-01-01'")) + 1;
            }
            $identyfikator_przewoznika = $this->Baza->GetValue("SELECT identyfikator FROM orderplus_przewoznik WHERE id_przewoznik = '{$Wartosci['id_przewoznik']}'");
            $Wartosci['numer_zlecenia'] = "{$Wartosci['numer_zlecenia_krotki']}/$identyfikator_przewoznika/$AktualnyMiesiac/$AktualnyRok";
            return $Wartosci;
        }

        function NewNumber($Wartosci, $ID){
            if(isset($Wartosci['numer_zlecenia_krotki'])){
                $id_oddzial = $this->Baza->GetValue("SELECT id_oddzial FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                if($this->Baza->GetValue("SELECT count(*) FROM $this->Tabela WHERE numer_zlecenia_krotki = '{$Wartosci['numer_zlecenia_krotki']}' AND termin_zaladunku >= '2012-01-01' AND id_oddzial = '$id_oddzial'") > 0){
                    $Wartosci['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT numer_zlecenia_krotki FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                }
                $OddzialDane = $this->Baza->GetData("SELECT * FROM orderplus_oddzial WHERE id_oddzial = '$id_oddzial'");
                $First = $OddzialDane['kod_pocztowy'][0];
                $Prefix = $OddzialDane['prefix'];
                $NrZlecenia = $this->Baza->GetValue("SELECT numer_zlecenia FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                $dane_numeru_zlecenia = explode('/', $NrZlecenia);
                $AktualnyMiesiac = $dane_numeru_zlecenia[1];
                $AktualnyRok = $dane_numeru_zlecenia[2];
                /*$Rodzaj = $dane_numeru_zlecenia[3];*/
                $Login = $dane_numeru_zlecenia[3];
            }else{
                $OddzialDane = $this->Baza->GetData("SELECT * FROM orderplus_oddzial WHERE id_oddzial = '{$_SESSION['id_oddzial']}'");
                $First = $OddzialDane['kod_pocztowy'][0];
                $Prefix = $OddzialDane['prefix'];
                $Rodzaj = (isset($_GET['soid']) > 0 ? "OV" : "RD");
                $AktualnyMiesiac = date("m");
                $AktualnyRok = date("Y");
                ### w lutym 2013 zmiana aby numeracja była ciągła a nie osobna dla każdego m-ca ###
                if($AktualnyRok."-".$AktualnyMiesiac."-01" >= "2013-02-01"){
                    ### Wprowadzenie obejścia do numeracji oddziału INT Wrocław (z powodu błędnej daty wymieszała się numeracja z poprzednią numeracją)
                    if($_SESSION['id_oddzial'] == 1){
                        $Wartosci['numer_zlecenia_krotki'] = intval($this->Baza->GetValue("SELECT max(numer_zlecenia_krotki) FROM $this->Tabela WHERE termin_zaladunku >= '2012-01-01' AND data_zlecenia >= '2013-02-01' AND id_oddzial = '{$_SESSION['id_oddzial']}' AND numer_zlecenia LIKE '%/$AktualnyRok/%' AND numer_zlecenia_krotki < 10000")) + 1;
                    }else{
                        $Wartosci['numer_zlecenia_krotki'] = intval($this->Baza->GetValue("SELECT max(numer_zlecenia_krotki) FROM $this->Tabela WHERE termin_zaladunku >= '2012-01-01' AND data_zlecenia >= '2013-02-01' AND id_oddzial = '{$_SESSION['id_oddzial']}' AND numer_zlecenia LIKE '%/$AktualnyRok/%'")) + 1;
                    }
                }else{
                    $Wartosci['numer_zlecenia_krotki'] = intval($this->Baza->GetValue("SELECT max(numer_zlecenia_krotki) FROM $this->Tabela WHERE termin_zaladunku >= '2012-01-01' AND id_oddzial = '{$_SESSION['id_oddzial']}' AND numer_zlecenia LIKE '%/$AktualnyMiesiac/$AktualnyRok/%'")) + 1;
                }
                
                $Login = $_SESSION['login'];
            }
            $identyfikator_przewoznika = $this->Baza->GetValue("SELECT identyfikator FROM orderplus_przewoznik WHERE id_przewoznik = '{$Wartosci['id_przewoznik']}'");
			if($Wartosci['numer_zlecenia_krotki'] <= 9){
				$Wartosci['numer_zlecenia_krotki'] = sprintf('%02d',$Wartosci['numer_zlecenia_krotki']);
            }
            $Wartosci['numer_zlecenia'] = "1{$Wartosci['numer_zlecenia_krotki']}/$AktualnyMiesiac/$AktualnyRok/$Login";
            return $Wartosci;
        }

        function ShowOK(){
            $Tresc = '<b>Rekord został zapisany</b><br/><br/>';
            $Tresc .= "<a href=\"#\" onclick=\"window.open('podglad.php?id=$this->ID');\"><img src=\"images/podglad.gif\" border=\"0\"></a>";
            $Tresc .= "&nbsp;&nbsp;<a href=\"?modul=$this->Parametr&akcja=popraw&id=$this->ID\"><img src=\"images/popraw.gif\" border=\"0\"></a>";
            $Tresc .= "&nbsp;&nbsp;<a href=\"?modul=zlecenia&akcja=email&id=$this->ID\"><img src=\"images/email.gif\" border=\"0\"></a>";
            $Tresc .= "<br><br><br><a href=\"$this->LinkPowrotu\"><img src=\"images/ok.gif\" border=\"0\"></a>";
            Usefull::ShowKomunikatOK($Tresc);
        }

        function WyswietlAJAX($Akcja){
            $Hash = $this->Baza->GetValue("SELECT hash FROM $this->Tabela WHERE $this->PoleID = '{$_POST['id']}'");
            if($Akcja == "get-edytowali"){
                include(SCIEZKA_SZABLONOW."close-ajax.tpl.php");
                $EdytowaliQ = $this->Baza->GetValue("SELECT edytowali FROM $this->Tabela WHERE $this->PoleID = '{$_POST['id']}'");
                if($EdytowaliQ != ""){
                    echo "edytował:<br />";
                    echo str_replace("#", "<br />", $EdytowaliQ);
                }
            }
            if($Akcja == "get-action-list"){
                
                $Dane = $this->PobierzDaneElementu($_POST['id']);
                $Akcje = array();
                $Akcje[] = array('title' => "Podgląd", "akcja_href" => "podglad.php?", "_blank" => true);
                $Akcje[] = array('title' => "Duplikuj", "akcja_href" => "?modul=zlecenia&akcja=duplikacja&");
                $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+3 days"));
                if ($this->Uzytkownik->IsAdmin() || $_SESSION["uprawnienia_id"] == 2 || $this->Dzis < $TerminEdycji){
                    $Akcje[] = array('title' => "Korekta", "akcja_href" => "?modul=zlecenia&akcja=edycja&");
                }
                if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                    $Akcje[] = array('title' => "Edytuj", "akcja_href" => "?modul=tabela_rozliczen_nowa&akcja=edycja&");
                }
                
                if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                    $Akcje[] = array('title' => "Anuluj", "akcja_href" => "?modul=zlecenia&akcja=anulowanie&");
                    $Akcje[] = array('title' => "Usuń", "akcja_href" => "?modul=zlecenia&akcja=kasowanie&");
                }
                $Akcje[] = array('title' => "Wyślij przewoźnikowi", "akcja_href" => "?modul=zlecenia&akcja=email&");
                $Akcje[] = array('title' => "Wyślij raport", "akcja_href" => "?modul=klienci_raporty&akcja=dodawanie&");
                $Akcje[] = array('title' => "Wyślij potwierdzenie", "akcja_href" => "?modul=klienci_potwierdzenia&akcja=dodawanie&");
                $Akcje[] = array('title' => "", "akcja_href" => "#");


                echo "<div style=' position:absolute; bottom:8px; left:10px;'>
                <a href=\"#\" id=\"button\" style='z-index:10000'>Kopiuj do schowka</a></div>";
                echo "<div id=\"copy\" style='display: none;'>http://orderplus.critical-cs.com/zlecenia/podglad.php?hash=$Hash</p></div>";
                echo " 
                    <script type=\"text/javascript\">
                    $(document).ready(function(){
                    $('#button').zclip({
                    path:'../../../js/ZeroClipboard.swf',
                    copy:$('#copy').text()
                    });

                    });


                    </script>";
                
                
                $this->ShowActionInPopup($Akcje, $_POST['id']);
            }
        }
}
?>
