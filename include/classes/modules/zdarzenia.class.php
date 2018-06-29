<?php
/**
 * Moduł zdarzenia
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Zdarzenia extends ModulBazowy {
        public $Klienci;
        public $Userzy;
        public $UserzyDodaj;
        public $KlientName;
        public $KlientID;
        public $Zapis = false;
        public $Zdarzenie = false;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'zdarzenia';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'temat';
            $this->Nazwa = 'Zdarzenia';
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->Userzy = UsefullBase::GetUsersLogin($this->Baza);
            $this->CzySaOpcjeWarunkowe = true;
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                $this->Zdarzenie = $this->PobierzDaneElementu($_GET['id']);
                $this->ID = $_GET['id'];
            }
            if(isset($_GET['event']) && is_numeric($_GET['event'])){
                $this->Zdarzenie = $this->PobierzDaneElementu($_GET['event']);
                $this->ID = $_GET['event'];
            }
            if(isset($_GET['cid']) && is_numeric($_GET['cid'])){
                $this->KlientID = $_GET['cid'];
                $this->KlientName = $this->Klienci[$_GET['cid']];
            }
            if($_GET['akcja'] == "lista_zdarzen"){
                $this->Filtry[] = array('opis' => "Priorytet", "nazwa" => "Priorytety_id", "opcje" => UsefullBase::GetPriorytety($this->Baza), "typ" => "lista");
                $this->Filtry[] = array('opis' => "Opiekun", "nazwa" => "pz.id_uzytkownik", "opcje" => $this->Userzy, "typ" => "lista");
                $this->Filtry[] = array();
                $this->Filtry[] = array('opis' => 'Od dnia', "nazwa" => "data_zdarzenia_od", "typ" => "data");
                $this->Filtry[] = array('opis' => 'Do dnia', "nazwa" => "data_zdarzenia_do", "typ" => "data");
                $this->Filtry[] = array();
            }
	}

        function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('id_klient', 'hidden_lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Klienci));
            $Formularz->DodajPole('specjalne', 'lista', 'Zadanie dodatkowe', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array('tak' => 'tak', 'nie' => 'nie')));
            if($this->WykonywanaAkcja == "dodawanie"){
                $Formularz->DodajPole('id_uzytkownik', 'lista', 'Opiekun', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->UserzyDodaj, 'wybierz' => true, 'domyslna' => ' -- wybierz opiekuna -- '));
            }else if($this->WykonywanaAkcja == "edycja" || $this->WykonywanaAkcja == "zamknij"){
                $Formularz->DodajPole('id_uzytkownik', 'hidden_lista', 'Opiekun', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'wybierz' => true, 'domyslna' => ' -- wybierz opiekuna -- '));
            }else{
                $Formularz->DodajPole('id_uzytkownik', 'lista', 'Opiekun', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'wybierz' => true, 'domyslna' => ' -- wybierz opiekuna -- '));
            }
            if($this->WykonywanaAkcja == "zamknij"){
                $Formularz->DodajPole('temat', 'tekstowo', 'Temat', array('tabelka' => Usefull::GetFormStandardRow()));
            }else{
                $Formularz->DodajPole('temat', 'tekst', 'Temat', array('tabelka' => Usefull::GetFormStandardRow()));
            }
            if($this->WykonywanaAkcja == "dodawanie"){ 
                $Formularz->DodajPole('data_poczatek', 'tekst_data', 'Data przypomnienia', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
                $Formularz->DodajPole('przypomnienie', 'tekst_data_przypomnienia', null, array('tabelka' => array('td_end' => 1, 'tr_end' => 1)));
                if(isset($_GET['event'])){
                    $Formularz->DodajPole('komentarz', 'tekst_dlugi', 'Komentarz<br />[poprzednie zadanie]', array('tabelka' => Usefull::GetFormStandardRow())); 
                    $Formularz->DodajPole('Statystyka_id', 'lista', 'Statystyka<br />[poprzednie zadanie]', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetStatystyki($this->Baza), 'wybierz' => true, 'domyslna' => '-- statystyka --'));
                    $Formularz->DodajPole('potencjal_id', 'lista', 'Potencjał klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPotencjaly($this->Baza), 'wybierz' => true, 'domyslna' => '-- potencjał --'));
                    $Formularz->DodajPole('Priorytet_id', 'lista', 'Priorytet', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPriorytety($this->Baza), 'wybierz' => true));
                }else{
                    $Formularz->DodajPole('Priorytet_id', 'lista', 'Priorytet', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPriorytety($this->Baza), 'wybierz' => true));
                }
                $Formularz->DodajPole('poinformuj', 'tekst_dlugi', 'Poinformuj', array('tabelka' => Usefull::GetFormStandardRow()));
                $Formularz->DodajPole('wyslij_do', 'podzbiór_checkbox_1n', 'Wyślij do', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'krotkie' => true));
            }else if($this->WykonywanaAkcja == "edycja"){
                $Formularz->DodajPole('data_poczatek', 'tekstowo', 'Data przypomnienia', array('tabelka' => Usefull::GetFormStandardRow()));
                $Formularz->DodajPole('data_przypomnienia', 'tekst_data', 'Przenieś na', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
                $Formularz->DodajPole('przypomnienie', 'tekst_data_przypomnienia', null, array('tabelka' => array('td_end' => 1, 'tr_end' => 1)));
                $Formularz->DodajPole('Priorytet_id', 'lista', 'Priorytet', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPriorytety($this->Baza), 'wybierz' => true));
            }else if($this->WykonywanaAkcja == "szczegoly"){
                $Formularz->DodajPole('Priorytet_id', 'lista', 'Priorytet', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPriorytety($this->Baza), 'wybierz' => true));
                $Formularz->DodajPole('data_zakonczenia', 'tekst_data', 'Wykonano', array('tabelka' => Usefull::GetFormStandardRow()));
                $Formularz->DodajPole('zalacznik', 'lista', 'Załączniki', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array('tak' => 'tak', 'nie' => 'nie')));
                $Formularz->DodajPole('komentarz', 'tekst_dlugi', 'Komentarz', array('tabelka' => Usefull::GetFormStandardRow()));
                $Formularz->DodajPole('Statystyka_id', 'lista', 'Statystyka', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::PolaczDwieTablice(array(0 => '-- statystyka --'), UsefullBase::GetStatystyki($this->Baza))));
            }else if($this->WykonywanaAkcja == "zamknij"){
                $Formularz->DodajPole('komentarz', 'tekst_dlugi', 'Komentarz', array('tabelka' => Usefull::GetFormStandardRow()));
                $Formularz->DodajPole('Priorytet_id', 'tekstowo_lista', 'Priorytet', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPriorytety($this->Baza), 'wybierz' => true));
                $Formularz->DodajPole('Statystyka_id', 'lista', 'Statystyka', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetStatystyki($this->Baza), 'wybierz' => true, 'domyslna' => '-- statystyka --'));
                $Formularz->DodajPole('potencjal_id', 'lista', 'Potencjał klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPotencjaly($this->Baza), 'wybierz' => true, 'domyslna' => '-- potencjał --'));
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function DomyslnyWarunek(){
            return "pz.id_klient = '{$_GET['cid']}'";
        }

        function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "data_zdarzenia_od"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."((z.data_poczatek >= '$Wartosc 00:00:00' AND z.data_przypomnienia is null) OR (z.data_przypomnienia >= '$Wartosc 00:00:00'))";
                                        }else if($Pole == "data_zdarzenia_do"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."((z.data_poczatek < '$Wartosc 00:00:00' AND z.data_przypomnienia is null) OR (z.data_przypomnienia <= '$Wartosc 00:00:00'))";
                                        }else{
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                        }
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}


	function PobierzListeElementow($Filtry = array()) {
		$Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT z.*, pz.id_uzytkownik,
                                            concat(k.nazwa,'<br><span style=\"font-size:0.7em;\">[ ',od.nazwa,' ]</span>') as nazwa
                                            FROM zdarzenia z
                                            LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                            LEFT JOIN orderplus_klient k ON(pz.id_klient = k.id_klient)
                                            LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = k.id_oddzial)
                                            $Where ORDER BY data_poczatek DESC",$this->ParametrPaginacji,30));
		$Wynik = array(
			"temat" => 'Temat zadania',
                        "nazwa" => 'Nazwa klienta',
                        "id_uzytkownik" => array('naglowek' => 'Wykonanie', 'elementy' => $this->Userzy),
                        "data_poczatek" => 'Data',
                        "data_zakonczenia" => 'Zakończono'
		);
		return $Wynik;
	}

        function DodajZdarzenieDoCRM($termin_zaladunku, $id_klient, $id_uzytkownik){
            $this->Baza->Query("INSERT INTO $this->Tabela SET Priorytet_id = '1', Statystyka_id = '4', data_utworzenia = now(), komentarz = '', zalacznik = 'nie',
                                            data_poczatek = now(), data_zakonczenia = now(), temat = 'Wykonane zlecenie dnia: $termin_zaladunku'");
            $ZdarzenieID = $this->Baza->GetLastInsertId();
            $this->Baza->Query("INSERT INTO powiazania_zdarzenia SET id_uzytkownik = '$id_uzytkownik', id_klient = '$id_klient', Zdarzenia_id = '$ZdarzenieID'");
	}

        function UstalIdKlientaWCRM($id_klient){
		$Result = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '$id_klient'");
		$NIP = preg_replace('|[^a-zA-Z0-9ĄĆĘŁŃÓŚŹŻąęćłńóśżź]|', '', $Result['nip']);
		$ClientID = $this->Baza->GetValue("SELECT id FROM klienci WHERE nip = '$NIP'");
		if(!$ClientID){
			$this->Baza->Query("INSERT INTO klienci SET
							Potencjal_id = '4',
							Kod_kraju_id = '0',
							nazwa = '{$Result['nazwa']}',
							miasto = '{$Result['miejscowosc']}',
							kod_pocztowy = '{$Result['kod_pocztowy']}',
							mail = '{$Result['emaile']}',
							adres = '{$Result['adres']}',
							nip = '$NIP'
							");
			$ClientID = $this->Baza->GetLastInsertId();
		}
		return $ClientID;
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                $Akcje[] = array('img' => "desc_button", 'title' => "Szczegoly", "akcja" => "szczegoly");
                if(is_null($Dane['data_zakonczenia']) || $TH){
                    $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                }else{
                    $Akcje[] = array('img' => "edit_button_grey");
                }
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $Akcje[] = array('img' => "document_button", 'title' => "Załączniki", "akcja_link" => "?modul=zalaczniki&event={$_GET['id']}");
                }
		return $Akcje;
	}

        function  IDNastepnego($ID) {
            return false;
        }

        function  IDPoprzedniego($ID) {
            return false;
        }

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case "zamknij":
                    $this->AkcjaZamknij($ID);
                    break;
                case "lista_zdarzen":
                    parent::AkcjaLista();
                    $this->ShowComments();
                    break;
                default:
                    $this->AkcjaLista();
                    break;
            }
	}

        function PobierzDaneElementu($ID, $Typ = null) {
            if(!$this->Zdarzenie){
                $Dane = parent::PobierzDaneElementu($ID);
                $Dane2 = $this->Baza->GetData("SELECT * FROM powiazania_zdarzenia WHERE Zdarzenia_id = '$ID'");
                $Dane['id_klient'] = $Dane2['id_klient'];
                $Dane['id_uzytkownik'] = $Dane2['id_uzytkownik'];
                $this->KlientName = $this->Klienci[$Dane['id_klient']];
                $this->KlientID = $Dane['id_klient'];
                $OddzialID = $this->Baza->GetValue("SELECT id_oddzial FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$Dane['id_uzytkownik']}'");
                $this->UserzyDodaj = UsefullBase::GetUsersLoginByOddzial($this->Baza, $OddzialID);
                $Dane['potencjal_id'] = $this->Baza->GetValue("SELECT potencjal_id FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient']}'");
                $Dane['data_poczatek'] = substr($Dane['data_poczatek'], 0, 10).($Dane['przypomnienie_mail'] == 1 ? " ".$Dane['przypomnienie_mail_godzina'] : "");
                $Dane['data_przypomnienia'] = substr($Dane['data_przypomnienia'], 0, 10);
                if($_GET['akcja'] != "edycja"){
                    $Dane['przypomnienie']['check'] = $Dane['przypomnienie_mail'];
                    $Dane['przypomnienie']['godzina'] = $Dane['przypomnienie_mail_godzina'];
                }
                return $Dane;
            }
            return $this->Zdarzenie;
        }

        function AkcjaPrzeladowanie($Wartosci, $Formularz){
            if(isset($Wartosci['id_klient'])){
                $this->KlientName = $this->Klienci[$Wartosci['id_klient']];
                $this->KlientID = $Wartosci['id_klient'];
            }
            return $Wartosci;
        }

        function AkcjaPrzeladowanieFormularz($Wartosci, $Formularz, $ID = null){
            #$OddzialID = $this->Baza->GetValue("SELECT id_oddzial FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$Wartosci['id_uzytkownik']}'");
            #$this->UserzyDodaj = UsefullBase::GetUsersLoginByOddzial($this->Baza, $OddzialID);
            #$Formularz->UstawOpcjePola("id_uzytkownik", "elementy", $this->UserzyDodaj, false);
            return $Formularz;
        }

        function PobierzDaneDomyslne(){
            if($_GET['cid']){
                $DaneDomyslne['id_klient'] = $_GET['cid'];
                $DaneDomyslne['specjalne'] = "nie";
                $Oddzialy = $this->Baza->GetValues("SELECT id_oddzial FROM orderplus_klient_oddzial WHERE id_klient = '{$DaneDomyslne['id_klient']}'");
                $this->UserzyDodaj = array();
                foreach($Oddzialy as $OddzialID){
                    $PobierzUserow = UsefullBase::GetUsersLoginByOddzial($this->Baza, $OddzialID);
                    $this->UserzyDodaj = Usefull::PolaczDwieTablice($this->UserzyDodaj, $PobierzUserow);
                }
            }else{
                $DaneDomyslne = $this->Zdarzenie;
                if(isset($_GET['event'])){
                    $DaneDomyslne['Priorytet_id'] = $DaneDomyslne['Priorytet_id'];
                    $DaneDomyslne['data_przypomnienia'] = date("Y-m-d", strtotime($DaneDomyslne['data_przypomnienia']));
                }
            }
            return $DaneDomyslne;
        }

        function  AkcjaDodawanie($ID) {
            echo "<div style='clear: both;'></div>\n";
            echo "<div class='form-title'>Nowe zdarzenie";
            echo "</div>\n";
            parent::AkcjaDodawanie($ID);
            $this->ShowComments();
        }

        function  AkcjaSzczegoly($ID) {
            parent::AkcjaSzczegoly($ID);
            $this->ShowComments();
        }
        
        function  AkcjaEdycja($ID) {
            if(is_null($this->Zdarzenie['data_zakonczenia'])){
                parent::AkcjaEdycja($ID);
                $this->ShowComments();
            }else{
                $this->WykonywanaAkcja = "szczegoly";
                Usefull::ShowKomunikatError('<b>Nie można edytować tego zdarzenia gdyż zostało ono zamknięte</b>');
                $this->AkcjaSzczegoly($ID);
            }
        }

        function AkcjaZamknij($ID){
            if(is_null($this->Zdarzenie['data_zakonczenia'])){
                parent::AkcjaEdycja($ID);
                $this->ShowComments();
            }else{
                $this->WykonywanaAkcja = "szczegoly";
                Usefull::ShowKomunikatError('<b>Nie można zamknąć tego zdarzenia gdyż zostało ono zamknięte</b>');
                $this->AkcjaSzczegoly($ID);
            }
        }

        function ShowComments(){
            if(!$this->Zapis){
                $comments = $this->GetComments($this->KlientID, 0); 
                $desc = $this->KlientName;
                include(SCIEZKA_SZABLONOW."komentarze.tpl.php");
            }
        }

        function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
            $Potencjal = (isset($Wartosci['potencjal_id']) ? $Wartosci['potencjal_id'] : false);
            $Tresc = (isset($Wartosci['poinformuj']) && $Wartosci['poinformuj'] != "" ? $Wartosci['poinformuj'] : false);
            $WyslijDo = (isset($Wartosci['wyslij_do']) ? $Wartosci['wyslij_do'] : false);
            $KlientID = $Wartosci['id_klient'];
            $UserID = $Wartosci['id_uzytkownik'];
            if(isset($Wartosci['przypomnienie'])){
                $Wartosci['przypomnienie_mail'] = isset($Wartosci['przypomnienie']['check']) ? 1 : 0;
                $Wartosci['przypomnienie_mail_godzina'] = $Wartosci['przypomnienie']['godzina'];
                $Wartosci['przypomnienie_mail_wyslano'] = 0;
            }
            unset($Wartosci['potencjal_id']);
            unset($Wartosci['poinformuj']);
            unset($Wartosci['wyslij_do']);
            unset($Wartosci['id_klient']);
            unset($Wartosci['id_uzytkownik']);
            unset($Wartosci['przypomnienie']);
            $Wartosci['data_przypomnienia'] = (isset($Wartosci['data_przypomnienia']) && $Wartosci['data_przypomnienia'] != "" ? $Wartosci['data_przypomnienia'] : "My_null");
            $Wartosci['komentarz'] = (isset($Wartosci['komentarz']) && $Wartosci['komentarz'] != "" ? $Wartosci['komentarz'] : "My_null");
            if($this->WykonywanaAkcja == "zamknij"){
                $Wartosci['data_zakonczenia'] = date("Y-m-d H:i:s");
            }
            if($ID){
                $Zapytanie = $this->Baza->PrepareUpdate($this->Tabela, $Wartosci, array($this->PoleID => $ID));
            }else {
                $Wartosci['data_utworzenia'] = date("Y-m-d H:i:s");
                $Wartosci['zalacznik'] = "nie";
                $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Wartosci);
            }
            if($this->Baza->Query($Zapytanie)){
                if(!$ID){
                    $ID = $this->Baza->GetLastInsertId();
                }
                if($this->WykonywanaAkcja == "dodawanie"){
                    $Values2['id_uzytkownik'] = $UserID;
                    $Values2['id_klient'] = $KlientID;
                    $Values2['Zdarzenia_id'] = $ID;
                    $Zap2 = $this->Baza->PrepareInsert("powiazania_zdarzenia", $Values2);
                    if(!$this->Baza->Query($Zap2)){
                        return false;
                    }
                    if($Tresc && $WyslijDo){
                        $Tresc = strip_tags($Tresc);
                        $Tresc .= '<br><br><b>Nazwa klienta: </b>'.$this->KlientName.'<br>
                         <b>Data kontaktu: </b>'.$Wartosci['data_poczatek'].'<br>
                         <b>Treść: </b>'.$Wartosci['temat'];
                        if(count($WyslijDo) > 0){
                            $Mail = new MailSMTP($this->Baza);
                            foreach($WyslijDo as $user_id){
                                if($Mail->SendMailsZdarzenie($user_id, $Tresc)){
                                    echo "WYSŁANO";
                                }
                            }
                        }
                    }
                    /*ustawianie klientowi opiekuna i potencjału jeżeli był wybrany*/
                    $ClientUpd['id_uzytkownik'] = $UserID;
                    if($Potencjal){
                        $ClientUpd['potencjal_id'] = $Potencjal;
                    }
                    $ClientUpdZap = $this->Baza->PrepareUpdate("orderplus_klient", $ClientUpd, array("id_klient" => $KlientID));
                    $this->Baza->Query($ClientUpdZap);
                    if(isset($_GET['event']) && is_numeric($_GET['event'])){
                            /*dodawane zdarzenie jest następnym zadaniem, więc: aktualizacja poprzedniego zadania*/
                            $_update['data_zakonczenia'] = date("Y-m-d H:i:s");
                            $_update['komentarz'] = $Wartosci['komentarz'];
                            $_update['Statystyka_id'] = $Wartosci['Statystyka_id'];
                            $ZapUpd = $this->Baza->PrepareUpdate($this->Tabela, $_update, array($this->PoleID => $_GET['event']));
                            if($this->Baza->Query($ZapUpd)){
                                $this->Baza->Query("UPDATE powiazania_zdarzenia SET id_kolejnego_zdarzenia = '$ID' WHERE Zdarzenia_id = '{$_GET['event']}'");
                            }else{
                                return false;
                            }
                     }
                }
                 $this->Zapis = true;
                return true;
            }
            else {
                return false;
            }
	}

        function SprawdzDane($Wartosci){
            if(isset($Wartosci['id_uzytkownik']) && $Wartosci['id_uzytkownik'] == 0){
                $this->Error = "Proszę wybrać opiekuna do zdarzenia";
                return false;
            }
            if(isset($Wartosci['Priorytet_id']) && $Wartosci['Priorytet_id'] == 0){
                $this->Error = "Proszę wybrać priorytet zdarzenia";
                return false;
            }
            if($this->WykonywanaAkcja == "dodawanie" || $this->WykonywanaAkcja == "edycja"){
                $PoleData = ($this->WykonywanaAkcja == "dodawanie" ? "data_poczatek" : "data_przypomnienia");
                if($Wartosci[$PoleData] != ""){
                    /*usuwanie ewentulanej godziny*/
                    $_temp=explode(' ', trim($Wartosci[$PoleData]));
                    $Wartosci[$PoleData] = $_temp[0];

                    if(date('Y-m-d') > $Wartosci[$PoleData])
                    {	/*data przeszła*/
                            $this->Error = 'Przesłana data jest datą przeszłą.';
                            return false;
                    }
                    elseif(Usefull::isWeekendDay($Wartosci[$PoleData]))
                    {	/*weekend*/
                            $this->Error = 'Przesłana data wypada w weekend.';
                            return false;
                    }
                    elseif($this->is_there_event($Wartosci[$PoleData], $Wartosci['id_uzytkownik'], false) >= 50)
                    {	/*ilość zadań na dany dzień została wyczerpana*/
                            if($Wartosci['specjalne']=='nie')
                            {	/*normalne zadanie*/
                                    $this->Error = 'Na dany dzień została zaplanowana już maksymalna liczba zadań.';
                                    return false;
                            }
                            elseif($Wartosci['specjalne']=='tak' && $this->is_there_event($Wartosci[$PoleData], $Wartosci['id_uzytkownik']) >= 60)
                            {	/*zadanie specjalne*/
                                    $this->Error = 'Na dany dzień została zaplanowana już maksymalna liczba zadań (włącznie z dodatkowymi).';
                                    return false;
                            }
                    }
                }
            }
            return true;
        }

        function GetComments($ID, $Limit = 5){
            return $this->Baza->GetRows("SELECT z.*, pz.* FROM zdarzenia z, powiazania_zdarzenia pz
                                            WHERE pz.id_klient = '$ID' AND pz.Zdarzenia_id = z.id AND z.komentarz IS NOT NULL
                                            ORDER BY data_zakonczenia DESC ".($Limit > 0 ? "LIMIT $Limit" : ""));
        }

        function AkcjaLista(){
                $Zdarzenia = new ZdarzeniaPrzepisz($this->Baza);
                if(isset($_GET['date']) && Usefull::CheckDate($_GET['date'])){
                        $_current_date=$_GET['date'];
                        /*zmienne do kalendarza*/
                        $_temp_array=explode('-',$_GET['date']);
                        $Year = isset($_GET['year']) ? $_GET['year'] : $_temp_array[0];
                        $Month= isset($_GET['month']) ? $_GET['month'] : intval($_temp_array[1]);
                }
                else
                {	/*nie został przekazany parametr, więc wyświetlany zostaje dzień dzisiejszy*/
                        $_current_date=date('Y-m-d');
                }
                /*lista zdarzeń*/
                $EventList = $this->EventList($_current_date);
                $Calendar = new Kalendarz($_GET);
                /*pobieranie wyświetlanych aktualnie dat*/
                $current_days = $Calendar->getDays();
                for($i=0;$i<(count($current_days));$i++)
                {
                        if($this->is_there_event($current_days[$i], $_SESSION['id_uzytkownik'], $_SESSION['uprawnienia_id'])){
                                /*treść w zależności od wybranego widoku*/
                                if($Calendar->getView() == "month")
                                {	/*miesiąc*/
                                        $_content=array();
                                }
                                elseif($Calendar->getView() == 'week')
                                {	/*tydzień*/
                                    $_content = $this->getContentAboutDayLong($current_days[$i]);
                                }
                                $_content['date'] = $current_days[$i];
                                $Calendar->setDayContent($current_days[$i], $_content);
                        }
                }
            ?>
                <div style="clear: both;"></div>
                <div style="float: left; padding: 15px; margin: 0px 50px; width: 1050px;">
                <div id="event_list">
             <?php
                include(SCIEZKA_SZABLONOW."event-list.tpl.php");
             ?>
                </div>
             <?php

             ?>
                <div id="calendar_box">
              <?php
                    $Calendar->ShowCalendar();
              ?>
                </div>
                </div>
               <?php
        }

        function GetEventList($date){            
             $Result = $this->Baza->GetRows("SELECT z.id, z.temat, z.data_poczatek, z.data_przypomnienia, z.data_zakonczenia, z.Priorytet_id, k.potencjal_id, k.nazwa, k.id_klient
                                                FROM zdarzenia z, powiazania_zdarzenia pz, orderplus_klient k
                                                WHERE ((z.data_poczatek= '$date' AND z.data_przypomnienia is null) OR z.data_przypomnienia = '$date')
                                                    AND pz.id_klient = k.id_klient AND pz.Zdarzenia_id = z.id AND pz.id_uzytkownik = '{$_SESSION['id_uzytkownik']}'
                                                    ORDER BY z.Priorytet_id, k.potencjal_id");
            return $Result;
        }

        function EventList($date){
            $Result = $this->GetEventList($date);
            $Events = array();
            foreach($Result as $Idx => $Res){
                $_temp_date1=explode(' ',$Res['data_poczatek']);
                $_temp_date2=explode(' ',$Res['data_przypomnienia']);
                $_temp_date3=explode(' ',$Res['data_zakonczenia']);
                $Events[]=array($Idx+1, $this->returnEventLink($Res['nazwa'],$Res['temat'],$Res['Priorytet_id'],$_temp_date1[0],$_temp_date2[0],$_temp_date3[0],$Res['id']));
            }
            return $Events;
        }

        function returnEventLink($_name,$_title,$_priorytet,$_date_start,$_date_next,$_date_end,$_id){	
            /*określanie koloru przypomnienia*/
            $_class = ($_date_end ? 'event_done' : 'priorytet_'.$_priorytet);
            $result='<div class="table_event_col_customer"><span class="event_content '.$_class.'" id="event_'.$_id.'" onclick="getEventDesc(\'\',\''.$_id.'\')">'.$_name.'</span><span class="mepp_event_list_title">'.$_title.'</span>';
            /*sprawdzanie, czy jest to zdarzenie zaległe*/
            $result.=($_date_start===date('Y-m-d') ? '' : '<span class="event_date">'.$_date_start.'</span></div>');
            return $result;
        }

        function is_there_event($date,$_id, $specjalne = true){
                return $this->Baza->GetValue("SELECT count(*) FROM zdarzenia z, powiazania_zdarzenia pz
                                                WHERE ((z.data_poczatek= '$date' AND z.data_przypomnienia is null) OR z.data_przypomnienia = '$date')
                                                        AND pz.Zdarzenia_id = z.id AND pz.id_uzytkownik = '$_id'".($specjalne ? " AND specjalne = 'nie'" : ""));
        }

        function getContentAboutDayLong($_date){
                $result=array();

                $_events = $this->GetEventList($_date);

                $result['date']=$_date;
                        /*ilość zadań*/
                $result['task_no']=count($_events);

                $result['event']=array();
                $result['task_past']=0;
                foreach($_events as $_event)
                {	$_temp_date=explode(' ',$_event['data_poczatek']);
                        if($_temp_date[0]!=$_date)
                        {	/*ilość zadań zaległych*/
                                $result['task_past']++;
                        }
                        /*określanie koloru przypomnienia*/
                        if($_event['data_zakonczenia'])
                        {	/*zakończone zadanie*/
                                $_class='event_done';
                        }
                        else
                        {	/*priorytet zadania*/
                                $_class='priorytet_'.$_event['Priorytet_id'];
                        }

                        /*nazwa klienta i id zadania*/
                        $result['event'][]=array(
                                        'class'	=>	$_class,
                                        'name'	=>	$_event['temat'],
                                        'id'	=>	$_event['id']
                                                                                );
                }

                /*zwracanie wyniku*/
                return $result;
        }

        function WyswietlAJAX($Action){
            if($Action == "get-event-content"){
                echo $this->getContentAboutDaySmall($_GET['date']);
            }
            if($Action == "get-event-content-id"){
                echo $this->returnEventInfo($_GET['ev']);
            }
            if($Action == "get-small-event-content-id"){
                echo $this->returnEventSmallInfo($_GET['ev']);
            }
        }

        function getContentAboutDaySmall($_date){	
        $_events = $this->GetEventList($_date);

	/*typ użytkownika*/
	$result='user;';/*($_user_right==1 ? 'admin' : ($_user_right==2 ? 'manager' : 'user')).';';*/

	/*ilość zadań i data*/
	$result.=count($_events).':'.$_date.';';

	$_temp_event=array();
	$_task_past=0;
	$_temp_events='';
	$i=0;
	foreach($_events as $_event)
	{	$_temp_date=explode(' ',$_event['data_poczatek']);
		if($_temp_date[0]!=$_date)
		{	/*ilość zadań zaległych*/
			$_task_past++;
		}

		if($i<10)
		{	/*zadania*/
			$a_open='<a href="?modul=klienci&akcja=szczegoly&id='.$_event['id_klient'].'" title="Klient: '.addslashes(addslashes($_event['nazwa'])).'" onclick="window.location=this.href">';
			$_temp_events.=';'.$a_open.addslashes(addslashes(substr($_event['nazwa'],0,20))).'...</a>@'.$_temp_date[0];

			$i++;
		}
	}


	$result.=$_task_past.$_temp_events;

	/*zwracanie wyniku*/
	return $result;
}

        function returnEventInfo($_event_id){
        /*pobieranie danych na temat zdarzenia*/
	$_event = $this->Baza->GetData("SELECT z.data_poczatek, z.data_przypomnienia, z.data_zakonczenia, z.tresc, z.temat, z.id,
                                            k.id_klient as klient_id, k.nazwa, k.telefon, k.emaile, z.Priorytet_id, k.potencjal_id, p.priorytet
                                            FROM zdarzenia z, powiazania_zdarzenia pz, orderplus_klient k, priorytet p
                                            WHERE z.id = '$_event_id' AND p.id = z.Priorytet_id AND pz.id_klient = k.id_klient AND pz.Zdarzenia_id = z.id
                                            AND pz.id_uzytkownik = '{$_SESSION['id_uzytkownik']}'");
	/*zwracanie pobranych danych*/
		/*tytuł*/
	$_event_title=preg_replace(array('/\n?/','/\r\n?/'),array('',''),$_event['temat']);
		/*data początkowa*/
	$_temp=explode(' ',$_event['data_poczatek']);
	$_start_date=$_temp[0] ? $_temp[0] : 'null';
		/*data przypomnienia, jeżeli zdarzenie zostało przeniesione*/
	$_temp=explode(' ',$_event['data_przypomnienia']);
	$_next_date=$_temp[0] ? $_temp[0] : 'null';
		/*data końcowa*/
	$_temp=explode(' ',$_event['data_zakonczenia']);
	$_end_date=$_temp[0] ? $_temp[0] : 'null';
		/*priorytet*/
	$_event_priorytet=$_event['priorytet'];
	$_event_priorytet_id=$_event['Priorytet_id'];
		/*treść*/
	$Comments =  $this->GetComments($_event['klient_id']);
        $_event_content = '';
        foreach($Comments as $_row){
            $_event_content.=preg_replace(array('/\n?/','/\r\n?/'),array('',''),nl2br(addslashes($_row['komentarz']))).'<date>'.$_row['data_zakonczenia'].'<comment>';
	}

	if(!$Comments)
	{	/*brak komentarzy*/
		$_event_content='brak komentarzy<date><comment>';
	}
        
		/*button do edycji*/
	if($_end_date==='null')
	{	$_event_buttons='<input type="button" value="przejdź" title="przejdź" onclick="window.location=\\\'?modul=zdarzenia&akcja=dodawanie&event='.$_event_id.'\\\'; return false;" class="form-button">'; 
	}
	else
            {
                $_event_buttons='<input type="button" value="pokaż" title="pokaż" onclick="window.location=\\\'?modul=zdarzenia&akcja=szczegoly&id='.$_event_id.'\\\'; return false;" class="form-button">';
            }

	$result=$_event_buttons.'<space>'.$_event_title.'<space>'.$_start_date.'<space>'.$_next_date.
            '<space>'.$_end_date.'<space>'.$_event_priorytet.'<space>'.$_event_priorytet_id.
            '<space>'.$_event_content.'<space>'.addslashes($_event['nazwa']).'<space>'.$_event['klient_id'].
            '<space>'.$_event['telefon'].'<space>'.$_event['mail'].
            '<space><space>'.$this->getOsobyKontakt($_event['klient_id']).'<space>';
	return $result;
}

        function returnEventSmallInfo($_event_id)
        {	/*pobieranie danych na temat zdarzenia*/
                $_event = $this->Baza->GetData("SELECT z.data_poczatek, z.data_przypomnienia, z.data_zakonczenia, z.tresc, z.temat, z.id,
                                            k.id_klient as klient_id, k.nazwa, k.telefon, k.emaile, z.Priorytet_id, k.potencjal_id, p.priorytet
                                            FROM zdarzenia z, powiazania_zdarzenia pz, orderplus_klient k, priorytet p
                                            WHERE z.id = '$_event_id' AND p.id = z.Priorytet_id AND pz.id_klient = k.id_klient AND pz.Zdarzenia_id = z.id
                                            AND pz.id_uzytkownik = '{$_SESSION['id_uzytkownik']}'");

                /*zwracanie pobranych danych*/
                        /*tytuł*/
                $_event_title=preg_replace(array('/\n?/','/\r\n?/'),array('',''),$_event['temat']);
                        /*data początkowa*/
                $_temp=explode(' ',$_event['data_poczatek']);
                $_start_date=$_temp[0] ? $_temp[0] : 'null';
                        /*data przypomnienia, jeżeli zdarzenie zostało przeniesione*/
                $_temp=explode(' ',$_event['data_przypomnienia']);
                $_next_date=$_temp[0] ? $_temp[0] : 'null';
                        /*data końcowa*/
                $_temp=explode(' ',$_event['data_zakonczenia']);
                $_end_date=$_temp[0] ? $_temp[0] : 'null';
                        /*priorytet*/
                $_event_priorytet_id=$_event['Priorytet_id'];
                $_event_priorytet=$_event['priorytet'];

                $result=$_event_id.'<space>'.$_event_title.'<space>'.$_start_date.'<space>'.$_next_date.'<space>'.$_end_date.'<space>'.$_event_priorytet.'<space>'.$_event_priorytet_id.'<space>null<space>';

                return $result;
        }

        function getOsobyKontakt($_klient_id)
        {
            $_kontakt = $this->Baza->GetRows("SELECT * FROM osoby_kontaktowe WHERE id_klient = '$_klient_id'");
            $_result='';
            foreach($_kontakt as $_row)
            {
                $_result.='<div class="ajax_box_comment" style="margin-top:10px;border-left:0px;border-right:0px;border-bottom:0px;">'.
                    '<div class="ajax_box_comment_kontakt_name" style="letter-spacing:2px;font-weight:bold;">'.
                    $_row['imie_nazwisko'].'</div>'.
                    '<table style="font-size: 0.9em;"><tbody>'.
                    '<tr><td class="desc">Telefon:</td><td>'.$_row['telefon'].'</td></tr>'.
                    '<tr><td class="desc">E-mail:</td><td>'.
                    ($_row['mail'] ? '<a href="mailto:'.$_row['mail'].'" class="ab_a" title="napisz do">'.$_row['mail'].'</a>' : '').
                    '</td></tr>'.
                    '</tbody></table></div>';
            }

            return $_result;
        }

        function ShowBigButtonActions($ID){
            if(isset($_GET['event']) && is_numeric($_GET['event'])){
                $ID = $_GET['event'];
            }
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(!is_null($ID)){
                    echo("<div style='float: left; display: inline;' id='zaczep'>");
                        if($this->WykonywanaAkcja != "edycja" && $this->WykonywanaAkcja != "szczegoly"){
                            echo "<a href='?modul=zdarzenia&akcja=edycja&id=$ID' class='form-button'>edycja zadania</a>";
                        }
                        if($this->WykonywanaAkcja != "zamknij" && $this->WykonywanaAkcja != "szczegoly"){
                            echo "<a href='?modul=zdarzenia&akcja=zamknij&id=$ID' class='form-button'>zamknij zadanie</a>";
                        }
                        if($this->WykonywanaAkcja == "edycja"){
                            echo "<a href='?modul=zdarzenia&akcja=dodawanie&event=$ID' class='form-button'>następne zadanie</a>";
                        }
                        if($this->WykonywanaAkcja != "szczegoly"){
                            echo "<a href='?modul=zalaczniki&akcja=dodawanie&event=$ID' class='form-button'>dodaj załącznik</a>";
                        }
                        echo "<a href='javascript:showInfoAboutCustomer(\"$this->KlientID\",\"\", document.getElementById(\"zaczep\"));' class='form-button'>klient</a>";
                    echo ("</div>");
                }else if(isset($_GET['cid'])){
                     echo("<div style='float: left; display: inline;' id='zaczep'>");
                        echo "<a href='javascript:showInfoAboutCustomer(\"{$_GET['cid']}\",\"\", document.getElementById(\"zaczep\"));' class='form-button'>klient</a>";
                     echo "</div>\n";
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if($this->WykonywanaAkcja == "lista_zdarzen"){
                include(SCIEZKA_SZABLONOW."filters-raporty.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
        }

}
?>