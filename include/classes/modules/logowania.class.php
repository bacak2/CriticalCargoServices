<?php
/**
 * Moduł raportów CRM
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Logowania extends RaportyCRM {

	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'logowania';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'login';
            $this->CzySaOpcjeWarunkowe = true;
            unset($this->Filtry);
            $this->Filtry[] = array('opis' => "Użytkownik", "nazwa" => "id_uzytkownik", "opcje" => $this->Userzy, "typ" => "lista");
            $this->Filtry[] = array('nazwa' => 'bbb');
            $this->Filtry[] = array('nazwa' => 'aaa');
            $this->Filtry[] = array('opis' => "Dzień", "nazwa" => "data_zdarzenia", "typ" => "data");
            $this->Filtry[] = array('opis' => 'Od dnia', "nazwa" => "data_zdarzenia_od", "typ" => "data");
            $this->Filtry[] = array('opis' => 'Do dnia', "nazwa" => "data_zdarzenia_do", "typ" => "data");

	}

        function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "data_zdarzenia_od"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."(login >= '$Wartosc')";
                                        }else if($Pole == "data_zdarzenia_do"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."(login <= '$Wartosc')";
                                        }else if($Pole == "data_zdarzenia"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."(login >= '$Wartosc 00:00:00' AND login <= '$Wartosc 23:59:59')";
                                        }else{
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                        }
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

	
	function PobierzListeElementow($Filtry = array()) {
		$Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT * FROM $this->Tabela $Where ORDER BY logout DESC",$this->ParametrPaginacji,30));
		$Wynik = array(
			"id_uzytkownik" => array('naglowek' => 'Nazwa użytkownika', 'elementy' => $this->Userzy),
                        "login" => 'Data logowania',
                        "logout" => 'Data wylogowania'
		);
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
		return $Akcje;
	}

}
?>
