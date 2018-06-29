<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Usefull{

	function __construct() {
	}

        function SortMyArray($array, $on, $order='SORT_DESC'){
            $new_array = array();
            $sortable_array = array();
            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == $on) {
                                $sortable_array[$k] = strtolower($v2);
                            }
                        }
                    } else {
                        $sortable_array[$k] = strtolower($v);
                    }
                }
                switch($order){
                    case 'SORT_ASC':
                        asort($sortable_array);
                        break;
                    case 'SORT_DESC':
                        arsort($sortable_array);
                        break;
                }
                foreach($sortable_array as $k => $v) {
                    $new_array[] = $array[$k];
                }
            }
            return $new_array;
        }

        function GetFormStandardRow(){
            return array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1, 'td_end' => 1, 'tr_end' => 1);
        }

        function GetFormWithoutTHRow(){
            return array('tr_start' => 1, 'td_start' => 1, 'td_end' => 1, 'tr_end' => 1);
        }

        function GetFormButtonRow(){
            return array("tr_start" => 1, "td_start" => 1, "td_colspan" => 2, "td_style" => "text-align: center;", "td_end" => 1, "tr_end" => 1);
        }

	/**
	 * Funkcja wyświetlająca paginacje
	 *
	 * @param string $LinkPodstawowy - link na którym dokonywana jest paginacja
	 * @param $pagin - numer strony w paginacji
	 * @param $WidoczneNaStronie - ile kolejnych stron ma być dostępnych z obecnej strony
	 * @param $IleStronPaginacji - ilość wszystkich stron
	 */
	function ShowPagination($LinkPodstawowy, $pagin = 0, $WidoczneNaStronie = 30, $IleStronPaginacji = 0, $AllOption = false){
		if ($IleStronPaginacji > 1){
                        if($AllOption){
                            echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=-1\"".($pagin == -1 ? " class=\"paginationBold\"" : "").">[Wszystkie]</a></span> ";
                        }
			$WidoczneNaStronie = ceil($WidoczneNaStronie/2);
			$Poczatek = $pagin - $WidoczneNaStronie;
			if ($Poczatek < 1){
				$Poczatek = 0;
			}
			$Koniec = $pagin + $WidoczneNaStronie + 1;
			if ($Koniec > $IleStronPaginacji){
				$Koniec = $IleStronPaginacji;
			}
                        echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=0\">[Pierwsza]</a></span> ";
                        if($pagin > 0){
                            echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($pagin-1)."\">[Poprzednia]</a></span> ";
                        }
			for ($i=$Poczatek;$i<$Koniec;$i++){
				$IWyswietl = $i+1;
				$klasa = "";
				if ($pagin == $i){
					$klasa = "class=\"paginationBold\"";
				}
				echo "<a href=\"$LinkPodstawowy&pagin=$i\" $klasa>$IWyswietl</a> ";
			}
                        if($pagin < $IleStronPaginacji-1){
                            echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($pagin+1)."\">[Następna]</a></span> ";
                        }
                        echo "<span class='split'><a href=\"$LinkPodstawowy&pagin=".($IleStronPaginacji-1)."\">[Ostatnia]</a></span> ";
		}
	}

	function ZmienFormatKwoty($Kwota){
		$NowaKwota = number_format($Kwota,2,"."," ");
		return $NowaKwota;
	}

        function WyswietlFormatWaluty($Kwota, $Waluta){
            if($Waluta == "EUR"){
                return "&euro;".number_format($Kwota,2,"."," ");
            }else{
                return number_format($Kwota,2,"."," ")." <small>$Waluta</small>";
            }
        }

	function WstawienieDoTablicy($klucz, $wartosc, $Tablica = null){
		if(is_null($Tablica) || $Tablica == false){
			$TablicaWynik = array($klucz => "$wartosc");
		}else{
			$TablicaWynik = array($klucz => "$wartosc")+$Tablica;
		}
		return $TablicaWynik;
	}

        function prepareURL($sText){
            // pozbywamy się polskich znaków diakrytycznych
          $aReplacePL = array(
          'ą' => 'a', 'ę' => 'e', 'ś' => 's', 'ć' => 'c',
          'ó' => 'o', 'ń' => 'n', 'ż' => 'z', 'ź' => 'z', 'ł' => 'l',
          'Ą' => 'A', 'Ę' => 'E', 'Ś' => 'S', 'Ć' => 'C',
          'Ó' => 'O', 'Ń' => 'N', 'Ż' => 'Z', 'Ź' => 'Z', 'Ł' => 'L'
          );
          $sText = str_replace(array_keys($aReplacePL), array_values($aReplacePL),$sText);
          // dla przejrzystości wszystko z małych liter
          $sText = strtolower($sText);
          // zmieniamy encje na zwykłe znaki
          $sText = html_entity_decode($sText);
          $sText = str_replace(' & ', '-&-', $sText);
          // wszystkie spacje i przecinki zamieniamy na myślniki
          $sText = str_replace(array(' ', ','), '_', $sText);
          // wszystkie + zamieniamy na myślniki
          $sText = str_replace('+', '-', $sText);
          // usuń wszytko co jest niedozwolonym znakiem
          $sText = preg_replace('/[^0-9a-z\_]+/', '', $sText);
          // zredukuj liczbę myślników do jednego obok siebie
          $sText = preg_replace('/[\_]+/', '_', $sText);
          // usuwamy możliwe myślniki na początku i końcu
          $sText = trim($sText, '_');
          return $sText;
        }

        function GetExtension($File){
            $Rozszerzenie = explode(".", $File);
            $El = count($Rozszerzenie)-1;
            $Exp = strtolower($Rozszerzenie[$El]);
            return $Exp;
        }

        function RedirectLocation($Href){
            ?>
            <script type="text/javascript">
                location.href="<?php echo $Href; ?>";
            </script>
            <?php
        }

	function ShowKomunikatOK($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_ok" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	function ShowKomunikatError($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_blad" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

	function ShowKomunikatOstrzezenie($Tekst, $Bolder = false){
            echo "<div style='clear: both;'></div>\n";
            echo '<div class="komunikat_ostrzezenie" id="Komunikaty2">'.($Bolder ? "<b>$Tekst</b>" : $Tekst).'</div>';
	}

        function ActiveUpperText($Text){
            $Text = strtoupper($Text);
            $Text = str_replace(array("ó", "ł", "ś", "ń", "ź", "ż", "ć", "ą", "ę", "ü", "ö", "ä"), array("Ó", "Ł", "Ś", "Ń", "Ź", "Ż", "Ć", "Ą", "Ę", "Ü", "Ö", "Ä"), $Text);
            return $Text;
        }

        function PolaczDwieTablice($Array1, $Array2){
		foreach($Array2 as $Key => $Value){
			if(is_array($Value)){
				$Array1[$Key] = Usefull::PolaczDwieTablice($Array1[$Key], $Array2[$Key]);
			}else{
				$Array1[$Key] = $Array2[$Key];
			}
		}
		return $Array1;
	}

        function GetSzablonLangs(){
            return array('PL' => 'PL');
        }

        function GetNotyLangs(){
            return array('PL' => 'PL', 'ENG' => 'ENG');
        }

        function GetStawkiVat(){
            return array("np" => "np", "zw" => "zw", "0" => "0", "22" => "22", "23" => "23");
        }

        function GetTakNie(){
            return array(1 => 'Tak', 0 => 'Nie');
        }

        function GetTakNie2(){
            return array("Yes" => 'Yes', "No" => 'No');
        }

        function GetOddzialy(){
            return array(2 => "Warszawa", 0 => 'wszystkie oddziały', 1 => 'Wrocław', 3 => 'Poznań', 4 => 'Gdynia');
        }

        function GetWaluty(){
            return array('PLN' => 'PLN', 'EUR' => 'EUR', 'USD' => 'USD');
        }

        function GetBanki(){
            return array("NBP" => "NBP", "KOM" => "Bank komercyjny (BPH)");
        }
        
        function GetTypyGodzin(){
            return array(0 => 'konkretna godzina', 1 => 'przedział godzinowy');
        }

        function KwotaSlownie($Kwota, $waluta, $szablon){
            if($szablon == "ENG"){
                return Usefull::KwotaSlownieEng($Kwota, $waluta);
            }else{
                return Usefull::KwotaSlowniePL($Kwota, $waluta);
            }
        }

        function KwotaSlowniePL($Kwota, $waluta){
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
           ($waluta == 'PLN' ? $Slownie .= 'zero złotych ': $Slownie .= 'zero '.$waluta);
           }
           elseif ($Zlote == 1) {
           ($waluta == 'PLN' ? $Slownie .= 'złoty ': ($waluta == 'EUR' ? $Slownie .= ' euros' : $Slownie .= ' '.$waluta));
           }
           elseif (in_array($Jednosci, array(2, 3, 4)) && $Dziesiatki != 10) {
           ($waluta == 'PLN' ? $Slownie .= 'złote ': ($waluta == 'EUR' ? $Slownie .= ' euros' : $Slownie .= ' '.$waluta));
           }
           else {
           ($waluta == 'PLN' ? $Slownie .= 'złotych ': ($waluta == 'EUR' ? $Slownie .= ' euros' : $Slownie .= ' '.$waluta));
           }
           $Grosze = round(100 * ($Kwota - $Zlote)) % 100;
           if ($Grosze) {
              $Slownie .= " $Grosze/100";
           }
           return trim($Slownie);
        }

        function PokazOpoznienie($Termin, $Wplata, $Return = false){
            if($Termin != "0000-00-00" && $Wplata != "0000-00-00" && $Wplata > $Termin){
                    $Data = explode("-",$Termin);
                    $Data2 = explode("-",$Wplata);
                    $date2 = mktime(0,0,0,$Data[1],$Data[2],$Data[0]);
                    $date1 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
                    $dateDiff = $date1 - $date2;
                    $fullDays = floor($dateDiff/(60*60*24));
                    $fullDays = $fullDays." dni";
            }else{
                    $fullDays = " --- ";
            }
            if($Return){
                return $fullDays;
            }
            echo $fullDays;
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

        function CheckDate($Data){
            $date = explode("-", $Data);
            if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$Data) && checkdate($date[1],$date[2],$date[0])){ 
                return true;
            }
            return false;
        }

        function GetFirstKey($Wartosci){
		$Keys = array_keys($Wartosci);
		return $Keys[0];
	}

        function SetKey($Elementy, $IDElementu){
            $Key = (isset($Elementy[$IDElementu]) ? $IDElementu : 0);
            if(is_null($Key)){
                    $Key = 0;
            }
            return $Key;
        }

        function isWeekendDay($_date){	
           $_temp_date=explode('-',$_date);
            $_mktime=mktime(0,0,0,$_temp_date[1],$_temp_date[2],$_temp_date[0]);
            if(date('N',$_mktime)==6 || date('N',$_mktime)==7)
            {	/*dzień to sobota lub niedziela*/
                    return true;
            }

            return false;
        }

        function NipValidate($Nip){
            return preg_replace('|[^a-zA-Z0-9]|', '', strtoupper($Nip));
        }

        function SortArray($Elementy, $Pole, $Wartosc){
            $ToSortuj = array();
            foreach($Elementy as $Element){
                $ToSortuj[] = $Element[$Pole];
            }
            array_multisort($ToSortuj, ($Wartosc == "ASC" ? SORT_ASC : SORT_DESC), $Elementy);
            return $Elementy;
        }

        function GetTabelaRozliczenKolumny($XLS = false){
            $Wynik['termin_zaladunku'] = array("naglowek" => "Data i godzina załadunku", "td_class" => "operacja");
            $Wynik['termin_rozladunku'] = array("naglowek" => "Data i godzina rozładunku", "td_class" => "operacja");
            $Wynik['id_kierowca'] = array("naglowek" => "Dane kierowcy i numer rejestracyjny", "td_class" => "operacja");
            $Wynik['miejsce_zaladunku'] = array("naglowek" => "Załadowca i miejsce załadunku", "td_class" => "operacja");
            $Wynik['odbiorca'] = array("naglowek" => "Odbiorca i miejsce rozładunku", "td_class" => "operacja");
            $Wynik['opis_ladunku'] = array("naglowek" => "Opis ładunku", "td_class" => "operacja");
            $Wynik['ilosc_km'] = array("naglowek" => "Ilość km", "td_class" => "operacja");
            $Wynik['typ_serwisu'] = array("naglowek" => "Typ serwisu", "td_class" => "operacja");
            $Wynik['id_klient'] = array("naglowek" => "Klient", "td_class" => "all");
            $Wynik['id_przewoznik'] = array("naglowek" => "Przewoźnik", "td_class" => "all");
            //$Wynik['id_oddzial'] = array("naglowek" => "Oddział", "td_class" => "all");
            $Wynik['id_uzytkownik'] = array("naglowek" => "Zlecenie wystawił", "td_class" => "all");
            $Wynik['stawka_klient'] = array("naglowek" => "Stawka netto klient", "td_class" => "all");
            $Wynik['stawka_przewoznik'] = array("naglowek" => "Stawka netto przewoźnik", "td_class" => "all");
            if($XLS){
                $Wynik['kurs'] = array("naglowek" => "Kurs klient", "td_class" => "all");
                $Wynik['kurs_przewoznik'] = array("naglowek" => "Kurs przewoźnik", "td_class" => "all");
                $Wynik['stawka_klient_pln'] = array("naglowek" => "Stawka netto klient PLN", "td_class" => "all");
                $Wynik['stawka_przewoznik_pln'] = array("naglowek" => "Stawka netto przewoźnik PLN", "td_class" => "all");
            }
            $Wynik['marza'] = array("naglowek" => "Marża", "td_class" => "operacja");
            if($XLS){
                $Wynik['stawka_za_km_klient'] = array("naglowek" => "Stawka za km klient", "td_class" => "all");
                $Wynik['stawka_za_km_przewoznik'] = array("naglowek" => "Stawka za km przewoźnik", "td_class" => "all");
                $Wynik['stawka_za_km_klient_pln'] = array("naglowek" => "Stawka za km klient PLN", "td_class" => "all");
                $Wynik['stawka_za_km_przewoznik_pln'] = array("naglowek" => "Stawka za km przewoźnik PLN", "td_class" => "all");
            }
            $Wynik['stawka_klient_brutto'] = array("naglowek" => "Stawka brutto klient", "td_class" => "admin");
            $Wynik['stawka_przewoznik_brutto'] = array("naglowek" => "Stawka brutto przewoźnik", "td_class" => "admin");
            if($XLS){
                $Wynik['stawka_klient_brutto_pln'] = array("naglowek" => "Stawka brutto klient PLN", "td_class" => "all");
                $Wynik['stawka_przewoznik_brutto_pln'] = array("naglowek" => "Stawka brutto przewoźnik PLN", "td_class" => "all");
            }
            #$Wynik['marza_brutto'] = array("naglowek" => "Marża brutto", "td_class" => "admin");
            $Wynik['nr_zlecenia_klienta'] = array("naglowek" => "Numer zlecenia klienta", "td_class" => "all");
            $Wynik['numer_zlecenia'] = array("naglowek" => "Numer zlecenia", "td_class" => "all");
            $Wynik['id_faktury'] = array("naglowek" => "Nr faktury klienta", "td_class" => "admin");
            if($XLS){
                $Wynik['data_wystawienia'] = array("naglowek" => "Data wystawienia", "td_class" => "admin");
            }
            $Wynik['faktura_przewoznika'] = array("naglowek" => "Nr faktury przewoźnika", "td_class" => "admin");
            if($XLS == false){
                $Wynik['terminy'] = array("naglowek" => "Płatności", "td_class" => "all");
            }
            $Wynik["data_sprzedazy"] = array("naglowek" => 'Data sprzedaży', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["termin_wlasny"] = array("naglowek" => 'Termin płatności klient', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["rzecz_zaplata_klienta"] = array("naglowek" => 'Rzeczywista zapłata klient', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["opoznienie_klient"] = array("naglowek" => 'Opóźnienie klient', "td_class" => "platnosci", 'td_styl' => 'text-align: center');
            $Wynik["data_wplywu"] = array("naglowek" => 'Data wpływu faktury przewoźnik', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["termin_przewoznika"] = array("naglowek" => 'Termin płatności przewoźnik', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["planowana_zaplata_przew"] = array("naglowek" => 'Planowana zapłata przewoźnik', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["rzecz_zaplata_przew"] = array("naglowek" => 'Rzeczywista zapłata przewoźnik', "td_class" => "platnosci", 'type' => 'date', 'td_styl' => 'text-align: center');
            $Wynik["opoznienie_przewoznik"] = array("naglowek" => 'Opóźnienie przewoźnik', "td_class" => "platnosci", 'td_styl' => 'text-align: center');
            $Wynik["fifo"] = array("naglowek" => 'FIFO', "td_class" => "platnosci", 'td_styl' => 'text-align: center');
            return $Wynik;
        }

        function StatusyPlatnosci(){
            $Statusy[0] = "-- brak --";
            $Statusy[1] = "drobne";
            $Statusy[2] = "regularne";
            $Statusy[3] = "krytyczne";
            $Statusy[4] = "wezwania do zapłaty";
            $Statusy[5] = "zgłoszenia trans";
            $Statusy[6] = "potwierdzona transakcja";
            return $Statusy;
        }
        
        function StatusyPlatnosciKlient(){
            $Statusy[0] = "-- brak --";
            $Statusy[1] = "Telefon";
            $Statusy[2] = "Mail";
            $Statusy[3] = "Do wyjaśnienia";
            $Statusy[4] = "Obiecane";
            $Statusy[5] = "Estymowane";
            $Statusy[6] = "Wezwanie do zapłaty";
            $Statusy[7] = "Sprawa w sądzie";
            $Statusy[8] = "Duplikat";
            $Statusy[9] = "Kompensata";
            return $Statusy;
        }

        function StatusyPlatnosciAirSea(){
            $Statusy[0] = "-- brak --";
            $Statusy[1] = "estymowane";
            $Statusy[2] = "regularne";
            $Statusy[3] = "krytyczne";
            $Statusy[4] = "wezwania do zapłaty";
            $Statusy[5] = "przedpłata";
            return $Statusy;
        }
        
        function ShowDate($Data){
            if($Data == "0000-00-00"){
                return "&nbsp;";
            }else{
                return $Data;
            }
        }

}
?>
