<?php
/**
 * Moduł zlecenia lotnicze
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class AirOrders extends ModulBazowy {

    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_air_orders';
            $this->PoleID = 'id_zlecenie';
            $this->PoleNazwy = 'numer_zlecenia';
            $this->Nazwa = 'Zlecenie';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Filtry[] = array("opis" => "Rodzaj", "nazwa" => "air_order_type", "typ" => "lista", "opcje" => array("I" => "---- Import ----", "E" => "---- Export ----"), 'domyslna' => '---- wszystkie ----');
            $this->Filtry[] = array("opis" => "Załadowca lub odbiorca", "nazwa" => "zaladowca", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "MAWB lub HAWB", "nazwa" => "awb", "typ" => "tekst");
	}

        function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
                        $Where = "data_zlecenia LIKE '20%'".($this->Uzytkownik->CheckNoOddzial() ? " AND id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).")" : "");
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "awb"){
                                            $WartoscCheck = str_replace("-", "", $Wartosc);
                                            $Where .= " AND (replace(mawb,'-','') LIKE '%$WartoscCheck%' OR replace(hawb,'-','') LIKE '%$WartoscCheck%')";
                                        }else if($Pole == "zaladowca"){
                                            $Where .= " AND (shipper LIKE '%$Wartosc%' OR consignee LIKE '%$Wartosc%'";
                                            $Shipery = $this->Baza->GetValues("SELECT id_klient FROM orderplus_klient WHERE nazwa LIKE '%$Wartosc%'");
                                            if(count($Shipery) > 0){
                                                $Where .= " OR id_klient_shipper IN(".implode(",",$Shipery).") OR id_klient_consignee IN(".implode(",",$Shipery).")";
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
            return "data_zlecenia >= '{$_SESSION['okresStart']}-01' AND data_zlecenia <= '{$_SESSION['okresEnd']}-31'".($this->Uzytkownik->CheckNoOddzial() ? " AND id_oddzial = '{$_SESSION['id_oddzial']}'" : "");
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"numer_zlecenia" => 'Numer',
                        "data_zlecenia" => 'Data',
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podglad", "akcja_href" => "podglad-lotnicze.php?");
                $Akcje[] = array('img' => "copy_button", 'title' => "Duplikuj", "akcja_link" => "?modul=$this->Parametr&akcja=duplikacja&did={$Dane[$this->PoleID]}");
                
                 $TerminEdycji = date("Y-m-d", strtotime("+7 days"));
                if ($this->Uzytkownik->IsAdmin() || $this->Dzis < $TerminEdycji){
                    $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                }else{
                    $Akcje[] = array('img' => "edit_button_grey", 'title' => "Edycja");
                }
                if($this->Baza->GetValue("SELECT count(*) FROM orderplus_zlecenie WHERE air_order_id = '{$Dane[$this->PoleID]}'") > 0){
                        $Akcje[] = array('img' => "list_zlecenie", 'title' => "Zlecenia", "akcja_link" => "?modul=zlecenia_lotnicze_zlec&akcja=lista&aoid={$Dane[$this->PoleID]}"); 
                }else if($TH){
                    $Akcje[] = array('img' => "list_zlecenie");
                }else{
                    $Akcje[] = array();
                }
                $Akcje[] = array('img' => "add_zlecenie", 'title' => "Dodaj zlecenie", "akcja_link" => "?modul=zlecenia&akcja=dodawanie&aoid={$Dane[$this->PoleID]}");
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
                case "dodaj_bl":
                    $BL = new BL($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                    $BL->AkcjaDodajBL($ID);
                    break;
                case "edytuj_bl": 
                    $BL = new BL($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                    $BL->AkcjaEdytujBL($ID);
                    break;
                case "dodaj_import":
                    $this->AkcjaDodawanieImport();
                    break;
                case "dodaj_export":
                    $this->AkcjaDodawanieExport();
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
            $TerminEdycji = date("Y-m-d", strtotime($Result['termin_rozladunku']."+7 days"));
            if($this->Uzytkownik->IsAdmin() || ($_SESSION["uprawnienia_id"] == 2 && $this->Dzis < $TerminEdycji)){
                return true;
            }
            return false;
        }

        function AkcjaDodawanieImport(){
            $Type = "I";
            $Skrot = "IMP";
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Air'])){
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Air'];
                    $Values['Koszty'] = $_POST['Koszty'];
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
                }
            }else{
                $Values['FCL'][0] = array();
                $Values['LCL'][0] = array();
                $Values['mode'] = "FCL";
                include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
            }
        }

        function AkcjaDodawanieExport(){
            if($this->WystawiamyDoIstniejacegoZlecenia()){
                 $Dane = $this->Baza->GetData("SELECT * FROM orderplus_air_orders_zlecenia WHERE zlecenie_ao_id = '{$_GET['zid']}'");
            }
            $Type = "E"; 
            $Skrot = "EXP";
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Air'])){
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Air'];
                    $Values['Koszty'] = $_POST['Koszty'];
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
                }
            }else{
                if($this->WystawiamyDoIstniejacegoZlecenia()){
                    $Values['depot'] = $Dane['miejsce_podjecia'];
                    $Values['terminal'] = $Dane['terminal'];
                    $Values['booking_no'] = $Dane['nr_booking'];
                    $Values['carrier'] = $Dane['carrier'];
                    $Values['feeder'] = $Dane['feeder'];
                    $Values['pod'] = $Dane['pod'];
                    $this->Baza->Query("SELECT * FROM orderplus_air_orders_fcl WHERE id_zlecenie = '0' AND no_so_id = '{$_GET['zid']}'");
                    while($FCLRes = $this->Baza->GetRow()){
                        $Values['FCL'][] = $FCLRes;
                    }
                    $MoznaPodpiac = $this->Baza->GetOptions("SELECT zlecenie_ao_id, numer_zlecenia FROM orderplus_air_orders_zlecenia WHERE id_zlecenie = '0' AND zlecenie_ao_id != '{$_GET['zid']}'");
                }else{
                    $Values['FCL'][0] = array();
                    $Values['LCL'][0] = array();
                }
                $Values['mode'] = "FCL";
                include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
            }
        }

        function AkcjaDuplikacja($ID){
            $Values = $this->Baza->GetData("SELECT * FROM orderplus_air_orders WHERE id_zlecenie = '{$_GET['did']}'");
            $Type = $Values['air_order_type'];
            $Skrot = ($Type == "E" ? "EXP" : "IMP");
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Air'])){
                $Type = $this->Baza->GetValue("SELECT air_order_type FROM orderplus_air_orders WHERE id_zlecenie = '{$_GET['did']}'");
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Air'];
                    $Values['Koszty'] = $_POST['Koszty'];
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
                }
            }else{
                $Values = $this->PobierzKontenery($Values, $_GET['did']);
                include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
            }
        }

        function PobierzKontenery($Values, $ID){
            $this->Baza->Query("SELECT * FROM orderplus_air_orders_fcl WHERE id_zlecenie = '$ID'");
            while($FCLRes = $this->Baza->GetRow()){
                $Values['FCL'][] = $FCLRes;
            }
            $this->Baza->Query("SELECT * FROM orderplus_air_orders_lcl WHERE id_zlecenie = '$ID'");
            while($LCLRes = $this->Baza->GetRow()){
                $Values['LCL'][] = $LCLRes;
            }
            if(count($Values['FCL']) == 0){
                $Values['FCL'][0] = array();
            }
            if(count($Values['LCL']) == 0){
                $Values['LCL'][0] = array();
            }
            return $Values;
        }

        function AkcjaEdycja($ID){
            $DefaultValues = $this->Baza->GetData("SELECT * FROM orderplus_air_orders WHERE id_zlecenie = '$ID'");
            $Type = $DefaultValues['air_order_type'];
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Air'])){
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Air'];
                    $Values['Koszty'] = $_POST['Koszty'];
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
                }
            }else{
                $Values = $DefaultValues;
                $Values = $this->PobierzKontenery($Values, $ID);
                include(SCIEZKA_SZABLONOW."forms/air-order-form.tpl.php");
            }
        }

        function WystawiamyDoIstniejacegoZlecenia(){
            return (isset($_GET['zid']) && $_GET['zid'] > 0 ? true : false);
        }

        function Zapisz($Type, $Skrot, $DefaultValues = array()){
            $SaveValues = $_POST['Air'];
            $SaveValues['mode'] = "FCL"; 
            $Error = false;
            if($SaveValues['mode'] == "FCL"){
                $SaveFCL = $SaveValues['FCL'];
                $SaveLCL = array();
            }else{
                $SaveFCL = array();
                $SaveLCL = $SaveValues['LCL'];
            }
            unset($SaveValues['FCL']);
            unset($SaveValues['LCL']);
                $AktualnyMiesiac = date("m");
                $AktualnyRok = date("y");

                if($SaveValues['inland_carrier_id'] == -1){
                    if($_POST['NewPrzewoznik'] != ""){
                        $Przewoznik = $_POST['NewPrzewoznik'];
                        $ZapPrzewoznik = $this->Baza->PrepareInsert("orderplus_przewoznik", $Przewoznik);
                        if($this->Baza->Query($ZapPrzewoznik)){
                            $SaveValues['inland_carrier_id'] = $this->Baza->GetLastInsertId();
                        }
                    }
                }

                $ID = false;
                $Now = date("Y-m-d H:i:s");
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $ID = $_GET['id'];
                    $AddEdytowali = "";
                    foreach($SaveValues as $Pole => $Value){
                        if($Value != $DefaultValues[$Pole]){
                            $AddEdytowali = $_SESSION['nazywasie']." ($Now)#";
                            break;
                        }
                    }
                    if($AddEdytowali == "" && $this->Parametr == "tabela_rozliczen_lotnicze"){
                        if(isset($_POST['Koszty']) && !$DefaultValues['Koszty']){
                            $AddEdytowali = $_SESSION['nazywasie']." ($Now)#";
                        }else if(!isset($_POST['Koszty']) && $DefaultValues['Koszty']){
                            $AddEdytowali = $_SESSION['nazywasie']." ($Now)#";
                        }else if(isset($_POST['Koszty']) && $DefaultValues['Koszty']){
                            foreach($_POST['Koszty'] as $Idx => $Val){
                                foreach($Val as $Pole => $Value){
                                    if($Value != $DefaultValues['Koszty'][$Idx][$Pole]){
                                        $AddEdytowali = $_SESSION['nazywasie']." ($Now)#";
                                        break;
                                        break;
                                    }
                                }
                            }
                            foreach($DefaultValues['Koszty'] as $Idx => $Val){
                                foreach($Val as $Pole => $Value){
                                    if($Value != $_POST['Koszty'][$Idx][$Pole]){
                                        $AddEdytowali = $_SESSION['nazywasie']." ($Now)#";
                                        break;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $SaveValues['edytowali'] = $DefaultValues['edytowali'].$AddEdytowali;
                    $Zapytanie = $this->Baza->PrepareUpdate("orderplus_air_orders", $SaveValues, array('id_zlecenie' => $_GET['id']));
                }else{
                    $SaveValues['id_uzytkownik'] = $_SESSION['id_uzytkownik'];
                    $SaveValues['id_oddzial'] = $_SESSION['id_oddzial'];
                    $SaveValues['data_zlecenia'] = date("Y-m-d");
                    $SaveValues['data_wprowadzenia'] = date("Y-m-d H:i:s");
                    $SaveValues['air_order_type'] = $Type;
                    if($Type == "I"){
                        if($SaveValues['data_zlecenia'] > "2014-05-31"){
                            $SaveValues['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT MAX(numer_zlecenia_krotki) FROM orderplus_air_orders WHERE air_order_type = '$Type' AND data_zlecenia LIKE '".date("Y-m")."-%'")+1;
                        }else{
                            $SaveValues['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT MAX(numer_zlecenia_krotki) FROM orderplus_air_orders WHERE air_order_type = '$Type' AND id_klient_consignee = '{$SaveValues['id_klient_consignee']}'")+1;
                        }
                        $Kli = substr($this->Baza->GetValue("SELECT nazwa  FROM orderplus_klient WHERE id_klient = '{$SaveValues['id_klient_consignee']}'"), 0, 3);
                        if($Kli == ""){
                            $Kli = substr($SaveValues['consignee'], 0, 3);
                        }
                    }else{
                        $Kli = substr($this->Baza->GetValue("SELECT nazwa  FROM orderplus_klient WHERE id_klient = '{$SaveValues['id_klient_shipper']}'"), 0, 3);
                        if($SaveValues['data_zlecenia'] > "2014-05-31"){
                            $SaveValues['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT MAX(numer_zlecenia_krotki) FROM orderplus_air_orders WHERE air_order_type = '$Type' AND data_zlecenia LIKE '".date("Y-m")."-%'")+1;
                        }else{
                            $SaveValues['numer_zlecenia_krotki'] = $this->Baza->GetValue("SELECT MAX(numer_zlecenia_krotki) FROM orderplus_air_orders WHERE air_order_type = '$Type' AND id_klient_shipper = '{$SaveValues['id_klient_shipper']}'")+1;
                        }
                        if($Kli == ""){
                            $Kli = substr($SaveValues['shipper'], 0, 3);
                        }
                    }
                    $SaveValues['numer_zlecenia'] = "$Skrot/$Kli/$AktualnyMiesiac/$AktualnyRok/{$SaveValues['numer_zlecenia_krotki']}";
                    $Zapytanie = $this->Baza->PrepareInsert("orderplus_air_orders", $SaveValues);
                }
                if($this->Baza->Query($Zapytanie)){
                    if(!$ID){
                        $ID = $this->Baza->GetLastInsertId();
                    }
                    $this->ID = $ID;
                    $IsFCL = array();
                    if(is_array($SaveFCL)){
                        foreach($SaveFCL as $FCL){
                            $IsFCL[] = $FCL['order_fcl_id'];
                        }
                    }
                    $this->Baza->Query("DELETE FROM orderplus_air_orders_fcl WHERE id_zlecenie = '$ID'".(count($IsFCL) > 0 ? " AND order_fcl_id NOT IN(".implode(",",$IsFCL).")" : ""));
                    $this->Baza->Query("DELETE FROM orderplus_air_orders_lcl WHERE id_zlecenie = '$ID'");
                    if(is_array($SaveFCL)){
                        foreach($SaveFCL as $FCL){
                            unset($FCL['cont_no_default']);
                            $FCL['id_zlecenie'] = $ID;
                            $FCL['cont_weight'] = str_replace(",", ".", $FCL['cont_weight']);
                            $FCL['cont_volume'] = str_replace(",", ".", $FCL['cont_volume']);
                            if($FCL['order_fcl_id'] > 0){
                                $OrderFCLId = $FCL['order_fcl_id'];
                                unset($FCL['order_fcl_id']);
                                $ZapFCL = $this->Baza->PrepareUpdate("orderplus_air_orders_fcl", $FCL, array('order_fcl_id' => $OrderFCLId));
                            }else{
                                unset($FCL['order_fcl_id']);
                                $ZapFCL = $this->Baza->PrepareInsert("orderplus_air_orders_fcl", $FCL);
                            }
                            $this->Baza->Query($ZapFCL);
                        }
                    }
                    if(is_array($SaveLCL)){
                        foreach($SaveLCL as $LCL){
                            unset($LCL['cont_no_default']);
                            $LCL['id_zlecenie'] = $ID;
                            $LCL['cont_weight'] = str_replace(",", ".", $LCL['cont_weight']);
                            $LCL['cont_volume'] = str_replace(",", ".", $LCL['cont_volume']);
                            $ZapLCL = $this->Baza->PrepareInsert("orderplus_air_orders_lcl", $LCL);
                            $this->Baza->Query($ZapLCL);
                        }
                    }
                    if(isset($_GET['zid']) && $_GET['zid'] > 0){
                        $this->Baza->Query("DELETE FROM orderplus_air_orders_fcl WHERE id_zlecenie = '0' AND cont_no = '{$Dane['cont_number']}'");
                        $this->Baza->Query("UPDATE orderplus_air_orders_zlecenia SET id_zlecenie = '$ID' WHERE zlecenie_ao_id = '{$_GET['zid']}'");
                        foreach($_POST['DoPodpiecia'] as $PodID){
                            $this->Baza->Query("UPDATE orderplus_air_orders_fcl SET id_zlecenie = '$ID', no_so_id = '0' WHERE id_zlecenie = '0' AND no_so_id = '$PodID'");
                            $this->Baza->Query("UPDATE orderplus_air_orders_zlecenia SET id_zlecenie = '$ID' WHERE zlecenie_ao_id = '$PodID'");
                        }
                    }
                    if($this->Parametr == "tabela_rozliczen_lotnicze"){

                        //mysql_query("DELETE FROM orderplus_air_orders_koszty WHERE id_zlecenie = '$ID'");
                        $Edytowane = array();
                        if(isset($_POST['Koszty'])){
                            foreach($_POST['Koszty'] as $Koszty){
                                $Koszty['koszt'] = str_replace(",", ".", $Koszty['koszt']);
                                $Koszty['kurs'] = str_replace(",", ".", $Koszty['kurs']);
                                if(isset($Koszty['id_koszt'])){
                                    $KosztID = $Koszty['id_koszt'];
                                    $Edytowane[] = $KosztID;
                                    unset($Koszty['id_koszt']);
                                    $ZapKoszty = $this->Baza->PrepareUpdate("orderplus_air_orders_koszty", $Koszty, array("id_koszt" => $KosztID));
                                }else{
                                    $Koszty['id_zlecenie'] = $ID;
                                    $ZapKoszty = $this->Baza->PrepareInsert("orderplus_air_orders_koszty", $Koszty);
                                }
                                $this->Baza->Query($ZapKoszty);
                            }
                        }
                        foreach($DefaultValues['Koszty'] as $Check){
                            if(!in_array($Check['id_koszt'], $Edytowane)){
                                //echo "DELETE FROM orderplus_air_orders_koszty WHERE id_koszt = '{$Check['id_koszt']}'<br />";
                                $this->Baza->Query("DELETE FROM orderplus_air_orders_koszty WHERE id_koszt = '{$Check['id_koszt']}'");
                            }
                        }
                    }
                }else{
                    $Error = true;
                    $this->Error = "Wystąpił błąd! Dane nie zostały zapisane";
                }

            if($Error){
                return false;
            }
            return true;
        }

        function ShowOK(){
            $Tresc = '<b>Rekord został zapisany</b><br/><br/>';
            $Tresc .= "<a href=\"#\" onclick=\"window.open('podglad-lotnicze.php?id=$this->ID');\"><img src=\"images/podglad.gif\" border=\"0\"></a>";
            $Tresc .= "&nbsp;&nbsp;<a href=\"?modul=$this->Parametr&akcja=edycja&id=$this->ID\"><img src=\"images/popraw.gif\" border=\"0\"></a><br>";
            $Tresc .= "<br><br><br><a href=\"$this->LinkPowrotu\"><img src=\"images/ok.gif\" border=\"0\"></a>";
            Usefull::ShowKomunikatOK($Tresc);
        }

        function AkcjaDrukuj($ID, $Akcja){
            if($this->SprawdzUprawnienie("zlecenia_lotnicze") || isset($_GET['hash'])){
                if($Akcja == "podglad"){
                    $zlecenie = $this->PobierzDaneElementu($ID);
                    $uzytkownik = $this->Baza->GetData("SELECT imie, nazwisko, id_oddzial FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$zlecenie['id_uzytkownik']}'");
                    $Terms = UsefullBase::GetTerms($this->Baza);
                    $Carriers = UsefullBase::GetCarriers($this->Baza);
                    $Size = UsefullBase::GetSizes($this->Baza);
                    $Types = UsefullBase::GetTypes($this->Baza);
                    if($zlecenie['mode'] == "FCL"){
                        $FCL = $this->Baza->GetRows("SELECT * FROM orderplus_air_orders_fcl WHERE id_zlecenie = '$ID'");
                        foreach($FCL as $FCLRes){
                            $Containers[] = $FCLRes;
                        }
                    }else{
                        $LCL = $this->Baza->GetRows("SELECT * FROM orderplus_air_orders_lcl WHERE id_zlecenie = '$ID'");
                        foreach($LCL as $LCLRes){
                            $Containers[] = $LCLRes;
                        }
                    }
                    include(SCIEZKA_SZABLONOW."druki/podglad-lotnicze.tpl.php");
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

        function WyswietlAJAX($Action){
            if($Action == "fcl-row"){
                include(SCIEZKA_SZABLONOW."forms/fcl-air-row.tpl.php");
            }
        }
}
?>
