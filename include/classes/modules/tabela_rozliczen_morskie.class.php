<?php
/**
 * Moduł tabela rozliczen
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class TabelaRozliczenMorskie extends ModulBazowy {
        public $Faktury = array();
        public $Waluty;
        public $PrzewoznicyIds;
        public $Marza = 0;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_sea_orders';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Zlecenie';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Filtry[] = array("opis" => "Rodzaj", "nazwa" => "sea_order_type", "typ" => "lista", "opcje" => array("I" => "---- Import ----", "E" => "---- Export ----"), 'domyslna' => '---- wszystkie ----');
            $this->Filtry[] = array("opis" => "Typ", "nazwa" => "mode", "typ" => "lista", "opcje" => array("FCL" => "---- FCL ----", "LCL" => "---- LCL ----"), 'domyslna' => '---- wszystkie ----');
	}

        function DomyslnyWarunek(){
            return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31'".(!$this->Uzytkownik->IsAdmin() ? " AND (id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") OR id_oddzial = '{$_SESSION['id_oddzial']}')" : "");
        }
	
	function PobierzListeElementow($Filtry = array(), $XLS = false) {
                $Klienci = UsefullBase::GetKlienci($this->Baza);
                $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
                $this->Waluty = UsefullBase::GetWaluty($this->Baza);
                $this->PrzewoznicyIds = UsefullBase::GetPrzewoznicyIds($this->Baza);
                $Wynik = array(
                    "numer_zlecenia" => array("naglowek" => "Numer zlecenia"),
                    "nabywca_id" => array("naglowek" => "Nabywca", "elementy" => $Klienci),
                    "id_klient_shipper" => array("naglowek" => "Shipper", "elementy" => $Klienci),
                    "id_klient_consignee" => array("naglowek" => "Consignee", "elementy" => $Klienci),
                    "id_przewoznik_agent" => array("naglowek" => "Agent", "elementy" => $Przewoznicy),
                    "bl_no" => array("naglowek" => "B/L No")
		);
               if(in_array($_SESSION["uprawnienia_id"], array(1,2,4))){
                    $Wynik['przychod'] = array("naglowek" => "Przychód");
                    $Wynik['koszty'] = array("naglowek" => "Koszty");
                    $Wynik['marza'] = array("naglowek" => "Marża");
                }
                $Wynik['id_uzytkownik'] = array("naglowek" => "Zlecenie wystawił", 'elementy' => UsefullBase::GetUsers($this->Baza));
                foreach($Wynik as $Key => $Val){
                    $Wynik[$Key]['td_styl'] = "vertical-align: top;".($Key == "numer_zlecenia" ? "white-space: nowrap;" : "");
                }
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY numer_zlecenia_krotki ASC");
                if($XLS == false){
                    echo "<script type='text/javascript' src='js/tabela-rozliczen.js'></script>";
                    echo "<div id='div_ajax' style='display: none;'></div>";
                }
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                $Akcje[] = array('img' => "faktura_button", 'title' => "Fakturuj", "akcja_link" => "?modul=faktury_morskie&akcja=dodawanie&soid={$Dane['id_zlecenie']}", "faktury" => $this->Faktury);
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_href" => "podglad-morskie.php?");
                $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+7 days"));
                if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                    $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "popraw");
                }else{
                    $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
                }
		return $Akcje;
	}

        function AkcjeNiestandardowe($ID) {
            switch($this->WykonywanaAkcja){
                case "popraw":
                    $this->AkcjaPopraw($ID);
                    break;
            }
        }

        function AkcjaPopraw($ID){
            $ZlecenieMorskie = new SeaOrders($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $DefaultValues = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$ID'");
            $Type = $Values['sea_order_type'];
            $DefaultValues['Koszty'] = false;
            $DefaultValues['Koszty'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '$ID'");
            if(!$DefaultValues['Koszty']){
                $DefaultValues["Koszty"] = array();
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Sea'])){
                if($ZlecenieMorskie->Zapisz($Type, $Skrot, $DefaultValues)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Sea'];
                    $Values['Koszty'] = $_POST['Koszty'];
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/sea-order-form.tpl.php");
                }
            }else{
                $Values = $DefaultValues;
                $Values = $ZlecenieMorskie->PobierzKontenery($Values, $ID);
                include(SCIEZKA_SZABLONOW."forms/sea-order-form.tpl.php");
            }
        }

        function MozeBycOperacja($ID){
            $Result = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
            $TerminEdycji = date("Y-m-d", strtotime($Result['termin_rozladunku']."+7 days"));
            if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                return true;
            }
            return false;
        }

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "id_klient_shipper"){
                $Element[$Nazwa] = $Element[$Nazwa]."<br />".$Element['shipper'];
            }
            if($Nazwa == "id_klient_consignee"){
                $Element[$Nazwa] = $Element[$Nazwa]."<br />".$Element['consignee'];
            }
            if($Nazwa == "id_przewoznik_agent"){
                $Element[$Nazwa] = $Element[$Nazwa]."<br />".$Element['agent'];
            }
            if($Nazwa == "przychod"){
                echo "<td$Styl>\n";
                  $this->Faktury = array();
                  $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$Element[$this->PoleID]}'", "id_faktury");
                  $StawkaKlientPLN = 0;
                  if(count($Faktury) > 0){
                      foreach($Faktury as $FID => $DaneFak){
                        $this->Faktury[$FID] = $DaneFak['numer'];
                        $Fakturki[] = $FID;
                        $this->Baza->Query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$FID'");
                        $PosMany = 0;
                        while($Pos = $this->Baza->GetRow()){
                            if($this->Waluty[$DaneFak['id_waluty']] == "PLN"){
                                $PosMany += $Pos['netto'];
                            }else{
                                $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                            }
                        }
                        echo number_format($PosMany,2,',',' ')." PLN<br />";
                        $StawkaKlientPLN += $PosMany;
                      }
                  }else{
                      echo "0,00 PLN";
                  }
                  echo "</td>\n";
                  $this->Marza = $StawkaKlientPLN;
                  $this->Sumowanie['przychod'] += $StawkaKlientPLN;
            }else if($Nazwa == "koszty"){
                $RamkaTabela = "#467AAC";
                  $StawkaPrzewoznikPLN = 0;
                  print ("<td$Styl>");
                    echo "<table border='0' cellpadding='3' cellspacing='0' style='margin: 0; width: 100%; border-collapse: collapse;'>";
                    $this->Baza->Query("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$Element[$this->PoleID]}'");
                    $KL = false;
                    while($KosztyRes = $this->Baza->GetRow()){
                        $Kwota = ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                        echo "<tr>";
                            echo "<td style='border-right: 1px solid $RamkaTabela; border-bottom: 1px solid $RamkaTabela;'>";
                                echo $this->PrzewoznicyIds[$KosztyRes['id_przewoznik']];
                            echo "</td>";
                            echo "<td style='border-bottom: 1px solid $RamkaTabela; white-space: nowrap;'>";
                                echo number_format($Kwota, 2, ",", " ")." PLN<br />";
                            echo "</td>";
                        echo "</tr>\n";
                        $StawkaPrzewoznikPLN += $Kwota;
                        $KL = true;
                    }
                    if($this->Baza->GetNumRows() == 0){
                        echo "0,00 PLN";
                    }
                    $this->Marza -= $StawkaPrzewoznikPLN;
                    $this->Sumowanie['koszty'] += $StawkaPrzewoznikPLN;
                    echo "</table>\n";
                  echo "</td>";
            }else if($Nazwa == "marza"){
                $this->Sumowanie['marza'] += $this->Marza;
                echo("<td$Styl><nobr>". number_format($this->Marza, 2, ',', ' ') . " PLN</nobr></td>");
            }else if($Nazwa == "id_uzytkownik"){
                print ("<td$Styl>{$Element[$Nazwa]}");
                    if($Element['edytowali'] != ""){
                        echo "<br /><br />edytował:<br />";
                        $Edytowali = explode("#",$Element['edytowali']);
                        foreach($Edytowali as $Edit){
                                if($Edit != ""){
                                        echo $Edit."<br />";
                                }
                        }
                    }
                echo "</td>\n";
            }else if($Nazwa == 'numer_zlecenia'){
                echo "<td$Styl>".stripslashes(nl2br($Element[$Nazwa]));
                    echo "<p style='text-align: center;'>";
                        echo "<input type='checkbox' class='CheckOrders' value='{$Element[$this->PoleID]}' name='Zlecenia[]'>";
                    echo "</p>";
                echo "</td>";
            }else{
                echo("<td$Styl>".stripslashes(nl2br($Element[$Nazwa]))."</td>");
            }
        }

        function ShowSuma($NazwaPola){
            if($NazwaPola == "koszty"){
                return "Ogół kosztów: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
            }else if($NazwaPola == "przychod"){
                return "Ogół sprzedaży: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
            }else if($NazwaPola == "marza"){
                return "Suma marży: <br /><nobr>".number_format($this->Sumowanie[$NazwaPola], 2, ',', ' ')." PLN";
            }else{
                return $this->Sumowanie[$NazwaPola];
            }
        }

        function ShowOK(){
            $this->LinkPowrotu = "?modul=$this->Parametr&&zmieniony={$_GET['id']}#Linia_{$_GET['id']}";
            Usefull::ShowKomunikatOK('<b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>');
        }

        function ShowActionsList($AkcjeNaLiscie, $Element){
		foreach ($AkcjeNaLiscie as $Actions){
                    echo("<td class='ikona'".(isset($Actions['faktury']) ? "id='faktury_{$Element[$this->PoleID]}'" : "").">");
                            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                                if(isset($Actions['faktury'])){
                                    echo "<a href=\"{$Actions['akcja_link']}\"><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a><br /><br />";
                                    foreach($Actions['faktury'] as $IDF => $Numer){ 
                                        echo "<nobr><a href='javascript:ShowOptions(\"#faktury_{$Element[$this->PoleID]}\", $IDF, \"faktura-morska\")' class=\"akcja\">$Numer</a></nobr><br />";
                                    }
                                }else{
                                    if(isset($Actions['img'])){
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
                                    }else{
                                        echo "&nbsp;";
                                    }
                                }
                            }
                    echo "</td>\n";
		}
	}

        function WyswietlAJAX($Action){
            if($Action == "koszt-row"){
                include(SCIEZKA_SZABLONOW."forms/koszt-row.tpl.php");
            }
        }

        function WystawiamyDoIstniejacegoZlecenia(){
            return (isset($_GET['zid']) && $_GET['zid'] > 0 ? true : false);
        }
        
        function ObrobkaDanychLista($Elementy) {
            $this->Sumowanie['numer_zlecenia'] = "<form name='orders' id='orders' target='_blank' action='' method='post'><input type='hidden' id='orders_ids' name='OrdersIDs' value='' /><input type='button' value='raport' class='form-button' onclick='RaportOrdersMorskie();' /></form>";
            return $Elementy;
        }

        function ObrobkaDanychXLS($Elementy, $Pola){
            foreach($Elementy as $Idx => $Element){
                $Elementy[$Idx]["id_klient_shipper"] = $Pola["id_klient_shipper"]['elementy'][$Element["id_klient_shipper"]]."\n".$Element['shipper'];
                $Elementy[$Idx]["id_klient_consignee"] = $Pola["id_klient_consignee"]['elementy'][$Element["id_klient_consignee"]]."\n".$Element['consignee'];
                $Elementy[$Idx]["id_przewoznik_agent"] = $Pola["id_przewoznik_agent"]['elementy'][$Element["id_przewoznik_agent"]]."\n".$Element['agent'];
                $Elementy[$Idx]["id_uzytkownik"] = $Pola["id_uzytkownik"]['elementy'][$Element["id_uzytkownik"]];
                $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$Element[$this->PoleID]}'", "id_faktury");
                $StawkaKlientPLN = 0;
                if(count($Faktury) > 0){
                    foreach($Faktury as $FID => $DaneFak){
                        $this->Baza->Query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$FID'");
                        $PosMany = 0;
                        while($Pos = $this->Baza->GetRow()){
                            if($this->Waluty[$DaneFak['id_waluty']] == "PLN"){
                                $PosMany += $Pos['netto'];
                            }else{
                                $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                            }
                        }
                        $StawkaKlientPLN += $PosMany;
                    }
                    $Elementy[$Idx]["przychod"] .= round($PosMany*1,2);
                }else{
                    $Elementy[$Idx]["przychod"] = "0,00";
                }
                $this->Marza = $StawkaKlientPLN;
                $this->Baza->Query("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$Element[$this->PoleID]}'");
                $Elementy[$Idx]['koszty_lista'] = array();
                $StawkaPrzewoznikPLN = 0;
                while($KosztyRes = $this->Baza->GetRow()){
                    $Kwota = ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']*1);
                    $Elementy[$Idx]['koszty_lista'][] = array('przewoznik' => $this->PrzewoznicyIds[$KosztyRes['id_przewoznik']], 'kwota' => number_format($Kwota,2,',',''));
                    $StawkaPrzewoznikPLN += $Kwota;
                }
                if($this->Baza->GetNumRows() == 0){
                    $Elementy[$Idx]['koszty'] = "0,00";
                }
                $this->Marza -= $StawkaPrzewoznikPLN;
                $Elementy[$Idx]['marza'] = round($this->Marza * 1, 2);
            }
            return $Elementy;
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID)){
                    echo("<div style='float: left;'>");
                        echo "<div style='float: left; color: #bcce00; font-weight: bold;'>RAPORTY:<br /><br /></div>";
                        echo "<div style='clear: both;'></div>\n";
                        if($this->Uzytkownik->DostepDoRaportu('analiza_wynikow')){
                            echo "<a href='raporty_analiza_wynikow_airsea.php' target='_blank' class='form-button'>analiza wyników 1</a>";
                        }                        
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
                    ?>
                    <div style='margin-bottom: 10px; color: #bcce00; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00; border-top: 1px solid #bbce00;'>
                        <?php
                            if($this->Uzytkownik->IsAdmin()){
                               ?>
                                <a href='tabela_rozliczen_morskie_xls.php' target='_blank' class='form-button' style="margin-left: 60px;">Generuj plik XLS</a>
                                <?php
                            }
                                ?>
                        <a href='drukuj-bl-form.php' target='_blank' class='form-button' style="margin-left: 40px;">Wydruk FBL</a>
                    </div>
                    <?php
            }else{
                echo "<div style='clear: both'></div>\n";
            }
        }
        
        function DodatkoweFiltryDoKolumn($Pola, $Elementy, $AkcjeNaLiscie){
            $Klienci['brak'] = '-- BRAK';
            
            if(isset($_POST['filtry'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = $_POST;
            }
            if(isset($_POST['czysc_filtry'])){
                unset($_SESSION[$this->Parametr]['filtry_kolumn']);
            }
            if(!isset($_SESSION[$this->Parametr]['filtry_kolumn'])){
                $_SESSION[$this->Parametr]['filtry_kolumn'] = array();
            }
            foreach($_SESSION[$this->Parametr]['filtry_kolumn']['filtry_id'] as $Pole => $Wartosc){
                if(intval($Wartosc) > 0 || $Wartosc == "brak"){
                    foreach($Elementy as $Idx => $Dane){
                        if($Wartosc == "brak"){
                            $Wartosc = 0;
                        }
                        if($Dane[$Pole] != $Wartosc){
                            unset($Elementy[$Idx]);
                        }
                    }
                }
            }
            foreach($Elementy as $Dane){
                $Klienci[$Dane['id_klient']] = $this->Klienci[$Dane['id_klient']];
            }
            $NewElementy = array_values($Elementy);
            asort($Klienci);
            $Filtry['nabywca_id'] = array("type" => "nabywca_id", 'elementy' => $Klienci, 'dodatki' => "style='width: 220px;'");
            include(SCIEZKA_SZABLONOW."filtry-kolumn.tpl.php");
            return $NewElementy;
        }

}
?>
