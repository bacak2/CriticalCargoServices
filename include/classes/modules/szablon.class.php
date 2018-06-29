<?php
/**
 * Moduł szablon
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Szablon extends ModulBazowy {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_szablon';
            $this->PoleID = 'id_szablon';
            $this->PoleNazwy = 'tytul';
            $this->Nazwa = 'Szablon';
            $this->CzySaOpcjeWarunkowe = true;
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('lp', 'tekst', 'Lp', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
            $Formularz->DodajPole('jezyk', 'lista', 'Język', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetSzablonLangs()));
            $Formularz->DodajPole('tytul', 'tekst', 'Tytuł', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('pelny_tekst', 'tekst_dlugi', 'Treść', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 500px;')));
            $Formularz->DodajPole('termin_platnosci_dni', 'tekst', 'Termin płatności przewoźnika w dniach', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => "width: 80px;")));
            $Formularz->DodajPole('status', 'podzbiór_radio', 'Wyświetlany', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetTakNie()));
            $Formularz->DodajTinyMCE('pelny_tekst', true);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"lp" => 'Lp',
                        "tytul" => 'Nazwa szablonu',
                        "jezyk" => array('naglowek' => 'Język', 'td_styl' => 'text-align: center;'),
                        "status" => array('naglowek' => 'Status', 'elementy' => array(0 => '-', 1 => '+'), 'td_styl' => 'text-align: center;')
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY lp ASC");
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                if(in_array($_SESSION['uprawnienia_id'], array(1,4))){
                    $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                    $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie", 'hidden' => (!$Dane[$this->PoleID] || $Dane[$this->PoleID] > 1 ? false : true)); 
                }
		return $Akcje;
	}
}
?>
