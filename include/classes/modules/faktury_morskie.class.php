<?php
/**
 * Moduł faktury
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class FakturyMorskie extends ModulBazowy {
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_sea_orders_faktury';
            $this->PoleID = 'id_faktury';
            $this->PoleNazwy = 'numer';
            $this->Nazwa = 'Faktury';
            $this->LinkPowrotu = '?modul=faktury';
            if(isset($_GET['back']) && $_GET['back'] == "tb"){
                $this->LinkPowrotu = '?modul=tabela_rozliczen_morskie';
            }
	}

        function AkcjaDrukuj($ID, $Akcja){
            if($this->SprawdzUprawnienie("faktury") || $Akcja == "wydruk_zbiorczy"){
                if($Akcja == "wydruk" ||  $Akcja == "wydruk_zbiorczy"){
                    $Waluty = UsefullBase::GetWaluty($this->Baza);
                    $Size = UsefullBase::GetSizes($this->Baza);
                    $Types = UsefullBase::GetTypes($this->Baza);
                    $Terms = UsefullBase::GetTerms($this->Baza);
                    $faktura = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'");
                    if($faktura['szablon_faktura'] == 'ENG'){
                        include(SCIEZKA_INCLUDE."faktura_lang/eng.php");
                    }else{
                       include(SCIEZKA_INCLUDE."faktura_lang/pl.php");
                    }
                    $FormyPlatnosci = $this->Baza->GetOptions("SELECT id_formy, ".($faktura['szablon_faktura'] == "ENG" ? "forma_en" : "forma")." FROM faktury_formy_platnosci");
                    $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '{$faktura['id_zlecenia']}'");
                    $UsedConty = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_faktury_fcl WHERE id_faktury = '$ID'");
                    $Conty = array();
                    if($SOI['mode'] == "FCL"){
                        $this->Baza->Query("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '{$faktura['id_zlecenia']}'");
                        while($FCLRes = $this->Baza->GetRow()){
                            if(in_array($FCLRes['cont_no'], $UsedConty)){
                                $Conty[] = $FCLRes;
                            }
                        }

                    }else{
                        $this->Baza->Query("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '{$faktura['id_zlecenia']}'");
                        while($LCLRes = $this->Baza->GetRow()){
                            if(in_array($LCLRes['cont_no'], $UsedConty)){
                                $Conty[] = $LCLRes;
                            }
                        }
                    }
                    if(($_SESSION['login'] == "artplusadmin2" && !isset($_GET['dev'])) || $_GET['dev'] == "dev"){
                        include(SCIEZKA_SZABLONOW."druki/drukuj-fakture-morska-new-design.tpl.php");
                    }else{
                        include(SCIEZKA_SZABLONOW."druki/drukuj-fakture-morska.tpl.php");
                    }
                }
            }
        }

        function ShowNaglowekDrukuj(){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_faktura.tpl.php');
        }

        function AkcjaDodawanie(){
            $SOID = (isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0);
            if($SOID > 0){
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$SOID'");
                $Values['FCL'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$SOID'");
                $Values['LCL'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$SOID'");
                if(count($Values['FCL']) == 0){
                    $Values['FCL'] = array();
                }
                if(count($Values['LCL']) == 0){
                    $Values['LCL'] = array();
                }
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Faktura'])){
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = PolaczDwieTablice($Values, $_POST['Faktura']);
                    $Error = "Wystąpił błąd! Dane nie zostały zapisane";
                    Usefull::ShowKomunikatError('<b>'.$Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/faktura-morska-form.tpl.php");
                }
            }else{
                $Wykorzystane = array();
                if($SOID > 0){
                    $Values['id_klienta'] = $SOI['nabywca_id'];
                    $Values['id_klient_text'] = $SOI['shipper'];
                    $Values['id_klient_odbiorca'] = $SOI['id_klient_consignee'];
                    $Values['odbiorca'] = $SOI['consignee'];
                    $Values['id_klient_shipper'] = $SOI['id_klient_shipper'];
                    $Values['shipper'] = $SOI['shipper'];
                    $Values['terms_id'] = $SOI['terms_id'];
                    $Values['terms_text'] = $SOI['terms_text'];
                    $Values['cont_number'] = array();
                }else{
                    $SOI = array();
                    $Values['FCL'][] = array();
                    $Values['LCL'][] = array();
                    $Values['cont_number'] = array();
                    $SOI['mode'] = "FCL";
                }
                $Values['Pos'][0] = array('lp' => 1);
                include(SCIEZKA_SZABLONOW."forms/faktura-morska-form.tpl.php");
            }
        }

        function AkcjaEdycja($ID){
            $SOID = (isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0);
            if($SOID > 0){
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$SOID'");
                $Values['FCL'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$SOID'");
                $Values['LCL'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$SOID'");
                if(count($Values['FCL']) == 0){
                    $Values['FCL'] = array();
                }
                if(count($Values['LCL']) == 0){
                    $Values['LCL'] = array();
                }
            }
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Faktura'])){
                if($this->Zapisz($Type, $Skrot)){
                    $this->ShowOK();
                }else{
                    $Values = PolaczDwieTablice($Values, $_POST['Faktura']);
                    $Error = "Wystąpił błąd! Dane nie zostały zapisane";
                    Usefull::ShowKomunikatError('<b>'.$Error.'</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/faktura-morska-form.tpl.php");
                }
            }else{
                $Wykorzystane = array();
                $Values['Pos'] = false;
                $Dane = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_faktury WHERE id_faktury = '$ID'");
                $Values = Usefull::PolaczDwieTablice($Values, $Dane);
                $Values['Pos'] = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$ID' ORDER BY lp ASC");
                if(!$Values['Pos']){
                    $Values['Pos'][0] = array();
                }
                $Values['cont_number'] = $this->Baza->GetValues("SELECT cont_number FROM orderplus_sea_orders_faktury_fcl WHERE id_faktury = '$ID'");
                include(SCIEZKA_SZABLONOW."forms/faktura-morska-form.tpl.php");
            }
        }

        function Zapisz(){
           $SaveValues = $_POST['Faktura'];
            $Positions = $SaveValues['Pos'];
            $Cont = $SaveValues['cont_number'];
            $Waluty = UsefullBase::GetWaluty($this->Baza);
            unset($SaveValues['Pos']);
            unset($SaveValues['cont_number']);
            $ID = false;
            $SaveValues['kurs'] = str_replace(",", ".", $SaveValues['kurs']);
            if($Waluty[$SaveValues['id_waluty']] != "PLN" && floatval($SaveValues['kurs']) == 0){
                $SaveValues['kurs'] = UsefullBase::PobierzKursZDnia($this->Baza, $SaveValues['data_wystawienia'], $Waluty[$SaveValues['id_waluty']], $SaveValues['id_klienta']);
            }
            if(isset($_GET['id']) && is_numeric($_GET['id'])){
                $ID = $_GET['id'];
                if(isset($SaveValues['numer'])){
                    $Explode = explode("/", trim($SaveValues['numer']));
                    if($SaveValues['data_wystawienia'] < '2013-01-11' && count($Explode) == 4){
                        $SaveValues['autonumer'] = $Explode[0];
                        $SaveValues['automiesiac'] = $Explode[1];
                        $SaveValues['autorok'] = $Explode[2];
                    }
                    if($SaveValues['data_wystawienia'] >= '2013-01-11' && count($Explode) == 5){
                        $SaveValues['autonumer'] = $Explode[2];
                        $SaveValues['automiesiac'] = $Explode[3];
                        $SaveValues['autorok'] = $Explode[4];
                    }
                }
                $Zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders_faktury", $SaveValues, array('id_faktury' => $_GET['id']));
            }else{
                $dataExp = explode("-", $SaveValues['data_wystawienia']);
                $autorok = $dataExp[0];
                $automiesiac = $dataExp[1];
                if($SaveValues['data_wystawienia'] < '2013-01-11'){
                    $autonumer = $this->Baza->GetValue("SELECT MAX(autonumer) FROM orderplus_sea_orders_faktury WHERE data_wystawienia LIKE '$autorok-%' AND data_wystawienia < '2013-01-11'");
                    $autonumer++;
                    $sugerowany_numer_faktury = "$autonumer/$automiesiac/$autorok/GD";
                }else{
                    $oddzial_id = $this->Baza->GetValue("SELECT id_oddzial FROM orderplus_sea_orders WHERE id_zlecenie = '{$_GET['soid']}'");
                    $prefix = ($oddzial_id == 10 ? "WAW/SEA/" : "GDY/SEA/");
                    $autonumer = $this->Baza->GetValue("SELECT MAX(autonumer) FROM orderplus_sea_orders_faktury WHERE data_wystawienia LIKE '$autorok-$automiesiac-%' AND data_wystawienia >= '2013-01-11'");
                    $autonumer++;
                    $sugerowany_numer_faktury = $prefix."$autonumer/$automiesiac/$autorok";
                }
                $SaveValues['autonumer'] = $autonumer;
                $SaveValues['automiesiac'] = $automiesiac;
                $SaveValues['autorok'] = $autorok;
                $SaveValues['numer'] = $sugerowany_numer_faktury;
                $SaveValues['id_zlecenia'] = $_GET['soid'];
                $SiedzibaID =  $this->Baza->GetValue("SELECT siedziba_id FROM orderplus_klient WHERE id_klient = '{$SaveValues['id_klienta']}'");
                $SaveValues['szablon_faktura'] = ($SiedzibaID ? ($SiedzibaID == 2 ? "ENG" : "PL") : "PL");
                $Zapytanie =  $this->Baza->PrepareInsert("orderplus_sea_orders_faktury", $SaveValues);
            }
            if($this->Baza->Query($Zapytanie)){
                if(!$ID){
                    $ID = $this->Baza->GetLastInsertId();
                }
                $this->Baza->Query("DELETE FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$ID'");
                foreach($Positions as $Pos){
                    $Pos['id_faktury'] = $ID;
                    $Pozycje = $this->Baza->PrepareInsert("orderplus_sea_orders_faktury_pozycje", $Pos);
                    $this->Baza->Query($Pozycje);
                }
                $this->Baza->Query("DELETE FROM orderplus_sea_orders_faktury_fcl WHERE id_faktury = '$ID'");
                if(count($Cont) > 0){
                    foreach($Cont as $FCL){
                        $FCLVal['id_faktury'] = $ID;
                        $FCLVal['cont_number'] = $FCL;
                        $ZapFCL = $this->Baza->PrepareInsert("orderplus_sea_orders_faktury_fcl", $FCLVal);
                        $this->Baza->Query($ZapFCL);
                    }
                }
                return true;
           }else{
               return false;
           }
        }

         function WyswietlAJAX($Action){
            if($Action == "faktura-pozycja"){
                include(SCIEZKA_SZABLONOW."forms/faktura-pozycja.tpl.php");
            }
            if($Action == "get-action-list"){
                $Akcje = array();
                $SOID = $this->Baza->GetValue("SELECT id_zlecenia FROM $this->Tabela WHERE $this->PoleID = '{$_POST['id']}'"); 
                $Akcje[] = array('title' => "Drukuj", "akcja_href" => "drukuj_fakture_morska.php?", "_blank" => true);
                $Akcje[] = array('title' => "Edycja", "akcja_href" => "?modul=faktury_morskie&akcja=edycja&back=tb&soid=$SOID&");
                $this->ShowActionInPopup($Akcje, $_POST['id']);
            }
        }

}
?>
