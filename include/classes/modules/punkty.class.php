<?php
/**
 * Moduł punktów przeładunku
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Punkty extends ModulBazowy {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_punkty';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'nazwa';
            $this->Nazwa = 'Punkt przeładunku';
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('nazwa', 'tekst', 'Nazwa punktu', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('ulica', 'tekst', 'Ulica', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('kod_pocztowy', 'tekst', 'Kod pocztowy', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('miasto', 'tekst', 'Miejscowość', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('kraj', 'tekst', 'Kraj', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
	
	function PobierzListeElementow($Filtry = array()) {
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT $this->PoleID, nazwa, ulica, CONCAT(kod_pocztowy,' ',miasto) as adres, kraj FROM $this->Tabela a $Where ORDER BY $this->PoleNazwy");
		$Wynik = array(
			"nazwa" => 'Punkt',
                        "ulica" => 'Adres',
                        "adres" => 'Miasto',
                        "kraj" => 'Kraj'
		);
		return $Wynik;
	}

}
?>
