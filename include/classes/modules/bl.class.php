<?php
/**
 * Moduł BL
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class BL extends ModulBazowy {
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
	}

        function AkcjaDodajBL($ID){
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Sea'])){
                if($this->Zapisz($ID)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Sea'];
                    Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/bl.tpl.php");
                }
            }else{
                $Values = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$ID'");
                include(SCIEZKA_SZABLONOW."forms/bl.tpl.php");
            }
        }

        function AkcjaEdytujBL($ID){
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Sea'])){
                if($this->Zapisz($ID)){
                    $this->ShowOK();
                }else{
                    $Values = $_POST['Sea'];
                    Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    include(SCIEZKA_SZABLONOW."forms/bl.tpl.php");
                }
            }else{
                $Values = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_bl WHERE id_zlecenie = '$ID'");
                include(SCIEZKA_SZABLONOW."forms/bl.tpl.php");
            }
        }

        function ShowOK(){
            $Tresc = '<b>Rekord został zapisany</b><br/><br/>';
            $Tresc .= "<a href=\"#\" onclick=\"window.open('drukuj-bl.php?id=$this->ID');\"><img src=\"images/podglad.gif\" border=\"0\"></a>"; 
            $Tresc .= "<br><br><br><a href=\"$this->LinkPowrotu\"><img src=\"images/ok.gif\" border=\"0\"></a>";
            Usefull::ShowKomunikatOK($Tresc);
        }

        function Zapisz($ID){
            $SaveValues = $_POST['Sea'];
            if(isset($_FILES['Attach'])){
                if (is_uploaded_file($_FILES['Attach']['tmp_name'])) {
                    $NazwaPliku = $ID."-".$_FILES["Attach"]['name'];
                    $Plik = "BL-Attach/$NazwaPliku";
                    $Sciezka = dirname($Plik);
                    $StaryUmask = umask(0);
                    if (move_uploaded_file($_FILES["Attach"]['tmp_name'], $Plik)) {
                       chmod($Plik, 0777);
                       $SaveValues['attachment'] = $_FILES["Attach"]['name'];
                    }
                    umask($StaryUmask);
                }
            }
            if($this->Baza->GetValue("SELECT count(*) FROM orderplus_sea_orders_bl WHERE id_zlecenie = '$ID'") > 0){
                $Zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders_bl", $SaveValues, array('id_zlecenie' => $ID));
            }else{
                $SaveValues['id_zlecenie'] = $ID;
                $SaveValues['numer_krotki'] = $this->Baza->GetValue("SELECT MAX(numer_krotki) FROM orderplus_sea_orders_bl")+1;
                if($SaveValues['numer_krotki'] < 100001){
                    $SaveValues['numer_krotki'] = 100001;
                    $SaveValues['numer'] = "MEPP{$SaveValues['numer_krotki']}";
                }
                $Zapytanie = $this->Baza->PrepareInsert("orderplus_sea_orders_bl", $SaveValues);
            }
            if($this->Baza->Query($Zapytanie)){
                $this->ID = $ID;
                return true;
            }
            return false;
        }

        function AkcjaDrukuj($ID, $Akcja){
            if($Akcja == "form"){
                $SOI_baza = $this->Baza->GetOptions("SELECT o.id_zlecenie, o.numer_zlecenia FROM orderplus_sea_orders o
                                                    LEFT JOIN orderplus_sea_orders_konosament ok ON(ok.sea_order_id = o.id_zlecenie)
                                                    WHERE sea_order_type = 'E' AND (ok.fbl_number IS NULL OR sea_order_id = '{$_POST['sea_order_id']}' OR sea_order_id = '$ID') ORDER BY numer_zlecenia ASC");
                $SOI = Usefull::PolaczDwieTablice(array(0 => 'brak zlecenia'), $SOI_baza);
                if($ID){
                    $Wartosci = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_konosament WHERE sea_order_id = '$ID'");
                }
                include(SCIEZKA_SZABLONOW."druki/bl-form-html.tpl.php");
            }else{
                $SOI = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$ID'");
                $zlecenie = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders_bl WHERE id_zlecenie = '$ID'");
                $Terms = UsefullBase::GetTerms($this->Baza);
                $Carriers = UsefullBase::GetCarriers($this->Baza);
                $Size = UsefullBase::GetSizes($this->Baza);
                $Types = UsefullBase::GetTypes($this->Baza);
                if($SOI['mode'] == "FCL"){
                    $FCL = mysql_query("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$ID'");
                    foreach($FCL as $FCLRes){
                        $Containers[] = $FCLRes;
                    }
                }else{
                    $LCL = mysql_query("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$ID'");
                    foreach($LCL as $LCLRes){
                        $Containers[] = $LCLRes;
                    }
                }
                include(SCIEZKA_SZABLONOW."druki/bl.tpl.php");
            }
        }

        function ShowNaglowekDrukuj($Akcja){
            include(SCIEZKA_SZABLONOW."naglowek_drukuj_bl.tpl.php");
        }
}
?>
