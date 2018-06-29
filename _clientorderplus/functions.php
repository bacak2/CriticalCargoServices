<?php
	function GetResultAsArray($Query){
            $Que = mysql_query($Query);
            $Result = array();
            if(mysql_num_rows($Que) > 0){
                while($Res = mysql_fetch_array($Que)){
                        $Result[$Res[0]] = $Res;
                }
            }
            return $Result;
	}

        function GetRows($Query){
            $Que = mysql_query($Query);
            $Result = array();
            if(mysql_num_rows($Que) > 0){
                while($Res = mysql_fetch_array($Que)){
                    $Result[] = $Res;
                }
            }
            return $Result;
	}
	
	function GetRow($Query){
		$Que = mysql_query($Query);
		if(mysql_num_rows($Que) > 0){
			$Result = mysql_fetch_array($Que);
			return $Result;
		}
		return false;
	}
	
	function GetOptions($Query){
		$Que = mysql_query($Query);
		if(mysql_num_rows($Que) > 0){
			$Result = array();
			while($Res = mysql_fetch_array($Que)){
				$Result[$Res[0]] = $Res[1];
			}
			return $Result;
		}
		return false;
	}

        function GetValues($Query){
		$Que = mysql_query($Query);
		if(mysql_num_rows($Que) > 0){
			$Result = array();
			while($Res = mysql_fetch_array($Que)){
				$Result[] = $Res[0];
			}
			return $Result;
		}
		return array();
	}
	
	function GetValue($Query){
		$Que = mysql_query($Query);
		if(mysql_num_rows($Que) > 0){
			return mysql_result($Que,0,0);
		}
		return false;		
	}
	
	function GetPrzewoznikClass(){
		$Classes = GetResultAsArray("SELECT klasa_id, klasa_nazwa, klasa_color FROM orderplus_przewoznik_klasy ORDER BY klasa_id");
		return $Classes;
	}
	
	function ShowWyborPrzedzialu($Submit = true, $start = false, $stop = false){
                if(!$start){
                    $start = mysql_result(mysql_query("SELECT data_zlecenia FROM orderplus_zlecenie WHERE data_zlecenia != '0000-00-00' ORDER BY data_zlecenia ASC"), 0, 0);
                }
                if(!$stop){
                    $stop = mysql_result(mysql_query("SELECT data_zlecenia FROM orderplus_zlecenie WHERE data_zlecenia != '0000-00-00' ORDER BY data_zlecenia DESC"), 0, 0);
                }
		echo "od:<select style=\"font-size: 11px;\" name=\"start\">";
		$temp = $start;
		do
		{
		   if(isset($_POST['start']))
		   {
		      $_POST['start'] == $temp ? $sel = 'selected' : $sel = '';
		   }
		   else
		   {
		   "{$_SESSION['okres']}-01" == $temp  ? $sel = 'selected' : $sel = '';
		   }
		   echo "<option value=\"$temp\" $sel>$temp</option>";
		   $temp = date('Y-m-d', strtotime("$temp +1 day"));
		}
		while($temp <= $stop);
		echo "</select>";
		
		
		echo "&nbsp;&nbsp;do:<select style=\"font-size: 11px;\" name=\"stop\">";
		$temp = $start;
		do
		{
		   if(isset($_POST['stop']))
		   {
		      $_POST['stop'] == $temp ? $sel = 'selected' : $sel = '';
		   }
		   else
		   {
		      $temp == $stop ? $sel = 'selected' : $sel = '';
		   }
		   echo "<option value=\"$temp\" $sel>$temp</option>";
		   $temp = date('Y-m-d', strtotime("$temp +1 day"));
		}
		while($temp <= $stop);
		echo "</select>";
                if($Submit){
                    echo "&nbsp;&nbsp;&nbsp;<input  style=\"font-size: 11px;\" type=\"submit\" value=\"Wybierz\" />";
                }

	}
	
	function ShowSelect($Name, $Options, $Value = 0, $Dodatki = false){
		echo ("<select name=\"$Name\"".($Dodatki ? $Dodatki : "").">");
			echo("<option value=\"0\"".($Value == 0 ? ' selected="selected"' : '')."> -- wybierz -- </option>");
			foreach($Options as $OptID => $OptName){
				echo("<option value='$OptID'".($Value == $OptID ? ' selected="selected"' : '').">$OptName</option>");
			}
		echo("</select>");		
	}
	
	function ShowSelectMultiple($Name, $Options, $Value = 0, $Dodatki = false){
		echo ("<select name=\"$Name\"".($Dodatki ? $Dodatki : "")." multiple size='10'>");
			echo("<option value=\"0\"".($Value == 0 ? ' selected="selected"' : '')."> -- wybierz -- </option>");
			foreach($Options as $OptID => $OptName){
				echo("<option value='$OptID'".($Value == $OptID ? ' selected="selected"' : '').">$OptName</option>");
			}
		echo("</select>");		
	}
	
	function GetFirstKey($Wartosci){
		$Keys = array_keys($Wartosci);
		return $Keys[0];
	}
	
	function ShowPrzewoznikSelect($Name, $Value, $Submit = true){
		echo ("<select name=\"$Name\" ".($Submit ? "onchange=\"this.form.nowy.value='stary'; this.form.submit();\"" : "").">");
			echo("<option value=\"0\"> Wybierz przewoźnika </option>");
			$Klasy = GetPrzewoznikClass();
			$przewoznicy = mysql_query("SELECT id_przewoznik, nazwa, klasa_id FROM orderplus_przewoznik ORDER BY nazwa");
			while ($przewoznik = mysql_fetch_object($przewoznicy)) {
				echo("<option value='$przewoznik->id_przewoznik'".($Value == $przewoznik->id_przewoznik ? ' selected="selected"' : '')."".($przewoznik->klasa_id > 0 ? " style='background-color: {$Klasy[$przewoznik->klasa_id]['klasa_color']};'" : "").">$przewoznik->nazwa ({$Klasy[$przewoznik->klasa_id]['klasa_nazwa']})</option>");
			}
		echo("</select>");
	}
	
	function ShowKlientSelect($Name, $Value, $PoleTermin = false){
		if(!in_array($_SESSION['uprawnienia_id'], array(1,4))){
			$warunex = "WHERE id_oddzial = {$_SESSION['id_oddzial']} OR id_oddzial = 0";
		}else{
			$warunex = '';
		}
		$klienci = mysql_query("SELECT id_klient, nazwa, termin_platnosci_dni FROM orderplus_klient $warunex ORDER BY nazwa");
		$Clients = array();
		while ($klient = mysql_fetch_array($klienci)){
			$Clients[$klient['id_klient']] = $klient;
		}
		if($PoleTermin){
			echo "<script type='text/javascript'>\n";
				echo "var Clients = new Array();";
				foreach($Clients as $ID => $Cli){
					echo "Clients[$ID] = {$Cli['termin_platnosci_dni']};";
				}
			echo "</script>\n";
		}
		echo ("<select name=\"$Name\"".($PoleTermin ? " onchange='document.getElementById(\"$PoleTermin\").value = Clients[this.value];'" : "").">");
			echo("<option value=\"0\"> Wybierz klienta </option>");
			foreach($Clients as $ID => $Cli){
				echo("<option value='$ID'".($Value == $ID ? ' selected="selected"' : '').">{$Cli['nazwa']}</option>");
			}
		echo("</select>");
	}
	
	function GetPrivilages(){
		$Priv = GetOptions("SELECT uprawnienia_id, uprawnienia_nazwa FROM orderplus_uprawnienia ORDER BY uprawnienia_lp");
		$Privilages[0] = "brak przypisania";
		foreach($Priv as $ID => $Pr){
			$Privilages[$ID] = $Pr;
		}
		return $Privilages;
	}
	
	function GetBranze(){
		return GetOptions("SELECT branza_id, branza_nazwa FROM orderplus_klient_branza ORDER BY branza_nazwa ASC");
	}
	
	function GetSiedziby(){
		return GetOptions("SELECT siedziba_id, siedziba_nazwa FROM orderplus_klient_siedziba ORDER BY siedziba_nazwa ASC");
	}
	
	function GetTypySerwisu(){
		return GetOptions("SELECT typ_serwisu_id, typ_serwisu_nazwa FROM orderplus_typy_serwisu ORDER BY typ_serwisu_id");
	}
	
	function GetBranzeKlientow(){
		return GetOptions("SELECT id_klient, branza_id FROM orderplus_klient");
	}

	function GetSiedzibyKlientow(){
		return GetOptions("SELECT id_klient, siedziba_id FROM orderplus_klient");
	}
	
	function GetKlienci(){
		return GetOptions("SELECT id_klient, nazwa FROM orderplus_klient".(!in_array($_SESSION['uprawnienia_id'], array(1,4,5)) ? " WHERE id_oddzial IN(0,{$_SESSION['id_oddzial']})" : "")." ORDER BY nazwa ASC");
	}
	
	function GetPrzewoznicy(){
		return GetOptions("SELECT id_przewoznik, nazwa FROM orderplus_przewoznik ORDER BY nazwa ASC");
	}
	
	function GetOddzialy(){
		return GetOptions("SELECT id_oddzial, nazwa FROM orderplus_oddzial ORDER BY nazwa ASC");
	}
	
	function GetCountryCodes(){
		return GetOptions("SELECT kod_kraju_id, CONCAT(kod_kraju_nazwa,' - ',kraj_nazwa) as nazwa FROM orderplus_kody_krajow ORDER BY kod_kraju_nazwa");
	}
	
	function GetUsers(){
		return GetOptions("SELECT id_uzytkownik, CONCAT(imie,' ',nazwisko) as dane FROM orderplus_uzytkownik");
	}
	
	function GetZlecenia($ClientID = 0, $Kody = array()){
            $Where = '';
            if(isset($Kody['kraj_1']) && $Kody['kraj_1'] > 0){
                $Where .= " AND kod_kraju_zaladunku = '{$Kody['kraj_1']}'";
            }
            if(isset($Kody['kraj_2']) && $Kody['kraj_2'] > 0){
                $Where .= " AND kod_kraju_rozladunku = '{$Kody['kraj_2']}'";
            }
            return GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND id_klient = '$ClientID' AND data_zlecenia >= '2010-01-01'$Where ORDER BY data_zlecenia DESC");
	}
	
	function DodajZdarzenieDoCRM($termin_zaladunku, $id_klient){
		mysql_query("INSERT INTO zdarzenia SET Priorytet_id = '1', Statystyka_id = '4', data_utworzenia = now(), komentarz = '', zalacznik = 'nie', 
						data_poczatek = now(), data_zakonczenia = now(), temat = 'Wykonane zlecenie dnia: $termin_zaladunku'");
		$ZdarzenieID = mysql_insert_id();
		$UzytkownikID = GetValue("SELECT id FROM uzytkownicy WHERE login = '{$_SESSION['login']}'");
		$KlientID = UstalIdKlientaWCRM($id_klient);
		mysql_query("INSERT INTO powiazania_zdarzenia SET Uzytkownicy_id = '$UzytkownikID', Klienci_id = '$KlientID', Zdarzenia_id = '$ZdarzenieID'");
	}
	
	function UstalIdKlientaWCRM($id_klient){
		$Query = mysql_query("SELECT * FROM orderplus_klient WHERE id_klient = '$id_klient'");
		$Result = mysql_fetch_array($Query);
		$NIP = preg_replace('|[^a-zA-Z0-9ąęśćńółżźĄĘŚĆŃÓŁŻŹ]|', '', $Result['nip']);
		$ClientID = GetValue("SELECT id FROM klienci WHERE nip = '$NIP'");
		if(!$ClientID){
			mysql_query("INSERT INTO klienci SET
							Potencjal_id = '4',
							Kod_kraju_id = '0',
							nazwa = '{$Result['nazwa']}',
							miasto = '{$Result['miejscowosc']}',
							kod_pocztowy = '{$Result['kod_pocztowy']}',
							mail = '{$Result['emaile']}',
							adres = '{$Result['adres']}',
							nip = '$NIP'
							");
			$ClientID = mysql_insert_id();
		}
		return $ClientID;
	}

        function ObliczIloscDniMiedzyDatami($data1, $data2){
            $Data2 = explode("-",$data2);
            $Data1 = explode("-",$data1);
	      $date2 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
	      $date1 = mktime(0,0,0,$Data1[1],$Data1[2],$Data1[0]);
              $dateDiff = $date1 - $date2;
	      $fullDays = floor($dateDiff/(60*60*24));
              return $fullDays;
        }

        function GetTypyGodzin(){
            return array(0 => 'konkretna godzina', 1 => 'przedział godzinowy');
        }

        function OrderNumberForClient($Number){
            $Exp = explode("/", $Number);
            $Exp[1] = "Critical&nbsp;Cargo&nbsp;Services";
            return implode("/", $Exp);
        }

        function PolaczDwieTablice($Array1, $Array2){
            if(is_array($Array2)){
		foreach($Array2 as $Key => $Value){
			if(is_array($Value)){
				$Array1[$Key] = PolaczDwieTablice($Array1[$Key], $Array2[$Key]);
			}else{
				$Array1[$Key] = $Array2[$Key];
			}
		}
            }
            return $Array1;
	}

        function GetYesNo(){
            return array("Yes" => "Yes", "No" => "No");
        }

        function PrepareInsert($Table, $Fields) {
		$Result = "INSERT INTO $Table SET";
		foreach ($Fields as $FieldName => $Value) {
			$Result .= " $FieldName = '".mysql_real_escape_string($Value)."',";
		}
		return rtrim($Result, ',');
	}

	function PrepareUpdate($Table, $Fields, $WhereFields) {
		$Result = "UPDATE $Table SET";
		foreach ($Fields as $FieldName => $Value) {
			$Result .= " $FieldName = '".mysql_real_escape_string($Value)."',";
		}
		$Result = rtrim($Result, ',');
		if (count($WhereFields)) {
			$Result .= " WHERE";
			foreach ($WhereFields as $FieldName => $Value) {
				$Result .= " $FieldName = '".mysql_real_escape_string($Value)."' AND";
			}
		}
		return rtrim($Result, ' AND');
	}

        function GetContainers($Values, $SOID){
            $FCL = mysql_query("SELECT * FROM orderplus_sea_orders_fcl WHERE id_zlecenie = '$SOID'");
            while($FCLRes = mysql_fetch_array($FCL)){
                    $Values['FCL'][] = $FCLRes;
            }
            $LCL = mysql_query("SELECT * FROM orderplus_sea_orders_lcl WHERE id_zlecenie = '$SOID'");
            while($LCLRes = mysql_fetch_array($LCL)){
                    $Values['LCL'][] = $LCLRes;
            }
            if(count($Values['FCL']) == 0){
                $Values['FCL'] = array();
            }
            if(count($Values['LCL']) == 0){
                $Values['LCL'] = array();
            }

            return $Values;
        }

        function GetUsed($SOID, $ID){
            $Wykorzystane = array();
            $Orders = GetValues("SELECT zlecenie_so_id FROM orderplus_sea_orders_zlecenia WHERE id_zlecenie = '$SOID' AND zlecenie_so_id != '$ID'");
            if(count($Orders) > 0){
                $Wykorzystane = GetValues("SELECT cont_number FROM orderplus_sea_orders_zlecenia_fcl WHERE zlecenie_so_id IN(".implode(",",$Orders).")");
            }
            return $Wykorzystane;
        }

        function ValidateContNumber($Conts){
            if(is_array($Conts)){
                foreach($Conts as $Idx => $Dane){
                    $Conts[$Idx]['cont_no_default'] = $Conts[$Idx]['cont_no'];
                    if($Conts[$Idx]['cont_no'] != ""){
                        $NO = preg_replace('/[^0-9A-Za-z]/', '', $Dane['cont_no']);
                        $NO_start = substr($NO, 0, 10);
                        $NO_end = substr($NO, 10, strlen($NO)-10);
                        $Conts[$Idx]['cont_no'] = $NO_start."-".$NO_end;
                    }
                }
            }
            return $Conts;
        }

        function CheckContNumbers($Values){
            $Checked = false;
            if(is_array($Values)){
                foreach($Values as $Cont){
                    if($Cont['cont_no_default'] != "" && !preg_match('/[A-Za-z]{4}+[0-9]{6}+\-[0-9]{1}$/', $Cont['cont_no'])){
                        $Checked[] = $Cont['cont_no_default'];
                    }
                }
            }
            return $Checked;
        }

        function PobierzKurs($Data, $waluta){
            $Dzien = date("l", strtotime($Data));
            if($Dzien == "Saturday" || $Dzien == "Sunday"){
                    $MinusDays = ($Dzien == "Saturday" ? 1 : 2);
                    $TerminKurs = date("Y-m-d", strtotime($Data."-$MinusDays days"));
            }else{
                    $TerminKurs = $Data;
            }
            $mala_waluta = strtolower($waluta);
            $kurs = GetValue("SELECT $mala_waluta FROM orderplus_kurs WHERE data_publikacji ='$TerminKurs' ORDER BY data_publikacji ASC LIMIT 1");
            return floatval($kurs);
        }

        function SetKey($Elementy, $IDElementu){
    $Key = (isset($Elementy[$IDElementu]) ? $IDElementu : 0);
    if(is_null($Key)){
            $Key = 0;
    }
    return $Key;
}

        function AddToDoRaportu($DaneDoRaportu, $Key){
    if(!key_exists($Key,$DaneDoRaportu)){
            $DaneDoRaportu[$Key]["ilosc_zlecen"] = 0;
            $DaneDoRaportu[$Key]["suma_klient"] = 0;
            $DaneDoRaportu[$Key]["suma_przewoznik"] = 0;
            $DaneDoRaportu[$Key]["marza"] = 0;
    }
    $DaneDoRaportu[$Key]["ilosc_zlecen"]++;
    return $DaneDoRaportu;
}

        function KwotaSlownie($Kwota, $waluta, $szablon){
            if($szablon == "ENG"){
                return KwotaSlownieEng($Kwota, $waluta);
            }else{
                return KwotaSlowniePL($Kwota, $waluta);
            }
        }

        function KwotaSlowniePL($Kwota, $waluta)
{
   $Potegi = array(
   9 => array("miliard", "miliardy", "miliardów"),
   6 => array("milion", "miliony", "milionów"),
   3 => array("tysiąc", "tysiące", "tysięcy"),
   0 => array()
   );

   $Liczby = array(
   1 => 'jeden', 2 => 'dwa', 3 => 'trzy', 4 => 'cztery', 5 => 'pięć', 6 => 'sześć', 7 => 'siedem', 8 => 'osiem', 9 => 'dziewięć', 10 => 'dziesięć',
   11 => 'jedenaście', 12 => 'dwanaście', 13 => 'trzynaście', 14 => 'czternaście', 15 => 'piętnaście', 16 => 'szesnaście', 17 => 'siedemnaście', 18 => 'osiemnaście', 19 => 'dziewiętnaście',
   20 => 'dwadzieścia', 30 => 'trzydzieści', 40 => 'czterdzieści', 50 => 'pięćdziesiąt', 60 => 'sześćdziesiąt', 70 => 'siedemdziesiąt', 80 => 'osiemdziesiąt', 90 => 'dziewięćdziesiąt',
   100 => 'sto', 200 => 'dwieście', 300 => 'trzysta', 400 => 'czterysta', 500 => 'pięćset', 600 => 'sześćset', 700 => 'siedemset', 800 => 'osiemset', 900 => 'dziewięćset'
   );

   $Slownie = '';
   $Kwota = round($Kwota, 2);
   foreach ($Potegi as $Potega => $Odmiany) {
      $Ilosc = intval($Kwota / (pow(10, $Potega))) % 1000;
      if ($Ilosc) {
         $Setki = 100 * intval($Ilosc / 100);
         $Dziesiatki = 10 * intval(($Ilosc - $Setki) / 10);
         $Jednosci = $Ilosc - $Setki - $Dziesiatki;
         if ($Setki) {
            $Slownie .= $Liczby[$Setki].' ';
         }
         if ($Dziesiatki == 10) {
            $Slownie .= $Liczby[$Dziesiatki+$Jednosci].' ';
         }
         else {
            if ($Dziesiatki) {
               $Slownie .= $Liczby[$Dziesiatki].' ';
            }
            if ($Jednosci) {
               if (!(($Potega > 0) && ($Ilosc == 1))) {
                  $Slownie .= $Liczby[$Jednosci].' ';
               }
            }
         }
         if ($Potega > 0) {
            if ($Ilosc == 1) {
               $Slownie .= $Odmiany[0].' ';
            }
            elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
               $Slownie .= $Odmiany[1].' ';
            }
            else {
               $Slownie .= $Odmiany[2].' ';
            }
         }
      }
   }

   if (!($Zlote = intval($Kwota))) {
   ($waluta == 'PLN') ? $Slownie .= 'zero złotych ': $Slownie .= 'zero '.$waluta;
   }
   elseif ($Zlote == 1) {
   ($waluta == 'PLN') ? $Slownie .= 'złoty ': $Slownie .= ' '.$waluta;
   }
   elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
   ($waluta == 'PLN') ? $Slownie .= 'złote ': 	$Slownie .= ' '.$waluta;
   }
   else {
   ($waluta == 'PLN') ? $Slownie .= 'złotych ': $Slownie .= ' '.$waluta;
   }
   $Grosze = round(100 * ($Kwota - $Zlote)) % 100;
   if ($Grosze) {
      $Slownie .= " $Grosze/100";
   }
   return trim($Slownie);
}

        function KwotaSlownieEng($Kwota, $waluta)
        {
           $Potegi = array(
           9 => array("miliard", "miliards", "miliard"),
           6 => array("million", "milions", "million"),
           3 => array("thousand", "thousands", "thousand"),
           0 => array()
           );

           $Liczby = array(
           1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
           11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fiveteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
           20 => 'twenty', 30 => 'thirty', 40 => 'fourty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety',
           100 => 'one hundred', 200 => 'two hundred', 300 => 'three hundred', 400 => 'four hundred', 500 => 'five hundred', 600 => 'six hundred', 700 => 'seven hundred', 800 => 'eight hundred', 900 => 'nine hundred'
           );

           $Slownie = '';
           $Kwota = round($Kwota, 2);
           foreach ($Potegi as $Potega => $Odmiany) {
              $Ilosc = intval($Kwota / (pow(10, $Potega))) % 1000;
              if ($Ilosc) {
                 $Setki = 100 * intval($Ilosc / 100);
                 $Dziesiatki = 10 * intval(($Ilosc - $Setki) / 10);
                 $Jednosci = $Ilosc - $Setki - $Dziesiatki;
                 if ($Setki) {
                    $Slownie .= $Liczby[$Setki].' ';
                 }
                 if ($Dziesiatki == 10) {
                    $Slownie .= $Liczby[$Dziesiatki+$Jednosci].' ';
                 }
                 else {
                    if ($Dziesiatki) {
                       $Slownie .= $Liczby[$Dziesiatki].' ';
                    }
                    if ($Jednosci) {
                       if (!(($Potega > 0) && ($Ilosc == 1))) {
                          $Slownie .= $Liczby[$Jednosci].' ';
                       }
                    }
                 }
                 if ($Potega > 0) {
                    if ($Ilosc == 1) {
                       $Slownie .= $Odmiany[0].' ';
                    }
                    elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
                       $Slownie .= $Odmiany[1].' ';
                    }
                    else {
                       $Slownie .= $Odmiany[2].' ';
                    }
                 }
              }
           }

           if (!($Zlote = intval($Kwota))) {
           ($waluta == 'PLN') ? $Slownie .= 'zero złotych ': $Slownie .= 'zero '.$waluta;
           }
           elseif ($Zlote == 1) {
           ($waluta == 'PLN') ? $Slownie .= 'złoty ': $Slownie .= ' '.$waluta;
           }
           elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
           ($waluta == 'PLN') ? $Slownie .= 'złote ': 	$Slownie .= ' '.$waluta;
           }
           else {
           ($waluta == 'PLN') ? $Slownie .= 'złotych ': $Slownie .= ' '.$waluta;
           }
           $Grosze = round(100 * ($Kwota - $Zlote)) % 100;
           if ($Grosze) {
              $Slownie .= " $Grosze/100";
           }
           return trim($Slownie);
        }

?>