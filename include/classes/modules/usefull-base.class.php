<?php
/**
 * Moduł funkcji użytecznych
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright       Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class UsefullBase{

	function __construct() {
	}

        function GetBranze($Baza){
		return $Baza->GetOptions("SELECT branza_id, branza_nazwa FROM orderplus_klient_branza ORDER BY branza_nazwa ASC");
	}

        function GetBranzeCRM($Baza){
		return $Baza->GetOptions("SELECT id, branza FROM branza ORDER BY branza ASC");
	}
 
	function GetSiedziby($Baza){
		return $Baza->GetOptions("SELECT siedziba_id, siedziba_nazwa FROM orderplus_klient_siedziba ORDER BY siedziba_nazwa ASC");
	}

        function GetTypySerwisu($Baza){
            return $Baza->GetOptions("SELECT typ_serwisu_id, typ_serwisu_nazwa FROM orderplus_typy_serwisu ORDER BY typ_serwisu_id");
        }

        function GetZlecenia($Baza, $ClientID = 0, $Kody = array()){
            $Where = '';
            if(isset($Kody['kraj_1']) && $Kody['kraj_1'] > 0){
                $Where .= " AND kod_kraju_zaladunku = '{$Kody['kraj_1']}'";
            }
            if(isset($Kody['kraj_2']) && $Kody['kraj_2'] > 0){
                $Where .= " AND kod_kraju_rozladunku = '{$Kody['kraj_2']}'";
            }
            return $Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND id_klient = '$ClientID' AND data_zlecenia >= '2010-01-01'$Where ORDER BY data_zlecenia DESC");
	}

        function GetPrzewoznicy($Baza){
            return $Baza->GetOptions("SELECT id_przewoznik, nazwa FROM orderplus_przewoznik ORDER BY nazwa");
        }

        function GetPrzewoznicyIds($Baza){
            return $Baza->GetOptions("SELECT id_przewoznik, identyfikator FROM orderplus_przewoznik");
        }

        function GetPrzewoznicyByNip($Baza, $Wartosc){
            return $Baza->GetValues("SELECT id_przewoznik FROM orderplus_przewoznik WHERE nip LIKE '%$Wartosc%'");
        }

        function GetPrzewoznicyWithClass($Baza){
            return $Baza->GetResultAsArray("SELECT id_przewoznik, nazwa, klasa_id FROM orderplus_przewoznik ORDER BY nazwa", "id_przewoznik");
        }

        function GetPrzewoznikClass($Baza){
            return $Baza->GetResultAsArray("SELECT klasa_id, klasa_nazwa, klasa_color FROM orderplus_przewoznik_klasy ORDER BY klasa_id", "klasa_id");
	}

        function GetKlienci($Baza){
            return $Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient ORDER BY nazwa");
        }

        function GetKlienciActive($Baza){
            return $Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient WHERE klient_status = '1' ORDER BY nazwa");
        }

        function GetKlienciAccessOddzial($Baza, $Uzytkownik){
            $warunex = "";
            if($Uzytkownik->CheckNoOddzial()){
                $Klienci = $Baza->GetValues("SELECT id_klient FROM orderplus_klient_oddzial WHERE id_oddzial = '{$_SESSION['id_oddzial']}'");
                $Klienci[] = 0;
                $warunek = "WHERE id_klient IN(".implode(",",$Klienci).")";
            }
            return $Baza->GetOptions("SELECT id_klient, nazwa  FROM orderplus_klient $warunek ORDER BY nazwa"); 
        }

        function GetKierowcy($Baza){
            return $Baza->GetOptions("SELECT id_kierowca, imie_nazwisko FROM orderplus_kierowca ORDER BY imie_nazwisko");
        }

        function GetDaneKierowcy($Baza){
            return $Baza->GetOptions("SELECT id_kierowca, CONCAT(imie_nazwisko,'<br>',dane_kierowcy) as kierowca FROM orderplus_kierowca");
        }

        function GetKlienciByNip($Baza, $Wartosc){
            return $Baza->GetValues("SELECT id_klient FROM orderplus_klient WHERE nip LIKE '%$Wartosc%'");
        }

        function GetKlienciWithTermin($Baza){
            $Klienci = $Baza->GetResultAsArray("SELECT k.id_klient, nazwa, termin_platnosci_dni FROM orderplus_klient k ".(!in_array($_SESSION["uprawnienia_id"], array(1,4)) ? "LEFT JOIN orderplus_klient_oddzial ko ON(ko.id_klient = k.id_klient) WHERE ko.id_oddzial = '{$_SESSION['id_oddzial']}' AND " : "WHERE ")."k.klient_status = '1' ORDER BY nazwa", "id_klient");
            return $Klienci;
        }

        function GetDaneKlienta($Baza, $KlientID){
            return $Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '$KlientID'");
        }

        function PobierzKursZDnia($Baza, $data_wystawienia, $waluta, $id_klient = false){
            if($id_klient){
                $Bank = $Baza->GetValue("SELECT kurs_waluty_bank FROM orderplus_klient WHERE id_klient = '$id_klient'");
                $Tabela = ($Bank == "KOM" ? "orderplus_kurs_bph" : "orderplus_kurs");
            }else{
                $Tabela = "orderplus_kurs";
            }
            $TerminKurs = $data_wystawienia;
            $zmiana_terminu = 0;
            $mala_waluta = strtolower($waluta);
            $kurs = $Baza->GetValue("SELECT $mala_waluta FROM $Tabela WHERE data_publikacji = '$TerminKurs' ORDER BY data_publikacji ASC LIMIT 1");
            $dzis = date("Y-m-d");
            if($kurs == false && $TerminKurs < $dzis){
                $kurs = $Baza->GetValue("SELECT $mala_waluta FROM $Tabela WHERE data_publikacji <= '$TerminKurs' ORDER BY data_publikacji DESC LIMIT 1");
            }
            if($kurs == false){
                $kurs = "0";
            }
            return $kurs;
        }

        function ShowWyborPrzedzialu($Baza, $Submit = true, $start = false, $stop = false){
                if(!$start){
                    $start = $Baza->GetValue("SELECT data_zlecenia FROM orderplus_zlecenie WHERE data_zlecenia != '0000-00-00' ORDER BY data_zlecenia ASC");
                }
                if(!$stop){
                    $stop = $Baza->GetValue("SELECT data_zlecenia FROM orderplus_zlecenie WHERE data_zlecenia != '0000-00-00' ORDER BY data_zlecenia DESC");
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
		   "{$_SESSION['okresStart']}-01" == $temp  ? $sel = 'selected' : $sel = '';
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

        function GetCountryCodes($Baza){
		return $Baza->GetOptions("SELECT kod_kraju_id, CONCAT(kod_kraju_nazwa,' - ',kraj_nazwa) as nazwa FROM orderplus_kody_krajow ORDER BY kod_kraju_nazwa");
	}

        function GetSzablony($Baza){
            return $Baza->GetOptions("SELECT id_szablon, tytul FROM orderplus_szablon WHERE status ='1'  ORDER BY lp ASC");
        }

        function GetDriversByPrzewoznik($Baza, $ID){
            return $Baza->GetOptions("SELECT id_kierowca, imie_nazwisko FROM orderplus_kierowca WHERE id_przewoznik = '$ID'");
        }

        function GetUsers($Baza){
            return $Baza->GetOptions("SELECT id_uzytkownik, CONCAT(imie,' ',nazwisko) as user FROM orderplus_uzytkownik");
        }

        function GetUsersLoginByOddzial($Baza, $OddzialID){
            return $Baza->GetOptions("SELECT id_uzytkownik, login FROM orderplus_uzytkownik WHERE id_oddzial = '$OddzialID'");
        }

        function GetUsersLogin($Baza){
            return $Baza->GetOptions("SELECT id_uzytkownik, login FROM orderplus_uzytkownik WHERE blokada = '0' ORDER BY login");
        }

        function GetWaluty($Baza){
            return $Baza->GetOptions("SELECT id_waluty, waluta FROM faktury_waluty");
        }

	function GetBranzeKlientow($Baza){
		return $Baza->GetOptions("SELECT id_klient, branza_id FROM orderplus_klient");
	}

	function GetSiedzibyKlientow($Baza){
		return $Baza->GetOptions("SELECT id_klient, siedziba_id FROM orderplus_klient");
	}

        function GetOddzialy($Baza, $Where = ""){
		return $Baza->GetOptions("SELECT id_oddzial, CONCAT(skrot,' ',prefix) as name FROM orderplus_oddzial".($Where != "" ? " WHERE $Where" : "")." ORDER BY nazwa ASC");
	}

        function GetZleceniaByClientAndTrasa($Baza, $ClientID, $Kody){
            $Where = '';
            if(isset($Kody['kraj_1']) && $Kody['kraj_1'] > 0){
                $Where .= " AND kod_kraju_zaladunku = '{$Kody['kraj_1']}'";
            }
            if(isset($Kody['kraj_2']) && $Kody['kraj_2'] > 0){
                $Where .= " AND kod_kraju_rozladunku = '{$Kody['kraj_2']}'";
            }
            return $Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND id_klient = '$ClientID' AND data_zlecenia >= '2010-01-01'$Where ORDER BY data_zlecenia DESC");
        }

        function GetTerms($Baza){
            return $Baza->GetOptions("SELECT terms_id, terma_nazwa FROM orderplus_sea_orders_terms ORDER BY terms_id");
        }

        function GetCarriers($Baza){
            return $Baza->GetOptions("SELECT carrier_id, carrier_name FROM orderplus_sea_orders_carriers");
        }

        function GetSizes($Baza){
            return $Baza->GetOptions("SELECT size_id, size_name FROM orderplus_sea_orders_sizes");
        }

        function GetTypes($Baza){
            return $Baza->GetOptions("SELECT type_id, type_name FROM orderplus_sea_orders_types");
        }

        function GetEmailePrzewoznika($Baza, $ID){
            return $Baza->GetValue("SELECT emaile FROM orderplus_przewoznik WHERE id_przewoznik = '$ID'");
        }

        function PobierzKursDoFaktury($Baza, $Pobierz, $Zwroc, $Termin, $id_klient = false, $ZwrocDaneKursu = false){
            if($id_klient){
                $Bank = $Baza->GetValue("SELECT kurs_waluty_bank FROM orderplus_klient WHERE id_klient = '$id_klient'");
                $Tabela = ($Bank == "KOM" ? "orderplus_kurs_bph" : "orderplus_kurs");
            }else{
                $Bank = "NBP";
                $Tabela = "orderplus_kurs";
            }
            $TerminKurs = $Termin;
           $KursZDniaZaladunku = mysql_query("SELECT * FROM $Tabela WHERE data_publikacji = '$TerminKurs'");
           if(mysql_num_rows($KursZDniaZaladunku) > 0){
              $kurs = mysql_fetch_object($KursZDniaZaladunku);
           }else{
              $KursZDniaZaladunku = mysql_query("SELECT * FROM $Tabela WHERE data_publikacji <= '$TerminKurs' ORDER BY data_publikacji DESC LIMIT 1");
              $kurs = mysql_fetch_object($KursZDniaZaladunku);
           }

           switch($Pobierz){
              case('PLN'):  $kursy['PLN'] = 1.0000;
                            $kursy['EUR'] = round($kurs->pln/$kurs->eur,4);
                            $kursy['USD'] = round($kurs->pln/$kurs->usd,4);
                            break;

              case('EUR'):	$kursy['PLN'] = $kurs->eur;
                              $kursy['EUR'] = 1.0000;
                              $kursy['USD'] = round($kurs->eur/$kurs->usd,4);
                                break;

              case('USD'):	$kursy['PLN'] = $kurs->usd;
                              $kursy['EUR'] = round($kurs->usd/$kurs->eur,4);
                              $kursy['USD'] = 1.0000;
                              break;
           }
           if($ZwrocDaneKursu){
               return array('data' => $kurs->data_publikacji, 'tabela' => $kurs->nr_tabeli, 'bank' => $Bank);
           }
           return number_format($kursy[$Zwroc], 4, ".","");
        }

        function GetGrupyFirm($Baza){
            return $Baza->GetOptions("SELECT id, grupa FROM grupy_firm ORDER BY grupa");
        }

        function GetPotencjaly($Baza){
            return $Baza->GetOptions("SELECT id, potencjal FROM potencjal ORDER BY id DESC");
        }

        function GetKodyKrajowCRM($Baza){
            return $Baza->GetOptions("SELECT id, CONCAT(kod,' - ',nazwa_kraju) as kraj FROM kod_kraju ORDER BY kod ASC");
        }

        function GetStatystyki($Baza){
            return $Baza->GetOptions("SELECT id, statystyka FROM statystyka ORDER BY statystyka");
        }

        function GetPriorytety($Baza){
            return $Baza->GetOptions("SELECT id, priorytet FROM priorytet ORDER BY priorytet");
        }

        function PobierzDostepDoKolumn($Baza, $UserID){
            $Kolumny = array();
            $Baza->Query("SELECT * FROM orderplus_uzytkownik_tabela_rozliczen WHERE id_uzytkownik = '$UserID'");
            while($Kol = $Baza->GetRow()){
                $Kolumny[] = ($Kol['tabela_kolumna'] == "widok" ? $Kol['tabela_widok'] : $Kol['tabela_kolumna']);
            }
            return $Kolumny;
        }

}
?>
