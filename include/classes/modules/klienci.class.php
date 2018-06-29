<?php
/**
 * Moduł klienci
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Klienci extends ModulBazowy {
        public $Branze;
        public $Userzy;
        public $Oddzialy;
        public $Kraje;
        public $Statusy = array("1" => 'Klient aktywny', "2" => 'Klient potencjalny'); 
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_klient';
            $this->PoleID = 'id_klient';
            $this->PoleNazwy = 'identyfikator';
            $this->Nazwa = 'Klient';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Oddzialy = UsefullBase::GetOddzialy($this->Baza);
            $this->Userzy = UsefullBase::GetUsersLogin($this->Baza);
            $this->Branze = UsefullBase::GetBranzeCRM($this->Baza);
            $this->Kraje = UsefullBase::GetKodyKrajowCRM($this->Baza);
            $this->Filtry[] = array('opis' => "Branża", "nazwa" => "branza_crm_id", "opcje" => $this->Branze, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Opiekun", "nazwa" => "id_uzytkownik", "opcje" => $this->Userzy, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Oddział", "nazwa" => "id_oddzial", "opcje" => $this->Oddzialy, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Klient", "nazwa" => "nazwa", "typ" => "tekst");
            $this->Filtry[] = array('opis' => "Kraj", "nazwa" => "kod_kraju_id", "opcje" => $this->Kraje, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Miasto", "nazwa" => "miejscowosc", "typ" => "tekst");
            $this->Filtry[] = array('opis' => "Status klienta", "nazwa" => "klient_status", "opcje" => $this->Statusy, "typ" => "lista");
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('nazwa', 'tekst', 'Nazwa klienta', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('klient_status', 'lista', 'Status klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Statusy, 'id' => 'klient_status', "atrybuty" => array("onchange" => "ChangeClientStatus();")));
            $Formularz->DodajPole('identyfikator', 'tekst', 'Identyfikator', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('client_login', 'tekst', 'Login', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('haslo', 'password', 'Hasło', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            }
            $Formularz->DodajPole('potencjal_id', 'lista', 'Potencjał klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetPotencjaly($this->Baza), 'wybierz' => true));
            $Formularz->DodajPole('branza_id', ($this->Uzytkownik->IsAdmin() ? 'lista_to_input' : 'lista'), 'Branża', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetBranze($this->Baza), 'wybierz' => true));
            $Formularz->DodajPole('branza_crm_id', 'lista_to_input', 'Branża CRM', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Branze, 'wybierz' => true, 'domyslna' => ' -- wybierz branże -- '));
            $Formularz->DodajPole('siedziba_id', 'lista', 'Siedziba', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetSiedziby($this->Baza), 'wybierz' => true));
            $Formularz->DodajPole('kod_kraju_id', 'lista', 'Kod kraju', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Kraje, 'wybierz' => true));
            $Formularz->DodajPole('adres', 'tekst', 'Adres', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('kod_pocztowy', 'tekst', 'Kod pocztowy', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('miejscowosc', 'tekst', 'Miasto', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('nip', 'tekst', 'NIP', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('emaile', 'tekst_dlugi', 'Adresy e-mail<br>(rozdzielane przecinkiem)', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('telefon', 'tekst', 'Telefon', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('strona_www', 'tekst', 'Strona WWW', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('os_kontaktowe', 'osoby_kontaktowe', 'Osoby kontaktowe', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('id_oddzial', 'podzbiór_checkbox_1n', 'Dostępny dla', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Oddzialy, 'tabela' => 'orderplus_klient_oddzial', 'pole' => 'id_oddzial', 'pole_where' => 'id_klient', 'krotkie' => true));
            $Formularz->DodajPole('id_uzytkownik', 'podzbiór_checkbox_1n', 'Opiekun handlowy', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'tabela' => 'orderplus_klient_opiekun_handlowy', 'pole' => 'id_uzytkownik', 'pole_where' => 'id_klient', 'krotkie' => true));
            //$Formularz->DodajPole('id_uzytkownik_op', 'podzbiór_checkbox_1n', 'Opiekun operacyjny', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'tabela' => 'orderplus_klient_opiekun_operacyjny', 'pole' => 'id_uzytkownik', 'pole_where' => 'id_klient', 'krotkie' => true));
            //$Formularz->DodajPole('dodatkowy_dostep', 'podzbiór', 'Dodatkowy dostęp do danych<br /><small>można zaznaczyć kilku z wciśniętym CTRL</small>', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Userzy, 'tabela' => 'orderplus_klient_uzytkownik_dostep', 'pole' => 'id_uzytkownik', 'pole_where' => 'id_klient'));
//            if($this->WykonywanaAkcja == "szczegoly"){
//                $Formularz->DodajPole('zdarzenie', 'tekstowo', 'Zdarzenia', array('tabelka' => Usefull::GetFormStandardRow()));
//            }

//            $Formularz->DodajPole('grupa_id', 'lista', 'Grupa', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetGrupyFirm($this->Baza), 'wybierz' => true, 'domyslna' => ' - wybierz grupę - '));
//            $Formularz->DodajPole('zone', 'tekst', 'Zone', array('tabelka' => Usefull::GetFormStandardRow()));
            
            $Formularz->DodajPole('adres_korespondencyjny', 'checkbox_tekst_dlugi', 'Adres korespondencyjny', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client'), 'etykieta' => 'Inny niż adres siedziby'));
            $Formularz->DodajPole('termin_platnosci_dni', 'tekst', 'Termin płatności', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;'), 'opis_dodatkowy_za' => ' dni', 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('waluta_fakturowania', 'lista', 'Waluta fakturowania', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetWaluty(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('kurs_waluty_bank', 'lista', 'Kurs waluty (bank)', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetBanki(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('kurs_waluty_dzien', 'lista_inny', 'Kurs waluty (dzień)', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array("1" => "z dnia załadunku", "-1" => "inny"), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('header_1', 'sam_tekst', 'Instrukcje specjalne', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'td_colspan' => 2, 'td_style' => 'background-color: #BCCE00; font-weight: bold;', 'td_end' => 1, 'tr_end' => 1), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('opis_na_fakturze', 'checkbox_tekst_dlugi', 'Opis na fakturze', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('dodatkowe_ustalenia', 'checkbox_tekst_dlugi', 'Ustalenia dotyczące indywidualnych preferencji i procedur', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty_tr' => array('class' => 'only_active_client')));
            $Formularz->DodajPole('zalaczniki', 'zalaczniki', 'Załącz dokumenty firmy', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('ostatnio_edytowal', 'tekstowo_lista', 'Ostatnio edytowane przez', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::PolaczDwieTablice(array(0 => '<span style="color: #FF0000;">nikt nie edytował jeszcze danych tego klienta</span>'),$this->Userzy)));
            $Formularz->DodajPoleWymagane('nazwa', false);
            $Formularz->DodajPoleWymagane('miejscowosc', false);
            $Formularz->DodajPoleWymagane('siedziba_id', false);
            $Formularz->DodajPoleWymagane('kod_kraju_id', false);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzDaneDomyslne(){
            unset($_SESSION['Kontakt'][$this->SID]);
            unset($_SESSION['Zalacznik'][$this->SID]);
            return array("klient_status" => "2", "id_oddzial" => array(3)); 
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID);
            $Dane['dodatkowy_dostep'] = $this->customerUserAccess($ID);
            $Dane['os_kontaktowe'] = $this->GetContactPersons($ID);
            $Dane['zalaczniki'] = $this->GetZalaczniki($ID);
            if($this->WykonywanaAkcja == "szczegoly"){
                $Dane['zdarzenie'] = $this->GetLastContact($ID);
            }
            return $Dane;
        }

        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
            if($_POST['OpcjaFormularza'] == "add_zalacznik"){
               $Zal = new Zalaczniki($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
               if(!$Zal->ZapiszZalacznikFromClient($ID)){
                   $Error = $Zal->JakiBlad();
                   Usefull::ShowKomunikatError('<b>'.$Error.'</b>');
               }
            }
            if($ID > 0){
                $Wartosci['os_kontaktowe'] = $this->GetContactPersons($ID);
                $Wartosci['zalaczniki'] = $this->GetZalaczniki($ID);
            }else{
                $Wartosci['os_kontaktowe'] = $_SESSION['Kontakt'][$this->SID];
                $Wartosci['zalaczniki'] = $_SESSION['Zalacznik'][$this->SID];
            }
            return $Wartosci;
        }

        function WyswietlAkcje($ID = null) {
            if($ID){
                if($this->WykonywanaAkcja == "szczegoly" && (!$this->CheckAccess($ID) && !in_array($_SESSION['id_oddzial'], $this->customerOddzialAccess($ID)))){
                    $this->ZablokowaneElementyIDs[] = $ID;
                }else if($this->WykonywanaAkcja != "szczegoly" && !$this->CheckAccess($ID)){
                    $this->ZablokowaneElementyIDs[] = $ID;
                }
                if($this->WykonywanaAkcja == "kasowanie" && !$this->Uzytkownik->IsAdmin()){
                    $this->ZablokowaneElementyIDs[] = $ID;
                }
            }
            if($this->WykonywanaAkcja != "import"){
		$this->ShowFilters();
                $this->ShowBigButtonActions($ID);
            }
		echo "<div id='Komunikaty' class='komunikat'></div>\n";
                echo "<script type='text/javascript' src='js/klienci.js'></script>";
		$this->WykonywaneAkcje($ID);
	}

        function customerUserAccess($ID){
            $Handlowi = $this->Baza->GetValues("SELECT id_uzytkownik FROM orderplus_klient_opiekun_handlowy WHERE id_klient = '$ID'");
            if(!$Handlowi){
                $Handlowi = array();
            }
            $Operacyjni = $this->Baza->GetValues("SELECT id_uzytkownik FROM orderplus_klient_opiekun_operacyjny WHERE id_klient = '$ID'");
            if(!$Operacyjni){
                $Operacyjni = array();
            }
            return array_merge($Handlowi, $Operacyjni);
        }

        function customerOddzialAccess($ID){
            $Oddzialy = $this->Baza->GetValues("SELECT id_oddzial FROM orderplus_klient_oddzial WHERE id_klient = '$ID'");
            if(!$Oddzialy){
                $Oddzialy = array();
            }
            return $Oddzialy;
        }

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Access = $this->CheckAccess($Dane[$this->PoleID]);
                if($Dane[$this->PoleID] > 0 && !$Access){
                    $this->ZablokowaneElementyIDs[] = $Dane[$this->PoleID];
                }
                
                $Akcje[] = array('img' => "desc_button", 'title' => "Szczegóły", "akcja" => "szczegoly", "extra" => true);
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $Akcje[] = array('img' => "mail_button", 'title' => "Drukuj kopertę", "akcja_href" => "klienci_druk_koperty.php?");
                }
                $Akcje[] = array('img' => "document_button", 'title' => "Karta klienta", "akcja_href" => "karta_klienta.php?", "extra" => true);
		$Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                if(isset($_GET['id']) && is_numeric($_GET['id']) && $Access){
                    $Akcje[] = array('img' => "clock_button", 'title' => "Zdarzenia", "akcja_link" => "?modul=zdarzenia&akcja=lista_zdarzen&cid={$_GET['id']}", 'target' => false);
                    #$Akcje[] = array('img' => "document_button", 'title' => "Załączniki", "akcja_link" => "?modul=zalaczniki&cid={$_GET['id']}", 'target' => false);
                    #$Akcje[] = array('img' => "phone_button", 'title' => "Osoby kontaktowe", "akcja_link" => "?modul=kontakty&cid={$_GET['id']}", 'target' => false);
                }
                if($this->Uzytkownik->IsAdmin()){
                    $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
                }
		return $Akcje;
	}

        function ShowActionsList($AkcjeNaLiscie, $Element){
            $PaginParam = ($this->ParametrPaginacji > 0 ? "&pagin=$this->ParametrPaginacji" : "");
		foreach ($AkcjeNaLiscie as $Actions){
                    echo("<td class='ikona'>");
                            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                                if(isset($Actions['img'])){
                                    if(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs) || (in_array($_SESSION['id_oddzial'], $this->customerOddzialAccess($Element[$this->PoleID])) && isset($Actions['extra']))){
                                        if($Actions['akcja']){
                                            echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}$PaginParam\"><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else if($Actions['akcja_href']){
                                            echo "<a href=\"{$Actions['akcja_href']}id={$Element[$this->PoleID]}\" target='_blank'><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else if($Actions['akcja_link']){
                                            echo "<a href=\"{$Actions['akcja_link']}\"".($Actions['target'] ? " target='_blank'" : "")."><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                        }else{
                                            echo "<img src=\"images/buttons/{$Actions['img']}.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                        }
                                    }else{
                                            echo "<img src=\"images/buttons/{$Actions['img']}_grey.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                    }
                                }else{
                                    echo "&nbsp;";
                                }
                            }
                    echo "</td>\n";
		}
	}

        function CheckAccess($ID){
            $Dane = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            if($this->Uzytkownik->IsAdmin() || in_array($_SESSION['id_uzytkownik'], $this->customerUserAccess($ID)) || $this->Uzytkownik->GetUprawnieniaID() == 2){
               return true;
            }
            return false;
        }
        
        function DomyslnyWarunek(){
            return "";
	}

        function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        if($Pole == "id_uzytkownik"){
                                            $Opiekuni = $this->Baza->GetValues("SELECT id_klient FROM orderplus_klient_opiekun_handlowy WHERE id_uzytkownik = '$Wartosc'");
                                            $Opiekuni[] = -1;
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$this->PoleID IN(".implode(",",$Opiekuni).")";
                                        }else if($Pole == "id_oddzial"){
                                            $Opiekuni = $this->Baza->GetValues("SELECT id_klient FROM orderplus_klient_oddzial WHERE id_oddzial = '$Wartosc'");
                                            $Opiekuni[] = -1;
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$this->PoleID IN(".implode(",",$Opiekuni).")";
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
	
	function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			"nazwa" => 'Klient',
                        "identyfikator" => 'Identyfikator',
                        "miejscowosc" => 'Miasto',
                        "kod_kraju_id" => array('naglowek' => "Kraj", 'elementy' => $this->Kraje),
                        "id_uzytkownik" => array('naglowek' => "Opiekun"),
                        "branza_crm_id" => array('naglowek' => "Branża", 'elementy' => $this->Branze)#,
                        #"id_oddzial" => array('naglowek' => "Oddział", 'elementy' => $this->Oddzialy),
		);
                $Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT * FROM $this->Tabela $Where ORDER BY nazwa",$this->ParametrPaginacji,$this->IloscNaStrone));
		return $Wynik;
	}

        function ZapiszDaneSzczegolowe($Wartosci, $Typ, $Pole){
            switch($Pole){
                case "haslo":
                    if($Wartosci[$Pole] != ""){
                        $Wartosci['client_haslo'] = $Wartosci[$Pole];
                        $Wartosci['client_haslo_hash'] = md5($Wartosci[$Pole]);
                    }
                    unset($Wartosci[$Pole]);
                    break;
                case "nip":
                    $Wartosci[$Pole] = Usefull::NipValidate($Wartosci[$Pole]);
                    break;
                
            }
            if($Typ == "lista_to_input"){
                if($Wartosci[$Pole]['id'] == "last"){
                    $TabelaZapis = ($Pole == "branza_crm_id" ? "branza" : "orderplus_klient_branza");
                    $PoleZapis = ($Pole == "branza_crm_id" ? "branza" : "branza_nazwa");
                    $this->Baza->Query("INSERT INTO $TabelaZapis SET $PoleZapis = '{$Wartosci[$Pole]['new']}'");
                    $ID = $this->Baza->GetLastInsertId();
                }else{
                    $ID = $Wartosci[$Pole]['id'];
                }
                unset($Wartosci[$Pole]);
                $Wartosci[$Pole] = $ID;
            }
            return $Wartosci;
        }

        function OperacjePrzedZapisem($Wartosci){
            $Wartosci['id_oddzial'] = ($this->Uzytkownik->GetUprawnieniaID() == 1 ? $Wartosci['id_oddzial'] : $_SESSION['id_oddzial']);
            if($this->WykonywanaAkcja == "dodawanie"){
                $Wartosci['dodal_uzytkownik'] = $_SESSION['id_uzytkownik'];
                $Wartosci['data_utworzenia'] = date("Y-m-d H:i:s");
            }else{
                $Wartosci['ostatnio_edytowal'] = $_SESSION['id_uzytkownik'];
            }
            return $Wartosci;
        }

        function GetList() {
            return $this->Baza->GetOptions("SELECT $this->PoleID, nazwa FROM $this->Tabela ORDER BY nazwa");
        }

        function GetListAktywni() {
            return $this->Baza->GetOptions("SELECT $this->PoleID, nazwa FROM $this->Tabela WHERE klient_status = '1' ORDER BY nazwa");
        }

        function GetEmaile($ID){
            $Emaile = $this->Baza->GetValue("SELECT emaile FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            $EmaileOs = $this->Baza->GetValues("SELECT mail FROM osoby_kontaktowe WHERE $this->PoleID = '$ID'");
            if($EmaileOs){
                $Emaile .= ",".implode(",",$EmaileOs);
            }
            return $Emaile;
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID)){
                    echo("<div style='float: left; display: inline;'>");
                        echo "<a href='klienci_bez_zadan.php' target='_blank' class='form-button'>klienci bez zadań</a>";
                        if($this->Uzytkownik->IsAdmin() || $this->Uzytkownik->GetUprawnieniaID() == 2){
                            echo "<a href='klienci_baza.php' target='_blank' class='form-button'>klienci baza</a>";
                        }
                    echo ("</div>");
                }else{
                    echo("<div style='float: left; display: inline;'>");
                        #echo "<a href='?modul=kontakty&akcja=dodawanie&cid=$ID' class='form-button'>dodaj kontakt</a>";
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if(!in_array($this->WykonywanaAkcja, array("dodawanie","dodaj_import","dodaj_export")) && is_null($ID) && !isset($_GET['did'])){
                include(SCIEZKA_SZABLONOW."filters-klienci.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
        }

	function IDPoprzedniego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy < (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) 
                                                ".(!$this->Uzytkownik->IsAdmin() ? " AND id_oddzial = '{$_SESSION['id_oddzial']}'" : "")."
                                                ".($this->Uzytkownik->GetUprawnieniaID() == 3 ? " AND id_uzytkownik = '{$_SESSION['id_uzytkownik']}'" : "")."
                                                ORDER BY $this->PoleNazwy DESC LIMIT 1");
	}

	function IDNastepnego($ID) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->Tabela WHERE $this->PoleNazwy > (SELECT $this->PoleNazwy FROM $this->Tabela WHERE $this->PoleID = '$ID' LIMIT 1) 
                                                ".(!$this->Uzytkownik->IsAdmin() ? " AND id_oddzial = '{$_SESSION['id_oddzial']}'" : "")."
                                                ".($this->Uzytkownik->GetUprawnieniaID() == 3 ? " AND id_uzytkownik = '{$_SESSION['id_uzytkownik']}'" : "")."
                                                ORDER BY $this->PoleNazwy ASC LIMIT 1");
	}

        function GetLastContact($ID){
            $_contact='';

            /*ostatni kontakt z użytkownikiem*/
            $_query = $this->Baza->GetValue("SELECT id_uzytkownik FROM powiazania_zdarzenia pz
                                                LEFT JOIN zdarzenia z ON(pz.Zdarzenia_id = z.id)
                                                WHERE z.data_zakonczenia IS NOT NULL AND pz.id_klient = '$ID'
                                                ORDER BY z.data_zakonczenia DESC LIMIT 1");

            $_contact.='Ostatni kontakt: <b>'.($_query!==false ? $this->Userzy[$_query] : 'brak').'</b><br />';

	/*najbliższe zadanie*/
            $_first = $this->Baza->GetData("SELECT temat, data_poczatek as data FROM zdarzenia z
                                                    LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                                    WHERE id_klient = '$ID' AND z.data_zakonczenia IS NULL
                                                        AND z.data_poczatek >= curdate()
                                                    ORDER BY data_poczatek DESC LIMIT 1");
            $_second = $this->Baza->GetData("SELECT temat, data_przypomnienia as data FROM zdarzenia z
                                                    LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                                    WHERE id_klient = '$ID' AND z.data_zakonczenia IS NULL
                                                        AND z.data_przypomnienia >= curdate()
                                                    ORDER BY data_przypomnienia DESC LIMIT 1");

                $_temp = $_first===false ? $_second : ($_second===false ? $_first : ($_first['data']<$_second['data'] ? $_first : $_second));
                if(isset($_temp['data']))
                {	$_tamp_arr=explode(' ',$_temp['data']);
                        $_temp['data']=$_tamp_arr[0];
                }
 
                $_contact.='Najbliższe zadanie: <b>'.($_temp===false ? 'nie ustalone' : $_temp['temat'].'&nbsp;&nbsp;['.$_temp['data'].']').'</b>';


                return $_contact;
        }

        function GetContactPersons($ID){
            return $this->Baza->GetRows("SELECT * FROM osoby_kontaktowe WHERE id_klient = '$ID'");
        }

        function GetZalaczniki($ID){
            $Zal = new Zalaczniki($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $Zal->PobierzZalacznikiKlienta($ID);
        }

        function WyswietlAJAX($Akcja){
            if($Akcja == "info-about-customer"){
                $this->InfoAboutCustomer($_POST['customer_id']);
            }
            if($Akcja == "get-action-list"){
                $Akcje = array();
                $Access = $this->CheckAccess($_POST['id']);
                if($_POST['id'] > 0 && !$Access && !in_array($_SESSION['id_oddzial'], $this->customerOddzialAccess($_POST['id']))){
                    exit;
                }
                $Akcje[] = array('title' => "Szczegóły", "akcja_href" => "?modul=klienci&akcja=szczegoly&");
                $Akcje[] = array('title' => "Karta klienta", "akcja_href" => "karta_klienta.php?", "_blank" => true);
                if($Access){
                    $Akcje[] = array('title' => "Edycja", "akcja_href" => "?modul=klienci&akcja=edycja&");
                }
                $this->ShowActionInPopup($Akcje, $_POST['id']);
            }
        }

        function InfoAboutCustomer($KlientID, $status = false){
            $customer = $this->Baza->GetData("SELECT k.*, p.potencjal FROM $this->Tabela k
                                                LEFT JOIN potencjal p ON(k.potencjal_id = p.id)
                                                WHERE k.id_klient = '$KlientID'");
            /*osoby kontaktowe*/
            $kontakt = $this->GetContactPersons($KlientID);

            /*link do szczegółów nt. klienta*/
            $link = "?modul=klienci&akcja=szczegoly&id=$KlientID";
            include(SCIEZKA_SZABLONOW."klient_desc.tpl.php");
        }

        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            if($Typ == "lista_to_input"){
                $Dane[$Pole]['id'] = $Dane[$Pole];
            }
            return $Dane;
        }

        function WykonajOperacjePoZapisie($Wartosci, $ID){
            if($this->WykonywanaAkcja == "dodawanie"){
                foreach($_SESSION['Kontakt'][$this->SID] as $Dane){
                    unset($Dane['id']);
                    $Dane['id_klient'] = $ID;
                    $Zapytanie = $this->Baza->PrepareInsert("osoby_kontaktowe", $Dane);
                    $this->Baza->Query($Zapytanie);
                }
                foreach($_SESSION['Zalacznik'][$this->SID] as $Dane){
                    unset($Dane['id']);
                    $Dane['id_klient'] = $ID;
                    $Zapytanie = $this->Baza->PrepareInsert("zalaczniki", $Dane);
                    $this->Baza->Query($Zapytanie);
                }
            }
        }

        function AkcjaDrukuj($ID){
            if($this->CheckAccess($ID) || in_array($_SESSION['id_oddzial'], $this->customerOddzialAccess($ID))){
                $PoleNaKarcie = $this->PobierzPolaNaKarte();
                $Dane = $this->PobierzDaneElementu($ID);
                $Dane['branza_crm_id'] = $this->Branze[$Dane['branza_crm_id']];
                $Siedziby = UsefullBase::GetSiedziby($this->Baza);
                $Dane['siedziba_id'] = $Siedziby[$Dane['siedziba_id']];
                $Dane['adres'] = $Dane['adres']."<br />".$Dane['kod_pocztowy']."<br />".$Dane['miejscowosc']."<br />".$this->Kraje[$Dane['kod_kraju_id']];
                foreach($Dane['id_oddzial'] as $ID){
                    $Opcje[] = $this->Oddzialy[$ID];
                }
                $Dane['id_oddzial'] = implode(", ", $Opcje);
                $Opcje = array();
                $Users = UsefullBase::GetUsers($this->Baza);
                foreach($Dane['id_uzytkownik'] as $ID){
                    $Opcje[] = $Users[$ID];
                }
                $Dane['id_uzytkownik'] = implode(", ", $Opcje);
                $Opcje = array();
                foreach($Dane['id_uzytkownik_op'] as $ID){
                    $Opcje[] = $Users[$ID];
                }
                $Dane['id_uzytkownik_op'] = implode(", ", $Opcje);
                $Opcje = array();
                if($Dane['adres_korespondencyjny']["check"] == "0"){
                    unset($PoleNaKarcie['fakturowanie']['adres_korespondencyjny']);
                }else{
                    $Dane['adres_korespondencyjny'] = nl2br($Dane['adres_korespondencyjny']["value"]);
                }
                $Waluty = Usefull::GetWaluty();
                $Dane['waluta_fakturowania'] = $Waluty[$Dane['waluta_fakturowania']];
                $Banki = Usefull::GetBanki();
                $Dane['kurs_waluty_bank'] = $Banki[$Dane['kurs_waluty_bank']];
                if($Dane['kurs_waluty_dzien']["check"] == "-1"){
                    $Dane['kurs_waluty_dzien'] = "inny<br />".nl2br($Dane['kurs_waluty_dzien']["value"]);
                }else{
                    $Dane['kurs_waluty_dzien'] = "z dnia załadunku";
                }
                if($Dane['opis_na_fakturze']['check'] == 0 && $Dane['dodatkowe_ustalenia']["check"] == 0){
                    unset($PoleNaKarcie['info_specjalne']);
                }else{
                    if($Dane['opis_na_fakturze']['check'] == 0){
                        unset($PoleNaKarcie['info_specjalne']['opis_na_fakturze']);
                    }else{
                       $Dane['opis_na_fakturze'] = nl2br($Dane['opis_na_fakturze']['value']);
                    }
                    if($Dane['dodatkowe_ustalenia']['check'] == 0){
                        unset($PoleNaKarcie['info_specjalne']['dodatkowe_ustalenia']);
                    }else{
                       $Dane['dodatkowe_ustalenia'] = nl2br($Dane['dodatkowe_ustalenia']['value']);
                    }
                }
                include(SCIEZKA_SZABLONOW."druki/karta-klienta.tpl.php");
            }
        }

        function PobierzPolaNaKarte(){
            $Pola['standard']['nazwa'] = "Nazwa klienta";
            $Pola['standard']['branza_crm_id'] = "Branża opis";
            $Pola['standard']['siedziba_id'] = "Siedziba";
            $Pola['standard']['adres'] = "Adres";
            $Pola['standard']['nip'] = "NIP";
            $Pola['standard']['os_kontaktowe'] = "Kontakt";
            $Pola['standard']['id_oddzial'] = "Oddział";
            $Pola['standard']['id_uzytkownik'] = "Opiekun handlowy";
            //$Pola['standard']['id_uzytkownik_op'] = "Opiekun operacyjny";
            $Pola['fakturowanie']['adres_korespondencyjny'] = "Adres korespondencyjny";
            $Pola['fakturowanie']['termin_platnosci_dni'] = "Termin płatności";
            $Pola['fakturowanie']['waluta_fakturowania'] = "Waluta fakturowania";
            $Pola['fakturowanie']['kurs_waluty_bank'] = "Kurs waluty (bank)";
            $Pola['fakturowanie']['kurs_waluty_dzien'] = "Kurs waluty (dzień)";
            $Pola['info_specjalne']['opis_na_fakturze'] = "opis na fakturze";
            $Pola['info_specjalne']['dodatkowe_ustalenia'] = "Ustalenia dotyczące indywidualnych preferencji i procedur";
            return $Pola; 
        }

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "id_uzytkownik"){
                $Opiekunowie = $this->Baza->GetValues("SELECT id_uzytkownik FROM orderplus_klient_opiekun_handlowy WHERE id_klient = '{$Element[$this->PoleID]}'");
                $OpShow = array();
                foreach($Opiekunowie as $OpID){
                    $OpShow[] = $this->Userzy[$OpID];
                }
                $Element[$Nazwa] = implode("<br />", $OpShow);
            }
            echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
        }
        
	function AkcjeNiestandardowe($ID){
            if($this->WykonywanaAkcja == "import"){
                $this->AkcjaImport();
            }else{
                $this->AkcjaLista();
            }
	}
        
        function AkcjaImport(){
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                $errors = array();
                $contents= file_get_contents($_FILES['importowane_dane']['tmp_name']);
                $lines = explode("<br />", nl2br($contents));
                $dates = array();
                $min_date = false;
                $max_date = false;
                $queries = array();
                foreach($lines as $line_count => $line){
                    $line = trim($line);
                    if(!empty($line)){
                        $dane_line = explode(";", $line);
                        if(count($dane_line) > 4){
                            if(!empty($dane_line[0])){
                                if(!empty($dane_line[3])){
                                    if(!empty($dane_line[4])){
                                        $siedziba = (strtolower(iconv("windows-1250", "utf-8", $dane_line[1])) == "międzynarodowa" ? 2 : 1);
                                        $kod_kraju = intval($this->Baza->GetValue("SELECT id FROM kod_kraju WHERE kod = '".strtoupper($dane_line[2])."'"));
                                        if($kod_kraju > 0){
                                            $queries[] = array('values' => array(
                                                                                'nazwa' => iconv("windows-1250", "utf-8", $dane_line[0]),
                                                                                'siedziba_id' => $siedziba,
                                                                                'kod_kraju_id' => $kod_kraju,
                                                                                'kod_pocztowy' => $dane_line[3],
                                                                                'miejscowosc' => iconv("windows-1250", "utf-8", $dane_line[4]),
                                                                                'emaile' => $dane_line[5],
                                                                                'telefon' => $dane_line[6],
                                                                                'strona_www' => $dane_line[7]
                                                                            )
                                                                );
                                        }else{
                                            $errors[] = "Błędny kod kraju firmy <b>'{$dane_line[0]}'</b> w linii ".($line_count+1);
                                        }
                                    }else{
                                        $errors[] = "Brak miejscowości firmy <b>'{$dane_line[0]}'</b> w linii ".($line_count+1);
                                    }
                                }else{
                                    $errors[] = "Brak kodu pocztowego firmy <b>'{$dane_line[0]}'</b> w linii ".($line_count+1);
                                }
                            }else{
                                $errors[] = "Brak nazwy firmy w linii ".($line_count+1);
                            }
                        }else{
                            $errors[] = "Za mało danych firmy <b>'{$dane_line[0]}'</b> w linii ".($line_count+1);
                        }
                    }
                }
                if(count($queries) > 0 && count($errors) == 0){
                    foreach($queries as $query_elements){
                        $save_dane = $query_elements['values'];
                        //$where = $query_elements['where'];
                        $query = $this->Baza->PrepareInsert($this->Tabela, $save_dane);
                        //var_dump($query);
                        if($this->Baza->Query($query)){
                            $dostep['id_klient'] = $this->Baza->GetLastInsertID();
                            $dostep['id_oddzial'] = 3;
                            $insert2 = $this->Baza->PrepareInsert("orderplus_klient_oddzial", $dostep);
                            $this->Baza->Query($insert2);
                        }else{
                            $errors[] = "Wystąpił błąd Mysql przy imporcie klienta {$save_dane['nazwa']}";
                        }
                    }
                    if(count($errors) > 0){
                        Usefull::ShowKomunikatError("<b>Wystąpiły błędy, nie wszyscy klienci zostali zaimportowani. Błędy:</b><br />".implode("<br />", $errors)."<br /><br /><a href='$this->LinkPowrotu'><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót </a>");
                    }else{
                        Usefull::ShowKomunikatOK("<b>Baza klientów została zaimportowana</b><br /><br /><a href='$this->LinkPowrotu'><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót </a>");
                    }
                }else{
                    Usefull::ShowKomunikatError("<b>Wystąpiły błędy, klienci nie zostali zaimportowani z pliku. Błędy:</b><br />".implode("<br />", $errors)."<br /><br /><a href='$this->LinkPowrotu'><img src='images/arrow_undo.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'> Powrót </a>");
                }
                return;
            }
            include(SCIEZKA_SZABLONOW."forms/csv-import.tpl.php");
        }        

}
?>
