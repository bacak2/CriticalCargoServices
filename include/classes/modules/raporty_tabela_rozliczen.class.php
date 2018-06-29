<?php
/**
 * Moduł tabela rozliczen
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class TabelaRozliczenRaporty extends ModulBazowy {

        public $styleBothBorder;
        public $styleRightBorder;
        public $styleLeftBorder;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
	}

        function AkcjaDrukuj($ID, $Akcja){
            $Waluty = UsefullBase::GetWaluty($this->Baza);
            $tryb = $_GET['tryb'];
            if($Akcja == "raporty"){
                $tryb_check = ($tryb == "klientmojatabela" ? "klient" : $tryb);
                if($this->Uzytkownik->DostepDoRaportu($tryb_check) == false){
                    die('BRAK UPRAWNIEŃ');
                }
                if($tryb == 'klient' || $tryb == "klientmojatabela")
                {
                   $Elementy = $this->Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient ORDER BY nazwa ASC");
                   $PoleID = "id_klient";
                }
                if($tryb == 'przewoznik')
                {
                   $Elementy = $this->Baza->GetOptions("SELECT id_przewoznik, nazwa FROM orderplus_przewoznik ORDER BY nazwa ASC");
                   $PoleID = "id_przewoznik";
                   $PoleMorskieID = "inland_carrier_id";
                }
                if($tryb == 'spedytor')
                {
                   $Elementy = $this->Baza->GetOptions("SELECT id_uzytkownik, CONCAT(imie,' ',nazwisko) as nazwa FROM orderplus_uzytkownik ORDER BY login ASC");
                   $PoleID = "id_uzytkownik";
                   $PoleMorskieID = "id_uzytkownik";
                }
                if($tryb == 'oddzial')
                {
                    $Elementy = $this->Baza->GetOptions("SELECT id_oddzial, nazwa FROM orderplus_oddzial ORDER BY nazwa ASC");
                   $PoleID = "id_oddzial";
                   $PoleMorskieID = "id_oddzial";
                }
                if($tryb == 'klientnaspedytora')
                {
                   $Elementy = $this->Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient ORDER BY nazwa ASC");
                   $PoleID = "id_klient";
                }

                if($tryb == "klientmojatabela"){
                        $InniUserzy = $this->Baza->GetValues("SELECT id_uzytkownik FROM orderplus_uzytkownik WHERE id_oddzial = '{$_SESSION['id_oddzial']}'");
                        $InniUserzy[] = $_SESSION['id_uzytkownik'];
                }
                include(SCIEZKA_SZABLONOW."druki/raporty.tpl.php");
            }else if($Akcja == "raporty2"){
                if($this->Uzytkownik->DostepDoRaportu($tryb) == false){
                    die('BRAK UPRAWNIEŃ');
                }
                switch($tryb){
                        case "branza": $Elementy = UsefullBase::GetBranze($this->Baza); $Naglowek = "wg. branży "; $Kolumna = "Branża"; $Klienci = UsefullBase::GetBranzeKlientow($this->Baza); $Pole = "id_klient"; break;
                        case "siedziba": $Elementy = UsefullBase::GetSiedziby($this->Baza); $Naglowek = "wg. siedziby klienta "; $Kolumna = "Siedziba"; $Klienci = UsefullBase::GetSiedzibyKlientow($this->Baza); $Pole = "id_klient"; break;
                        case "typ_serwisu": $Elementy = UsefullBase::GetTypySerwisu($this->Baza); $Elementy['FCL'] = 'FCL'; $Elementy['LCL'] = 'LCL'; $Naglowek = "wg. typu serwisu "; $Kolumna = "Typ serwisu"; $Pole = "typ_serwisu"; break;
                }
                include(SCIEZKA_SZABLONOW."druki/raporty2.tpl.php");
            }else if($Akcja == "raporty3"){
                if($this->Uzytkownik->DostepDoRaportu("trasy") == false){
                    die('BRAK UPRAWNIEŃ');
                }
                include(SCIEZKA_SZABLONOW."druki/raporty3.tpl.php");
            }else if($Akcja == "analiza_wynikow"){
                if($this->Uzytkownik->DostepDoRaportu("analiza_wynikow") == false){
                    die('BRAK UPRAWNIEŃ');
                }
                $this->RaportAnalizaWynikow();
            }else if($Akcja == "analiza_wynikow_airsea"){
                if($this->Uzytkownik->DostepDoRaportu("analiza_wynikow") == false){
                    die('BRAK UPRAWNIEŃ');
                }
                $this->RaportAnalizaWynikowAirSea();
            }
            else if($Akcja == "analiza_wynikow_stara"){
                if($this->Uzytkownik->DostepDoRaportu("analiza_wynikow_stara") == false){
                    die('BRAK UPRAWNIEŃ');
                }
                include(SCIEZKA_SZABLONOW."druki/raporty_analiza_wynikow.tpl.php");
            }

        }

        function ShowNaglowekDrukuj($Akcja){
            include(SCIEZKA_SZABLONOW."naglowek_drukuj_raporty.tpl.php");
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

        function MakeKey($Termin, $Data){
            if($Termin == "miesieczny"){
                $Key = date("Y-m", strtotime($Data));
            }
            if($Termin == "tygodniowy"){
                $dzien_w_roku = date("z", strtotime($Data));
                $week = ceil(($dzien_w_roku+1) / 7);
                $year = date("Y", strtotime($Data));
                $Key = $year."-".$week;
            }
            if($Termin == "dzienny"){
                $Key = $Data;
            }
            return $Key;
        }

        function RaportAnalizaWynikow($XLS = false){
            $Rodzaj = (isset($_POST['rodzaj']) ? $_POST['rodzaj'] : 0);
             $warunek = ($this->Uzytkownik->IsAdmin() ? "" : "id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ");
             $warunek_morski = "";
             $warunek_lotniczy = "";
             if($Rodzaj == 1){
                $Oddzialy = UsefullBase::GetOddzialy($this->Baza, ($this->Uzytkownik->IsAdmin() ? "" : "id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") "));
                $OddzialID = (isset($_POST['oddzial']) ? $_POST['oddzial'] : $_SESSION['id_oddzial']);
                $warunek = "id_oddzial = '$OddzialID' AND ";
                $warunek_morski = "id_oddzial = '$OddzialID' AND ";
                $warunek_lotniczy = "id_oddzial = '$OddzialID' AND ";
            }else if($Rodzaj == 2){
               $Spedytorzy = $this->Baza->GetOptions("SELECT id_uzytkownik, CONCAT(login,' (',imie,' ',nazwisko,')') as nazwa FROM orderplus_uzytkownik".($this->Uzytkownik->IsAdmin() ? "" : " WHERE id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") ")." ORDER BY login ASC");
               $SpedID = (isset($_POST['spedid']) ? $_POST['spedid'] :  Usefull::GetFirstKey($Spedytorzy));
               $warunek = "id_uzytkownik = '$SpedID' AND ";
               $warunek_morski = "id_uzytkownik = '$SpedID' AND ";
               $warunek_lotniczy = "id_uzytkownik = '$SpedID' AND ";
            }
            $Lata = array();
            $EndRok = date("Y")*1;
            for($Rok = 2009; $Rok <= $EndRok; $Rok++){
                $Lata[$Rok] = $Rok;
            }
            $Miesiace = array("01" => 'styczeń', "02" => "luty", "03" => "marzec", "04" => "kwiecień", "05" => "maj", "06" => "czerwiec", "07" => "lipiec", "08" => "sierpień",
                                "09" => "wrzesień", "10" => "październik", "11" => "listopad", "12" => "grudzień");
            $Tygodnie = array();
            for($Weeke = 1; $Weeke <= 53; $Weeke++){
                $Tygodnie[$Weeke] = $Weeke." tydzień";
            }
            $Termin = (isset($_POST['rodzaj_termin']) ? $_POST['rodzaj_termin'] : "miesieczny");
            $MiesiacOd = (isset($_POST['miesiac_1']) ? $_POST['miesiac_1'] : date("m"));
            $MiesiacDo = (isset($_POST['miesiac_2']) ? $_POST['miesiac_2'] : date("m"));
            $RokOd = (isset($_POST['rok_1']) ? $_POST['rok_1'] : date("Y"));
            $RokDo = (isset($_POST['rok_2']) ? $_POST['rok_2'] : date("Y"));
            $TydzienOd = (isset($_POST['tydzien_1']) ? $_POST['tydzien_1'] : 1);
            $TydzienDo = (isset($_POST['tydzien_2']) ? $_POST['tydzien_2'] : 52);
            $totalna_suma_marzy = 0;
            $totalna_suma_klienta = 0;
            $totalna_suma_przewoznika = 0;
            $totalna_suma_zlecen = 0;

            if($Termin == "miesieczny"){
                $Start = $RokOd."-".$MiesiacOd."-01";
                $End = $RokDo."-".$MiesiacDo."-31";
            }
            if($Termin == "tygodniowy"){
                $DzienStart = 7 * ($TydzienOd - 1);
                $DzienEnd = 7 * ($TydzienDo);

                if($DzienEnd > 365){
                    $DzienEnd = 365;
                }

                $Start = date("Y-m-d", strtotime("$RokOd-01-01 + $DzienStart days"));
                $End = date("Y-m-d", strtotime("$RokDo-01-01 + $DzienEnd days"));
            }

            if($Termin == "dzienny"){
                $Start = $RokOd."-".$MiesiacOd."-01";
                $End = date("Y-m-d", strtotime($RokDo."-".$MiesiacDo."-01 +1 month -1 day"));
            }

            $filtr_datowy = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";
            $filtr_datowy_morski = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";
            $filtr_datowy_lotniczy = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";

            $DaneDoRaportu = array();
            $KlienciIDS = array();
            $z2 = "SELECT * FROM orderplus_zlecenie WHERE $warunek ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy ORDER BY data_zlecenia ASC";
            $w2 = mysql_query($z2);
             while($zleconko = mysql_fetch_array($w2)){
                    $Key = $this->MakeKey($Termin, $zleconko["data_zlecenia"]);
                    $KlienciIDS[] = $zleconko['id_klient'];
                    
                    if(!key_exists($zleconko['id_klient'],$DaneDoRaportu)){
                        $DaneDoRaportu[$zleconko['id_klient']] = array();
                    }
                    if(!key_exists($Key,$DaneDoRaportu[$zleconko['id_klient']])){
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["ilosc_zlecen"] = 0;
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_klient"] = 0;
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_przewoznik"] = 0;
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["marza"] = 0;
                    }
                    $DaneDoRaportu[$zleconko['id_klient']][$Key]["ilosc_zlecen"]++;
                    if($zleconko["waluta"] == "PLN"){
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_klient"] += $zleconko["stawka_klient"];
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_przewoznik"] += $zleconko["stawka_przewoznik"];
                            $marza = $zleconko["stawka_klient"] - $zleconko["stawka_przewoznik"];
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["marza"] += $marza;
                    }
                    else{
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_klient"] += $zleconko["stawka_klient"]*$zleconko["kurs"];
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["suma_przewoznik"] += $zleconko["stawka_przewoznik"]*$zleconko["kurs_przewoznik"];
                            $marza = ($zleconko["stawka_klient"]*$zleconko["kurs"]) - ($zleconko["stawka_przewoznik"]*$zleconko["kurs_przewoznik"]);
                            $DaneDoRaportu[$zleconko['id_klient']][$Key]["marza"] += $marza;
                    }
//                    if($zleconko['id_klient'] == "0"){
//                        //echo "data zlecenia: ".$zleconko['data_zlecenia']."  ".$zleconko['numer_zlecenia']." marża: $marza<br />"; //kwota sprzedaży: ".($zleconko["waluta"] == "PLN" ? $zleconko["stawka_klient"] : $zleconko["stawka_klient"]*$zleconko["kurs"])."<br />";
//                    }
             }

             $klienci_ids = array_unique($KlienciIDS);
             if(count($klienci_ids) > 0){
                $klienci = $this->Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient WHERE id_klient IN(".implode(",",$klienci_ids).")ORDER BY nazwa");
             }else{
                 $klienci = array();
             }
             $klienci_keys = array_keys($klienci);
             $UsunieciKlienci = array();
             foreach($DaneDoRaportu as $klient_id => $dane_by_okres){
                 foreach($dane_by_okres as $keyek => $Dane){
                     if(!key_exists($keyek, $SumaIlosciZlecen)){
                         $SumaObrotow[$keyek] = 0;
                         $SumaPrzewoznik[$keyek] = 0;
                         $SumaMarzy[$keyek] = 0;
                         $SumaIlosciZlecen[$keyek] = 0;
                     }
                        $SumaIlosciZlecen[$keyek] += $Dane["ilosc_zlecen"];
                        $SumaObrotow[$keyek] += $Dane["suma_klient"];
                        $SumaPrzewoznik[$keyek] += $Dane["suma_przewoznik"];
                        $SumaMarzy[$keyek] += $Dane["marza"];
                        if(!in_array($klient_id, $klienci_keys)){
                            $UsunieciKlienci[$keyek]['ilosc_zlecen'] += $Dane["ilosc_zlecen"];
                            $UsunieciKlienci[$keyek]['suma_klient'] += $Dane["suma_klient"];
                            $UsunieciKlienci[$keyek]['suma_przewoznik'] += $Dane["suma_przewoznik"];
                            $UsunieciKlienci[$keyek]['marza'] += $Dane["marza"];
                            //var_dump($klient_id);
                        }
                 }
             }
             if($_SESSION['login'] == "artplusadmin"){
                 //$KlienciIDS = $this->Baza->GetValues("SELECT id_klient FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' ORDER BY termin_zaladunku ASC");
             }
             
            if($XLS){
                $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
                                "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ",
                                "CA", "CB", "CC", "CD", "CE", "CF", "CG", "CH", "CI", "CJ", "CK", "CL", "CM", "CN", "CO", "CP", "CQ", "CR", "CS", "CT", "CU", "CV", "CW", "CX", "CY", "CZ",
                                "DA", "DB", "DC", "DD", "DE", "DF", "DG", "DH", "DI", "DJ", "DK", "DL", "DM", "DN", "DO", "DP", "DQ", "DR", "DS", "DT", "DU", "DV", "DW", "DX", "DY", "DZ",
                                "EA", "EB", "EC", "ED", "EE", "EF", "EG", "EH", "EI", "EJ", "EK", "EL", "EM", "EN", "EO", "EP", "EQ", "ER", "ES", "ET", "EU", "EV", "EW", "EX", "EY", "EZ",
                                "FA", "FB", "FC", "FD", "FE", "FF", "FG", "FH", "FI", "FJ", "FK", "FL", "FM", "FN", "FO", "FP", "FQ", "FR", "FS", "FT", "FU", "FV", "FW", "FX", "FY", "FZ",
                                "GA", "GB", "GC", "GD", "GE", "GF", "GG", "GH", "GI", "GJ", "GK", "GL", "GM", "GN", "GO", "GP", "GQ", "GR", "GS", "GT", "GU", "GV", "GW", "GX", "GY", "GZ",
                                "HA", "HB", "HC", "HD", "HE", "HF", "HG", "HH", "HI", "HJ", "HK", "HL", "HM", "HN", "HO", "HP", "HQ", "HR", "HS", "HT", "HU", "HV", "HW", "HX", "HY", "HZ",
                                "IA", "IB", "IC", "ID", "IE", "IF", "IG", "IH", "II", "IJ", "IK", "IL", "IM", "IN", "IO", "IP", "IQ", "IR", "IS", "IT", "IU", "IV", "IW", "IX", "IY", "IZ");
                include(SCIEZKA_INCLUDE."PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Analiza wyników");
                $ActiveSheet = $objPHPExcel->getActiveSheet();
                 $tablica_key = array();
                 $idx = 1;
                 $this->SetCSStoXML();
                 if($Termin == "miesieczny"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $month_key = date("m", strtotime($date_check));
                        $rok_key = date("Y", strtotime($date_check));
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "{$Miesiace[$month_key]} $rok_key");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}2", "Ilość zleceń");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+1]}2", "Sprzedaż");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+2]}2", "Koszt");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+3]}2", "Marża");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+4]}2", "% marża");
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +1 months"));
                        $tablica_key[] = "$rok_key-$month_key";
                    }
                 }
                 if($Termin == "tygodniowy"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $yearday = date("z", strtotime($date_check));
                        $week_key = ceil(($yearday+1) / 7);
                        $rok_key = date("Y", strtotime($date_check));
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "$week_key tydzień $rok_key");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +7 days"));
                        $tablica_key[] = "$rok_key-$week_key";
                    }
                 }
                 if($Termin == "dzienny"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "$date_check");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +1 days"));
                        $tablica_key[] = "$date_check";
                    }
                 }
                 $fill_to = $idx-1;
                 $ActiveSheet->getColumnDimension("A")->setWidth(35);
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getFont()->setBold(true);
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getFont()->setBold(true);

                $row_idx = 3;
                foreach($klienci as $klient_id => $klient_name){
                        $column_idx = 0;
                        $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", $klient_name);
                        $column_idx++;
                        foreach($tablica_key as $ID){
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleLeftBorder);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["ilosc_zlecen"], 0, ",",""));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["suma_klient"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["suma_przewoznik"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["marza"], 2, ',', ''));
                            $column_idx++;
                            $Dzielnik = ($DaneDoRaportu[$klient_id][$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$klient_id][$ID]["marza"]*100)/$DaneDoRaportu[$klient_id][$ID]["suma_klient"]);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($Dzielnik, 2, ',', '')  ." %");
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleRightBorder);
                            $column_idx++;     
                          }
                          $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                      $row_idx++;
                }
                if(count($UsunieciKlienci) > 0){
                    $column_idx = 0;
                        $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", "Usunięci klienci");
                        $column_idx++;
                        foreach($tablica_key as $ID){
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleLeftBorder);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($UsunieciKlienci[$ID]["ilosc_zlecen"], 0, ",",""));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($UsunieciKlienci[$ID]["suma_klient"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($UsunieciKlienci[$ID]["suma_przewoznik"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($UsunieciKlienci[$ID]["marza"], 2, ',', ''));
                            $column_idx++;
                            $Dzielnik = ($UsunieciKlienci[$ID]["suma_klient"] == 0 ? 0 : ($UsunieciKlienci[$ID]["marza"]*100)/$UsunieciKlienci[$ID]["suma_klient"]);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($Dzielnik, 2, ',', '')  ." %");
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleRightBorder);
                            $column_idx++;     
                          }
                          $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                      $row_idx++;
                }
                $column_idx = 1;
                foreach($tablica_key as $ID){
                    $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleLeftBorder);
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaIlosciZlecen[$ID], 0, ",",""));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaObrotow[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaPrzewoznik[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaMarzy[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleRightBorder);
                    $column_idx++;
                }
                $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getFont()->setBold(true);
                $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('EEEEEEEE');
                
                $ActiveSheet->setTitle('Analiza wyników'); 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="analiza_wynikow.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }else{
                include(SCIEZKA_SZABLONOW."druki/raporty_analiza_wynikow_nowa.tpl.php");
            }
        }

        function SetCSStoXML(){
            $this->styleBothBorder = array(
                  'borders' => array(
                    'right' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('argb' => '000000000')
                    ),
                    'left' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('argb' => '000000000')
                    )
                  )
                );
            $this->styleRightBorder = array(
                  'borders' => array(
                    'right' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('argb' => '000000000')
                    )
                  )
                );
            $this->styleLeftBorder = array(
                  'borders' => array(
                    'left' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('argb' => '000000000')
                    )
                  )
                );
        }

        function RaportAnalizaWynikowAirSea($XLS = false){
            $Rodzaj = (isset($_POST['rodzaj']) ? $_POST['rodzaj'] : 0);
             if($this->Uzytkownik->CheckNoOddzial() == false){
                $warunek_morski = "";
                $warunek_lotniczy = "";
             }else{
                 $warunek_morski = " id_oddzial = '{$_SESSION['id_oddzial']}' AND";
                 $warunek_lotniczy = " id_oddzial = '{$_SESSION['id_oddzial']}' AND";
             }
             if($Rodzaj == 1){
                $Oddzialy = array();
                $pick = "sea_waw";
                if($this->Uzytkownik->CheckNoOddzial() == false || $_SESSION['id_oddzial'] == 4){
                    $Oddzialy['sea'] = "GDY SEA";
                    $Oddzialy['air'] = "GDY AIR";
                    $pick = "sea";
                }
                if($this->Uzytkownik->CheckNoOddzial() == false || $_SESSION['id_oddzial'] == 10){
                    $Oddzialy['sea_waw'] = "WAW SEA";
                }
                $OddzialID = (isset($_POST['oddzial']) ? $_POST['oddzial'] : $pick);
                $warunek = "id_oddzial = '$OddzialID' AND ";
                if($this->Uzytkownik->CheckNoOddzial() == false){
                    $warunek_morski = " id_oddzial = '".($OddzialID == "sea" || $OddzialID == "air" ? "4" : "10")."' AND";
                    $warunek_lotniczy = " ";
                }else{
                    $warunek_morski = " id_oddzial = '".($OddzialID == "sea" || $OddzialID == "air" ? "4" : "10")."' AND";
                    $warunek_lotniczy = " id_oddzial = '".($OddzialID == "sea" || $OddzialID == "air" ? "4" : "10")."' AND";
                }
            }else if($Rodzaj == 2){
               $Spedytorzy = $this->Baza->GetOptions("SELECT id_uzytkownik, CONCAT(login,' (',imie,' ',nazwisko,')') as nazwa FROM orderplus_uzytkownik".($this->Uzytkownik->IsAdmin() ? "" : " WHERE id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") ")." ORDER BY login ASC");
               $SpedID = (isset($_POST['spedid']) ? $_POST['spedid'] :  Usefull::GetFirstKey($Spedytorzy));
               $warunek = "id_uzytkownik = '$SpedID' AND ";
               $warunek_morski = "id_uzytkownik = '$SpedID' AND ";
               $warunek_lotniczy = "id_uzytkownik = '$SpedID' AND ";
            }
            $Lata = array();
            $EndRok = date("Y")*1;
            for($Rok = 2009; $Rok <= $EndRok; $Rok++){
                $Lata[$Rok] = $Rok;
            }
            $Miesiace = array("01" => 'styczeń', "02" => "luty", "03" => "marzec", "04" => "kwiecień", "05" => "maj", "06" => "czerwiec", "07" => "lipiec", "08" => "sierpień",
                                "09" => "wrzesień", "10" => "październik", "11" => "listopad", "12" => "grudzień");
            $Tygodnie = array();
            for($Weeke = 1; $Weeke <= 53; $Weeke++){
                $Tygodnie[$Weeke] = $Weeke." tydzień";
            }
            $Termin = (isset($_POST['rodzaj_termin']) ? $_POST['rodzaj_termin'] : "miesieczny");
            $MiesiacOd = (isset($_POST['miesiac_1']) ? $_POST['miesiac_1'] : date("m"));
            $MiesiacDo = (isset($_POST['miesiac_2']) ? $_POST['miesiac_2'] : date("m"));
            $RokOd = (isset($_POST['rok_1']) ? $_POST['rok_1'] : date("Y"));
            $RokDo = (isset($_POST['rok_2']) ? $_POST['rok_2'] : date("Y"));
            $TydzienOd = (isset($_POST['tydzien_1']) ? $_POST['tydzien_1'] : 1);
            $TydzienDo = (isset($_POST['tydzien_2']) ? $_POST['tydzien_2'] : 52);
            $totalna_suma_marzy = 0;
            $totalna_suma_klienta = 0;
            $totalna_suma_przewoznika = 0;
            $totalna_suma_zlecen = 0;

            if($Termin == "miesieczny"){
                $Start = $RokOd."-".$MiesiacOd."-01";
                $End = $RokDo."-".$MiesiacDo."-31";
            }
            if($Termin == "tygodniowy"){
                $DzienStart = 7 * ($TydzienOd - 1);
                $DzienEnd = 7 * ($TydzienDo);

                if($DzienEnd > 365){
                    $DzienEnd = 365;
                }

                $Start = date("Y-m-d", strtotime("$RokOd-01-01 + $DzienStart days"));
                $End = date("Y-m-d", strtotime("$RokDo-01-01 + $DzienEnd days"));
            }

            if($Termin == "dzienny"){
                $Start = $RokOd."-".$MiesiacOd."-01";
                $End = date("Y-m-d", strtotime($RokDo."-".$MiesiacDo."-01 +1 month -1 day"));
            }

            $filtr_datowy = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";
            $filtr_datowy_morski = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";
            $filtr_datowy_lotniczy = "data_zlecenia >= '$Start' AND data_zlecenia <= '$End'";

            $DaneDoRaportu = array();
            $KlienciIDS = array();
            if($Rodzaj != 1 || $OddzialID == "sea" || $OddzialID == "sea_waw"){
                $zm2 = "SELECT * FROM orderplus_sea_orders WHERE $warunek_morski ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_morski ORDER BY data_zlecenia";
                $this->Baza->Query($zm2);
                $seas = array();
                while($zlecenia_morskie = $this->Baza->GetRow()){
                    $seas[] = $zlecenia_morskie;
                }
                foreach($seas as $zleconko_morskie){
                    $Key = $this->MakeKey($Termin, $zleconko_morskie["data_zlecenia"]);
                    $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$zleconko_morskie['id_zlecenie']}'", "id_faktury");
                    if($Faktury){
                        $SumaFakturZlecenia = 0;
                        $KwotyFakturZlecenia = array();
                        foreach($Faktury as $FID => $DaneFak){
                            $PosMany = 0;
                            $KlienciIDS[] = $DaneFak['id_klienta'];
                            if(!key_exists($DaneFak['id_klienta'],$DaneDoRaportu)){
                                $DaneDoRaportu[$DaneFak['id_klienta']] = array();
                            }
                            if(!key_exists($DaneFak['id_klienta'],$DaneDoRaportu)){
                                $DaneDoRaportu[$DaneFak['id_klienta']] = array();
                                $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["ilosc_zlecen"] = 0;
                                $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["suma_klient"] = 0;
                                $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["suma_przewoznik"] = 0;
                                $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["marza"] = 0;
                            }
                            if(!key_exists($DaneFak['id_klienta'],$KwotyFakturZlecenia)){
                                $KwotyFakturZlecenia[$DaneFak['id_klienta']] = 0;
                            }
                            $Pozycje = mysql_query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '$FID'");
                            while($Pos = mysql_fetch_array($Pozycje)){
                                if($DaneFak['id_waluty'] == 1){
                                    $PosMany += $Pos['netto'];
                                }else{
                                    $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                                }
                            }
                            $KwotyFakturZlecenia[$DaneFak['id_klienta']] += $PosMany;
                            $SumaFakturZlecenia += $PosMany;
                        }
                        ### pobranie kosztów ###
                          $Koszty = mysql_query("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$zleconko_morskie['id_zlecenie']}'");
                          $KwotaKosztu = 0;
                          while($KosztyRes = mysql_fetch_array($Koszty)){
                             $KwotaKosztu += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                          }
                        foreach($KwotyFakturZlecenia as $id_klient => $suma_faktur){
                            $DaneDoRaportu[$id_klient][$Key]["ilosc_zlecen"]++;
                            $DaneDoRaportu[$id_klient][$Key]["suma_klient"] += $suma_faktur;
                            $PrzelicznikKosztu = $suma_faktur / $SumaFakturZlecenia;
                            $Koszt_dla_klienta = $KwotaKosztu * $PrzelicznikKosztu;
                            $DaneDoRaportu[$id_klient][$Key]["suma_przewoznik"] += $Koszt_dla_klienta;
                            $marza = $suma_faktur - $Koszt_dla_klienta;
                            $DaneDoRaportu[$id_klient][$Key]["marza"] += $marza;
                        }
                    }
                }
            }

            if($Rodzaj != 1 || $OddzialID == "air"){
                $zm2 = "SELECT * FROM orderplus_air_orders WHERE $warunek_lotniczy ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND $filtr_datowy_lotniczy ORDER BY data_zlecenia";
                $this->Baza->Query($zm2);
                $airs = array();
                while($zlecenia_lotnicze = $this->Baza->GetRow()){
                    $airs[] = $zlecenia_lotnicze;
                }
                foreach($airs as $zleconko_lotnicze){
                    $Key = $this->MakeKey($Termin, $zleconko_lotnicze["data_zlecenia"]);
                    $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, id_klienta, numer FROM orderplus_air_orders_faktury WHERE id_zlecenia = '{$zleconko_lotnicze['id_zlecenie']}'", "id_faktury");
                    $SumaFakturZlecenia = 0;
                    $KwotyFakturZlecenia = array();
                    foreach($Faktury as $FID => $DaneFak){
                        $PosMany = 0;
                        $KlienciIDS[] = $DaneFak['id_klienta'];
                        if(!key_exists($DaneFak['id_klienta'],$DaneDoRaportu)){
                            $DaneDoRaportu[$DaneFak['id_klienta']] = array();
                        }
                        if(!key_exists($DaneFak['id_klienta'],$DaneDoRaportu)){
                            $DaneDoRaportu[$DaneFak['id_klienta']] = array();
                            $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["ilosc_zlecen"] = 0;
                            $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["suma_klient"] = 0;
                            $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["suma_przewoznik"] = 0;
                            $DaneDoRaportu[$DaneFak['id_klienta']][$Key]["marza"] = 0;
                        }
                        if(!key_exists($DaneFak['id_klienta'],$KwotyFakturZlecenia)){
                            $KwotyFakturZlecenia[$DaneFak['id_klienta']] = 0;
                        }
                        $Pozycje = mysql_query("SELECT * FROM orderplus_air_orders_faktury_pozycje WHERE id_faktury = '$FID'");
                        while($Pos = mysql_fetch_array($Pozycje)){
                            if($DaneFak['id_waluty'] == 1){
                                $PosMany += $Pos['netto'];
                            }else{
                                $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                            }
                        }
                        $KwotyFakturZlecenia[$DaneFak['id_klienta']] += $PosMany;
                        $SumaFakturZlecenia += $PosMany;
                    }
                    ### pobranie kosztów ###
                      $Koszty = mysql_query("SELECT * FROM orderplus_air_orders_koszty WHERE id_zlecenie = '{$zleconko_lotnicze['id_zlecenie']}'");
                      $KwotaKosztu = 0;
                      while($KosztyRes = mysql_fetch_array($Koszty)){
                         $KwotaKosztu += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                      }
                    foreach($KwotyFakturZlecenia as $id_klient => $suma_faktur){
                        $DaneDoRaportu[$id_klient][$Key]["ilosc_zlecen"]++;
                        $DaneDoRaportu[$id_klient][$Key]["suma_klient"] += $suma_faktur;
                        $PrzelicznikKosztu = $suma_faktur / $SumaFakturZlecenia;
                        $Koszt_dla_klienta = $KwotaKosztu * $PrzelicznikKosztu;
                        $DaneDoRaportu[$id_klient][$Key]["suma_przewoznik"] += $Koszt_dla_klienta;
                        $marza = $suma_faktur - $Koszt_dla_klienta;
                        $DaneDoRaportu[$id_klient][$Key]["marza"] += $marza;
                    }
                }
            }

             foreach($DaneDoRaportu as $dane_by_okres){
                 foreach($dane_by_okres as $keyek => $Dane){
                     if(!key_exists($keyek, $SumaIlosciZlecen)){
                         $SumaObrotow[$keyek] = 0;
                         $SumaPrzewoznik[$keyek] = 0;
                         $SumaMarzy[$keyek] = 0;
                         $SumaIlosciZlecen[$keyek] = 0;
                     }
                        $SumaIlosciZlecen[$keyek] += $Dane["ilosc_zlecen"];
                        $SumaObrotow[$keyek] += $Dane["suma_klient"];
                        $SumaPrzewoznik[$keyek] += $Dane["suma_przewoznik"];
                        $SumaMarzy[$keyek] += $Dane["marza"];
                 }
             }
//             if($_SESSION['login'] == "artplusadmin"){
//                 $KlienciIDS = $this->Baza->GetValues("SELECT id_klient FROM orderplus_zlecenie WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' ORDER BY termin_zaladunku ASC");
//             }
             $klienci_ids = array_unique($KlienciIDS);
             if(count($klienci_ids) > 0){
                $klienci = $this->Baza->GetOptions("SELECT id_klient, nazwa FROM orderplus_klient WHERE id_klient IN(".implode(",",$klienci_ids).") ORDER BY nazwa");
             }else{
                 $klienci = array();
             }
            if($XLS){
                $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ",
                                "BA", "BB", "BC", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BK", "BL", "BM", "BN", "BO", "BP", "BQ", "BR", "BS", "BT", "BU", "BV", "BW", "BX", "BY", "BZ",
                                "CA", "CB", "CC", "CD", "CE", "CF", "CG", "CH", "CI", "CJ", "CK", "CL", "CM", "CN", "CO", "CP", "CQ", "CR", "CS", "CT", "CU", "CV", "CW", "CX", "CY", "CZ",
                                "DA", "DB", "DC", "DD", "DE", "DF", "DG", "DH", "DI", "DJ", "DK", "DL", "DM", "DN", "DO", "DP", "DQ", "DR", "DS", "DT", "DU", "DV", "DW", "DX", "DY", "DZ",
                                "EA", "EB", "EC", "ED", "EE", "EF", "EG", "EH", "EI", "EJ", "EK", "EL", "EM", "EN", "EO", "EP", "EQ", "ER", "ES", "ET", "EU", "EV", "EW", "EX", "EY", "EZ",
                                "FA", "FB", "FC", "FD", "FE", "FF", "FG", "FH", "FI", "FJ", "FK", "FL", "FM", "FN", "FO", "FP", "FQ", "FR", "FS", "FT", "FU", "FV", "FW", "FX", "FY", "FZ",
                                "GA", "GB", "GC", "GD", "GE", "GF", "GG", "GH", "GI", "GJ", "GK", "GL", "GM", "GN", "GO", "GP", "GQ", "GR", "GS", "GT", "GU", "GV", "GW", "GX", "GY", "GZ",
                                "HA", "HB", "HC", "HD", "HE", "HF", "HG", "HH", "HI", "HJ", "HK", "HL", "HM", "HN", "HO", "HP", "HQ", "HR", "HS", "HT", "HU", "HV", "HW", "HX", "HY", "HZ",
                                "IA", "IB", "IC", "ID", "IE", "IF", "IG", "IH", "II", "IJ", "IK", "IL", "IM", "IN", "IO", "IP", "IQ", "IR", "IS", "IT", "IU", "IV", "IW", "IX", "IY", "IZ");
                include(SCIEZKA_INCLUDE."PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Analiza wyników");
                $ActiveSheet = $objPHPExcel->getActiveSheet();
                 $tablica_key = array();
                 $idx = 1;
                 $this->SetCSStoXML();
                 if($Termin == "miesieczny"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $month_key = date("m", strtotime($date_check));
                        $rok_key = date("Y", strtotime($date_check));
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "{$Miesiace[$month_key]} $rok_key");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}2", "Ilość zleceń");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+1]}2", "Sprzedaż");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+2]}2", "Koszt");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+3]}2", "Marża");
                        $ActiveSheet->setCellValue("{$Kolumny[$idx+4]}2", "% marża");
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +1 months"));
                        $tablica_key[] = "$rok_key-$month_key";
                    }
                 }
                 if($Termin == "tygodniowy"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $yearday = date("z", strtotime($date_check));
                        $week_key = ceil(($yearday+1) / 7);
                        $rok_key = date("Y", strtotime($date_check));
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "$week_key tydzień $rok_key");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +7 days"));
                        $tablica_key[] = "$rok_key-$week_key";
                    }
                 }
                 if($Termin == "dzienny"){
                    for($date_check = $Start; $date_check <= $End; $date_check = $new_date_check){
                        $ActiveSheet->setCellValue("{$Kolumny[$idx]}1", "$date_check");
                        $merge_to = $idx+4;
                        $ActiveSheet->mergeCells("{$Kolumny[$idx]}1:{$Kolumny[$merge_to]}1");
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}1")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}1")->applyFromArray($this->styleRightBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$idx]}2")->applyFromArray($this->styleLeftBorder);
                        $ActiveSheet->getStyle("{$Kolumny[$merge_to]}2")->applyFromArray($this->styleRightBorder);
                        $idx = $idx+5;
                        $new_date_check = date("Y-m-d", strtotime($date_check." +1 days"));
                        $tablica_key[] = "$date_check";
                    }
                 }
                 $fill_to = $idx-1;
                 $ActiveSheet->getColumnDimension("A")->setWidth(35);
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ActiveSheet->getStyle("A1:{$Kolumny[$fill_to]}1")->getFont()->setBold(true);
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ActiveSheet->getStyle("A2:{$Kolumny[$fill_to]}2")->getFont()->setBold(true);

                $row_idx = 3;
                foreach($klienci as $klient_id => $klient_name){
                        $column_idx = 0;
                        $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", $klient_name);
                        $column_idx++;
                        foreach($tablica_key as $ID){
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleLeftBorder);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["ilosc_zlecen"], 0, ",",""));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["suma_klient"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["suma_przewoznik"], 2, ',', ''));
                            $column_idx++;
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($DaneDoRaportu[$klient_id][$ID]["marza"], 2, ',', ''));
                            $column_idx++;
                            $Dzielnik = ($DaneDoRaportu[$klient_id][$ID]["suma_klient"] == 0 ? 0 : ($DaneDoRaportu[$klient_id][$ID]["marza"]*100)/$DaneDoRaportu[$klient_id][$ID]["suma_klient"]);
                            $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($Dzielnik, 2, ',', '')  ." %");
                            $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleRightBorder);
                            $column_idx++;
                          }
                          $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                      $row_idx++;
                }
                $column_idx = 1;
                foreach($tablica_key as $ID){
                    $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleLeftBorder);
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaIlosciZlecen[$ID], 0, ",",""));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaObrotow[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaPrzewoznik[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->setCellValue("{$Kolumny[$column_idx]}$row_idx", number_format($SumaMarzy[$ID], 2, ',', ''));
                    $column_idx++;
                    $ActiveSheet->getStyle("{$Kolumny[$column_idx]}$row_idx")->applyFromArray($this->styleRightBorder);
                    $column_idx++;
                }
                $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getFont()->setBold(true);
                $ActiveSheet->getStyle("A$row_idx:{$Kolumny[$column_idx]}$row_idx")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('EEEEEEEE');

                $ActiveSheet->setTitle('Analiza wyników');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="analiza_wynikow.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }else{
                $AirSea = true;
                include(SCIEZKA_SZABLONOW."druki/raporty_analiza_wynikow_nowa.tpl.php");
            }
        }
        
}
?>
