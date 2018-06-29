<?php
/**
 * Obsługa formularzy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus	
 * @version		1.0
 */

class FormularzSimple{

	function __construct() {
	}
	
	function FormStart($Name = "", $Action = "", $Method = "post", $TargetBlank = false){
		echo "<form ".($Name != "" ? "name = '$Name' id = '$Name' " : "")."action='$Action' method='$Method'".($TargetBlank ? " target='_blank'" : "")." enctype='multipart/form-data'>";
	}
	
	
	function FormEnd(){
		echo "</form>\n";
	}
	
	function PoleSelect($Name, $Options, $Value = "", $Dodatki = ""){
		echo "<select name='$Name'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			foreach($Options as $Key => $Wartosc){
				echo "<option value='$Key'".($Key == $Value ? " selected='selected'" : "").">$Wartosc</option>\n";
			}
		echo "</select>\n";		
	}
	
	function PoleSelectMulti($Name, $Options, $Value = "", $Dodatki = ""){
		echo "<select name='$Name'".($Dodatki != "" ? " $Dodatki" : "")." multiple='multiple'>\n";
			foreach($Options as $Key => $Wartosc){
				echo "<option value='$Key'".(in_array($Key, $Value) ? " selected='selected'" : "").">$Wartosc</option>\n";
			}
		echo "</select>\n";		
	}
	
	function PoleInputText($Name, $Value, $Dodatki = ""){
		echo "<input type='text' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	function PoleFile($Name, $Value, $Dodatki = ""){
		echo "<input type='file' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

	function PolePassword($Name, $Value, $Dodatki = ""){
		echo "<input type='password' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	function PoleTextarea($Name, $Value, $Dodatki = ""){
		echo "<textarea name='$Name'".($Dodatki != "" ? " $Dodatki" : "").">$Value</textarea>\n";
	}
	
	function PoleHidden($Name, $Value, $Dodatki = ""){
		echo "<input type='hidden' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	function PoleSubmitImage($Name, $Value, $Src, $Dodatki = ""){
		echo "<input type='image' src='$Src' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	function PoleButton($Name, $Value, $Dodatki = ""){
		echo "<input type='button' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

	function PoleSubmit($Name, $Value, $Dodatki = ""){
		echo "<input type='submit' name='$Name' value='$Value'".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}
	
	function SelectData($Nazwa, $YearStart, $YearEnd, $Wartosc, $Dodatki = ""){
		$WartoscExp = explode("-", $Wartosc);
		echo "<select id='{$Nazwa}_day' name='{$Nazwa}[day]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Day = 1; $Day <= 31; $Day++){
				$D = ($Day < 10 ? "0" : "").$Day;
				echo "<option value='$D'".($D == $WartoscExp[2] ? " selected='selected'" : "").">$D</option>\n";
			}
		echo "</select>\n";
		echo "<select id='{$Nazwa}_month' name='{$Nazwa}[month]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Month = 1; $Month <= 12; $Month++){
				$M = ($Month < 10 ? "0" : "").$Month;
				echo "<option value='$M'".($M == $WartoscExp[1] ? " selected='selected'" : "").">".$this->LiczbaNaMiesiac($M)."</option>\n";
			}
		echo "</select>\n";
		echo "<select id='{$Nazwa}_year' name='{$Nazwa}[year]'".($Dodatki != "" ? " $Dodatki" : "").">\n";
			for($Year = $YearStart; $Year <= $YearEnd; $Year++){
				echo "<option value='$Year'".($Year == $WartoscExp[0] ? " selected='selected'" : "").">$Year</option>\n";
			}
		echo "</select>\n";
	}
	
	function LiczbaNaMiesiac($miesiac){
		if($miesiac < 10){
			$miesiac = "0".$miesiac;
		}
		$miesiac = str_replace("00", "0", $miesiac);
		switch($miesiac){
			case("01"): return KALENDARZ_MIESIACE_STYCZEN;
			case("02"): return KALENDARZ_MIESIACE_LUTY;
			case("03"): return KALENDARZ_MIESIACE_MARZEC;
			case("04"): return KALENDARZ_MIESIACE_KWIECIEN;
			case("05"): return KALENDARZ_MIESIACE_MAJ;
			case("06"): return KALENDARZ_MIESIACE_CZERWIEC;
			case("07"): return KALENDARZ_MIESIACE_LIPIEC;
			case("08"): return KALENDARZ_MIESIACE_SIERPIEN;
			case("09"): return KALENDARZ_MIESIACE_WRZESIEN;
			case("10"): return KALENDARZ_MIESIACE_PAZDZIERNIK;
			case("11"): return KALENDARZ_MIESIACE_LISTOPAD;
			case("12"): return KALENDARZ_MIESIACE_GRUDZIEN;
			default: return $miesiac;
		}
	}
	
	function PoleCheckbox($Name, $Value, $Wartosc = null, $Etykieta = "", $Dodatki = ""){
		echo "<input type='checkbox' name='$Name' value='$Value'".($Wartosc == $Value ? " checked" : "")."".($Dodatki != "" ? " $Dodatki" : "")." /> $Etykieta\n";
	}
	
	function PoleRadio($Name, $Value, $Wartosc = null, $Dodatki = ""){
		echo "<input type='radio' name='$Name' value='$Value'".($Wartosc == $Value ? " checked" : "")."".($Dodatki != "" ? " $Dodatki" : "")." />\n";
	}

        function PoleData($Name, $Value, $ID = false, $Form = "formularz", $Wstecz = 0, $Dodatki = ""){
            if(!$ID){
                $ID = $Name;
            }
            if($Value == "0000-00-00"){
                $Value = "";
            }
            echo "<nobr><input type=\"text\" id=\"$ID\" name=\"$Name\" style='width: 100px;' value=\"$Value\"".($Dodatki != "" ? " $Dodatki" : "").">&nbsp;&nbsp;<img src='images/kalendarz.png' onclick='wstecz = $Wstecz; showKal(document.getElementById(\"$ID\"));' style='margin-left: 10px; cursor: pointer; vertical-align: middle;'></nobr>";
        }
}
?>
