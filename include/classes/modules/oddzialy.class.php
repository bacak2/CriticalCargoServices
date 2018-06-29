<?php
/**
 * Moduł oddziałów
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Oddzialy extends ModulBazowy {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_oddzial';
            $this->PoleID = 'id_oddzial';
            $this->PoleNazwy = 'nazwa';
            $this->Nazwa = 'Oddziały';
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('nazwa', 'tekst', 'Nazwa oddziału', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('skrot', 'tekst', 'Skrót<br /><small>do numeracji zleceń,faktur</small>', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('prefix', 'tekst', 'Prefix<br /><small>do numeracji zleceń,faktur</small>', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('kod_pocztowy', 'tekst', 'Kod pocztowy', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
	
	function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			"nazwa" => 'Nazwa oddziału',
                        "kierownik_id" => 'Kierownicy oddziału'
		);
                $Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela $Where ORDER BY $this->PoleNazwy");
		return $Wynik;
	}

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "kierownik_id"){
                $Kierownicy = $this->Baza->GetValues("SELECT login FROM orderplus_uzytkownik WHERE id_oddzial = '{$Element[$this->PoleID]}' AND uprawnienia_id = '2'");
                if($Kierownicy){
                    $Element[$Nazwa] = implode("<br />", $Kierownicy);
                }
            }
            echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
        }

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
		$Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
		}
                $Akcje[] = array('img' => "desc_button", 'title' => "Szczegóły", "akcja" => "szczegoly");
		return $Akcje;
	}

}
?>
