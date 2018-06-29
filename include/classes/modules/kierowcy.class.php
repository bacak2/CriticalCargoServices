<?php
/**
 * Moduł kierowcy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Kierowcy extends ModulBazowy {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_kierowca';
            $this->PoleID = 'id_kierowca';
            $this->PoleNazwy = 'imie_nazwisko';
            $this->Nazwa = 'Kierowca';
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('id_przewoznik', 'lista', 'Przewoźnik', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->GetPrzewoznicy()));
            $Formularz->DodajPole('imie_nazwisko', 'tekst', 'Imię i Nazwisko', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('rejestracja', 'tekst', 'Rejestracja', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('dane_kierowcy', 'tekst_dlugi', 'Dane kierowcy', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"imie_nazwisko" => 'Imię i Nazwisko',
                        "id_przewoznik" => array('naglowek' => 'Przewoźnik', 'elementy' => $this->GetPrzewoznicy())
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela a $Where ORDER BY $this->PoleNazwy");
		return $Wynik;
	}

        function GetKierowcyByPrzewoznik($PrzewoznikID){
            return $this->Baza->GetOptions("SELECT $this->PoleID, $this->PoleNazwy FROM orderplus_kierowca WHERE id_przewoznik = '$PrzewoznikID' ORDER BY $this->PoleNazwy ASC");
        }

        function GetPrzewoznicy(){
            $Przewoznicy = new Przewoznicy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $Przewoznicy->GetList();
        }

}
?>
