<?php
/**
 * Moduł raportów CRM
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class RaportyCRM extends ModulBazowy {
        public $Userzy;
        public $Oddzialy;
        public $Statystyki;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'zdarzenia';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'temat';
            $this->CzySaOpcjeWarunkowe = true;
            $this->Oddzialy = UsefullBase::GetOddzialy($this->Baza);
            $this->Userzy = UsefullBase::GetUsersLogin($this->Baza);
            $this->Statystyki = UsefullBase::GetStatystyki($this->Baza);
            $this->Filtry[] = array('opis' => "Opiekun", "nazwa" => "pz.id_uzytkownik", "opcje" => $this->Userzy, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Oddział", "nazwa" => "od.id_oddzial", "opcje" => $this->Oddzialy, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Statystyka", "nazwa" => "Statystyka_id", "opcje" => $this->Statystyki, "typ" => "lista");
            $this->Filtry[] = array('opis' => "Klient", "nazwa" => "k.nazwa", "typ" => "tekst");
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
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."((z.data_poczatek >= '$Wartosc 00:00:00' AND z.data_przypomnienia is null) OR (z.data_przypomnienia >= '$Wartosc 00:00:00'))";
                                        }else if($Pole == "data_zdarzenia_do"){
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."((z.data_poczatek < '$Wartosc 00:00:00' AND z.data_przypomnienia is null) OR (z.data_przypomnienia <= '$Wartosc 00:00:00'))";
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
		$this->Baza->Query($this->QueryPagination("SELECT z.*, pz.id_uzytkownik,
                                            concat(k.nazwa,'<br><span style=\"font-size:0.7em;\">[ ',od.nazwa,' ]</span>') as nazwa
                                            FROM zdarzenia z
                                            LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id) 
                                            LEFT JOIN orderplus_klient k ON(pz.id_klient = k.id_klient)
                                            LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = k.id_oddzial)
                                            $Where ORDER BY data_poczatek DESC",$this->ParametrPaginacji,30));
		$Wynik = array(
			"temat" => 'Temat zadania',
                        "nazwa" => 'Nazwa klienta',
                        "Statystyka_id" => array('naglowek' => 'Statystyka', 'elementy' => $this->Statystyki),
                        "id_uzytkownik" => array('naglowek' => 'Wykonanie', 'elementy' => $this->Userzy),
                        "data_poczatek" => 'Data',
                        "data_zakonczenia" => 'Zakończono'
		);
		return $Wynik;
	}

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID) && $this->WykonywanaAkcja != "specyfikacja"){
                    echo("<div style='float: left; display: inline;'>");
                        echo "<a href='?modul=logowania' class='form-button'>lista logowań</a>";
                        echo "<a href='?modul=raporty' class='form-button'>zestawienie zdarzeń</a>";
                        echo "<a href='?modul=day_raport' class='form-button'>zestawienie dzienne/okresowe</a>";
                        echo "<a href='?modul=client_raport' class='form-button'>Raport dopisanych klientów</a>";
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if(!in_array($this->WykonywanaAkcja, array("dodawanie","dodaj_import","dodaj_export")) && is_null($ID) && !isset($_GET['did'])){
                $this->ShoFiltry();
            }
            echo "<div style='clear: both'></div>\n";
        }

        function ShoFiltry(){
            include(SCIEZKA_SZABLONOW."filters-raporty.tpl.php");
        }

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
		$Akcje[] = array('img' => "desc_button", 'title' => "Szczegóły", "akcja_link" => "?modul=zdarzenia&akcja=szczegoly&id={$Dane[$this->PoleID]}");
		return $Akcje;
	}

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "data_poczatek" || $Nazwa == "data_zakonczenia" && !is_null($Element[$Nazwa])){
                $Element[$Nazwa] = date("Y-m-d",  strtotime($Element[$Nazwa]));
            }
            echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
        }

        function  AkcjeNiestandardowe($ID) {
            switch($this->WykonywanaAkcja){
                case "client_raport": break;
                case "day_raport": break;
            }
        }

}
?>
