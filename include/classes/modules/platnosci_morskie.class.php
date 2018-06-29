<?php
/**
 * Moduł platnosci morskie
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class PlatnosciMorskie extends ModulBazowy {
        public $Klienci;
        public $Przewoznicy;
        public $PrzewoznicyId;
        public $Waluty;
        public $DataFaktury;
        public $Waluta;
        public $UzyteKoszty;
        public $UzyteFaktury;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->ModulyBezDodawania[] = $this->Parametr;
            $this->ModulyBezKasowanie[] = $this->Parametr;
            $this->Tabela = 'orderplus_sea_orders';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Płatności morskie';
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $this->PrzewoznicyId = UsefullBase::GetPrzewoznicyIds($this->Baza);
            $this->Waluty = UsefullBase::GetWaluty($this->Baza);
            $this->Filtry[] = array("opis" => "NIP", "nazwa" => "nip_search", "typ" => "tekst");
	}

	function &GenerujFormularz($Wartosci, $Mapuj = false) {
            $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
            $Formularz->DodajPole('numer_zlecenia', 'tekstowo', 'Zlecenie nr.', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('faktura_wlasna', 'tekst', 'Numer faktury klienta', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('faktura_przewoznika', 'tekst', 'Numer faktury przewoźnika', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('stawka_vat_klient', 'lista', 'Stawka VAT - klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetStawkiVat()));
            $Formularz->DodajPole('stawka_vat_przewoznik', 'lista', 'Stawka VAT - przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetStawkiVat()));
            $Formularz->DodajPole('kurs', 'kurs', 'Kurs waluty ', array('tabelka' => Usefull::GetFormStandardRow(), 'decimal' => true));
            $Formularz->DodajPole('data_wplywu', 'tekst_data', 'Data wpływu', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('termin_wlasny', 'tekst_data', 'Termin płatności klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('rzecz_zaplata_klienta', 'tekst_data', 'Rzeczywista zapłata klienta', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('termin_przewoznika', 'tekst_data', 'Termin płatności przewoźnika', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('planowana_zaplata_przew', 'tekst_data', 'Planowana zapłata dla przewoźnika', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('rzecz_zaplata_przew', 'tekst_data', 'Rzeczywista zapłata przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('waluta', 'hidden', null);
            if(is_array($Wartosci)){
                $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
                $Formularz->UstawOpisPola('kurs', "Kurs waluty {$Values['waluta']}", false);
            }
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
            return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31'".($this->Uzytkownik->CheckNoOddzial() ? "AND id_oddzial = {$_SESSION['id_oddzial']}" : "");
        }
	
	function PobierzListeElementow($Filtry = array(), $XLS = false) {
                if($XLS == false){
                    echo "<div id='div_ajax' style='display: none;'></div>";
                    if($this->Uzytkownik->IsAdmin()){
                    ?>
                    <div style='margin-bottom: 10px; color: #bcce00; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00; border-top: 1px solid #bbce00;'>
                        <a href='platnosci_morskie_xls.php' target='_blank' class='form-button'>Generuj plik XLS</a>
                    </div>
                    <?php
                    }
                }
                $Wynik = array(
                        "numer_zlecenia" => 'Numer Zlecenia',
                        "faktura_wlasna" => 'Nr naszej faktury',
                        "data_faktury_wlasnej" => 'Data wystawienia faktury dla klienta',
                        "id_klient" => array('naglowek' => 'Klient', 'elementy' => $this->Klienci),
                        "stawka_klient" => 'Kwota brutto<br />dla klienta');
                if($XLS){
                    $Wynik["stawka_klient_pln"] = 'Kwota brutto<br />dla klienta PLN';
                }
                        $Wynik["data_wplywu"] = array('naglowek' => 'Data wpływu', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["termin_wlasny"] = array('naglowek' => 'Termin płatności<br />klient', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["rzecz_zaplata_klienta"] = array('naglowek' => 'Rzeczywista zapłata<br />od klienta', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["opoznienie_klient"] = array('naglowek' => 'Opóźnienie', 'td_styl' => 'text-align: center');
                        $Wynik["id_przewoznik"] = array('naglowek' => 'Przewoźnik', 'elementy' => $this->Przewoznicy);
                        $Wynik["faktura_przewoznika"] = 'Nr faktury przewoźnika';
                        $Wynik["stawka_przewoznik"] = 'Kwota brutto<br />dla przewoźnika';
                        if($XLS){
                            $Wynik["stawka_przewoznik_pln"] = 'Kwota brutto<br />dla przewoźnika PLN';
                        }
                        $Wynik["termin_przewoznika"] = array('naglowek' => 'Termin płatności<br />przewoźnik', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["planowana_zaplata_przew"] = array('naglowek' => 'Planowana zapłata<br />dla przewoźnika', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["rzecz_zaplata_przew"] = array('naglowek' => 'Rzeczywista zapłata<br />dla przewoźnika', 'type' => 'date', 'td_styl' => 'text-align: center');
                        $Wynik["opoznienie_przewoznik"] = array('naglowek' => 'Opóźnienie', 'td_styl' => 'text-align: center');
                        $Wynik["fifo"] = array('naglowek' => 'Wskaźnik FIFO', 'td_styl' => 'text-align: center');
                        
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY numer_zlecenia_krotki ASC");
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
		return $Akcje;
	}

        function ObrobkaDanychLista($Elementy){
            foreach($Elementy as $Idx => $Element){
                $Elementy[$Idx]['faktury'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$Element[$this->PoleID]}'");
                $Elementy[$Idx]['koszty'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$Element[$this->PoleID]}'");
            }
            return $Elementy;
        }

        function ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie){
            $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
            $Faktury = $Element['faktury'];
            $Koszty = $Element['koszty'];
            $Check = array(count($Faktury),count($Koszty));
            $Rows = max($Check);
            if($Rows < 1){
                $Rows = 1;
            }
            $Rowspan = ($Rows > 1 ? " rowspan='$Rows'" : "");
            echo("<tr style='background-color: $KolorWiersza;' id='wiersz_$Licznik'>");
            echo("<td class='licznik' style='vertical-align: top;'$Rowspan>$Licznik</td>");
            print ("<td style='vertical-align: top;'$Rowspan><nobr>{$Element['numer_zlecenia']}</nobr></td>");
            $this->ShowRecords($Faktury, $Koszty, 0, $Styl);
            if($this->CzySaOpcjeWarunkowe){
                    $AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Element);
            }
            $this->ShowActionsList($AkcjeNaLiscie, $Element, $Rows);
            echo("</tr>");
            if($Rows > 1){
                for($i = 1; $i < $Rows; $i++){
                    echo "<tr style='background-color: $KolorWiersza;'>\n";
                        $this->ShowRecords($Faktury, $Koszty, $i, $Styl);
                    echo "</tr>\n";
                }
            }
        }

        function ShowActionsList($AkcjeNaLiscie, $Element, $Rows = 1){
		foreach ($AkcjeNaLiscie as $Actions){
                    echo("<td class='ikona'".($Rows > 1 ? " rowspan='$Rows'" : "").">");
                            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                                if(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs)){
                                    if($Actions['akcja']){
                                        echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}\"><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                    }else if($Actions['akcja_href']){
                                        echo "<a href=\"{$Actions['akcja_href']}id={$Element[$this->PoleID]}\" target='_blank'><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                    }else if($Actions['akcja_link']){
                                        echo "<a href=\"{$Actions['akcja_link']}\"><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                                    }else{
                                        echo "<img src=\"images/buttons/{$Actions['img']}.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                    }
                                }else{
                                        echo "<img src=\"images/buttons/{$Actions['img']}_grey.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                                }
                            }
                    echo "</td>\n";
		}
	}

        function ShowRecords($Faktury, $Koszty, $i){
            $Styl = " style='vertical-align: top;'";
            print ("<td$Styl>".(isset($Faktury[$i]) ? $Faktury[$i]['numer'] : "&nbsp;")."</td>");
            print ("<td$Styl>".(isset($Faktury[$i]) ? $Faktury[$i]['data_wystawienia'] : "&nbsp;")."</td>");
            print ("<td$Styl>".(isset($Faktury[$i]) ? $this->Klienci[$Faktury[$i]['id_klienta']] : "&nbsp;")."</td>");
            print ("<td$Styl>");
                if(isset($Faktury[$i])){
                    $Pozycje = mysql_query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '{$Faktury[$i]['id_faktury']}'");
                    $PosMany = 0;
                    $PosManyWaluta = 0;
                    while($Pos = mysql_fetch_array($Pozycje)){
                        if($this->Waluty[$Faktury[$i]['id_waluty']] == "PLN"){
                            $PosMany += $Pos['brutto'];
                        }else{
                            $PosMany += $Pos['brutto'] * $Faktury[$i]['kurs'];
                        }
                        $PosManyWaluta += $Pos['brutto'];
                    }
                    echo "<nobr>".number_format($PosManyWaluta,2,',',' ')." {$this->Waluty[$Faktury[$i]['id_waluty']]}</nobr><br />";
                    if($this->Waluty[$Faktury[$i]['id_waluty']] != "PLN"){
                        echo "<nobr>".number_format($PosMany,2,',',' ')." PLN</nobr><br />";
                        if($Faktury[$i]['kurs'] == 0){
                            echo "<br />Nie podano kursu waluty {$this->Waluty[$Faktury[$i]['id_waluty']]}!";
                        }
                    }
                        if(!in_array($Faktury[$i]['id_faktury'], $this->UzyteFaktury)){
                            $this->UzyteFaktury[] = $Faktury[$i]['id_faktury'];
                            $this->Sumowanie['stawka_klient'] += $PosMany;
                        }
                    }else{
                        echo "&nbsp;";
                    }
                print ("</td>");
                print ("<td$Styl>".(isset($Faktury[$i]) && $Faktury[$i]['data_wplywu'] != "0000-00-00" ? $Faktury[$i]['data_wplywu'] : "&nbsp;")."</td>");
                print ("<td$Styl>".(isset($Faktury[$i]) && $Faktury[$i]['termin_platnosci'] != "0000-00-00" ? $Faktury[$i]['termin_platnosci'] : "&nbsp;")."</td>");
                print ("<td$Styl>".(isset($Faktury[$i]) && $Faktury[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Faktury[$i]['rzeczywista_zaplata'] : "&nbsp;")."</td>");
                print ("<td$Styl>");
                    if(isset($Faktury[$i])){
                        Usefull::PokazOpoznienie($Faktury[$i]['termin_platnosci'], $Faktury[$i]['rzeczywista_zaplata']);
                    }
                echo ("</td>");
                print ("<td$Styl>".(isset($Koszty[$i]) ? $this->Przewoznicy[$Koszty[$i]['id_przewoznik']] : "&nbsp;")."</td>");
                print ("<td$Styl>".(isset($Koszty[$i]) ? $Koszty[$i]['nr_faktury'] : "&nbsp;")."</td>");
                print ("<td$Styl>");
                    if(isset($Koszty[$i])){
                        $Brutto1 = $Koszty[$i]['koszt_kwota_1']*(1+(intval($Koszty[$i]['stawka_vat'])/100));
                        $Brutto2 = $Koszty[$i]['koszt_kwota_2']*(1+(intval($Koszty[$i]['stawka_vat_2'])/100));
                        $Brutto = $Brutto1 + $Brutto2;
                        $Kwota = ($Koszty[$i]['waluta'] > 1 ? $Brutto * $Koszty[$i]['kurs'] : $Brutto);
                        echo number_format($Brutto,2,',',' ')." {$this->Waluty[$Koszty[$i]['waluta']]}<br />";
                        if($this->Waluty[$Koszty[$i]['waluta']] != "PLN"){
                            echo "<nobr>".number_format($Kwota,2,',',' ')." PLN</nobr><br />";
                            if($Koszty[$i]['kurs'] == 0){
                                echo "<br />Nie podano kursu waluty {$this->Waluty[$Koszty[$i]['waluta']]}!";
                            }
                        }
                        if(!in_array($Koszty[$i]['id_koszt'], $this->UzyteKoszty)){
                            $this->UzyteKoszty[] = $Koszty[$i]['id_koszt'];
                            $this->Sumowanie['stawka_przewoznik'] += $Kwota;
                        }
                    }else{
                        echo "&nbsp;";
                    }
                print ("</td>");
                print ("<td$Styl>".(isset($Koszty[$i]) && $Koszty[$i]['termin_platnosci'] != "0000-00-00" ? $Koszty[$i]['termin_platnosci'] : "&nbsp;")."</td>");
                print ("<td$Styl>".(isset($Koszty[$i]) && $Koszty[$i]['planowana_zaplata_przew'] != "0000-00-00" ? $Koszty[$i]['planowana_zaplata_przew'] : "&nbsp;")."</td>"); 
                print ("<td$Styl>".(isset($Koszty[$i]) && $Koszty[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Koszty[$i]['rzeczywista_zaplata'] : "&nbsp;")."</td>");
                print ("<td$Styl>");
                    if(isset($Koszty[$i])){
                        Usefull::PokazOpoznienie($Koszty[$i]['termin_platnosci'], $Koszty[$i]['rzeczywista_zaplata']);
                    }
                echo ("</td>");
                print ("<td$Styl>");
                    if(isset($Faktury[$i]) && $Faktury[$i]['data_wystawienia'] && $Faktury[$i]['data_wplywu'] != "0000-00-00"){
                        $fullDays = Usefull::ObliczIloscDniMiedzyDatami($Faktury[$i]['data_wystawienia'], $Faktury[$i]['data_wplywu']);
                        echo $fullDays;
                    }else{
                        echo "&nbsp;";
                    }
                echo ("</td>");
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
            return $Wartosci;
        }

        function AkcjaEdycja($ID){
            if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['nowy'] == "nowy"){
                if($this->Zapisz($ID)){
                    $this->ShowOK();
                }else{
                    Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    $zlecenie = $this->PobierzDaneElementu($ID);
                    $StawkiVat = Usefull::GetStawkiVat();
                    $Statusy = Usefull::StatusyPlatnosciAirSea();
                    $StatusyKlient = Usefull::StatusyPlatnosciKlient();
                    include(SCIEZKA_SZABLONOW."forms/platnosci-morskie-form.tpl.php");
                }
            }else{
                $zlecenie = $this->PobierzDaneElementu($ID);
                $Faktury = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '$ID'");
                $Koszty = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '$ID'");
                $StawkiVat = Usefull::GetStawkiVat();
                $Statusy = Usefull::StatusyPlatnosciAirSea();
                $StatusyKlient = Usefull::StatusyPlatnosciKlient();
                include(SCIEZKA_SZABLONOW."forms/platnosci-morskie-form.tpl.php");
            }
        }

        function Zapisz($ID){
            $Fak = $_POST['Faktury'];
            $Costs = $_POST['Koszty'];
            foreach($Fak as $FID => $Faktura){
                $Zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders_faktury", $Faktura, array("id_faktury" => $FID));
                $this->Baza->Query($Zapytanie);
            }
            foreach($Costs as $CID => $Koszt){
                if(isset($Koszt['koszt_kwota_1'])){
                    $Koszt['koszt_kwota_1'] = str_replace(",", ".", $Koszt['koszt_kwota_1']);
                }
                if(isset($Koszt['koszt_kwota_2'])){
                    $Koszt['koszt_kwota_2'] = str_replace(",", ".", $Koszt['koszt_kwota_2']);
                }
                $Zap2 = $this->Baza->PrepareUpdate("orderplus_sea_orders_koszty", $Koszt, array("id_koszt" => $CID));
                $this->Baza->Query($Zap2);
            }
            return true;
        }

        function DodatkoweFiltryDoKolumn($Pola, $Elementy, $AkcjeNaLiscie){
            $FakturyPrzewoznikow['brak'] = '-- brak faktury --';
            $FiltrJest = false;
            if(isset($_POST['filtry'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = $_POST;
            }
            if(isset($_POST['czysc_filtry'])){
                unset($_SESSION[$this->Parametr]['filtry_kolumn']);
            }
            if(!isset($_SESSION[$this->Parametr]['filtry_kolumn'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = array();
            }
            ### Gdy dodajemy sortowanie to wyświetlamy rekordy 1:1 ###
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty']) || isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'])
                || $_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id']){
                $check_sort = false;
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'] as $sort){
                    if($sort != ""){
                        $check_sort = true;
                        break;
                    }
                }
                if(!$check_sort){
                   foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $sort){
                        if($sort != ""){
                            $check_sort = true;
                            break;
                        }
                   }       
                }

                if(!$check_sort){
                   foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $sort){
                        if($sort != ""){
                            $check_sort = true;
                            break;
                        }
                   }
                }

                if($check_sort){
                    $StareElementy = $Elementy;
                    $Elementy = array();
                    foreach($StareElementy as $NowyElement){
                        if($NowyElement['koszty'] == false){
                            $NowyElement['koszty'] = array();
                        }
                        if($NowyElement['faktury'] == false){
                            $NowyElement['faktury'] = array();
                        }
                        if(count($NowyElement['koszty']) == 0 && count($NowyElement['faktury']) == 0){
                            $wstaw_rekord = array();
                            $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                            $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                            $wstaw_rekord['koszty'] = array();
                            $wstaw_rekord['faktury'] = array();
                            $Elementy[] = $wstaw_rekord;
                            continue;
                        }
                        if(count($NowyElement['koszty']) > count($NowyElement['faktury'])){
                            $Array1 = $NowyElement['koszty'];
                            $Array2 = $NowyElement['faktury'];
                            $one_key = "koszty";
                            $second_key = "faktury";
                        }else{
                            $Array1 = $NowyElement['faktury'];
                            $Array2 = $NowyElement['koszty'];
                            $one_key = "faktury";
                            $second_key = "koszty";
                        }
                        foreach($Array1 as $dane_array_1){
                            if(count($Array2) == 0){
                                $wstaw_rekord = array();
                                $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                                $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                                $wstaw_rekord[$one_key][] = $dane_array_1;
                                $wstaw_rekord[$second_key] = array();
                                $Elementy[] = $wstaw_rekord;
                                continue;
                            }
                            foreach($Array2 as $dane_array_2){
                                $wstaw_rekord = array();
                                $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                                $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                                $wstaw_rekord[$one_key][] = $dane_array_1;
                                $wstaw_rekord[$second_key][] = $dane_array_2;
                                $Elementy[] = $wstaw_rekord;
                            }
                        }
                    }
                }
            }
            ### <END> ###
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'])){
                $sort_one_key = false;
                $sort_one_how = false;
                $sort_two_key = false;
                $sort_two_how = false;
                ## ustawiamy max 2 parametry wg. których sortujemy ##
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'] as $Pole => $Wartosc){
                    if($Wartosc != ""){
                        if($sort_one_key != false && $sort_two_key != false){
                            break;
                        }
                        if($sort_one_key == false){
                            $sort_one_key = $Pole;
                            $sort_one_how = $Wartosc;
                            continue;
                        }
                        if($sort_two_key == false){
                            $sort_two_key = $Pole;
                            $sort_two_how = $Wartosc;
                            continue;
                        }
                    }
                }
                ## jeżeli są sorty to robimy tablice do sortowania
                if($sort_one_key != false){
                    foreach($Elementy as $Idx => $Dane){
                        $sort_one_array[] = $this->GetSortElementByKey($Dane, $sort_one_key);
                        if($sort_two_key != false){
                            $sort_two_array[] = $this->GetSortElementByKey($Dane, $sort_two_key);
                        }
                    }
                    if($sort_two_key != false){ 
                        array_multisort($sort_one_array, ($sort_one_how == "ASC" ? SORT_ASC : SORT_DESC), $sort_two_array, ($sort_two_how == "ASC" ? SORT_ASC : SORT_DESC),$Elementy);
                    }else{
                        array_multisort($sort_one_array, ($sort_one_how == "ASC" ? SORT_ASC : SORT_DESC), $Elementy);
                    }
                }
            }
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'])){
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $Pole => $Wartosc){
                    if($Wartosc != ""){
                        foreach($Elementy as $Idx => $Dane){
                            if($Pole == "faktura_przewoznika" && $Wartosc == "brak"){
                                $Wartosc = "";
                            }
                            if($Pole == "faktura_przewoznika"){
                                if($Dane['koszty'][0]['nr_faktury'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else{
                                if($Dane[$Pole] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }
                        }
                        $FiltrJest = true;
                        break;
                    }
                }
            }
            if(!$FiltrJest && isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'])){
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $Pole => $Wartosc){
                    if(intval($Wartosc) > 0){
                        foreach($Elementy as $Idx => $Dane){
                            if($Pole == "id_przewoznik"){
                                if($Dane['koszty'][0]['id_przewoznik'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else if($Pole == "id_klient"){
                                if($Dane['faktury'][0]['id_klienta'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else{
                                if($Dane[$Pole] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }
                        }
                        break;
                    }
                }
            }
            foreach($Elementy as $Dane){
                $NumeryZlecen[$Dane['numer_zlecenia']] = $Dane['numer_zlecenia'];
                foreach($Dane['koszty'] as $koszty){
                    $FakturyPrzewoznikow[$koszty['nr_faktury']] = $koszty['nr_faktury'];
                    $Przewoznicy[$koszty['id_przewoznik']] = $this->Przewoznicy[$koszty['id_przewoznik']];
                }
                foreach($Dane['faktury'] as $faktury){
                    $Klienci[$faktury['id_klienta']] = $this->Klienci[$faktury['id_klienta']];
                }
            }
            asort($NumeryZlecen);
            asort($FakturyPrzewoznikow);
            asort($Klienci);
            asort($Przewoznicy);
            $Filtry['numer_zlecenia'] = array("type" => "filtr", 'elementy' => $NumeryZlecen);
            $Filtry['faktura_wlasna'] = array("type" => "sort");
            $Filtry['data_faktury_wlasnej'] = array("type" => "sort");
            $Filtry['data_wplywu'] = array("type" => "sort");
            $Filtry['termin_wlasny'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_klienta'] = array("type" => "sort");
            $Filtry['faktura_przewoznika'] = array("type" => "filtr", "elementy" => $FakturyPrzewoznikow);
            $Filtry['termin_przewoznika'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_przew'] = array("type" => "sort");
            $Filtry['planowana_zaplata_przew'] = array("type" => "sort");
            $Filtry['id_klient'] = array("type" => "filtr_id", 'elementy' => $Klienci, 'dodatki' => "style='width: 220px;'");
            $Filtry['id_przewoznik'] = array("type" => "filtr_id", 'elementy' => $Przewoznicy, 'dodatki' => "style='width: 220px;'");
            include(SCIEZKA_SZABLONOW."filtry-kolumn.tpl.php");
            return $Elementy;
        }
        
        function DodatkoweFiltryDoKolumnXLS($Elementy){
            $FiltrJest = false;
            if(!isset($_SESSION[$this->Parametr]['filtry_kolumn'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = array();
            }
            ### Gdy dodajemy sortowanie to wyświetlamy rekordy 1:1 ###
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty']) || isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'])
                || $_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id']){
                $check_sort = false;
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'] as $sort){
                    if($sort != ""){
                        $check_sort = true;
                        break;
                    }
                }
                if(!$check_sort){
                   foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $sort){
                        if($sort != ""){
                            $check_sort = true;
                            break;
                        }
                   }       
                }

                if(!$check_sort){
                   foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $sort){
                        if($sort != ""){
                            $check_sort = true;
                            break;
                        }
                   }
                }

                if($check_sort){
                    $StareElementy = $Elementy;
                    $Elementy = array();
                    foreach($StareElementy as $NowyElement){
                        if($NowyElement['koszty'] == false){
                            $NowyElement['koszty'] = array();
                        }
                        if($NowyElement['faktury'] == false){
                            $NowyElement['faktury'] = array();
                        }
                        if(count($NowyElement['koszty']) == 0 && count($NowyElement['faktury']) == 0){
                            $wstaw_rekord = array();
                            $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                            $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                            $wstaw_rekord['koszty'] = array();
                            $wstaw_rekord['faktury'] = array();
                            $Elementy[] = $wstaw_rekord;
                            continue;
                        }
                        if(count($NowyElement['koszty']) > count($NowyElement['faktury'])){
                            $Array1 = $NowyElement['koszty'];
                            $Array2 = $NowyElement['faktury'];
                            $one_key = "koszty";
                            $second_key = "faktury";
                        }else{
                            $Array1 = $NowyElement['faktury'];
                            $Array2 = $NowyElement['koszty'];
                            $one_key = "faktury";
                            $second_key = "koszty";
                        }
                        foreach($Array1 as $dane_array_1){
                            if(count($Array2) == 0){
                                $wstaw_rekord = array();
                                $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                                $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                                $wstaw_rekord[$one_key][] = $dane_array_1;
                                $wstaw_rekord[$second_key] = array();
                                $Elementy[] = $wstaw_rekord;
                                continue;
                            }
                            foreach($Array2 as $dane_array_2){
                                $wstaw_rekord = array();
                                $wstaw_rekord['id_zlecenie'] = $NowyElement['id_zlecenie'];
                                $wstaw_rekord['numer_zlecenia'] = $NowyElement['numer_zlecenia'];
                                $wstaw_rekord[$one_key][] = $dane_array_1;
                                $wstaw_rekord[$second_key][] = $dane_array_2;
                                $Elementy[] = $wstaw_rekord;
                            }
                        }
                    }
                }
            }
            ### <END> ###
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'])){
                $sort_one_key = false;
                $sort_one_how = false;
                $sort_two_key = false;
                $sort_two_how = false;
                ## ustawiamy max 2 parametry wg. których sortujemy ##
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty'] as $Pole => $Wartosc){
                    if($Wartosc != ""){
                        if($sort_one_key != false && $sort_two_key != false){
                            break;
                        }
                        if($sort_one_key == false){
                            $sort_one_key = $Pole;
                            $sort_one_how = $Wartosc;
                            continue;
                        }
                        if($sort_two_key == false){
                            $sort_two_key = $Pole;
                            $sort_two_how = $Wartosc;
                            continue;
                        }
                    }
                }
                ## jeżeli są sorty to robimy tablice do sortowania
                if($sort_one_key != false){
                    foreach($Elementy as $Idx => $Dane){
                        $sort_one_array[] = $this->GetSortElementByKey($Dane, $sort_one_key);
                        if($sort_two_key != false){
                            $sort_two_array[] = $this->GetSortElementByKey($Dane, $sort_two_key);
                        }
                    }
                    if($sort_two_key != false){ 
                        array_multisort($sort_one_array, ($sort_one_how == "ASC" ? SORT_ASC : SORT_DESC), $sort_two_array, ($sort_two_how == "ASC" ? SORT_ASC : SORT_DESC),$Elementy);
                    }else{
                        array_multisort($sort_one_array, ($sort_one_how == "ASC" ? SORT_ASC : SORT_DESC), $Elementy);
                    }
                }
            }
            if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'])){
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $Pole => $Wartosc){
                    if($Wartosc != ""){
                        foreach($Elementy as $Idx => $Dane){
                            if($Pole == "faktura_przewoznika" && $Wartosc == "brak"){
                                $Wartosc = "";
                            }
                            if($Pole == "faktura_przewoznika"){
                                if($Dane['koszty'][0]['nr_faktury'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else{
                                if($Dane[$Pole] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }
                        }
                        $FiltrJest = true;
                        break;
                    }
                }
            }
            if(!$FiltrJest && isset($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'])){
                foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $Pole => $Wartosc){
                    if(intval($Wartosc) > 0){
                        foreach($Elementy as $Idx => $Dane){
                            if($Pole == "id_przewoznik"){
                                if($Dane['koszty'][0]['id_przewoznik'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else if($Pole == "id_klient"){
                                if($Dane['faktury'][0]['id_klienta'] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }else{
                                if($Dane[$Pole] != $Wartosc){
                                    unset($Elementy[$Idx]);
                                }
                            }
                        }
                        break;
                    }
                }
            }
            return $Elementy;
        }

        function AkcjaDrukuj($ID){
            //$this->Baza->EnableLog();
            $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $Klienci = UsefullBase::GetKlienci($this->Baza);
            $Oddzialy = array('sea' => 'OVS');
            if($_SESSION['id_oddzial'] != 10){
                $Oddzialy['air'] = 'AIR';
            }
            $Rodzaje = array("termin-platnosci" => "Raport Termin płatności", 'planowana' => "Raport Planowana zapłata", "rzeczywista" => "Raport Rzeczywista zapłata");
            include(SCIEZKA_SZABLONOW.'druki/raport_platnosci_dla_przewoznikow_airsea.tpl.php');
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID)){
                    echo("<div style='float: left;'>");
                        echo "<div style='float: left; color: #bcce00; font-weight: bold;'>RAPORTY:<br /><br /></div>";
                        echo "<div style='clear: both;'></div>\n";
                        if($this->Uzytkownik->DostepDoRaportu('platnosci_dla_przewoznikow')){
                            echo "<a href='raporty_platnosci_dla_przewoznikow_airsea.php' target='_blank' class='form-button'>Raport płatności dla przewoźników</a>";
                        }
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if($this->WykonywanaAkcja == "lista"){
            if(!in_array($this->WykonywanaAkcja, array("dodawanie","dodaj_import","dodaj_export")) && is_null($ID) && !isset($_GET['did'])){
                include(SCIEZKA_SZABLONOW."filters.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
            }else{
                echo "<div style='clear: both'></div>\n";
            }
        }

        function GetSortElementByKey($Dane, $key){
            if($key == "faktura_wlasna"){
                return $Dane['faktury'][0]['numer'];
            }
            if($key == 'data_faktury_wlasnej'){
                return $Dane['faktury'][0]['data_wystawienia'];
            }
            if($key == 'data_wplywu'){
                return $Dane['faktury'][0]['data_wplywu'];
            }
            if($key == 'termin_wlasny'){
                return $Dane['faktury'][0]['termin_platnosci'];
            }
            if($key == 'rzecz_zaplata_klienta'){
                return $Dane['faktury'][0]['rzeczywista_zaplata'];
            }
            if($key == 'termin_przewoznika'){
                return $Dane['koszty'][0]['termin_platnosci'];
            }
            if($key == 'rzecz_zaplata_przew'){
                return $Dane['koszty'][0]['rzeczywista_zaplata'];
            }
            if($key == 'planowana_zaplata_przew'){
                return $Dane['koszty'][0]['planowana_zaplata_przew'];
            }
            return false;
        }

        function ShowNaglowekDrukuj($Akcja){
            if($Akcja == "airsea_raport"){
                include(SCIEZKA_SZABLONOW."naglowek_drukuj_raporty.tpl.php");
            }else{
                parent::ShowNaglowekDrukuj($Akcja);
            }
        }

}
?>
