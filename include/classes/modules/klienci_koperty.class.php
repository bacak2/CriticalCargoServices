<?php
/**
 * Moduł klienci - wydruk kopert
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class KlienciKoperty extends ModulBazowy {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_klient';
            $this->PoleID = 'id_klient';
            $this->PoleNazwy = 'identyfikator';
            $this->Nazwa = 'Klient';
	}

	function  AkcjaDrukuj($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            include(SCIEZKA_SZABLONOW."druki/druk-koperty.tpl.php");
        }
}
?>
