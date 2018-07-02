<?php
/**
 * Moduł tabela rozliczen - nowa, połączone moduły tabela roliczeń, płatności, faktury
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class TabelaRozliczenNowa extends TabelaRozliczen {
        public $Users;
        public $Oddzialy;
        public $Korekty;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Oddzialy = UsefullBase::GetOddzialy($this->Baza);
            $this->Filtry = array();
            $this->Filtry[] = array("opis" => "NIP", "nazwa" => "nip_search", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Numer zlecenia", "nazwa" => "numer_zlecenia", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Faktura klient", "nazwa" => "faktura_wlasna", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Faktura przewoznik", "nazwa" => "faktura_przewoznika", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Znajdź trasę", "nazwa" => "trasa", "typ" => "trasa", "opcje" => $this->KodyKrajow, 'domyslna' => 'dowolne');
//            if($_SESSION['login'] == "artplusadmin"){
//                $Idki = $this->Baza->GetOptions("SELECT z.id_zlecenie, f.numer FROM orderplus_zlecenie z LEFT JOIN faktury f ON(f.id_faktury = z.id_faktury) WHERE f.numer != z.faktura_wlasna AND z.faktura_wlasna != '' AND z.termin_zaladunku >= '2012-01-01'");
//                foreach($Idki as $ZlecID => $Numer){
//                    $this->Baza->Query("UPDATE orderplus_zlecenie SET faktura_wlasna = '$Numer' WHERE id_zlecenie = '$ZlecID'");
//                }
//            }
            if(isset($_GET['ret']) && $_GET['ret'] == "sea"){
                $this->LinkPowrotu = "?modul=zlecenia_morskie_zlec";
            }
            if(isset($_GET['ret']) && $_GET['ret'] == "air"){
                $this->LinkPowrotu = "?modul=zlecenia_lotnicze_zlec";
            }
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
            $this->PaginBy = "array";
	}

        function DomyslnyWarunek(){
            $Where = false;
            if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
                for ($i = 0; $i < count($this->Filtry); $i++) {
                    $Pole = $this->Filtry[$i]['nazwa'];
                    if (isset($_SESSION['Filtry'][$Pole])) {
                        if(in_array($Pole, array("numer_zlecenia", "faktura_wlasna", "faktura_przewoznika"))){
                            $Where = true;
                            $_SESSION[$this->Parametr]['filtry_kolumn'] = array();
                        }
                    }
                }
            }
            return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0'".(!$Where ? " AND termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31'" : "").(!$this->Uzytkownik->IsAdmin() ? " AND id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).")" : "");
        }

        function PobierzListeElementow($Filtry = array(), $XLS = false) {
            $this->Users = UsefullBase::GetUsers($this->Baza);
            $Wynik = Usefull::GetTabelaRozliczenKolumny($XLS);
            $Wynik['typ_serwisu']['elementy'] = UsefullBase::GetTypySerwisu($this->Baza);
            //$Wynik['id_oddzial']['elementy'] = $this->Oddzialy;
            $Dostep = UsefullBase::PobierzDostepDoKolumn($this->Baza, $this->UserID);
            if($XLS){
                if($this->Uzytkownik->IsAdmin() || in_array("id_faktury", $Dostep)){
                    $Dostep[] = "data_wystawienia";
                }
                if($this->Uzytkownik->IsAdmin() || in_array("stawka_klient", $Dostep)){
                    $Dostep[] = "kurs";
                    $Dostep[] = "stawka_klient_pln";
                    $Dostep[] = "stawka_za_km_klient";
                    $Dostep[] = "stawka_za_km_klient_pln";
                }
                if($this->Uzytkownik->IsAdmin() || in_array("stawka_klient_brutto", $Dostep)){
                    $Dostep[] = "stawka_klient_brutto_pln";
                }
                if($this->Uzytkownik->IsAdmin() || in_array("stawka_przewoznik", $Dostep)){
                    $Dostep[] = "kurs_przewoznik";
                    $Dostep[] = "stawka_przewoznik_pln";
                    $Dostep[] = "stawka_za_km_przewoznik";
                    $Dostep[] = "stawka_za_km_przewoznik_pln";
                }
                if($this->Uzytkownik->IsAdmin() || in_array("stawka_przewoznik_brutto", $Dostep)){
                    $Dostep[] = "stawka_przewoznik_brutto_pln";
                }
            }
            if($XLS == false){
                echo "<script type='text/javascript' src='js/tabela-rozliczen.js'></script>";
                echo "<div id='div_ajax' style='display: none;'></div>";
                include(SCIEZKA_SZABLONOW."tabela-rozliczen-widoki.tpl.php");
            }
            foreach($Wynik as $IDx => $Dane){
                if(!$this->Uzytkownik->IsAdmin() && !in_array($IDx, $Dostep)){
                    unset($Wynik[$IDx]);
                }
            }
            if($XLS == false){
                foreach($Wynik as $Key => $Val){
                    if($Key != "id_faktury"){
                        $Wynik[$Key]['td_styl'] = "vertical-align: top;";
                    }
                    $CheckParam = explode(" ", $Val['td_class']);
                    $Ukryj = true;
                    foreach($CheckParam as $Param){
                        if($_SESSION['TabelaRozliczenWidok'][$Param]){
                            $Ukryj = false;
                        }
                    }
                    if($Ukryj){
                        $Wynik[$Key]['td_styl'] .= " display: none;";
                    }
                }
            }
            $Where = $this->GenerujWarunki();
            $Sort = $this->GenerujSortowanie();
			//echo "SELECT * FROM $this->Tabela a $Where ORDER BY $Sort";
            $this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY $Sort");
            return $Wynik;
	}

        function ObrobkaDanychLista($Elementy){
            foreach($Elementy as $Idx => $Element){
                ### Obliczenie stawki dla klienta brutto ###
                $StawkaVatKlient= (in_array(strtolower($Element['stawka_vat_klient']), array("np","zw")) ? 0 :  $Element['stawka_vat_klient']);
                $StawkaKlient = $Element['stawka_klient']*(1+$StawkaVatKlient/100);
                $Elementy[$Idx]['stawka_klient_brutto'] = $StawkaKlient;
                ### Obliczanie stawki dla przewoźników brutto ###
                $StawkaVatPrzewoznik = (in_array(strtolower($Element['stawka_vat_przewoznik']), array("np","zw")) ? 0 :  $Element['stawka_vat_przewoznik']);
                $StawkaPrzewoznik = $Element['stawka_przewoznik']*(1+$StawkaVatPrzewoznik/100);
                $Elementy[$Idx]['stawka_przewoznik_brutto'] = $StawkaPrzewoznik;
                ### Oblicz marżę brutto ###
                if ($Element['waluta'] == "PLN") {
                    $marza_brutto = $StawkaKlient - $StawkaPrzewoznik;
                    $marza = $Element['stawka_klient'] - $Element['stawka_przewoznik'];
                    $Elementy[$Idx]['stawka_klient_pln'] = $stawka_klient_pln = $Element['stawka_klient'];
                    $Elementy[$Idx]['stawka_przewoznik_pln'] = $stawka_przewoznik_pln = $Element['stawka_przewoznik'];
                    $Elementy[$Idx]['stawka_klient_brutto_pln'] = $stawka_klient_brutto_pln = $StawkaKlient;
                    $Elementy[$Idx]['stawka_przewoznik_brutto_pln'] = $stawka_przewoznik_brutto_pln = $StawkaPrzewoznik;
                }else{
                    $marza_brutto = ($StawkaKlient * $Element['kurs']) - ($StawkaPrzewoznik * $Element['kurs_przewoznik']);
                    $marza = ($Element['stawka_klient'] * $Element['kurs']) - ($Element['stawka_przewoznik'] * $Element['kurs_przewoznik']);
                    $Elementy[$Idx]['stawka_klient_pln'] = $stawka_klient_pln = $Element['stawka_klient'] * $Element['kurs'];
                    $Elementy[$Idx]['stawka_przewoznik_pln'] = $stawka_przewoznik_pln = $Element['stawka_przewoznik'] * $Element['kurs_przewoznik'];
                    $Elementy[$Idx]['stawka_klient_brutto_pln'] = $stawka_klient_brutto_pln = $StawkaKlient * $Element['kurs'];
                    $Elementy[$Idx]['stawka_przewoznik_brutto_pln'] = $stawka_przewoznik_brutto_pln = $StawkaPrzewoznik * $Element['kurs_przewoznik'];
                }
                $Elementy[$Idx]['marza_brutto'] = "<nobr>".number_format($marza_brutto, 2, ',', ' ') . " PLN</nobr>";
                $Elementy[$Idx]['marza_liczba'] = $marza;
                $Elementy[$Idx]['marza'] = "<nobr>".number_format($marza, 2, ',', ' ') . " PLN</nobr>";
                
                $this->Sumowanie['marza_brutto'] += $marza_brutto;
                $this->Sumowanie['marza'] += $marza;
                $this->Sumowanie['stawka_klient'] += $stawka_klient_pln;
                $this->Sumowanie['stawka_przewoznik'] += $stawka_przewoznik_pln;
                $this->Sumowanie['stawka_klient_brutto'] += $stawka_klient_brutto_pln;
                $this->Sumowanie['stawka_przewoznik_brutto'] += $stawka_przewoznik_brutto_pln;

                ### Czy jest faktura ###
                $IdFaktury = $Element['id_faktury'];
                if($IdFaktury > 0){
                    $Faktura = $this->Baza->GetData("SELECT * FROM faktury WHERE id_faktury = '$IdFaktury'");
                    $Elementy[$Idx]['id_faktury'] = "<div class='nr_faktury'><a href='javascript:ShowOptions(\"#faktura_{$Element[$this->PoleID]}\", {$Element['id_faktury']}, \"faktura\")' style='font-weight: bold;'>{$Faktura['numer']}</a></div><div class='faktura_data_wystawienia'>{$Faktura['data_wystawienia']}</div><div class='faktura_data_sprzedazy'>{$Faktura['data_sprzedazy']}</div><br /><br /><input type='checkbox' class='CheckInvoice' value='{$Element['id_faktury']}' name='Drukuj[normal][]'>";
                    $Elementy[$Idx]['data_sprzedazy'] = $Faktura['data_sprzedazy'];
                    $Elementy[$Idx]['data_wystawienia'] = $Faktura['data_wystawienia'];
                    $Elementy[$Idx]['data_wystawienia_sort'] = $Faktura['data_wystawienia']." ".str_pad( $Faktura['autonumer'], 3, '0', STR_PAD_LEFT);
                    $Elementy[$Idx]['nr_faktury'] = $Faktura['numer'];
                    $Elementy[$Idx]['nr_faktury_krotki'] = str_pad( $Faktura['autonumer'], 3, '0', STR_PAD_LEFT);
                    if($Faktura && $Element['faktura_wlasna'] == ""){
                        $this->Baza->Query("UPDATE $this->Tabela SET faktura_wlasna = '{$Faktura['numer']}' WHERE $this->PoleID = '{$Element[$this->PoleID]}'");
                    }
                }elseif ($Element['id_klient']) {
                    $Elementy[$Idx]['id_faktury'] = "<a href='?modul=$this->Parametr&akcja=faktura&id={$Element[$this->PoleID]}'><b>Wystaw</b></a>";
                }else{
                    $Elementy[$Idx]['id_faktury'] = "&nbsp;";
                }
                ### Ustawienie divów Terminów ###
                $Elementy[$Idx]['termin_zaladunku'] = $Element['termin_zaladunku']."<br />".$Element['godzina_zaladunku'];
                $Elementy[$Idx]['termin_rozladunku'] = $Element['termin_rozladunku']."<br />".$Element['godzina_rozladunku'];
                if($Element['rzecz_zaplata_klienta'] == "0000-00-00"){
                    $Elementy[$Idx]["opoznienie_klient"] = Usefull::PokazOpoznienie($Element['termin_wlasny'], date("Y-m-d") , true);
                }else{
                    $Elementy[$Idx]["opoznienie_klient"] = (!is_null($Elementy[$Idx]["opoznienie_klient"]) ? $Elementy[$Idx]["opoznienie_klient"]." dni" : "---");
                }
                if($Element['rzecz_zaplata_przew'] == "0000-00-00"){
                    $Elementy[$Idx]["opoznienie_przewoznik"] = Usefull::PokazOpoznienie($Element['termin_przewoznika'], date("Y-m-d"), true);
                }else{
                    $Elementy[$Idx]["opoznienie_przewoznik"] = (!is_null($Elementy[$Idx]["opoznienie_przewoznik"]) ? $Elementy[$Idx]["opoznienie_przewoznik"]." dni" : "---");
                }
                //$Elementy[$Idx]["fifo"] = ($IdFaktury > 0 && $Element['data_wplywu'] != "0000-00-00" ?  : "");

            }
            $this->Sumowanie['id_faktury'] = "<form name='print_invoices' id='print_invoices' target='_blank' action='drukuj_faktury_zbiorczo.php' method='post'><input type='hidden' id='invoice_ids' name='FakturyIDs' value='' /><input type='button' value='drukuj zaznaczone' class='form-button' onclick='PrintInvoices();' /></form>";
            $this->Sumowanie['id_faktury'] .= "<form name='print_invoices_no_bg' id='print_invoices_no_bg' target='_blank' action='drukuj_faktury_zbiorczo.php?bg=no' method='post'><input type='hidden' id='invoice_no_bg_ids' name='FakturyIDs' value='' /><input type='button' value='drukuj bez tła' class='form-button' onclick='PrintInvoicesNoBg();' /></form>";
            $this->Sumowanie['numer_zlecenia'] = "<form name='orders' id='orders' target='_blank' action='' method='post'><input type='hidden' id='orders_ids' name='OrdersIDs' value='' /><input type='button' value='raport' class='form-button' onclick='RaportOrders();' /><br /><input type='button' value='potwierdzenie' class='form-button' onclick='PotwierdzenieOrders();' /></form>";
            $this->Sumowanie['numer_zlecenia2'] = "<form name='fakturaZbiorcza' id='orders' target='_blank' action='' method='post'><input type='hidden' id='orders_ids' name='OrdersIDs' value='' /><input type='button' value='raport' class='form-button' onclick='RaportOrders();' /><br /><input type='button' value='faktura zbiorcza' class='form-button' onclick='PotwierdzenieOrders();' /></form>";
            return $Elementy;
        }

        function ObrobkaDanychXLS($Elementy){
            foreach($Elementy as $Idx => $Element){
                $IdFaktury = $Element['id_faktury'];
                $Elementy[$Idx] = $this->PobierzDaneFaktury($Elementy[$Idx], $Element);
                ### Ustawienie Terminów ###
                $Elementy[$Idx]['termin_zaladunku'] = $Element['termin_zaladunku']."\n".$Element['godzina_zaladunku'];
                $Elementy[$Idx]['termin_rozladunku'] = $Element['termin_rozladunku']."\n".$Element['godzina_rozladunku'];
                if($Element['rzecz_zaplata_przew'] == "0000-00-00"){
                    $Elementy[$Idx]["opoznienie_przewoznik"] = Usefull::PokazOpoznienie($Element['termin_przewoznika'], date("Y-m-d"), true);
                }else{
                    $Elementy[$Idx]["opoznienie_przewoznik"] = (!is_null($Elementy[$Idx]["opoznienie_przewoznik"]) ? $Elementy[$Idx]["opoznienie_przewoznik"]." dni" : "---");
                }
                //$Elementy[$Idx]["fifo"] = ($IdFaktury > 0 && $Element['data_wplywu'] != "0000-00-00" ? Usefull::ObliczIloscDniMiedzyDatami($Elementy[$Idx]['data_wystawienia'], $Element['data_wplywu']) : "");
                $Elementy[$Idx]["id_kierowca"] = $Element['kierowca_dane']."\n".($Element['os_kontaktowa'] != "" ? "osoba kontaktowa - {$Element['os_kontaktowa']}" : "");
                $Elementy[$Idx]["id_klient"] = $this->Klienci[$Element["id_klient"]];
                $Elementy[$Idx]["id_przewoznik"] = $this->Przewoznicy[$Element["id_przewoznik"]];
                $Elementy[$Idx]["id_uzytkownik"] = $this->Users[$Element["id_uzytkownik"]];
                $StawkaKlient = $Element['stawka_klient'];
                $StawkaPrzewoznik = $Element['stawka_przewoznik'];
                $StawkaVatKlient= (in_array(strtolower($Element['stawka_vat_klient']), array("np","zw")) ? 0 :  $Element['stawka_vat_klient']);
                $StawkaVatPrzewoznik = (in_array(strtolower($Element['stawka_vat_przewoznik']), array("np","zw")) ? 0 :  $Element['stawka_vat_przewoznik']);
                $StawkaKlientBrutto = $Element['stawka_klient']*(1+$StawkaVatKlient/100);
                $StawkaPrzewoznikBrutto = $Element['stawka_przewoznik']*(1+$StawkaVatPrzewoznik/100);
                if($Element['waluta'] == "PLN"){
                    $Elementy[$Idx]['stawka_klient_pln'] = $StawkaKlient;
                    $Elementy[$Idx]['stawka_przewoznik_pln'] = $StawkaPrzewoznik;
                    $Elementy[$Idx]['stawka_klient_brutto'] = $StawkaKlientBrutto;
                    $Elementy[$Idx]['stawka_klient_brutto_pln'] = $StawkaKlientBrutto;
                    $Elementy[$Idx]['stawka_przewoznik_brutto'] = $StawkaPrzewoznikBrutto;
                    $Elementy[$Idx]['stawka_przewoznik_brutto_pln'] = $StawkaPrzewoznikBrutto;
                    $Elementy[$Idx]['marza'] = $StawkaKlient - $StawkaPrzewoznik;
                    $Elementy[$Idx]['marza_brutto'] = $StawkaKlientBrutto - $StawkaPrzewoznikBrutto;

                }else{
                    $Elementy[$Idx]['stawka_klient_pln'] = $StawkaKlient * $Element['kurs'];
                    $Elementy[$Idx]['stawka_przewoznik_pln'] = $StawkaPrzewoznik * $Element['kurs_przewoznik'];
                    $Elementy[$Idx]['stawka_klient_brutto'] = $StawkaKlientBrutto;
                    $Elementy[$Idx]['stawka_klient_brutto_pln'] = $StawkaKlientBrutto * $Element['kurs'];
                    $Elementy[$Idx]['stawka_przewoznik_brutto'] = $StawkaPrzewoznikBrutto;
                    $Elementy[$Idx]['stawka_przewoznik_brutto_pln'] = $StawkaPrzewoznikBrutto * $Element['kurs_przewoznik'];
                    $Elementy[$Idx]['marza'] = ($StawkaKlient * $Element['kurs']) - ($StawkaPrzewoznik * $Element['kurs_przewoznik']);
                    $Elementy[$Idx]['marza_brutto'] = $StawkaKlientBrutto - $StawkaPrzewoznikBrutto;
                }
                $Elementy[$Idx]['stawka_za_km_klient'] =  number_format($Element['stawka_klient']/$Element['ilosc_km'], 2, ',', ' ')."  ".$Element['waluta'];
                $Elementy[$Idx]['stawka_za_km_przewoznik'] =  number_format($Element['stawka_przewoznik']/$Element['ilosc_km'], 2, ',', ' ')."  ".$Element['waluta'];
                $Elementy[$Idx]['stawka_za_km_klient_pln'] =  number_format($Element['stawka_klient_pln']/$Element['ilosc_km'], 2, ',', ' ');
                $Elementy[$Idx]['stawka_za_km_przewoznik_pln'] =  number_format($Element['stawka_przewoznik_pln']/$Element['ilosc_km'], 2, ',', ' ');
                $Elementy[$Idx]['stawka_klient'] = $Element['stawka_klient']." ".$Element['waluta'];
                $Elementy[$Idx]['stawka_przewoznik'] = $Element['stawka_przewoznik']." ".$Element['waluta'];
                $Elementy[$Idx]['stawka_klient_brutto'] = $Elementy[$Idx]['stawka_klient_brutto']." ".$Element['waluta'];
                $Elementy[$Idx]['stawka_przewoznik_brutto'] = $Elementy[$Idx]['stawka_przewoznik_brutto']." ".$Element['waluta'];
            }
            return $Elementy;
        }

        function PobierzDaneFaktury($Wartosci, $Element){
            ### Czy jest faktura ###
            if($Element['id_faktury'] > 0){
                $Faktura = $this->Baza->GetData("SELECT * FROM faktury WHERE id_faktury = '{$Element['id_faktury']}'");
                $Wartosci['id_faktury'] = $Faktura['numer'];
                $Wartosci['data_sprzedazy'] = $Faktura['data_sprzedazy'];
                $Wartosci['data_wystawienia'] = $Faktura['data_wystawienia'];
                $Wartosci['nr_faktury'] = $Faktura['numer'];
                $Wartosci['nr_faktury_krotki'] = $Faktura['autonumer'];
            }
            return $Wartosci;
        }

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja_link" => "?modul=platnosci_nowe&akcja=edycja&id={$Dane[$this->PoleID]}");
		return $Akcje;
	}

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "stawka_klient" || $Nazwa == "stawka_przewoznik" || $Nazwa == 'stawka_przewoznik_brutto' || $Nazwa == 'stawka_klient_brutto'){
                if($Element['waluta'] == "PLN"){
                    $StawkaPLN = $Element[$Nazwa];
                }else{
                    $KursPrzelicz = ($Nazwa == "stawka_przewoznik" || $Nazwa == 'stawka_przewoznik_brutto' ? $Element['kurs_przewoznik'] : $Element['kurs']);
                    $StawkaPLN = $Element[$Nazwa] * $KursPrzelicz;
                }
                //$this->Sumowanie[$Nazwa] += $StawkaPLN;
                print ("<td$Styl><nobr>" . number_format($Element[$Nazwa], 2, ',', ' ') . " {$Element['waluta']}</nobr>");
                if ($Element['waluta'] != "PLN") {
                    if ($KursPrzelicz > 0) {
                        echo("<br><nobr>" . number_format($StawkaPLN, 2, ',', ' ') . " PLN</nobr>");
                    }else {
                        echo("<br>Nie podano kursu waluty {$Element['waluta']}!");
                    }
                }
                if($_GET['dev'] == "test2"){
                    if($Element['waluta'] != "PLN"){
                        echo "<br /><nobr>" . number_format($Element[$Nazwa."_pln"], 2, ',', ' ') . " PLN</nobr>";
                    }else{
                        echo "<br /><nobr>" . number_format($Element[$Nazwa], 2, ',', ' ') . " PLN</nobr>";
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
            }else if($Nazwa == "id_uzytkownik"){
                if($Element['edytowali'] != ""){
                    echo("<td$Styl id='uzytkownik_{$Element[$this->PoleID]}'><a href='javascript:ShowOptions(\"#uzytkownik_{$Element[$this->PoleID]}\", {$Element[$this->PoleID]}, \"edytowali\")'>{$this->Users[$Element[$Nazwa]]}</a></td>");
                }else{
                    echo("<td$Styl id='uzytkownik_{$Element[$this->PoleID]}'>{$this->Users[$Element[$Nazwa]]}</td>");
                }
            }else if($Nazwa == "terminy"){
                include(SCIEZKA_SZABLONOW."tabela-rozliczen-terminy.tpl.php");
            }else if($Nazwa == "id_klient"){
                echo("<td$Styl id='klient_{$Element[$this->PoleID]}'><a href='javascript:ShowOptions(\"#klient_{$Element[$this->PoleID]}\", {$Element[$Nazwa]}, \"klient\")'>{$this->Klienci[$Element[$Nazwa]]}</a></td>");
            }else if($Nazwa == "id_przewoznik"){
                echo("<td$Styl id='przewoznik_{$Element[$this->PoleID]}'><a href='javascript:ShowOptions(\"#przewoznik_{$Element[$this->PoleID]}\", {$Element[$Nazwa]}, \"przewoznik\")'>{$this->Przewoznicy[$Element[$Nazwa]]}</a></td>");
            }else if($Nazwa == "numer_zlecenia"){
                echo("<td$Styl id='zlecenie_{$Element[$this->PoleID]}'>");
                    echo "<a href='javascript:ShowOptions(\"#zlecenie_{$Element[$this->PoleID]}\", {$Element[$this->PoleID]}, \"zlecenie\")'>{$Element[$Nazwa]}</a>";
                    if($Element['korekta'] > 0){
                        $this->GetKorekty($Element['korekta']);
                    }
                    echo "<p style='text-align: center;'>";
                        echo "<input type='checkbox' class='CheckOrders' value='{$Element[$this->PoleID]}' name='Zlecenia[]'>";
                    echo "</p>";
                echo("</td>");
            }else if($Nazwa == "id_faktury"){
                echo("<td$Styl id='faktura_{$Element[$this->PoleID]}' style='width: 180px; vertical-align: top; text-align: center;' >{$Element[$Nazwa]}</td>");
            }else if($Nazwa == "faktura_przewoznika"){
                echo("<td$Styl id='platnosci_{$Element[$this->PoleID]}'><a href='javascript:ShowOptions(\"#platnosci_{$Element[$this->PoleID]}\", {$Element[$this->PoleID]}, \"platnosci\")'>{$Element[$Nazwa]}</a></td>");
            }else if($Nazwa == "id_kierowca"){
                echo("<td$Styl>");
                    echo nl2br($Element['kierowca_dane'])."<br />".($Element['os_kontaktowa'] != "" ? "<small>osoba kontaktowa - </small>{$Element['os_kontaktowa']}" : "");
                echo("</td>");
            }else{
                echo("<td$Styl>".stripslashes(nl2br($Element[$Nazwa]))."</td>");
            }
        }

        function DodatkoweFiltryDoKolumn($Pola, $Elementy, $AkcjeNaLiscie){
            $Users = UsefullBase::GetUsers($this->Baza);
            $FakturyPrzewoznika['brak'] = '-- brak faktury --';
            $Klienci['brak'] = '-- BRAK';
            $Przewoznicy['brak'] = '-- BRAK';
//            foreach($Elementy as $Dane){
//                $NumeryZlecen[$Dane['numer_zlecenia']] = $Dane['numer_zlecenia'];
//                //$Kierowcy[$Dane['id_kierowca']] = $this->Kierowcy[$Dane['id_kierowca']];
//                $NumeryZlecenKlienta[$Dane['nr_zlecenia_klienta']] = $Dane['nr_zlecenia_klienta'];
//                $Klienci[$Dane['id_klient']] = $this->Klienci[$Dane['id_klient']];
//                $Przewoznicy[$Dane['id_przewoznik']] = $this->Przewoznicy[$Dane['id_przewoznik']];
//                $Usersi[$Dane['id_uzytkownik']] = $Users[$Dane['id_uzytkownik']];
//                $FakturyWlasne[$Dane['faktura_wlasna']] = $Dane['faktura_wlasna'];
//                $FakturyPrzewoznika[$Dane['faktura_przewoznika']] = $Dane['faktura_przewoznika'];
//                $Oddzialy[$Dane['id_oddzial']] = $this->Oddzialy[$Dane['id_oddzial']];
//            }
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
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $Pole => $Wartosc){
                if($Wartosc != ""){
                    foreach($Elementy as $Idx => $Dane){
                        if($Pole == "faktura_przewoznika" && $Wartosc == "brak"){
                            $Wartosc = "";
                        }
                        if($Dane[$Pole] != $Wartosc){
                            $this->Sumowanie['marza_brutto'] -= $Dane['marza_brutto'];
                            $this->Sumowanie['marza'] -= $Dane['marza_liczba'];
                            $this->Sumowanie['stawka_klient'] -= $Dane['stawka_klient_pln'];
                            $this->Sumowanie['stawka_przewoznik'] -= $Dane['stawka_przewoznik_pln'];
                            $this->Sumowanie['stawka_klient_brutto'] -= $Dane['stawka_klient_brutto_pln'];
                            $this->Sumowanie['stawka_przewoznik_brutto'] -= $Dane['stawka_przewoznik_brutto_pln'];
                            unset($Elementy[$Idx]);
                        }
                    }
                }
            }
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $Pole => $Wartosc){
                if(intval($Wartosc) > 0 || $Wartosc == "brak"){
                    foreach($Elementy as $Idx => $Dane){
                        if($Wartosc == "brak"){
                            $Wartosc = 0;
                        }
                        if($Dane[$Pole] != $Wartosc){
                            $this->Sumowanie['marza_brutto'] -= $Dane['marza_brutto'];
                            $this->Sumowanie['marza'] -= $Dane['marza_liczba'];
                            $this->Sumowanie['stawka_klient'] -= $Dane['stawka_klient_pln'];
                            $this->Sumowanie['stawka_przewoznik'] -= $Dane['stawka_przewoznik_pln'];
                            $this->Sumowanie['stawka_klient_brutto'] -= $Dane['stawka_klient_brutto_pln'];
                            $this->Sumowanie['stawka_przewoznik_brutto'] -= $Dane['stawka_przewoznik_brutto_pln'];
                            unset($Elementy[$Idx]);
                        }
                    }
                }
            }
            foreach($Elementy as $Dane){
                $NumeryZlecen[$Dane['numer_zlecenia']] = $Dane['numer_zlecenia'];
                //$Kierowcy[$Dane['id_kierowca']] = $this->Kierowcy[$Dane['id_kierowca']];
                $NumeryZlecenKlienta[$Dane['nr_zlecenia_klienta']] = $Dane['nr_zlecenia_klienta'];
                $Klienci[$Dane['id_klient']] = $this->Klienci[$Dane['id_klient']];
                $Przewoznicy[$Dane['id_przewoznik']] = $this->Przewoznicy[$Dane['id_przewoznik']];
                $Usersi[$Dane['id_uzytkownik']] = $Users[$Dane['id_uzytkownik']];
                $FakturyWlasne[$Dane['faktura_wlasna']] = $Dane['faktura_wlasna'];
                $FakturyPrzewoznika[$Dane['faktura_przewoznika']] = $Dane['faktura_przewoznika'];
                $Oddzialy[$Dane['id_oddzial']] = $this->Oddzialy[$Dane['id_oddzial']];
            }
            $NewElementy = array_values($Elementy);
            asort($NumeryZlecen);
            asort($Kierowcy);
            asort($NumeryZlecenKlienta);
            asort($Klienci);
            asort($Przewoznicy);
            asort($Usersi);
            asort($FakturyWlasne);
            asort($FakturyPrzewoznika);
            asort($Oddzialy);
            $Filtry['termin_zaladunku'] = array("type" => "sort");
            $Filtry['termin_rozladunku'] = array("type" => "sort");
            $Filtry['numer_zlecenia'] = array("type" => "filtr", 'elementy' => $NumeryZlecen);
            //$Filtry['id_kierowca'] = array("type" => "filtr_id", 'elementy' => $Kierowcy);
            $Filtry['nr_zlecenia_klienta'] = array("type" => "filtr", 'elementy' => $NumeryZlecenKlienta, 'dodatki' => "style='width: 220px;'");
            $Filtry['miejsce_zaladunku'] = array("type" => "sort");
            $Filtry['odbiorca'] = array("type" => "sort");
            //$Filtry['id_oddzial'] = array("type" => "filtr_id", 'elementy' => $Oddzialy);
            $Filtry['id_klient'] = array("type" => "filtr_id", 'elementy' => $Klienci, 'dodatki' => "style='width: 220px;'");
            $Filtry['id_przewoznik'] = array("type" => "filtr_id", 'elementy' => $Przewoznicy, 'dodatki' => "style='width: 220px;'");
            $Filtry['id_uzytkownik'] = array("type" => "filtr_id", 'elementy' => $Usersi);
            $Filtry['faktura_wlasna'] = array("type" => "filtr", 'elementy' => $FakturyWlasne);
            $Filtry['faktura_przewoznika'] = array("type" => "filtr", 'elementy' => $FakturyPrzewoznika);
            $Filtry['data_wplywu'] = array("type" => "sort");
            $Filtry['termin_wlasny'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_klienta'] = array("type" => "sort");
            $Filtry['termin_przewoznika'] = array("type" => "sort");
            $Filtry['planowana_zaplata_przew'] = array("type" => "sort");
            $Filtry['rzecz_zaplata_przew'] = array("type" => "sort");
            $Filtry['opoznienie_klient'] = array("type" => "sort_table");
            $Filtry['opoznienie_przewoznik'] = array("type" => "sort_table");
            $Filtry['id_faktury'] = array("type" => "sort_table", "elementy" => array("nr_faktury_krotki" => "Numer", "data_wystawienia_sort" => "Data wystawienia"));
            include(SCIEZKA_SZABLONOW."filtry-kolumn.tpl.php");
            ### Ustawienie domyślnie na ostatnią stronę ###
            $LiczbaWszystkich = count($NewElementy);
            $IleStron = ceil($LiczbaWszystkich/$this->IloscNaStrone);
            $this->ParametrPaginacji = isset($_GET['pagin']) ? $_GET['pagin'] : (isset($_SESSION['sort'][$this->Parametr]['pagin']) ? $_SESSION['sort'][$this->Parametr]['pagin'] : ($IleStron-1));
            ### Ustawienie domyślnie na ostatnią stronę --> END ###
            return $NewElementy;
        }
        
        function DodatkoweFiltryDoKolumnXLS($Elementy){
            $Users = UsefullBase::GetUsers($this->Baza);
            $FiltrJest = false;
            if(!isset($_SESSION[$this->Parametr]['filtry_kolumn'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = array();
            }
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry'] as $Pole => $Wartosc){
                if($Wartosc != ""){
                    foreach($Elementy as $Idx => $Dane){
                        if($Pole == "faktura_przewoznika" && $Wartosc == "brak"){
                            $Wartosc = "";
                        }
                        if($Dane[$Pole] != $Wartosc){
                            $this->Sumowanie['marza_brutto'] -= $Dane['marza_brutto'];
                            $this->Sumowanie['marza'] -= $Dane['marza_liczba'];
                            $this->Sumowanie['stawka_klient'] -= $Dane['stawka_klient_pln'];
                            $this->Sumowanie['stawka_przewoznik'] -= $Dane['stawka_przewoznik_pln'];
                            $this->Sumowanie['stawka_klient_brutto'] -= $Dane['stawka_klient_brutto_pln'];
                            $this->Sumowanie['stawka_przewoznik_brutto'] -= $Dane['stawka_przewoznik_brutto_pln'];
                            unset($Elementy[$Idx]);
                        }
                    }
                }
            }
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $Pole => $Wartosc){
                if(intval($Wartosc) > 0 || $Wartosc == "brak"){
                    foreach($Elementy as $Idx => $Dane){
                        if($Wartosc == "brak"){
                            $Wartosc = 0;
                        }
                        if($Dane[$Pole] != $Wartosc){
                            $this->Sumowanie['marza_brutto'] -= $Dane['marza_brutto'];
                            $this->Sumowanie['marza'] -= $Dane['marza_liczba'];
                            $this->Sumowanie['stawka_klient'] -= $Dane['stawka_klient_pln'];
                            $this->Sumowanie['stawka_przewoznik'] -= $Dane['stawka_przewoznik_pln'];
                            $this->Sumowanie['stawka_klient_brutto'] -= $Dane['stawka_klient_brutto_pln'];
                            $this->Sumowanie['stawka_przewoznik_brutto'] -= $Dane['stawka_przewoznik_brutto_pln'];
                            unset($Elementy[$Idx]);
                        }
                    }
                }
            }
            $NewElementy = array_values($Elementy);
            return $NewElementy;
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
                            echo "<a href='raporty_analiza_wynikow.php' target='_blank' class='form-button'>analiza wyników 1</a>";
                        }
                        if($this->Uzytkownik->DostepDoRaportu('analiza_wynikow_stara')){
                            echo "<a href='raporty_analiza_wynikow2.php' target='_blank' class='form-button'>analiza wyników 2</a>";
                        }
                        if($this->Uzytkownik->DostepDoRaportu('zestawienie_faktur')){
                            echo "<a href='zestawienie_faktur.php' target='_blank' class='form-button'>Zestawienie faktur</a>";
                        }
                        if($this->Uzytkownik->DostepDoRaportu('platnosci_dla_przewoznikow')){
                            echo "<a href='raporty_platnosci_dla_przewoznikow.php' target='_blank' class='form-button'>Raport płatności dla przewoźników</a>";
                        }
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if($this->WykonywanaAkcja != "dodawanie" && $this->WykonywanaAkcja != "specyfikacja" && is_null($ID)){
                include(SCIEZKA_SZABLONOW."filters-tabela-rozliczen-nowa.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
        }

        function GetKorekta($ID){
            return $this->Baza->GetData("SELECT id_zlecenie, numer_zlecenia, korekta FROM $this->Tabela WHERE id_zlecenie = '$ID'");
        }

        function GetKorekty($ID){
            $Korekta = $this->GetKorekta($ID);
            if($Korekta){
                echo "<br /><a href='podglad.php?id={$Korekta['id_zlecenie']}' style='margin-left: 20px;' target='_blank'>{$Korekta['numer_zlecenia']}</a>";
                if($Korekta['korekta'] > 0){
                    $this->GetKorekty($Korekta['korekta']);
                }
            }
        }

        function AkcjaDodawanie(){
            $Zlecenia = new Zlecenia($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $Zlecenia->SetWykonywanaAkcja($this->WykonywanaAkcja);
            $Zlecenia->AkcjaDodawanie();
        }

        function ShowPaginacjaTable(){
            echo("<table class='paginacja_table'>");
			echo("<tr>");
				echo("<td style='font-size: 12px; text-align: left; padding-left: 500px;'>");
					Usefull::ShowPagination("?modul=$this->Parametr".(isset($_GET['sort']) ? "&sort={$_GET['sort']}" : "").(isset($_GET['sort_how']) ? "&sort_how={$_GET['sort_how']}" : ""), $this->ParametrPaginacji, 30, $this->IleStronPaginacji, true);
				echo("</td>");
			echo("</tr>");
		echo("</table>");
        }
}
?>