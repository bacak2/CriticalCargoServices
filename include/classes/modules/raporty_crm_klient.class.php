<?php
/**
 * Moduł raportów CRM
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class RaportyCRMClient extends RaportyCRM {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            unset($this->Filtry);
            $this->Filtry[] = array('opis' => "Oddział", "nazwa" => "id_oddzial", "opcje" => $this->Oddzialy, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Opiekun", "nazwa" => "id_uzytkownik", "opcje" => $this->Userzy, "typ" => "lista");
            $this->Filtry[] = array();
            $this->Filtry[] = array('opis' => 'Od dnia', "nazwa" => "data_zdarzenia_od", "typ" => "data");
            $this->Filtry[] = array('opis' => 'Do dnia', "nazwa" => "data_zdarzenia_do", "typ" => "data");
            $this->Filtry[] = array(); 

	}

        function AkcjaLista(){

        }

        function ShoFiltry(){
            $Action = "raport_dopisanych_klientow.php";
            include(SCIEZKA_SZABLONOW."filters-raporty-export.tpl.php");
        }

}
?>
