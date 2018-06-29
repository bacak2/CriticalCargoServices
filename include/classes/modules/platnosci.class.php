<?php
/**
 * Moduł platnosci
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Platnosci extends ModulBazowy {
        public $Klienci;
        public $Przewoznicy;
        public $DataFaktury;
        public $Waluta;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            if($Parametr == "platnosci_nowe"){
                $this->ParametrPaginacji = isset($_GET['pagin']) ? $_GET['pagin'] : (isset($_SESSION['sort']["tabela_rozliczen_nowa"]['pagin']) ? $_SESSION['sort']["tabela_rozliczen_nowa"]['pagin'] : 0);
                $this->LinkPowrotu = "?modul=tabela_rozliczen_nowa".($this->ParametrPaginacji > 0 ? "&pagin=$this->ParametrPaginacji" : "");
                $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            }
            $this->ModulyBezDodawania[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->Tabela = 'orderplus_zlecenie';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Płatności';
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $this->Filtry[] = array("opis" => "NIP", "nazwa" => "nip_search", "typ" => "tekst");
	}

	function &GenerujFormularz($Wartosci, $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('numer_zlecenia', 'tekstowo', 'Zlecenie nr.', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1, 'tr_end' => 1, 'td_colspan' => 3)));
            $Formularz->DodajPole('faktura_wlasna', 'tekst', 'Numer faktury klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1)));
            $Formularz->DodajPole('faktura_przewoznika', 'tekst', 'Numer faktury przewoźnika', array('tabelka' => array('td_start' => 1, 'th_show' => 1)));
            $Formularz->DodajPole('waluta_faktura_przewoznik', 'lista', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'elementy' => Usefull::GetWaluty()));
            $Formularz->DodajPole('stawka_vat_klient', 'lista', 'Stawka VAT - klient', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'elementy' => Usefull::GetStawkiVat()));
            $Formularz->DodajPole('stawka_vat_przewoznik', 'lista', 'Stawka VAT - przewoźnik', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'elementy' => Usefull::GetStawkiVat()));
            $Formularz->DodajPole('kurs', 'kurs', 'Kurs waluty EUR (klient)', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'decimal' => true));
            $Formularz->DodajPole('kurs_przewoznik', 'kurs', 'Kurs waluty EUR (przewoźnik)', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'decimal' => true, 'kurs_param' => 'pobierz_kurs_przewoznik'));
            $Formularz->DodajPole('pusta_1', 'xxxx', null, array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'td_end' => 1, 'td_colspan' => 2)));
            $Formularz->DodajPole('data_wplywu', 'tekst_data', 'Data wpływu', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'id' => 'data_wplywu', 'zmiana' => true, 'atrybuty' => array('style' => 'width: 100px;', 'onchange' => 'ObliczTerminPrzewoznika()')));
            $Formularz->DodajPole('termin_wlasny', 'tekst_data', 'Termin płatności klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('termin_przewoznika', 'tekst_data', 'Termin płatności przewoźnika', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'id' => 'termin_przewoznika','atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('planowana_zaplata_klient', 'tekst_data', 'Planowana zapłata klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('planowana_zaplata_przew', 'tekst_data', 'Planowana zapłata dla przewoźnika', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('rzecz_zaplata_klienta', 'tekst_data', 'Rzeczywista zapłata klienta', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 100px;')));            
            $Formularz->DodajPole('rzecz_zaplata_przew', 'tekst_data', 'Rzeczywista zapłata przewoźnik', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('platnosci_komentarz_klient', 'tekst_dlugi', 'Komentarz - klient', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1)));
            $Formularz->DodajPole('platnosci_komentarz', 'tekst_dlugi', 'Komentarz - przewoźnik', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1)));
            $Formularz->DodajPole('platnosci_status_klient', 'lista', 'Status - klient', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), "elementy" => Usefull::StatusyPlatnosciKlient()));
            $Formularz->DodajPole('platnosci_status', 'lista', 'Status - przewoźnik', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), "elementy" => Usefull::StatusyPlatnosci()));
            $Formularz->DodajPole('pozostalo_klient', 'tekst', 'Pozostało', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'decimal' => true, 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('pozostalo_przewoznik', 'tekst', 'Pozostało', array('tabelka' => array('tr_end' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1), 'decimal' => true, 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('waluta', 'hidden', null);
            $Formularz->DodajPole('termin_platnosci_przewoznik_dni', 'hidden', null, array("id" => 'termin_platnosci_dni'));
            if(is_array($Wartosci)){
                $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
                $Formularz->UstawOpisPola('kurs', "Kurs waluty {$Values['waluta']} (klient)", false);
                $Formularz->UstawOpisPola('kurs_przewoznik', "Kurs waluty {$Values['waluta']} (przewoźnik)", false);
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1, 'tr_end' => 1, 'td_colspan' => 4), "elementy" => $this->PrzyciskiFormularza));
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
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "nip_search"){
                                            $Clients = UsefullBase::GetPrzewoznicyByNip($Baza, $Wartosc);
                                            $Clients[] = -1;
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."id_przewoznik IN(".imlode(",",$Clients).")";
                                        }else{
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                        }
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function DomyslnyWarunek(){
            return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31'".($this->Uzytkownik->CheckNoOddzial() ? "AND id_oddzial = {$_SESSION['id_oddzial']}" : "");
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"numer_zlecenia" => array('naglowek' => 'Numer zlecenia', 'td_styl' => 'white-space: nowrap;'),
                        "faktura_wlasna" => 'Nr naszej faktury',
                        "data_faktury_wlasnej" => 'Data wystawienia faktury dla klienta',
                        "id_przewoznik" => array('naglowek' => 'Przewoźnik', 'elementy' => $this->Przewoznicy),
                        "faktura_przewoznika" => 'Nr faktury przewoźnika',
                        "id_klient" => array('naglowek' => 'Klient', 'elementy' => $this->Klienci),
                        "stawka_przewoznik" => 'Kwota brutto<br />dla przewoźnika',
                        "stawka_klient" => 'Kwota brutto<br />dla klienta',
                        "data_wplywu" => array('naglowek' => 'Data wpływu', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "termin_wlasny" => array('naglowek' => 'Termin płatności<br />klient', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "rzecz_zaplata_klienta" => array('naglowek' => 'Rzeczywista zapłata<br />od klienta', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "opoznienie_klient" => array('naglowek' => 'Opóźnienie', 'td_styl' => 'text-align: center'),
                        "termin_przewoznika" => array('naglowek' => 'Termin płatności<br />przewoźnik', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "planowana_zaplata_przew" => array('naglowek' => 'Planowana zapłata<br />dla przewoźnika', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "rzecz_zaplata_przew" => array('naglowek' => 'Rzeczywista zapłata<br />dla przewoźnika', 'type' => 'date', 'td_styl' => 'text-align: center'),
                        "opoznienie_przewoznik" => array('naglowek' => 'Opóźnienie', 'td_styl' => 'text-align: center'),
                        "fifo" => array('naglowek' => 'Wskaźnik FIFO', 'td_styl' => 'text-align: center')
		);
                        
		$Where = $this->GenerujWarunki();
                $Sort = $this->GenerujSortowanie();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY $Sort");
		return $Wynik;
	}

        function GenerujSortowanie(){
            if(isset($_POST['sorty'])){
                foreach($_POST['sorty'] as $Pole => $Wartosc){
                    if($Wartosc != ""){
                        return "$Pole $Wartosc";
                    }
                }
            }
            return "numer_zlecenia_krotki ASC";
        }

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
		return $Akcje;
	}

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "data_faktury_wlasnej"){
                $this->DataFaktury = false;
                if($Element['faktura_wlasna'] != ""){
                    $this->DataFaktury = $this->Baza->GetValue("SELECT data_wystawienia FROM faktury WHERE numer = '{$Element['faktura_wlasna']}'");
                }
                $Element[$Nazwa] = $this->DataFaktury;
            }
            if($Nazwa == "stawka_przewoznik"){
                $StawkaVatPrzewoznik = (in_array(strtolower($Element['stawka_vat_przewoznik']), array("np","zw")) ? 0 :  $Element['stawka_vat_przewoznik']);
                $StawkaPrzewoznik = $Element['stawka_przewoznik']*(1+$StawkaVatPrzewoznik/100);
                print ("<td$Styl><nobr>" . number_format($StawkaPrzewoznik, 2, ',', ' ') . " {$Element['waluta']}</nobr>");
                    if ($Element['waluta'] != "PLN"){
                        if ($Element['kurs_przewoznik'] > 0){
                            echo("<br><nobr>" . number_format($StawkaPrzewoznik * $Element['kurs_przewoznik'], 2, ',', ' ') . " PLN</nobr>");
                        }else {
                            echo("<br>Nie podano kursu waluty {$Element['waluta']}!");
                        }
                    }
                print ("</td>");
                $this->Sumowanie['stawka_przewoznik'] += ($Element['waluta'] == "PLN" ? $StawkaPrzewoznik : ($StawkaPrzewoznik * $Element['kurs_przewoznik']));
            }else if($Nazwa == "stawka_klient"){
                $StawkaVatKlient= (in_array(strtolower($Element['stawka_vat_klient']), array("np","zw")) ? 0 :  $Element['stawka_vat_klient']);
                $StawkaKlient = $Element['stawka_klient']*(1+$StawkaVatKlient/100);
                print ("<td$Styl><nobr>" . number_format($StawkaKlient, 2, ',', ' ') . " {$Element['waluta']}</nobr>");
                    if ($Element['waluta'] != "PLN"){
                        if ($Element['kurs'] > 0){
                            echo("<br><nobr>" . number_format($StawkaKlient * $Element['kurs'], 2, ',', ' ') . " PLN</nobr>");
                        }else {
                            echo("<br>Nie podano kursu waluty {$Element['waluta']}!");
                        }
                    }
                    $this->Sumowanie['stawka_klient'] += ($Element['waluta'] == "PLN" ? $StawkaKlient : ($StawkaKlient * $Element['kurs']));
                print ("</td>");
            }else{
                echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
            }
        }

        function ShowSuma($NazwaPola){
            if($NazwaPola == "stawka_przewoznik"){
                return "Ogół kosztów: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
            }else if($NazwaPola == "stawka_klient"){
                return "Ogół sprzedaży: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
            }else{
                return $this->Sumowanie[$NazwaPola];
            }
        }

        function AkcjaDrukuj($ID){
            $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $Klienci = UsefullBase::GetKlienci($this->Baza);
            $Oddzialy = $this->Baza->GetOptions("SELECT id_oddzial, CONCAT(skrot,' ',prefix) as name FROM orderplus_oddzial WHERE id_oddzial IN(2,6,3,7,1,8,5)".($this->Uzytkownik->IsAdmin() == false ? " AND id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).")" : "")." ORDER BY field(id_oddzial,2,6,3,7,1,8,5)");
            $Rodzaje = array("termin-platnosci" => "Raport Termin płatności", 'planowana' => "Raport Planowana zapłata", "rzeczywista" => "Raport Rzeczywista zapłata");
            include(SCIEZKA_SZABLONOW.'druki/raport_platnosci_dla_przewoznikow.tpl.php');
        }

        function ShowNaglowekDrukuj(){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_raporty.tpl.php');
        }

        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
            $DaneDefault = $this->PobierzDaneElementu($ID);
            $Wartosci['numer_zlecenia'] = $DaneDefault['numer_zlecenia'];
            $Wartosci['termin_zaladunku'] = $DaneDefault['termin_zaladunku'];
            $Wartosci['id_klient'] = $DaneDefault['id_klient'];
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

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID)){
                    echo("<div style='float: left; display: inline;'>");
                        echo "<a href='raporty_platnosci_dla_przewoznikow.php' target='_blank' class='form-button'>Raport płatności dla przewoźników</a>";
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if($this->WykonywanaAkcja != "dodawanie" && is_null($ID)){
                include(SCIEZKA_SZABLONOW."filters.tpl.php");
            }
        }

        function DodatkoweFiltryDoKolumn($Pola, $Elementy, $AkcjeNaLiscie){
            foreach($Elementy as $Idx => $Dane){
                ### sprawdzenie i aktualizacja faktur jeżeli jest a nie wprowadzona ###
                if($Dane['faktura_wlasna'] == "" && $Dane['id_faktury'] > 0){
                    $FakturaSprawdz = $this->Baza->GetData("SELECT numer FROM faktury WHERE id_faktury = '{$Dane['id_faktury']}'");
                    if($FakturaSprawdz){
                        if($this->Baza->Query("UPDATE $this->Tabela SET faktura_wlasna = '{$FakturaSprawdz['numer']}' WHERE $this->PoleID = '{$Dane[$this->PoleID]}'")){
                            $Elementy[$Idx]['faktura_wlasna'] = $FakturaSprawdz['numer'];
                            $Dane['faktura_wlasna'] = $FakturaSprawdz['numer'];
                        }
                    }
                }

                $NumeryZlecen[$Dane['numer_zlecenia']] = $Dane['numer_zlecenia'];
                $FakturyWlasne[$Dane['faktura_wlasna']] = $Dane['faktura_wlasna'];
                $FakturyPrzewoznika[$Dane['faktura_przewoznika']] = $Dane['faktura_przewoznika'];
                $Klienci[$Dane['id_klient']] = $this->Klienci[$Dane['id_klient']];
                $Przewoznicy[$Dane['id_przewoznik']] = $this->Przewoznicy[$Dane['id_przewoznik']];
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
            asort($FakturyWlasne);
            asort($FakturyPrzewoznika);
            asort($Klienci);
            asort($Przewoznicy);
            $Filtry['numer_zlecenia'] = array("type" => "filtr", 'elementy' => $NumeryZlecen);
            $Filtry['faktura_wlasna'] = array("type" => "filtr", 'elementy' => $FakturyWlasne);
            $Filtry['id_przewoznik'] = array("type" => "filtr_id", 'elementy' => $Przewoznicy);
            $Filtry['faktura_przewoznika'] = array("type" => "filtr", 'elementy' => $FakturyPrzewoznika);
            $Filtry['id_klient'] = array("type" => "filtr_id", 'elementy' => $Klienci);
            $Filtry['data_wplywu'] = array("type" => "sort");
            $Filtry['termin_wlasny'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_klienta'] = array("type" => "sort");
            $Filtry['termin_przewoznika'] = array("type" => "sort");
            $Filtry['planowana_zaplata_przew'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_przew'] = array("type" => "sort");
            include(SCIEZKA_SZABLONOW."filtry-kolumn.tpl.php");
            return $Elementy;
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $Dane["termin_platnosci_przewoznik_dni"] = intval($this->Baza->GetValue("SELECT termin_platnosci_dni FROM orderplus_szablon WHERE id_szablon = '{$Dane['id_szablon']}'"));
            return $Dane;
        }

        function  ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
            unset($Wartosci['termin_platnosci_przewoznik_dni']);
            $FakturaID = $this->Baza->GetValue("SELECT id_faktury FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            if($FakturaID > 0 && $Wartosci['data_wplywu'] != '0000-00-00'){
                $DataFaktury = $this->Baza->GetValue("SELECT data_wystawienia FROM faktury WHERE id_faktury = '$FakturaID'");
                $Wartosci['fifo'] = Usefull::ObliczIloscDniMiedzyDatami($DataFaktury, $Wartosci['data_wplywu']);
            }
            if($Wartosci['rzecz_zaplata_klienta'] != '0000-00-00'){
                $Wartosci['opoznienie_klient'] = Usefull::PokazOpoznienie($Wartosci['termin_wlasny'], $Wartosci['rzecz_zaplata_klienta'], true);
            }
            if($Wartosci['rzecz_zaplata_przew'] != '0000-00-00'){
                $Wartosci['opoznienie_przewoznik'] = Usefull::PokazOpoznienie($Wartosci['termin_przewoznika'], $Wartosci['rzecz_zaplata_przew'], true);
            }
            return parent::ZapiszDaneElementu($Formularz, $Wartosci, $PrzeslanePliki, $ID);
        }

        function WyswietlAJAX($Akcja){
            if($Akcja == "get-action-list"){
                $Akcje = array();
                $Akcje[] = array('title' => "Edycja", "akcja_href" => "?modul=platnosci_nowe&akcja=edycja&");
                $this->ShowActionInPopup($Akcje, $_POST['id']);
            }
        }
}
?>
