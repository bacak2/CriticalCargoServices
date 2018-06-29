<?php
/**
 * Moduł zlecenia morskie
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class ZleceniaMorskie extends ModulBazowy {
    public $SOIE;
    public $SOIEK;
    public $Emaile;
    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_sea_orders_zlecenia';
            $this->PoleID = 'zlecenie_so_id';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Zlecenie';
            $this->CzySaOpcjeWarunkowe = true;
            $this->SOIE = $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_sea_orders WHERE ".($this->Uzytkownik->CheckNoOddzial() ? "id_oddzial = '{$_SESSION['id_oddzial']}' AND" : "")." data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31' ORDER BY numer_zlecenia_krotki ASC, numer_zlecenia ASC"); 
            $this->SOIEK = array_keys($this->SOIE); 
            $this->SOIE = Usefull::PolaczDwieTablice(array("all" => "---- bez SO ----"), $this->SOIE);
            $this->Filtry[] = array("opis" => "Sea Order", "nazwa" => "sea_order_id", "typ" => "lista", "opcje" => $this->SOIE, 'domyslna' => '---- wszystkie ----');
            $this->Filtry[] = array("opis" => "Nr kontenera lub B/L", "nazwa" => "nr_kontenera", "typ" => "tekst");
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
                                        if($Wartosc == "all"){
                                            $Wartosc = 0;
                                        }
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "nr_kontenera"){
                                            $SOIKI_BL = $this->Baza->GetValues("SELECT id_zlecenie FROM orderplus_sea_orders WHERE bl_no LIKE '%$Wartosc%'");
                                            $SOIKI_BL[] = -1;
                                            if(count($SOIKI_BL) > 0){
                                                $Where .=  ($Where != '' ? ' AND ' : '')."(id_zlecenie IN(".implode(",", $SOIKI_BL).")";
                                            }
                                            $Conty = $this->Baza->GetValues("SELECT zlecenie_so_id FROM orderplus_sea_orders_zlecenia_fcl WHERE cont_number LIKE '%$Wartosc%'");
                                            if(count($Conty) > 0){
                                                $Where .= " OR zlecenie_so_id IN(".implode(",",$Conty).")";
                                            }
                                            $Where .= ")";
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
            for ($i = 0; $i < count($this->Filtry); $i++) {
                $Pole = $this->Filtry[$i]['nazwa'];
                if ($Pole == "nr_kontenera" && isset($_SESSION['Filtry'][$Pole])) {
                    return "";
                }
            }
            return "id_zlecenie IN(0,".implode(",", $this->SOIEK).")";
        }

        function GenerujWarunkiDrogowe($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunekDrogowe();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        if($Wartosc == "all"){
                                            $Wartosc = 0;
                                        }
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "nr_kontenera"){
                                            $SOIKI_BL = $this->Baza->GetValues("SELECT id_zlecenie FROM orderplus_sea_orders WHERE bl_no LIKE '%$Wartosc%'");
                                            $SOIKI_BL[] = -1;
                                            if(count($SOIKI_BL) > 0){
                                                $Where .=  ($Where != '' ? ' AND ' : '')."(sea_order_id IN(".implode(",", $SOIKI_BL).")";
                                            }
                                            $Conty = $this->Baza->GetValues("SELECT zlecenie_so_id FROM orderplus_sea_orders_zlecenia_fcl WHERE cont_number LIKE '%$Wartosc%'");
                                            if(count($Conty) > 0){
                                                $Where .= " OR zlecenie_so_id IN(".implode(",",$Conty).")";
                                            }
                                            $Where .= ")";
                                        }else{
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                        }
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function DomyslnyWarunekDrogowe(){
            for ($i = 0; $i < count($this->Filtry); $i++) {
                $Pole = $this->Filtry[$i]['nazwa'];
                if ($Pole == "nr_kontenera" && isset($_SESSION['Filtry'][$Pole])) {
                    return "sea_order_id > 0";
                }
            }
            return "sea_order_id > 0 AND sea_order_id IN(0,".implode(",", $this->SOIEK).")";
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"numer_zlecenia" => 'Numer zlecenia',
                        "id_zlecenie" => array("naglowek" => 'Numer SO'),
                        "conty" => "Kontener"
		);
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                if($Dane['typek'] == "morskie" || $TH){
                    $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_link" => "podglad-morskie-zlec.php?soid={$Dane['id_zlecenie']}&id={$Dane['zlecenie_so_id']}", "target" => true);
                    $Akcje[] = array('img' => "mail_button", 'title' => "Email", "akcja" => "email");
                    $Akcje[] = array('img' => "copy_button", 'title' => "Kopiuj", "akcja_link" => "?modul=$this->Parametr&akcja=duplikacja&soid={$Dane['id_zlecenie']}&did={$Dane['zlecenie_so_id']}");

                     $TerminEdycji = date("Y-m-d", strtotime("+1 days"));
                    if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                        $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja_link" => "?modul=$this->Parametr&akcja=edycja&soid={$Dane['id_zlecenie']}&id={$Dane['zlecenie_so_id']}");
                    }else{
                        $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
                    }
                    if($Dane['id_zlecenie'] == 0){
                        $Akcje[] = array('img' => "add_button", 'title' => "Wystaw SO", "akcja_link" => "?modul=zlecenia_morskie&akcja=dodaj_export&zid={$Dane['zlecenie_so_id']}");
                    }else{
                        $Akcje[] = array();
                    }
                    if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                        $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "?modul=$this->Parametr&akcja=kasowanie&soid={$Dane['id_zlecenie']}&id={$Dane['zlecenie_so_id']}");
                    }else{
                        $Akcje[] = array('img' => "delete_button_grey", 'title' => "Kasowanie");
                    }
                }
                if($Dane['typek'] == "drogowe"){
                    $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_link" => "podglad.php?&id={$Dane['id_zlecenie']}", "target" => true);
                    $Akcje[] = array('img' => "mail_button", 'title' => "Email", "akcja_link" => "?modul=zlecenia&akcja=email&id={$Dane['id_zlecenie']}&ret=sea");
                    $Akcje[] = array('img' => "copy_button", 'title' => "Kopiuj", "akcja_link" => "?modul=zlecenia&akcja=duplikacja&id={$Dane['id_zlecenie']}&ret=sea");
                    $TerminEdycji = date("Y-m-d", strtotime($Dane['termin_rozladunku']."+3 days"));
                    if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                        $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja_link" => "?modul=tabela_rozliczen_nowa&akcja=edycja&id={$Dane['id_zlecenie']}&ret=sea");
                    }else{
                        $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
                    }
                    if($Dane['id_zlecenie'] == 0){
                        $Akcje[] = array('img' => "add_button", 'title' => "Wystaw SO", "akcja_link" => "?modul=zlecenia_morskie&akcja=dodaj_export&zid={$Dane['zlecenie_so_id']}");
                    }else{
                        $Akcje[] = array();
                    }
                    if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                        $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "?modul=zlecenie&akcja=kasowanie&id={$Dane['id_zlecenie']}&ret=sea");
                    }else{
                        $Akcje[] = array('img' => "delete_button_grey", 'title' => "Kasowanie");
                    }
                }
		return $Akcje;
	}

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case "email":
                    $this->AkcjaEmail($ID);
                    break;
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

        function AkcjaDodawanie(){
            $SOID = (isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0);
            if($SOID > 0){
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$SOID'");
            }else{
                $SOI = array('sea_order_type' => "E");
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['SeaZlec'])){
                if($this->Zapisz($SOID, $SOI)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['SeaZlec'];
                    if($SOID > 0){
                        $Wykorzystane = $this->GetUsed($SOID, $ID);
                        $Values = $this->GetContainers($Values, $SOID);
                    }else{
                        $SOI['mode'] = "FCL";
                    }
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    if($SOI['sea_order_type'] == "I"){
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                    }else{
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                    }
                }
            }else{
                if($SOID){
                    $Wykorzystane = $this->GetUsed($SOID, $ID);
                    $Values = $this->GetContainers($Values, $SOID);
                }else{
                    $Values['FCL'][] = array();
                    $Values['LCL'][] = array();
                    $SOI['mode'] = "FCL";
                }
                $Values['instrukcje'] = "Prosze opisywac kontenery w porcie tylko i wylacznie z firma 'SHIPCONTROL' - w przeciwnym wypadku koszty beda refakturowane na Panstwa";
                $Values['instrukcje'] .= "\r\n\r\nADRES KORESPONDENCYJNY:\r\nMEPP European Freight Solutions Sp. z o.o.\r\nBranch Office GDYNIA\r\nul. Szkolna 10/2\r\n81363 Gdynia, Poland\r\n";
                $Values['ocean_carrier_id'] = $SOI['ocean_carrier_id'];
                $Values['ocean_carrier_text'] = $SOI['ocean_carrier_text'];
                $Values['customs_clearence'] = $SOI['customs_clearence'];
                $Values['shipper'] = $SOI['shipper'];
                $Values['id_klient_shipper'] = $SOI['id_klient_shipper'];
                $Values['consignee'] = $SOI['consignee'];
                $Values['id_klient_consignee'] = $SOI['id_klient_consignee'];
                $Values['cont_number'] = array();
                if($SOI['sea_order_type'] == "I"){
                    $Values['miejsce_podjecia'] = $SOI['terminal'];
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                }else{
                    $Values['informacje_dodatkowe'] = "Prosze opisywac kontenery w porcie tylko i wylacznie z firma 'SHIPCONTROL' - w przeciwnym wypadku koszty beda refakturowane na Panstwa";
                    $Values['informacje_dodatkowe'] .= "\r\n\r\nADRES KORESPONDENCYJNY:\r\nMEPP European Freight Solutions Sp. z o.o.\r\nBranch Office GDYNIA\r\nul. Szkolna 10/2\r\n81363 Gdynia, Poland\r\n";
                    $Values['miejsce_podjecia'] = $SOI['depot'];
                    $Values['terminal'] = $SOI['terminal'];
                    $Values['nr_booking'] = $SOI['booking_no'];
                    $Values['vessel'] = $SOI['vessel'];
                    $Values['feeder'] = $SOI['feeder'];
                    $Values['pod'] = $SOI['pod'];
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                }
            }
        }

        function AkcjaDuplikacja($ID){
            $SOID = (isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0);
            if($SOID > 0){
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$SOID'");
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['SeaZlec'])){
                if($this->Zapisz($SOID, $SOI)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['SeaZlec'];
                    if($SOID > 0){
                        $Wykorzystane = $this->GetUsed($SOID, $_GET['did']);
                        $Values = $this->GetContainers($Values, $SOID);
                    }else{
                        $SOI['mode'] = "FCL";
                    }
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    if($SOI['sea_order_type'] == "I"){
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                    }else{
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                    }
                }
            }else{
                $Values = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_zlecenia WHERE zlecenie_so_id = '{$_GET['did']}'");
                $Values['cont_number'] = array();
                if($SOID > 0){
                    $Wykorzystane = $this->GetUsed($SOID, $_GET['did']);
                    $Values = $this->GetContainers($Values, $SOID);
                }else{
                    $SOI = array();
                    $SOI['mode'] = "FCL";
                    $Values['FCL'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$SOID' AND no_so_id = '{$_GET['did']}'");
                }
                if($SOI['sea_order_type'] == "I"){
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                }else{
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                }
            }
        }

        function AkcjaEdycja($ID){
            $SOID = (isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0);
            if($SOID > 0){
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$SOID'");
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['SeaZlec'])){
                if($this->Zapisz($SOID, $SOI)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['SeaZlec'];
                    if($SOID > 0){
                        $Wykorzystane = $this->GetUsed($SOID, $ID);
                        $Values = $this->GetContainers($Values, $SOID);
                    }else{
                        $SOI['mode'] = "FCL";
                    }
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    if($SOI['sea_order_type'] == "I"){
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                    }else{
                        include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                    }
                }

            }else{
                $Values = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_zlecenia WHERE zlecenie_so_id = '$ID'");
                $Values['cont_number'] = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id = '$ID'");
                if($SOID > 0){
                    $Wykorzystane = $this->GetUsed($SOID, $ID);
                    $Values = $this->GetContainers($Values, $SOID);
                }else{
                    $SOI = array();
                    $SOI['mode'] = "FCL";
                    $this->Baza->Query("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$SOID' AND no_so_id = '$ID'");
                    while($FCLRes = $this->Baza->GetRow()){
                        $Values['FCL'][] = $FCLRes;
                    }
                }
                if($SOI['sea_order_type'] == "I"){
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-import.tpl.php");
                }else{
                    include(SCIEZKA_SZABLONOW."forms/zlecenie-form-export.tpl.php");
                }
            }
        }

        function Zapisz($SOID, $SOI, $DefalutValues = array()){
            $SaveValues = $_POST['SeaZlec'];
            $Error = false;
            if((isset($SaveValues['cont_number']) && count($SaveValues['cont_number']) > 0) || ($SOID == 0 && count($SaveValues['FCL'] > 0))){
                $Conty = $SaveValues['FCL'];
                $UsedConty = $SaveValues['cont_number'];
                unset($SaveValues['FCL']);
                unset($SaveValues['cont_number']);
                $ID = false;
                $SaveValues['id_zlecenie'] = $SOID;
                $Conty = $this->ValidateContNumber($Conty);
                $CheckedFCL = $this->CheckContNumbers($Conty);
                if($SOID > 0 || !$CheckedFCL){
                    if(isset($_GET['id']) && is_numeric($_GET['id'])){
                        $ID = $_GET['id'];
                        $identyfikator_przewoznika = $this->Baza->GetValue("SELECT identyfikator FROM orderplus_przewoznik WHERE id_przewoznik = '{$SaveValues['id_przewoznik_to']}'");
                        $NumerZlecenia = $this->Baza->GetValue("SELECT numer_zlecenia FROM orderplus_sea_orders_zlecenia WHERE zlecenie_so_id = '{$_GET['id']}'");
                        $NumerExp = explode("/", $NumerZlecenia);
                        $SaveValues['numer_zlecenia'] = "{$NumerExp[0]}/$identyfikator_przewoznika/{$NumerExp[2]}/{$NumerExp[3]}/SO";
                        $Zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders_zlecenia", $SaveValues, array('zlecenie_so_id' => $_GET['id']));
                    }else{
                        $SaveValues['id_uzytkownik'] = $_SESSION['id_uzytkownik'];
                        $identyfikator_przewoznika = $this->Baza->GetValue("SELECT identyfikator FROM orderplus_przewoznik WHERE id_przewoznik = '{$SaveValues['id_przewoznik_to']}'");
                        $SaveValues['numer_krotki'] = intval($this->Baza->GetValue("SELECT max(soz.numer_krotki) FROM orderplus_sea_orders_zlecenia soz LEFT JOIN orderplus_sea_orders so ON(so.id_zlecenie = soz.id_zlecenie) WHERE so.sea_order_type = '{$SOI['sea_order_type']}' OR so.sea_order_type IS NULL")) + 1;
                        $KoniecNr = date("m/Y");
                        $SaveValues['numer_zlecenia'] = "{$SaveValues['numer_krotki']}/$identyfikator_przewoznika/$KoniecNr/SO";
                        $Zapytanie = $this->Baza->PrepareInsert("orderplus_sea_orders_zlecenia", $SaveValues);
                    }
                    if($this->Baza->Query($Zapytanie)){
                        if(!$ID){
                            $ID = $this->Baza->GetLastInsertId();
                        }
                        $this->ID = $ID;
                        if($SOID == 0){
                            $this->Baza->Query("DELETE FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '0' AND no_so_id = '$ID'");
                            $UsedConty = array();
                            foreach($Conty as $Cont){
                                unset($Cont['cont_no_default']);
                                $Cont['id_zlecenie'] = 0;
                                $Cont['no_so_id'] = $ID;
                                $Cont['cont_weight'] = str_replace(",", ".", $Cont['cont_weight']);
                                $Cont['cont_volume'] = str_replace(",", ".", $Cont['cont_volume']);
                                $ZapFCL = $this->Baza->PrepareInsert("orderplus_sea_orders_fcl", $Cont);
                                $this->Baza->Query($ZapFCL);
                                echo mysql_error();
                                $UsedConty[] = $Cont['cont_no'];
                            }
                        }
                        $this->Baza->Query("DELETE FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id = '$ID'");
                        foreach($UsedConty as $ContNo){
                            $FCLSave['cont_number'] = $ContNo;
                            $FCLSave['zlecenie_so_id'] = $ID;
                            $Zap2 = $this->Baza->PrepareInsert("orderplus_sea_orders_zlecenia_fcl", $FCLSave);
                            $this->Baza->Query($Zap2);
                        }
                    }else{
                        $Error = true;
                        $this->Error = "Wystąpił błąd! Dane nie zostały zapisane";
                    }
                }else{
                    $Error = true;
                    $this->Error = "Nieprawidłowe numery kontenerów:".($CheckedFCL ? "<br />".implode("<br />", $CheckedFCL) : "");
                }
            }else{
                $Error = true;
                $this->Error = "Nie wybrano żadnego kontenera!";
            }

            if($Error){
                return false;
            }
            return true;
        }

       function ValidateContNumber($Conts){
            if(is_array($Conts)){
                foreach($Conts as $Idx => $Dane){
                    $Conts[$Idx]['cont_no_default'] = $Conts[$Idx]['cont_no'];
                    if($Conts[$Idx]['cont_no'] != ""){
                        $NO = preg_replace('/[^0-9A-Za-z]/', '', $Dane['cont_no']);
                        $NO_start = substr($NO, 0, 10);
                        $NO_end = substr($NO, 10, strlen($NO)-10);
                        $Conts[$Idx]['cont_no'] = $NO_start."-".$NO_end;
                    }
                }
            }
            return $Conts;
        }

        function CheckContNumbers($Values){
            $Checked = false;
            if(is_array($Values)){
                foreach($Values as $Cont){
                    if($Cont['cont_no_default'] != "" && !preg_match('/[A-Za-z]{4}+[0-9]{6}+\-[0-9]{1}$/', $Cont['cont_no'])){
                        $Checked[] = $Cont['cont_no_default'];
                    }
                }
            }
            return $Checked;
        }

        function GetContainers($Values, $ID){
            $this->Baza->Query("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$ID'");
            while($FCLRes = $this->Baza->GetRow()){
                $Values['FCL'][] = $FCLRes;
            }
            $this->Baza->Query("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$ID'");
            while($LCLRes = $this->Baza->GetRow()){
                $Values['LCL'][] = $LCLRes;
            }
            if(count($Values['FCL']) == 0){
                $Values['FCL'] = array();
            }
            if(count($Values['LCL']) == 0){
                $Values['LCL'] = array();
            }

            return $Values;
        }

        function GetUsed($SOID, $ID){
            $Wykorzystane = array();
            $Orders = $this->Baza->GetValues("SELECT zlecenie_so_id FROM orderplus_sea_orders_zlecenia WHERE id_zlecenie = '$SOID' AND zlecenie_so_id != '$ID'");
            if($Orders){
                $Wykorzystane = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id IN(".implode(",",$Orders).")");
            }
            return $Wykorzystane;
        }

        function ShowOK(){
            $Tresc = '<b>Rekord został zapisany</b><br/><br/>';
            $Tresc .= "<a href=\"#\" onclick=\"window.open('podglad-morskie-zlec.php?soid={$_GET['soid']}&id=$this->ID');\"><img src=\"images/podglad.gif\" border=\"0\"></a>";
            $Tresc .= "&nbsp;&nbsp;<a href=\"?modul=$this->Parametr&akcja=edycja&id=$this->ID\"><img src=\"images/popraw.gif\" border=\"0\"></a><br>"; 
            $Tresc .= "<br><br><br><a href=\"$this->LinkPowrotu\"><img src=\"images/ok.gif\" border=\"0\"></a>";
            Usefull::ShowKomunikatOK($Tresc);
        }

        function AkcjaDrukuj($ID, $Akcja){
            if($this->SprawdzUprawnienie("zlecenia_morskie_zlec") || isset($_GET['hash'])){
                if($Akcja == "podglad" || $Akcja == "podglad_przewoznik"){
                    if($Akcja == "podglad_przewoznik"){
                        $hash = $_GET['hash'];
                        $zlecenie = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE hash = '$hash'");
                        $ID = $zlecenie['zlecenie_so_id'];
                        $soid = $zlecenie['id_zlecenie'];
                    }else{
                        $zlecenie = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                        $soid = $_GET['soid'];
                    }
                    if($soid == 0){
                        $SeaOrder = array('mode' => 'FCL', 'sea_order_type' => "E");
                    }else{
                        $SeaOrder = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$soid'");
                    }
                    
                    $warunki_szablon = $this->Baza->GetData("SELECT * FROM orderplus_szablon WHERE id_szablon='{$zlecenie['id_szablon']}'");
                    $uzytkownik = $this->Baza->GetData("SELECT * FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$zlecenie['id_uzytkownik']}'");
                    $przewoznik_to = $this->Baza->GetData("SELECT * FROM orderplus_przewoznik WHERE id_przewoznik = '{$zlecenie['id_przewoznik_to']}'");
                    $Terms = UsefullBase::GetTerms($this->Baza);
                    $Carriers = UsefullBase::GetCarriers($this->Baza);
                    $Size = UsefullBase::GetSizes($this->Baza);
                    $Types = UsefullBase::GetTypes($this->Baza);
                    $Containers = array();
                    if($SeaOrder['mode'] == "FCL"){
                        $Conty = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id = '$ID'");
                        if(count($Conty) > 0){
                            $Containers = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$soid'");
                        }
                    }else{
                        $Conty = GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_lcl WHERE zlecenie_so_id = '$ID'");
                        if(count($Conty) > 0){
                            $Containers = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$soid'");
                        }
                    }
                    include(SCIEZKA_SZABLONOW."druki/podglad-morskie-zlec.tpl.php");
                }
            }
        }

        function ShowNaglowekDrukuj($Akcja){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_zlecenie.tpl.php');
        }

        function WyswietlAJAX($Action){
            if($Action == "fcl-row"){
                include(SCIEZKA_SZABLONOW."forms/fcl-row.tpl.php");
            }
        }

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "conty"){
                $Conty = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id = '{$Element['zlecenie_so_id']}'");
                echo("<td$Styl>".implode("<br />",$Conty)."</td>");
            }else if($Nazwa == "id_zlecenie"){
                if($Element[$Nazwa] == 0){
                    $Numer = "Bez SO";
                }else if($Element['typek'] == "morskie"){
                    $Numer = $this->Baza->GetValue("SELECT numer_zlecenia FROM orderplus_sea_orders WHERE id_zlecenie = '{$Element[$Nazwa]}'");
                }else  if($Element['typek'] == "drogowe"){
                    $Numer = $this->Baza->GetValue("SELECT numer_zlecenia FROM orderplus_sea_orders WHERE id_zlecenie = '{$Element['sea_order_id']}'");
                }
                echo("<td$Styl>$Numer</td>");
            }else{
                echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
            }
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $this->Emaile = UsefullBase::GetEmailePrzewoznika($this->Baza, $Dane['id_przewoznik_to']);
            if(!$this->Emaile){
                $this->Emaile = array();
            }else{
                $this->Emaile = explode(",", $this->Emaile);
            }
            return $Dane;
        }

        function AkcjaEmail($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularzWyslij($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
                                echo "<div style='clear: both;'></div>\n";
                                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
				if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
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
            if($Hash == ""){
                $id_przewoznika = $this->Baza->GetValue("SELECT id_przewoznik_to FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                $Hash = md5(date('r').$id_przewoznika);
                mysql_query("UPDATE o$this->Tabela SET hash = '$Hash' WHERE $this->PoleID = '$ID'");
            }
            $Mail = new MailSMTP($this->Baza);
            $tresc_maila = $this->GetEmailTresc($Hash);
            $SendEmails = "";
            $NoError = true;
            $Mail->SetEmail("MEPP Order System <order@meppeurope.com>");
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
		$tresc_maila .= "<meta http-equiv=\"content-type\" content=\"text/html;charset=ISO-8859-2\">\r\n";
		$tresc_maila .= "<title>Wydruk zlecenia</title>\r\n";
		$tresc_maila .= "\r\n</head>";
		$tresc_maila .= "<body bgcolor=\"#ffffff\">\r\n";
		$tresc_maila .= "<p>Szanowni Państwo</p>";
		$tresc_maila .= "<p>Po kliknięciu w poniższy link możliwe będzie wydrukowanie zlecenia:<br><b><a href=\"http://plus.meppeurope.com/zlecenia/podglad-morskie-zlec.php?hash=$Hash\">Drukuj zlecenie</a></b></p>";
		$tresc_maila .= "<p>MEPP European Freight Solutions Sp. z o.o.<br />ul.Mińska 63A, 03-828 Warszawa<br />NIP: 1132761110; REGON: 141763906</p>";
		$tresc_maila .= "<p></p>";
		$tresc_maila .= "</body></html>\r\n";
            return $tresc_maila;
        }

        function AkcjaLista($Filtry = array()){
		$this->WykonajAkcjeDodatkowa();
		$Pola = $this->PobierzListeElementow($Filtry);
                $Elementy = $this->PobierzZlecenia();
                
                $Puste = array();
		$AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Puste, true);
?>
<table class="lista">
	<tr>
		<th class='licznik'>Lp</th>
<?php
foreach ($Pola as $NazwaPola => $Opis) {
?>
		<?php
		$Styl = '';
		if(is_array($Opis)){
			$Styl = (isset($Opis['styl']) ? " style='{$Opis['styl']}'" : '');
                        $Styl .= (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
			$Opis = $Opis['naglowek'];
		}
                $this->ShowTH($NazwaPola, $Styl, $Opis);
		?>
<?php
}
	foreach($AkcjeNaLiscie as $Actions){
		echo "<th class='ikona'><img class='ikonka' src='images/buttons/{$Actions['img']}_grey.png' title='{$Actions['title']}' alt='{$Actions['title']}'></th>";
	}
?>
	</tr>
<?php

$Licznik = ($this->ParametrPaginacji > 0 ? $this->ParametrPaginacji*$this->IloscNaStrone : 0);
if(isset($_SESSION[$this->Parametr]['filtry_kolumn']['sorty_table'])){
    foreach($_SESSION[$this->Parametr]['filtry_kolumn']['sorty_table'] as $Pole => $Wartosc){
        if($Wartosc != ""){
            $Elementy = Usefull::SortArray($Elementy, $Pole, $Wartosc);
        }
    }
}
if($this->PaginBy == "array"){
    $ElementyObrob = $Elementy;
    $Elementy = array();
    $Elementy = $this->ArrayPagination($ElementyObrob, $this->ParametrPaginacji);
}
foreach($Elementy as $Element){
    $Licznik++;
    $this->ShowTR($Pola, $Element, $Licznik, $AkcjeNaLiscie);
}
if(count($this->Sumowanie) > 0){
    ?>
    <tr>
        <th class='licznik'>&nbsp;</th>
        <?php
            foreach ($Pola as $NazwaPola => $Opis) {
                $Styl = (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
                if(isset($this->Sumowanie[$NazwaPola])){
                    echo "<th$Styl>".$this->ShowSuma($NazwaPola)."</th>";
                }else{
                    echo "<th$Styl>&nbsp;</th>";
                }
            }
            foreach($AkcjeNaLiscie as $Actions){
		echo "<th class='ikona'>&nbsp;</th>";
            }
        ?>
    </tr>
    <?php
}
?>
</table>
<?php

	}

        function PobierzZlecenia(){
            $Where = $this->GenerujWarunki();
            $this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY id_zlecenie DESC");
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Element['typek'] = "morskie";
                $Elementy[] = $Element;
                $Numerki[] = $Element['numer_krotki'];
            }
            $WhereDrogowe = $this->GenerujWarunkiDrogowe();
            $this->Baza->Query("SELECT * FROM orderplus_zlecenie a $WhereDrogowe ORDER BY id_zlecenie DESC");
            while ($Element = $this->Baza->GetRow()) {
                $Element['typek'] = "drogowe";
                $Elementy[] = $Element;
                $Numerki[] = $Element['numer_zlecenia'];
            }
            array_multisort($Numerki, SORT_DESC, $Elementy);
            return $Elementy;
        }

}
?>
