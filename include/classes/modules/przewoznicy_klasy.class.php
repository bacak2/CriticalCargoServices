<?php
/**
 * Moduł klas przewoźników
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class PrzewoznicyKlasy {
	
	function __construct() {
	}

        function GetClasses($Baza){
            return $Baza->GetResultAsArray("SELECT klasa_id, klasa_nazwa, klasa_color FROM orderplus_przewoznik_klasy ORDER BY klasa_id", "klasa_id");
        }

        function GetClassesNames($Baza){
            return $Baza->GetOptions("SELECT klasa_id, klasa_nazwa FROM orderplus_przewoznik_klasy ORDER BY klasa_id");
        }

}
?>
