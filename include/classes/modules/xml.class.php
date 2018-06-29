<?php
/**
 * Moduł XML
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */

require_once(SCIEZKA_MODULOW."usefull.class.php");
class XML extends ModulBazowy {
	
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
	}

        function WyswietlAJAX($Action){
            if($Action == "raport_platnosci"){
                $this->GetRaportPlatnosci();
            }
            if($Action == "raport_platnosci_airsea"){
                $this->GetRaportPlatnosciAirSea();
            }
            if($Action == "klienci_bez_zadan"){
                $this->RaportKlienciBezZadan();
            }
            if($Action == "klienci_baza"){
                $this->RaportKlienciBaza();
            }
            if($Action == "zestawienie_dzienne"){
                $this->RaportZestawienieDzienne();
            }

            if($Action == "raport_dopisanych"){
                $this->RaportClient();
            }

            if($Action == "tabela_rozliczen"){
                $this->TabelaRozliczenXLS();
            }

            if($Action == "tabela_rozliczen_morskie"){
                $this->TabelaRozliczenMorskieXLS();
            }

            if($Action == "tabela_rozliczen_lotnicze"){
                $this->TabelaRozliczenLotniczeXLS();
            }

            if($Action == "raport_analiza_wynikow"){
                $Raporty = new TabelaRozliczenRaporty($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $Raporty->RaportAnalizaWynikow(true);
            }

            if($Action == "raport_analiza_wynikow_airsea"){
                $Raporty = new TabelaRozliczenRaporty($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                $Raporty->RaportAnalizaWynikowAirSea(true);
            }
            
            if($Action == "platnosci_morskie"){
                $this->PlatnosciMorskieXLS();
            }

            if($Action == "test_xls"){
                $this->TestXLS();
            }
        }

        function GetRaportPlatnosci(){
            $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $Klienci = UsefullBase::GetKlienci($this->Baza);
            $Oddzialy = $this->Baza->GetOptions("SELECT id_oddzial, CONCAT(skrot,' ',prefix) as name FROM orderplus_oddzial WHERE id_oddzial IN(2,6,3,7,1,8,5)".($this->Uzytkownik->IsAdmin() == false ? " AND id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).")" : "")." ORDER BY field(id_oddzial,2,6,3,7,1,8,5)");
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")
                                         ->setLastModifiedBy("MEPP")
                                         ->setTitle("Raport platnosci");


            $lp = 1;
            $kolor = 'white';
            $totalna_suma_marzy = 0;
            $totalna_suma_klienta = 0;
            $totalna_suma_przewoznika = 0;
            $totalna_suma_zlecen = 0;
            
            if(isset($_POST['start'])){
                $start = $_POST['start'];
              }else{
                $start = "{$_SESSION['okresStart']}-01";
              }
              if(isset($_POST['stop'])){
                  $stop = $_POST['stop'];
              }else{
                  $stop = "{$_SESSION['okresEnd']}-31";
              }
            if(isset($_POST['person-type'])){
                $person_type = $_POST['person-type'];
            }else{
                $person_type = "przewoznik";
            }
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
            if($person_type == "klient"){
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);                
            }            
            $pole_termin = ($person_type == "przewoznik" ? "termin_przewoznika" : "termin_wlasny");
            $pole_planowana = ($person_type == "przewoznik" ? "planowana_zaplata_przew" : "planowana_zaplata_klient");
            $pole_rzeczywista = ($person_type == "przewoznik" ? "rzecz_zaplata_przew" : "rzecz_zaplata_klienta");
            $pole_komentarz = ($person_type == "przewoznik" ? "platnosci_komentarz" : "platnosci_komentarz_klient");
            $pole_status = ($person_type == "przewoznik" ? "platnosci_status" : "platnosci_status_klient");
            $pole_opoznienie = ($person_type == "przewoznik" ? "opoznienie_przewoznik" : "opoznienie_klient");
            $pole_faktura = ($person_type == "przewoznik" ? "faktura_przewoznika" : "faktura_wlasna");
            $pole_stawka = ($person_type == "przewoznik" ? "stawka_przewoznik" : "suma_pozycji");
            $pole_vat = ($person_type == "przewoznik" ? "stawka_vat_przewoznik" : "stawka_vat_klient");
            $pole_kurs = ($person_type == "przewoznik" ? "kurs_przewoznik" : "kurs");
            $pole_pozostalo = ($person_type == "przewoznik" ? "pozostalo_przewoznik" : "wplacono");
            $pole_waluta = ($person_type == "przewoznik" ? "waluta_faktura_przewoznik" : "waluta_klient");
            $pole_id = ($person_type == "przewoznik" ? "id_przewoznik" : "id_klient");
            
            if($person_type == "przewoznik"){
                $Statusy = Usefull::StatusyPlatnosci();
                $StatusyKolejnosc = array(3,4,5,2,1,0);
            }else{
                $Statusy = Usefull::StatusyPlatnosciKlient();
                $StatusyKolejnosc = array(6,7,9,8,3,4,5,2,1,0);
            }
            
            if($_POST['raport-type'] == "termin-platnosci"){
                $filtr_datowy = "$pole_termin >= '$start' AND $pole_termin <= '$stop' AND $pole_rzeczywista = '0000-00-00'";
                $pole_sort = $pole_termin; 
            }else if($_POST['raport-type'] == "rzeczywista"){ 
                $filtr_datowy = "$pole_rzeczywista >= '$start' AND $pole_rzeczywista <= '$stop'";
                $pole_sort = $pole_rzeczywista;
            }else{
                $filtr_datowy = "$pole_planowana >= '$start' AND $pole_planowana <= '$stop' AND $pole_rzeczywista = '0000-00-00'";
                $pole_sort = $pole_planowana;
            }

            if(isset($_POST['oddzial']) && $_POST['oddzial'] > -1){
                $filtr_datowy .= " AND t.id_oddzial = '{$_POST['oddzial']}'";
            }

            if(isset($_POST['status']) && $_POST['status'] > -1){
                $filtr_datowy .= " AND $pole_status = '{$_POST['status']}'";
            }
            if($this->Uzytkownik->IsAdmin() == false){
                $warunek .= "t.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
            }

            $DaneDoRaportu = array();
            $this->Baza->Query("SELECT t.*, DATEDIFF(NOW(), $pole_termin) as opoznienie ".($person_type == "klient" ? ", (SELECT SUM(brutto) FROM faktury_pozycje WHERE id_faktury = f.id_faktury) as suma_pozycji, f.wplacono, f.data_wystawienia, IF(f.id_waluty = 1,'PLN','EUR') as waluta_klient " : "")."
                            FROM orderplus_zlecenie t
                            LEFT JOIN orderplus_klient k ON(k.id_klient = t.id_klient)
                            LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = t.id_przewoznik)
                            ".($person_type == "klient" ? "LEFT JOIN faktury f ON(f.id_faktury = t.id_faktury)" : "")."
                            WHERE $warunek ((t.ost_korekta = 1) OR (t.ost_korekta = 0 AND t.korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND $filtr_datowy ORDER BY ".($person_type == "przewoznik" ? "p.nazwa ASC" : "k.nazwa ASC")." ,$pole_sort ASC");
            $start = 0;
            $Zlecenia = array();
            $OddzialyZlecenia = array();
            while($zleconko = $this->Baza->GetRow()){
                if($person_type == "przewoznik"){
                    $Zlecenia["all"][$zleconko[$pole_status]][] = $zleconko;
                }else{
                    $Zlecenia["all"][$zleconko[$pole_status]][$zleconko['id_faktury']] = $zleconko;
                }
                $OddzialyZlecenia[] = "all";
            }
            $OddzialyZlecenia = array_unique($OddzialyZlecenia);
            $Oddzialy["all"] = "all"; 
            $lp = 1;
            $Sumy = array();
            $SumyPLN = 0;
            $oddzial_id = "all";
            foreach($Oddzialy as $oddzial_id => $oddzial_nazwa){
                if(!in_array($oddzial_id, $OddzialyZlecenia)){
                      continue;
                  }
                  $SumyOddzial = array();
                    $SumyOddzialPLN = 0;
                    //$start++;
                    //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($start), $oddzial_nazwa);
                    $start++;
                    $pierwszy_status = true;
                    foreach($StatusyKolejnosc as $status_id){
                        $SumaStatus = array();
                        $SumaStatusPLN = 0;
                          if(count($Zlecenia[$oddzial_id][$status_id]) == 0){
                              continue;
                          }
                        if($pierwszy_status == false){
                            $start++;
                        }
                        $pierwszy_status = false;
                        $kolor = "FFFFFF";
                        $LastClient = 0;
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($start), $Statusy[$status_id]);
                        $start++;
                        $last_cell = ($person_type == "przewoznik" ? "L" : "M");
                        if($person_type == 'przewoznik'){
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.($start), 'Lp.')
                                            ->setCellValue('B'.($start), 'Numer Zlecenia')
                                            ->setCellValue('C'.($start), 'Klient')
                                            ->setCellValue('D'.($start), 'Przewoznik')
                                            ->setCellValue('E'.($start), 'Nr faktury '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('F'.($start), 'Kwota brutto dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta").' (waluta)')
                                            ->setCellValue('G'.($start), 'Kwota brutto dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta").' (PLN)')
                                            ->setCellValue('H'.($start), 'Termin platnosci '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('I'.($start), 'Planowana zaplata dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('J'.($start), 'Rzeczywista zaplata dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('K'.($start), 'Opoznienie')
                                            ->setCellValue('L'.($start), 'Komentarz');
                            $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':L'.$start)->getFont()->setBold(true);
                        }else{
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.($start), 'Lp.')
                                            ->setCellValue('B'.($start), 'Klient')
                                            ->setCellValue('C'.($start), 'Nr faktury '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('D'.($start), 'Kwota brutto dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta").' (waluta)')
                                            ->setCellValue('E'.($start), 'Kwota brutto dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta").' (PLN)')
                                            ->setCellValue('F'.($start), 'Pozostało EUR')
                                            ->setCellValue('G'.($start), 'Pozostało PLN')
                                            ->setCellValue('H'.($start), 'Data wystawienia faktury')
                                            ->setCellValue('I'.($start), 'Termin platnosci '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('J'.($start), 'Planowana zaplata dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('K'.($start), 'Rzeczywista zaplata dla '.($person_type == "przewoznik" ? "przewoznika" : "klienta"))
                                            ->setCellValue('L'.($start), 'Opoznienie')
                                            ->setCellValue('M'.($start), 'Komentarz');
                            $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':M'.$start)->getFont()->setBold(true);
                        }
                        $start++;
                        foreach($Zlecenia[$oddzial_id][$status_id] as $zleconko){
                            if($zleconko[$pole_id] != $LastClient){
                                $kolor = ($kolor == "FFFFFF" ? "E6F0FF" : "FFFFFF");
                            }
                            $LastClient = $zleconko[$pole_id];
                            if($person_type == "przewoznik"){
                                $StawkaVatPrzewoznik = (in_array(strtolower($zleconko[$pole_vat]), array("np","zw")) ? 0 :  $zleconko[$pole_vat]);
                                $StawkaPrzewoznik = $zleconko[$pole_stawka]*(1+$StawkaVatPrzewoznik/100);
                                $Kurs = ($zleconko[$pole_waluta] != 'PLN' ? $zleconko[$pole_kurs] : 1);
                                if($zleconko[$pole_pozostalo] != ""){
                                    $StawkaPrzewoznik = $zleconko[$pole_pozostalo]/$Kurs;
                                }
                            }else{
                                $StawkaPrzewoznik = $zleconko[$pole_stawka];
                                $Kurs = ($zleconko[$pole_waluta] != 'PLN' ? $zleconko[$pole_kurs] : 1);
                            }
                            $Sumy[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                            $SumyOddzial[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                            $SumaStatus[$zleconko[$pole_waluta]] += $StawkaPrzewoznik;
                            
                            $StawkaPrzewoznikPLN = $StawkaPrzewoznik * $Kurs;
                            $SumyPLN += $StawkaPrzewoznikPLN;
                            $SumyOddzialPLN += $StawkaPrzewoznikPLN;
                            $SumaStatusPLN += $StawkaPrzewoznikPLN;
                            $objPHPExcel->getActiveSheet()->getStyle('A'.($start).':M'.($start))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                                    'startcolor' => array('rgb' => $kolor) 
                                                                                                            ));
                            if($person_type == "przewoznik"){
                                $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.($start), $lp)
                                            ->setCellValue('B'.($start), $zleconko['numer_zlecenia'])
                                            ->setCellValue('C'.($start), $Klienci[$zleconko['id_klient']])
                                            ->setCellValue('D'.($start), $Przewoznicy[$zleconko['id_przewoznik']])
                                            ->setCellValue('E'.($start), $zleconko[$pole_faktura])
                                            ->setCellValue('F'.($start), ($zleconko[$pole_waluta] != "PLN" ? number_format($StawkaPrzewoznik, 2, ',', ' ')  ." {$zleconko[$pole_waluta]}" : ""))
                                            ->setCellValue('G'.($start), number_format($StawkaPrzewoznikPLN, 2, ',', ' ')  ." PLN")
                                            ->setCellValue('H'.($start), $zleconko[$pole_termin])
                                            ->setCellValue('I'.($start), $zleconko[$pole_planowana])
                                            ->setCellValue('J'.($start), $zleconko[$pole_rzeczywista])
                                            ->setCellValue('K'.($start), ($zleconko[$pole_rzeczywista] == "0000-00-00" ? $zleconko['opoznienie']*(-1) : ""))
                                            ->setCellValue('L'.($start), $zleconko[$pole_komentarz]);
                                 if($zleconko['opoznienie'] > 0){
                                    $objPHPExcel->getActiveSheet()->getStyle('K'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => 'FF0000')));
                                 }else if($zleconko['opoznienie'] >= -5){
                                    $objPHPExcel->getActiveSheet()->getStyle('K'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => '009900')));
                                }
                            }else{
                                $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.($start), $lp)
                                            ->setCellValue('B'.($start), $Klienci[$zleconko['id_klient']])
                                            ->setCellValue('C'.($start), $zleconko[$pole_faktura])
                                            ->setCellValue('D'.($start), ($zleconko[$pole_waluta] != "PLN" ? number_format($StawkaPrzewoznik, 2, ',', ' ')  ." {$zleconko[$pole_waluta]}" : ""))
                                            ->setCellValue('E'.($start), number_format($StawkaPrzewoznikPLN, 2, ',', ' ')  ." PLN")
                                            ->setCellValue('F'.($start), ($zleconko[$pole_waluta] != "PLN" && $zleconko[$pole_pozostalo] > 0 ? ($StawkaPrzewoznik - $zleconko[$pole_pozostalo]) : ""))
                                            ->setCellValue('G'.($start), ($zleconko[$pole_waluta] == "PLN" && $zleconko[$pole_pozostalo] > 0 ? ($StawkaPrzewoznik - $zleconko[$pole_pozostalo]) : ""))                                                
                                            ->setCellValue('H'.($start), $zleconko['data_wystawienia'])
                                            ->setCellValue('I'.($start), $zleconko[$pole_termin])
                                            ->setCellValue('J'.($start), $zleconko[$pole_planowana])
                                            ->setCellValue('K'.($start), $zleconko[$pole_rzeczywista])
                                            ->setCellValue('L'.($start), ($zleconko[$pole_rzeczywista] == "0000-00-00" ? $zleconko['opoznienie']*(-1) : ""))
                                            ->setCellValue('M'.($start), $zleconko[$pole_komentarz]);
                                 if($zleconko['opoznienie'] > 0){
                                    $objPHPExcel->getActiveSheet()->getStyle('L'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => 'FF0000')));
                                 }else if($zleconko['opoznienie'] >= -5){
                                    $objPHPExcel->getActiveSheet()->getStyle('L'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => '009900')));
                                }
                            }
                            $lp++;
                            $start++;
                        }
                       $takomorka = $start;
                       $sum_cell_1 = ($person_type == "przewoznik" ? "E" : "C");
                       $sum_cell_2 = ($person_type == "przewoznik" ? "F" : "D");
                       $sum_cell_3 = ($person_type == "przewoznik" ? "G" : "E");
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue("$sum_cell_1".($start), "SUMA - $oddzial_nazwa - {$Statusy[$status_id]}");
                       $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_1".$start)->getFont()->setBold(true);
                       foreach($SumaStatus as $Wal => $Kwota){
                            $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue("$sum_cell_2".($start), number_format($Kwota, 2, ",", " ")." $Wal");
                            $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_2".$start)->getFont()->setBold(true);
                            $start++;
                        }
                        /** dodajemy sume w PLN **/
                        $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue("$sum_cell_3".($takomorka), number_format($SumaStatusPLN, 2, ",", " ")." PLN");
                        $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_3".$takomorka)->getFont()->setBold(true);
                    }
                    $takomorka = $start;
//                   $objPHPExcel->setActiveSheetIndex(0)
//                                            ->setCellValue('E'.($start), "SUMA - $oddzial_nazwa");
//                   $objPHPExcel->getActiveSheet()->getStyle('E'.$start)->getFont()->setBold(true);
//                   foreach($SumyOddzial as $Wal => $Kwota){
//                        $objPHPExcel->setActiveSheetIndex(0)
//                                            ->setCellValue('F'.($start), number_format($Kwota, 2, ",", " ")." $Wal");
//                        $objPHPExcel->getActiveSheet()->getStyle('F'.$start)->getFont()->setBold(true);
//                        $start++;
//                    }
//                    /** dodajemy sume w PLN **/
//                    $objPHPExcel->setActiveSheetIndex(0)
//                                        ->setCellValue('G'.($takomorka), number_format($SumyOddzialPLN, 2, ",", " ")." PLN");
//                    $objPHPExcel->getActiveSheet()->getStyle('G'.$takomorka)->getFont()->setBold(true);
//                    $start++;
                }
               
               $takomorka = $start;
               $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue("$sum_cell_1".($start), "SUMA");
               $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_1".$start)->getFont()->setBold(true);
               foreach($Sumy as $Wal => $Kwota){
                    $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue("$sum_cell_2".($start), number_format($Kwota, 2, ",", " ")." $Wal");
                    $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_2".$start)->getFont()->setBold(true);
                    $start++;
                }
                /** dodajemy sume w PLN **/
                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue("$sum_cell_3".($takomorka), number_format($SumyPLN, 2, ",", " ")." PLN");
                $objPHPExcel->getActiveSheet()->getStyle("$sum_cell_3".$takomorka)->getFont()->setBold(true);
                
                $objPHPExcel->getActiveSheet()->setTitle('Raport platnosci');
                $objPHPExcel->setActiveSheetIndex(0);
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="raport_platnosci_mepp.xls"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    $objWriter->save('php://output');
                    exit;
        }

        function GetRaportPlatnosciAirSea(){
            $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $Klienci = UsefullBase::GetKlienci($this->Baza);
            $Oddzialy = array('sea' => 'OVS', 'air' => 'AIR');
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")
                                         ->setLastModifiedBy("MEPP")
                                         ->setTitle("Raport platnosci ");

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);

            $lp = 1;
            $kolor = 'white';
            $totalna_suma_marzy = 0;
            $totalna_suma_klienta = 0;
            $totalna_suma_przewoznika = 0;
            $totalna_suma_zlecen = 0;
            
            if(isset($_POST['start'])){
                $start = $_POST['start'];
              }else{
                $start = "{$_SESSION['okresStart']}-01";
              }
              if(isset($_POST['stop'])){
                  $stop = $_POST['stop'];
              }else{
                  $stop = "{$_SESSION['okresEnd']}-31";
              }
              if(isset($_POST['person-type'])){ 
                $person_type = $_POST['person-type'];
            }else{
                $person_type = "przewoznik";
            }
    
            $pole_planowana = ($person_type == "przewoznik" ? "planowana_zaplata_przew" : "planowana_zaplata");
            $pole_faktura = ($person_type == "przewoznik" ? "nr_faktury" : "numer");
            $pole_id = ($person_type == "przewoznik" ? "id_przewoznik" : "id_klienta");
            if($person_type == "przewoznik"){
                $Statusy = $Statusy = Usefull::StatusyPlatnosciAirSea();
                $StatusyKolejnosc = array(3,4,5,2,1,0);
            }else{
                $Statusy = Usefull::StatusyPlatnosciKlient();
                $StatusyKolejnosc = array(6,7,9,8,3,4,5,2,1,0);
            }            
            if($_POST['raport-type'] == "termin-platnosci"){
                $filtr_datowy = "(termin_platnosci >= '$start' AND termin_platnosci <= '$stop') AND rzeczywista_zaplata = '0000-00-00'";
                $pole_sort = "termin_platnosci";
            }else if($_POST['raport-type'] == "rzeczywista"){
                $filtr_datowy = "(rzeczywista_zaplata >= '$start' AND rzeczywista_zaplata <= '$stop')";
                $pole_sort = "rzeczywista_zaplata";
            }else{
                $filtr_datowy = "($pole_planowana >= '$start' AND $pole_planowana <= '$stop') AND rzeczywista_zaplata = '0000-00-00'";
                $pole_sort = $pole_planowana;
            }


            if(isset($_POST['status']) && $_POST['status'] > -1){
                $filtr_datowy .= " AND platnosci_status = '{$_POST['status']}'";
            }

            if($this->Uzytkownik->IsAdmin() == false){
                $warunek_morski .= "so.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
                $warunek_lotniczy .= "ao.id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") AND ";
            }
            
            if($person_type == "przewoznik" && isset($_POST['id_przewoznik']) && $_POST['id_przewoznik'] > -1){
                $warunek_morski .= "sok.id_przewoznik = '{$_POST['id_przewoznik']}' AND ";
                $warunek_lotniczy .= "aok.id_przewoznik = '{$_POST['id_przewoznik']}' AND ";
            }

            if($person_type == "klient" && isset($_POST['id_klient']) && $_POST['id_klient'] > -1){
                $warunek_morski .= "sok.id_klienta = '{$_POST['id_klient']}' AND ";
                $warunek_lotniczy .= "aok.id_klienta = '{$_POST['id_klient']}' AND ";
            }

            $DaneDoRaportu = array();
            $OddzialyZlecenia = array('sea', 'air');
            $Zlecenia = array();
            ### Pobieranie zleceń SEA ###
            if(!isset($_POST['oddzial']) || $_POST['oddzial'] == -1 || $_POST['oddzial'] == "sea"){
                 if($person_type == "przewoznik"){
                    $this->Baza->Query("SELECT sok.*, so.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_sea_orders_koszty sok
                                            LEFT JOIN orderplus_sea_orders so ON(so.id_zlecenie = sok.id_zlecenie)
                                            LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = sok.id_przewoznik)
                                            WHERE $warunek_morski ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
                 }else{
                    $this->Baza->Query("SELECT sok.*, so.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_sea_orders_faktury sok
                                        LEFT JOIN orderplus_sea_orders so ON(so.id_zlecenie = sok.id_zlecenia)
                                        LEFT JOIN orderplus_klient p ON(p.id_klient = sok.id_klienta)
                                        WHERE $warunek_morski ((so.ost_korekta = 1) OR (so.ost_korekta = 0 AND so.korekta = 0))
                                                AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
                }
                while($zleconko = $this->Baza->GetRow()){
                    $Zlecenia['sea'][$zleconko['platnosci_status']][] = $zleconko;
                }
            }

            if(!isset($_POST['oddzial']) || $_POST['oddzial'] == -1 || $_POST['oddzial'] == "air"){
                if($person_type == "przewoznik"){
                    $this->Baza->Query("SELECT aok.*, ao.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_air_orders_koszty aok
                                            LEFT JOIN orderplus_air_orders ao ON(ao.id_zlecenie = aok.id_zlecenie)
                                            LEFT JOIN orderplus_przewoznik p ON(p.id_przewoznik = aok.id_przewoznik)
                                            WHERE $warunek_lotniczy ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))
                                            AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC");
                }else{
                    $this->Baza->Query("SELECT aok.*, ao.numer_zlecenia, DATEDIFF(NOW(), termin_platnosci) as opoznienie FROM orderplus_air_orders_faktury  aok
                                        LEFT JOIN orderplus_air_orders ao ON(ao.id_zlecenie = aok.id_zlecenia)
                                        LEFT JOIN orderplus_klient p ON(p.id_klient = aok.id_klienta)
                                        WHERE $warunek_lotniczy ((ao.ost_korekta = 1) OR (ao.ost_korekta = 0 AND ao.korekta = 0))
                                        AND $filtr_datowy ORDER BY p.nazwa ASC, $pole_sort ASC"); 
                }
                while($zleconko = $this->Baza->GetRow()){
                    $Zlecenia['air'][$zleconko['platnosci_status']][] = $zleconko;
                }
            }
            
            $start = 0;
            $lp = 1;
            $Sumy = array();
            foreach($Oddzialy as $oddzial_id => $oddzial_nazwa){
                if(!in_array($oddzial_id, $OddzialyZlecenia)){
                      continue;
                  }
                  $SumyOddzial = array();
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($start), $oddzial_nazwa);
                    $start++;
                    $pierwszy_status = true;
                    foreach($StatusyKolejnosc as $status_id){
                        $SumaStatus = array();
                          if(count($Zlecenia[$oddzial_id][$status_id]) == 0){
                              continue;
                          }
                        if($pierwszy_status == false){
                            $start++;
                        }
                        $pierwszy_status = false;
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.($start), $Statusy[$status_id]);
                        $start++;
                        $kolor = "FFFFFF";
                        $LastClient = 0;
                        $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.($start), 'Lp.')
                                        ->setCellValue('B'.($start), 'Numer Zlecenia')
                                        ->setCellValue('C'.($start), 'Nr faktury '.($person_type == "przewoznik" ? "dostawcy" : "klienta"))
                                        ->setCellValue('D'.($start), ($person_type == "przewoznik" ? "Dostawca" : "Klient"))
                                        ->setCellValue('E'.($start), 'PLN')
                                        ->setCellValue('F'.($start), 'USD')
                                        ->setCellValue('G'.($start), 'EUR')
                                        ->setCellValue('H'.($start), 'Termin platnosci')
                                        ->setCellValue('I'.($start), 'Termin platnosci planowany')
                                        ->setCellValue('J'.($start), 'Termin platnosci rzeczywisty')
                                        ->setCellValue('K'.($start), 'Opóźnienie')
                                        ->setCellValue('L'.($start), 'Komentarz');
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':L'.$start)->getFont()->setBold(true);
                        $start++;
                        foreach($Zlecenia[$oddzial_id][$status_id] as $zleconko){
                            if($zleconko[$pole_id] != $LastClient){
                                $kolor = ($kolor == "FFFFFF" ? "E6F0FF" : "FFFFFF");
                            }
                            $LastClient = $zleconko[$pole_id];
                            if($person_type == "przewoznik"){
                                $StawkaVatPrzewoznik_1 = (in_array(strtolower($zleconko['stawka_vat']), array("np","zw")) ? 0 :  $zleconko['stawka_vat']);
                                $StawkaVatPrzewoznik_2 = (in_array(strtolower($zleconko['stawka_vat_2']), array("np","zw")) ? 0 :  $zleconko['stawka_vat_2']);
                                $StawkaPrzewoznik_1 = $zleconko['koszt_kwota_1'] + ($zleconko['koszt_kwota_1'] * $StawkaVatPrzewoznik_1/100);
                                $StawkaPrzewoznik_2 = $zleconko['koszt_kwota_2'] + ($zleconko['koszt_kwota_2'] * $StawkaVatPrzewoznik_2/100);
                                $StawkaPrzewoznik = $StawkaPrzewoznik_1 + $StawkaPrzewoznik_2;
                                $Sumy[$zleconko['waluta']] += $StawkaPrzewoznik;
                                $SumyOddzial[$zleconko['waluta']] += $StawkaPrzewoznik;
                                $SumaStatus[$zleconko['waluta']] += $StawkaPrzewoznik;
                            }else{
                                $table_pozycje = ($oddzial_id == "sea" ? "orderplus_sea_orders_faktury_pozycje" : "orderplus_air_orders_faktury_pozycje");
                                $StawkaPrzewoznik = $this->Baza->GetValue("SELECT SUM(brutto) FROM $table_pozycje WHERE id_faktury = '{$zleconko['id_faktury']}'");
                                #echo "SELECT SUM(brutto) FROM $table_pozycje WHERE id_faktury = '{$zleconko['id_faktury']}'";
                                if($StawkaPrzewoznik == false){
                                    $StawkaPrzewoznik = 0;
                                }
                                $Sumy[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                                $SumyOddzial[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                                $SumaStatus[$zleconko['id_waluty']] += $StawkaPrzewoznik;
                            }
                            $objPHPExcel->getActiveSheet()->getStyle('A'.($start).':L'.($start))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                                    'startcolor' => array('rgb' => $kolor) 
                                                                                                            ));
                            $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.($start), $lp)
                                        ->setCellValue('B'.($start), $zleconko['numer_zlecenia'])
                                        ->setCellValue('C'.($start), $zleconko[$pole_faktura])
                                        ->setCellValue('D'.($start), ($person_type == "przewoznik" ? $Przewoznicy[$zleconko['id_przewoznik']] : $Klienci[$zleconko['id_klienta']]))
                                        ->setCellValue('E'.($start), ($zleconko['id_waluty'] == 1 || $zleconko['waluta'] == 1 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : 0.00))
                                        ->setCellValue('F'.($start), ($zleconko['id_waluty'] == 2 || $zleconko['waluta'] == 2 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : 0.00))
                                        ->setCellValue('G'.($start), ($zleconko['id_waluty'] == 3 || $zleconko['waluta'] == 3 ? number_format($StawkaPrzewoznik, 2, ',', ' ') : 0.00))
                                        ->setCellValue('H'.($start), $zleconko['termin_platnosci'])
                                        ->setCellValue('I'.($start), $zleconko[$pole_planowana])
                                        ->setCellValue('J'.($start), $zleconko['rzeczywista_zaplata'])
                                        ->setCellValue('K'.($start), ($zleconko['rzeczywista_zaplata'] == "0000-00-00" ? $zleconko['opoznienie']*(-1) : ""))
                                        ->setCellValue('L'.($start), $zleconko[$pole_komentarz]);
                                        if($zleconko['opoznienie'] > 0){
                                           $objPHPExcel->getActiveSheet()->getStyle('K'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => 'FF0000')));
                                        }else if($zleconko['opoznienie'] >= -5){
                                           $objPHPExcel->getActiveSheet()->getStyle('K'.($start))->getFont()->applyFromArray(array('color' => array('rgb' => '009900')));
                                       }
                            $lp++;
                            $start++;
                        }
                       $takomorka = $start;
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('D'.($start), "SUMA - $oddzial_nazwa - {$Statusy[$status_id]}");
                       $objPHPExcel->getActiveSheet()->getStyle('D'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('E'.($start), number_format($SumaStatus[1], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('E'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('F'.($start), number_format($SumaStatus[2], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('F'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('G'.($start), number_format($SumaStatus[3], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('G'.$start)->getFont()->setBold(true);
                       $start++;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('D'.($start), "SUMA - $oddzial_nazwa");
                   $objPHPExcel->getActiveSheet()->getStyle('D'.$start)->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('E'.($start), number_format($SumyOddzial[1], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('E'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('F'.($start), number_format($SumyOddzial[2], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('F'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('G'.($start), number_format($SumyOddzial[3], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('G'.$start)->getFont()->setBold(true);
                       $start++;
                }

               $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('D'.($start), "SUMA");
               $objPHPExcel->getActiveSheet()->getStyle('D'.$start)->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('E'.($start), number_format($Sumy[1], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('E'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('F'.($start), number_format($Sumy[2], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('F'.$start)->getFont()->setBold(true);
                       $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('G'.($start), number_format($Sumy[3], 2, ",", " ")." $Wal");
                       $objPHPExcel->getActiveSheet()->getStyle('G'.$start)->getFont()->setBold(true);
                       $start++;
                $objPHPExcel->getActiveSheet()->setTitle('Raport platnosci dla przew');
                $objPHPExcel->setActiveSheetIndex(0);
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="raport_platnosci_mepp_air.xls"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    $objWriter->save('php://output');
                    exit;
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

        function RaportKlienciBezZadan(){
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")
                                                                     ->setLastModifiedBy("MEPP")
                                                                     ->setTitle("Baza klientów bez zadań MEPP");
            $ActiveSheet = $objPHPExcel->getActiveSheet();
            $ActiveSheet->getStyle('A1:H1')->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
            $ActiveSheet->getStyle('A1:H1')
                    ->getAlignment()->setWrapText(true);
            $ActiveSheet->getStyle('A1:H1')->getFont()->setSize(12);

            $ActiveSheet->getStyle('A1:H1')->getFont()->setBold(true);
            $ActiveSheet->getStyle('A1:H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $ActiveSheet->getColumnDimension('A:H')->setWidth(30);
            $ActiveSheet->getColumnDimension('B')->setWidth(15);
            $ActiveSheet->getColumnDimension('F')->setWidth(15);
            $ActiveSheet->getColumnDimension('G')->setWidth(15);
            $ActiveSheet->getColumnDimension('H')->setWidth(20);

            $ActiveSheet->getRowDimension('1')->setRowHeight(30);

            $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A1', 'Nazwa')
                                    ->setCellValue('B1', 'kraj')
                                    ->setCellValue('C1', 'adres')
                                    ->setCellValue('D1', 'telefon')
                                    ->setCellValue('E1', 'e-mail')
                                    ->setCellValue('F1', 'branża')
                                    ->setCellValue('G1', 'opiekun')
                                    ->setCellValue('H1', 'Oddział');

            /*dane na temat klienta*/
            $_next_event = $this->Baza->GetValues("SELECT k.id_klient FROM zdarzenia z
                                                    LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                                    LEFT JOIN orderplus_klient k ON(pz.id_klient = k.id_klient)
                                                    WHERE z.data_zakonczenia IS NULL
                                                    GROUP BY k.id_klient ORDER BY k.id_klient"); 
            $_not_in='';
            foreach($_next_event as $_ab_temp)
            {
                if(!is_null($_ab_temp))
                {
                    $_not_in.=($_not_in!='' ? ',' : '').'\''.$_ab_temp.'\'';
                }
            }
            $_customers = $this->Baza->GetRows("SELECT k.id_klient, k.nazwa, kk.kod, kk.nazwa_kraju, k.adres, k.kod_pocztowy, k.miejscowosc, k.emaile, k.telefon, k.nip, b.branza, u.login, od.nazwa as od_nazwa
                                                    FROM orderplus_klient k
                                                    LEFT JOIN kod_kraju kk ON(kk.id = k.kod_kraju_id)
                                                    LEFT JOIN branza b ON(k.branza_crm_id = b.id)
                                                    LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = k.id_uzytkownik)
                                                    LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = k.id_oddzial)
                                                    WHERE id_klient NOT IN($_not_in) AND k.usuniety = 'nie'
                                                    ".(!$this->Uzytkownik->IsAdmin() ? " AND k.id_oddzial = '{$_SESSION['id_oddzial']}'" : "")."
                                                    ".($_SESSION['uprawnienia_id'] == 3 ? " AND k.id_uzytkownik = '{$_SESSION['id_uzytkownik']}'" : "")."
                                                        ORDER BY od_nazwa, u.login, k.nazwa");

            $_start_row=3;

            for($i=0;$i<count($_customers);$i++)
            {
                    $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.($_start_row+$i), $_customers[$i]['nazwa'])
                                    ->setCellValue('B'.($_start_row+$i), $_customers[$i]['nazwa_kraju'].' ['.$_customers[$i]['kod'].']')
                                    ->setCellValue('C'.($_start_row+$i), $_customers[$i]['adres'].', '.$_customers[$i]['kod_pocztowy'].' '.$_customers[$i]['miasto'])
                                    ->setCellValue('D'.($_start_row+$i), 'tel: '.$_customers[$i]['telefon'])
                                    ->setCellValue('E'.($_start_row+$i), $_customers[$i]['emaile'])
                                    ->setCellValue('F'.($_start_row+$i), $_customers[$i]['branza'])
                                    ->setCellValue('G'.($_start_row+$i), $_customers[$i]['login'])
                                    ->setCellValue('H'.($_start_row+$i), $_customers[$i]['od_nazwa']);

                    $ActiveSheet->getStyle('A'.($_start_row+$i).':J'.($_start_row+$i))
                            ->getAlignment()->setWrapText(true);
            }


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $ActiveSheet->setTitle('Baza klientów bez zadań MEPP');
            $objPHPExcel->setActiveSheetIndex(0);



            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="baza_klientow_bez_zadan_mepp.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        function GenerujWarunkiDzienny($Mode = "base") {
		$Where = "";
                if(isset($_POST) && count($_POST)){
                    foreach($_POST as $Pole => $Wartosc){
                        if($Wartosc != ""){
                            if($Pole == "id_uzytkownik"){
                                if($Mode == "client"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."k.dodal_uzytkownik = '$Wartosc'"; 
                                }else{
                                    $Where .= ($Where != '' ? ' AND ' : '')."u.$Pole = '$Wartosc'";
                                }
                            }
                            if($Pole == "id_oddzial"){
                                $Where .= ($Where != '' ? ' AND ' : '')."od.$Pole = '$Wartosc'";
                            }
                            if($Mode == "base"){
                                if($Pole == "data_zdarzenia_od"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."((z.data_poczatek >= '$Wartosc 00:00:00' AND z.data_przypomnienia is null  and z.data_zakonczenia is null) OR (z.data_przypomnienia >= '$Wartosc 00:00:00' and z.data_zakonczenia is null) OR (z.data_zakonczenia >= '$Wartosc 00:00:00'))";
                                }else if($Pole == "data_zdarzenia_do"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."((z.data_poczatek <= '$Wartosc 23:59:59' AND z.data_przypomnienia is null  and z.data_zakonczenia is null) OR (z.data_przypomnienia <= '$Wartosc 23:59:59' and z.data_zakonczenia is null) OR (z.data_zakonczenia <= '$Wartosc 23:59:59'))";
                                }else if($Pole == "data_zdarzenia"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."((z.data_poczatek >= '$Wartosc 00:00:00' AND z.data_poczatek <= '$Wartosc 23:59:59' AND z.data_przypomnienia is null  and z.data_zakonczenia is null) OR (z.data_przypomnienia >= '$Wartosc 00:00:00' AND z.data_przypomnienia <= '$Wartosc 23:59:59' and z.data_zakonczenia is null) OR (z.data_zakonczenia >= '$Wartosc 00:00:00' AND z.data_zakonczenia <= '$Wartosc 23:59:59'))";
                                }
                            }else if($Mode == "client"){
                                if($Pole == "data_zdarzenia_od"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."k.data_utworzenia >= '$Wartosc 00:00:00'";
                                }else if($Pole == "data_zdarzenia_do"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."k.data_utworzenia <= '$Wartosc 23:59:59'";
                                }else if($Pole == "data_zdarzenia"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."k.data_utworzenia >= '$Wartosc 00:00:00' AND k.data_utworzenia <= '$Wartosc 23:59:59'";
                                }
                            }else{
                                if($Pole == "data_zdarzenia_od"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."(data_wprowadzenia >= '$Wartosc 00:00:00')";
                                }else if($Pole == "data_zdarzenia_do"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."(data_wprowadzenia <= '$Wartosc 23:59:59')";
                                }else if($Pole == "data_zdarzenia"){
                                    $Where .= ($Where != '' ? ' AND ' : '')."(data_wprowadzenia LIKE '$Wartosc%')";
                                }
                            }
                        }
                    }
                }
		
		return ($Where != '' ? "WHERE $Where" : '');
        }

        function RaportZestawienieDzienne(){
            /*nazwy statystyk*/
            $statystyki = UsefullBase::GetStatystyki($this->Baza);
            /*tworzenie pliku excel*/
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")
								 ->setLastModifiedBy("MEPP")
								 ->setTitle("Zestawienie dzienne MEPP");

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);

            $Where = $this->GenerujWarunkiDzienny();
            $base = $this->Baza->GetRows("SELECT z.*, u.id_uzytkownik, u.login, k.id_klient, k.nazwa as kl_nazwa, od.nazwa as od_nazwa
                                            FROM zdarzenia z
                                            LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                            LEFT JOIN orderplus_klient k ON(pz.id_klient = k.id_klient)
                                            LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = pz.id_uzytkownik)
                                            LEFT JOIN orderplus_oddzial od ON(k.id_oddzial = od.id_oddzial)
                                            $Where
                                            ORDER BY od_nazwa, u.login, z.data_zakonczenia, kl_nazwa
                                            ");
            if(!$base){
                $base = array();
            }
            foreach($base as $i => $base_dane){
                $base[$i]['statystyka'] = $statystyki[$base_dane['Statystyka_id']];
            }
            $Where = $this->GenerujWarunkiDzienny('orderplus');
            $orderplus = $this->Baza->GetRows("SELECT od.nazwa as od_nazwa, u.login, oz.numer_zlecenia as temat, k.nazwa as kl_nazwa, oz.data_zlecenia as data_zakonczenia,
                                                        stawka_klient, stawka_przewoznik, kurs, waluta
                                                        FROM orderplus_zlecenie oz
                                                        LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = oz.id_uzytkownik)
                                                        LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = oz.id_oddzial)
                                                        LEFT JOIN orderplus_klient k ON(k.id_klient = oz.id_klient) $Where");
            for($i =0 ; $i<count($orderplus) ; $i++)
            {
                $orderplus[$i]['Statystyka_id'] = 11;
                $orderplus[$i]['statystyka'] = $statystyki[11];
                /*
                 * Dodanie marzy od zleceń zrealizowanych
                 */
                if ($orderplus[$i]['waluta'] == "PLN") {
                    $Kurs = 1;
                    $KursPrz = 1;
                }else{
                    $Kurs = $orderplus[$i]['kurs'];
                    $KursPrz = $orderplus[$i]['kurs_przewoznik'];
                }
                $RealMarza = $orderplus[$i]['stawka_klient'] - $orderplus[$i]['stawka_przewoznik'];
                $MarzaPLN = round(($orderplus[$i]['stawka_klient']*$Kurs) - ($orderplus[$i]['stawka_przewoznik']*$KursPrz),2);
                $orderplus[$i]['marza'] = $RealMarza;
                $orderplus[$i]['marza_pln'] = $MarzaPLN;
                $orderplus[$i]['komentarz'] = number_format($RealMarza, 2, ",", " ")." ".$orderplus[$i]['waluta'];
            }
            if(!$orderplus){
                $orderplus = array();
            }
            $_result = array_merge($base,$orderplus);

        $orderplus_morskie = $this->Baza->GetRows("SELECT oz.id_zlecenie, od.nazwa as od_nazwa, u.login, oz.numer_zlecenia as temat, oz.data_zlecenia as data_zakonczenia
                                                        FROM orderplus_sea_orders oz
                                                        LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = oz.id_uzytkownik)
                                                        LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = oz.id_oddzial) $Where
                                                        ");
		;
        foreach($orderplus_morskie as $i => $ord_dane)
        {
            $orderplus_morskie[$i]['Statystyka_id'] = 11;
            $orderplus_morskie[$i]['statystyka'] = $statystyki[11];
            $orderplus_morskie[$i]['waluta'] = "PLN";
            $orderplus_morskie[$i]['kl_nazwa'] = "";
            /*
             * Wyliczenie marży
             */
            $Faktury = $this->Baza->GetResultAsArray("SELECT id_faktury, id_waluty, kurs, numer FROM orderplus_sea_orders_faktury WHERE id_zlecenia = '{$ord_dane['id_zlecenie']}'", "id_faktury");
            $PosMany = 0;
            foreach($Faktury as $DaneFak){
                $Pozycje = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury ='{$DaneFak['id_faktury']}'");
                foreach($Pozycje as $Pos){
                    if($DaneFak['id_waluty'] == 1){
                        $PosMany += $Pos['netto'];
                    }else{
                        $PosMany += $Pos['netto'] * $DaneFak['kurs'];
                    }
                }
                $Koszty = $this->Baza->GetRows("SELECT * FROM orderplus_sea_orders_koszty WHERE id_zlecenie = '{$ord_dane['id_zlecenie']}'");
                $Kwota = 0;
                foreach($Koszty as $KosztyRes){
                     $Kwota += ($KosztyRes['waluta'] > 1 ? $KosztyRes['koszt'] * $KosztyRes['kurs'] : $KosztyRes['koszt']);
                }
                $RealMarza = $PosMany - $Kwota;
                $MarzaPLN = $RealMarza;
                $orderplus_morskie[$i]['marza'] = round($RealMarza,2);
                $orderplus_morskie[$i]['marza_pln'] = round($MarzaPLN,2);
                $orderplus_morskie[$i]['komentarz'] = number_format($RealMarza, 2, ",", " ")." PLN";
            }
        }
        if(!$orderplus_morskie){
            $orderplus_morskie = array();
        }
        $_result = array_merge($_result, $orderplus_morskie);
       

	$_stats=array('end'=>0);
        $_stats_od=array();
        $_stats_user=array();

	for($i=0;$i<count($_result);$i++)
	{
		/*obliczenia statystyk*/
			// statystyki dla uzytkownik�w i oddzia��w
                        if(isset($_stats_user[$_result[$i]['login']])===false)
                        {
                            $_stats_user[$_result[$i]['login']]['wykonane_kontakty'] = 0;
                            $_stats_user[$_result[$i]['login']]['skuteczne_kontakty']= 0;
                            $_stats_user[$_result[$i]['login']]['zapytanie_niezrealizowane']= 0;
                            $_stats_user[$_result[$i]['login']]['zapytanie_otrzymane']= 0;
                            $_stats_user[$_result[$i]['login']]['marza']['PLN']= 0;
                            $_stats_user[$_result[$i]['login']]['marza']['EUR']= 0;
                            $_stats_user[$_result[$i]['login']]['marza']['USD']= 0;
                            $_stats_user[$_result[$i]['login']]['end']= 1;
                        }
                        else
                        {
                            $_stats_user[$_result[$i]['login']]['end']++;
                        }

                        if(isset($_stats_od[$_result[$i]['od_nazwa']])===false)
                        {
                            $_stats_od[$_result[$i]['od_nazwa']]['wykonane_kontakty'] = 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['skuteczne_kontakty']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['zapytanie_niezrealizowane']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['zapytanie_otrzymane']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['marza']['PLN']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['marza']['EUR']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['marza']['USD']= 0;
                            $_stats_od[$_result[$i]['od_nazwa']]['end']= 1;
                        }
                        else
                        {
                            $_stats_od[$_result[$i]['od_nazwa']]['end']++;
                        }
		if($_result[$i]['Statystyka_id']!==null )
		{

                        // statystyki og�lne
			if(isset($_stats[$_result[$i]['Statystyka_id']])===false)
			{
				$_stats[$_result[$i]['Statystyka_id']]=1;
			}
			else
			{
				$_stats[$_result[$i]['Statystyka_id']]++;
			}


                        // Wykonane kontakty
                        if($_result[$i]['Statystyka_id'] != 11 && $_result[$i]['Statystyka_id'] != 9 && $_result[$i]['Statystyka_id'] != 5)
                        {
                            $_stats_user[$_result[$i]['login']]['wykonane_kontakty']++;
                            $_stats_od[$_result[$i]['od_nazwa']]['wykonane_kontakty']++;
                        }
                        // Skuteczne kontakty
                        if($_result[$i]['Statystyka_id'] == 3 || $_result[$i]['Statystyka_id'] == 6 || $_result[$i]['Statystyka_id'] == 2 || $_result[$i]['Statystyka_id'] == 10 || $_result[$i]['Statystyka_id'] == 7)
                        {

                            $_stats_user[$_result[$i]['login']]['skuteczne_kontakty']++;
                            $_stats_od[$_result[$i]['od_nazwa']]['skuteczne_kontakty']++;
                        }
                        // Zapytanie niezrealizowane
                        if($_result[$i]['Statystyka_id'] == 4)
                        {
                            $_stats_user[$_result[$i]['login']]['zapytanie_niezrealizowane']++;
                            $_stats_od[$_result[$i]['od_nazwa']]['zapytanie_niezrealizowane']++;
                        }
                        // Zapytanie otrzymane
                        if($_result[$i]['Statystyka_id'] == 11)
                        {
                            $_stats_user[$_result[$i]['login']]['zapytanie_otrzymane']++;
                            $_stats_od[$_result[$i]['od_nazwa']]['zapytanie_otrzymane']++;
                        }
                        // Marza PLN
                        if(isset($_result[$i]['marza'])){
                            if(!isset($_stats_user[$_result[$i]['login']]['marza'][$_result[$i]['waluta']])){
                                $_stats_user[$_result[$i]['login']]['marza'][$_result[$i]['waluta']] = 0;
                            }
                            if(!isset($_stats_od[$_result[$i]['od_nazwa']]['marza'][$_result[$i]['waluta']])){
                                $_stats_od[$_result[$i]['od_nazwa']]['marza'][$_result[$i]['waluta']] = 0;
                            }
                            $_stats_user[$_result[$i]['login']]['marza'][$_result[$i]['waluta']] += round($_result[$i]['marza'],2);
                            $_stats_od[$_result[$i]['od_nazwa']]['marza'][$_result[$i]['waluta']] += round($_result[$i]['marza'],2);
                        }
                        $_stats['end']++;
                        }
                    }

                    /*wyświetlanie statystyk*/
                    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
                    $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
                    $objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A1', 'Statystyki');

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A2', 'zakończone')
                                            ->setCellValue('B2', ($_stats['end']==0 ? '0' :  number_format((($_stats['end']/count($_result))*100), 2, '.', '')).'% ['.$_stats['end'].'/'.count($_result).']')
                                            ->setCellValue('A3', 'w tym:');
                    $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

                    $start=4;
                    $wykonane_kontakty = 0;
                    $skuteczne_kontakty = 0;
                    $zapytanie_niezrealizowane = 0;
                    $zapytanie_otrzymane = 0;
                    //print_r($_stats);
                    foreach($_stats as $id => $name)
                    {
                            if($id=='end') continue;
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, $statystyki[$id])
                                            ->setCellValue('B'.$start, ($_stats['end']==0 ? '0' : number_format((($name/$_stats['end'])*100), 2, '.', '')).'% ['.$name.'/'.$_stats['end'].']');
                            $start++;
                            if($id != 11 &&  $id != 9 && $id != 5)
                            {
                                $wykonane_kontakty += $name;
                            }
                            if($id == 3 || $id == 6 || $id == 2 || $id == 10 || $id == 7)
                            {
                                $skuteczne_kontakty +=$name;
                            }
                            if($id == 4)
                            {
                                $zapytanie_niezrealizowane +=$name;
                            }
                            if($id == 11)
                            {
                                $zapytanie_otrzymane +=$name;
                            }
                    }
                  /////////////////////////////////////////////////////////////////////////////////////////
                   /*  Marcin Starzyk : 02.02.11
                    *
                    *  Dodanie nowych statystyk :
                    *  1)
                    *  2)
                    *  3)
                    */
                    //print_r($_stats_user);print_r($_stats_od);
                    //die();
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Statystyki Dodatkowe');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    $start++;

                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Wskaźnik efektywności operacyjnej')
                                            ->setCellValue('B'.$start, (($zapytanie_niezrealizowane+$zapytanie_otrzymane)==0 ? '0' : number_format(($zapytanie_otrzymane/($zapytanie_niezrealizowane+$zapytanie_otrzymane)*100), 2, '.', '')).'% ['.$zapytanie_otrzymane.'/'.($zapytanie_niezrealizowane+$zapytanie_otrzymane).']');


                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Ilość wykonanych kontaktów ')
                                            ->setCellValue('B'.$start, ((count($_result)-$zapytanie_otrzymane)==0 ? '0' : number_format((($wykonane_kontakty/(count($_result)-$zapytanie_otrzymane))*100), 2, '.', '')).'% ['.$wykonane_kontakty.'/'.(count($_result)-$zapytanie_otrzymane).']');


                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Ilość skutecznych kontaktów')
                                            ->setCellValue('B'.$start, ($wykonane_kontakty==0 ? '0' : number_format((($skuteczne_kontakty/$wykonane_kontakty)*100), 2, '.', '')).'% ['.$skuteczne_kontakty.'/'.$wykonane_kontakty.']');

                    // Wskaznik efektywnosci operacyjnej
                    $start= $start + 2;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Wskaźnik efektywności operacyjnej');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);

                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Oddziały:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_od as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, (($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane'])==0 ? '0' : number_format((($value['zapytanie_otrzymane']/($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane']))*100), 2, '.', '')).'% ['.$value['zapytanie_otrzymane'].'/'.($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane']).']');
                    }
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Użytkownicy:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_user as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, (($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane'])==0 ? '0' : number_format((($value['zapytanie_otrzymane']/($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane']))*100), 2, '.', '')).'% ['.$value['zapytanie_otrzymane'].'/'.($value['zapytanie_otrzymane']+$value['zapytanie_niezrealizowane']).']');
                    }

                    // ilosc wykonanych kontakt�w
                    $start+=2;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Ilość wykonanych kontaktów');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Oddziały:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_od as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, (($value['end']-$value['zapytanie_otrzymane'])==0 ? '0' : number_format((($value['wykonane_kontakty']/($value['end']-$value['zapytanie_otrzymane']))*100), 2, '.', '')).'% ['.$value['wykonane_kontakty'].'/'.($value['end']-$value['zapytanie_otrzymane']).']');
                    }
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Użytkownicy:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_user as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, (($value['end']-$value['zapytanie_otrzymane'])==0 ? '0' : number_format((($value['wykonane_kontakty']/($value['end']-$value['zapytanie_otrzymane']))*100), 2, '.', '')).'% ['.$value['wykonane_kontakty'].'/'.($value['end']-$value['zapytanie_otrzymane']).']');
                    }

                    // ilosc skutecznych kontakt�w
                    $start+=2;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Ilość skutecznych kontaktów');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Oddziały:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_od as  $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, ($value['wykonane_kontakty']==0 ? '0' : number_format((($value['skuteczne_kontakty']/$value['wykonane_kontakty'])*100), 2, '.', '')).'% ['.$value['skuteczne_kontakty'].'/'.$value['wykonane_kontakty'].']');
                    }
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Użytkownicy:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_user as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, ($value['wykonane_kontakty']==0 ? '0' : number_format((($value['skuteczne_kontakty']/$value['wykonane_kontakty'])*100), 2, '.', '')).'% ['.$value['skuteczne_kontakty'].'/'.$value['wykonane_kontakty'].']');
                    }

                    // Wysokość marzy
                    $start+=2;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Wielkość marży');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Oddziały:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_od as  $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, round($value['marza']['PLN'],2)." PLN")
                                                ->setCellValue('C'.$start, round($value['marza']['EUR'],2)." EUR")
                                                ->setCellValue('D'.$start, round($value['marza']['USD'],2)." USD");
                    }
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$start, 'Użytkownicy:');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    foreach($_stats_user as $key =>$value)
                    {
                        $start++;
                        $objPHPExcel->setActiveSheetIndex(0)
                                                ->setCellValue('A'.$start, $key)
                                                ->setCellValue('B'.$start, round($value['marza']['PLN'],2)." PLN")
                                                ->setCellValue('C'.$start, round($value['marza']['EUR'],2)." EUR")
                                                ->setCellValue('D'.$start, round($value['marza']['USD'],2)." USD");
                    }




                    /*echo 'Wykonane : '.$wykonane_kontakty.'<br>';
                     echo 'Skuteczne : '.$skuteczne_kontakty.'<br>';
                     echo 'Niezrealizowane : '.$zapytanie_niezrealizowane.'<br>';
                     echo 'Otrzymane : '.$zapytanie_otrzymane.'<br>';
                     echo Kohana::debug($_stats);
                     exit();*/

                    // Nag��wki statystyk
                    $_start_row=++$start;
                    $objPHPExcel->getActiveSheet()->getRowDimension($_start_row)->setRowHeight(30);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('EEEEEEEE');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)
                            ->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFont()->setSize(12);

                    $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.$_start_row, 'Temat')
                                            ->setCellValue('B'.$_start_row, 'Data zdarzenia')
                                            ->setCellValue('C'.$_start_row, 'Klient')
                                            ->setCellValue('D'.$_start_row, 'Opiekun')
                                            ->setCellValue('E'.$_start_row, 'Oddział')
                                            ->setCellValue('F'.$_start_row, 'Status')
                                            ->setCellValue('G'.$_start_row, 'Komentarz');
                    $_start_row++;
                    for($i=0;$i<count($_result);$i++)
                    {

                            // Wy�wietlanie statystyk
                            $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue('A'.($_start_row+$i), $_result[$i]['temat'])
                                            ->setCellValue('B'.($_start_row+$i), $_result[$i]['data_zakonczenia'])
                                            ->setCellValue('C'.($_start_row+$i), $_result[$i]['kl_nazwa'])
                                            ->setCellValue('D'.($_start_row+$i), $_result[$i]['login'])
                                            ->setCellValue('E'.($_start_row+$i), $_result[$i]['od_nazwa'])
                                            ->setCellValue('F'.($_start_row+$i), ($_result[$i]['Statystyka_id']===null ? 'NIE WYKONANE' : $_result[$i]['statystyka']))
                                            ->setCellValue('G'.($_start_row+$i), (isset($_result[$i]['komentarz'])===false ? '' : $_result[$i]['komentarz']));

                            $objPHPExcel->getActiveSheet()->getStyle('A'.($_start_row+$i).':F'.($_start_row+$i))
                                    ->getAlignment()->setWrapText(true);
                    }






                    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                    $objPHPExcel->getActiveSheet()->setTitle('Zestawienie dzienne MEPP');
                    $objPHPExcel->setActiveSheetIndex(0);



                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="zestawienie_dzienne.xls"');
                    header('Cache-Control: max-age=0');

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    $objWriter->save('php://output');
                    exit;
        }

        function RaportClient(){
            	/*tworzenie pliku excel*/
                $Users = UsefullBase::GetUsers($this->Baza);
                include(SCIEZKA_INCLUDE."PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("MEPP")
                                             ->setLastModifiedBy("MEPP")
                                             ->setTitle("Raport Dopisanych klientow MEPP");

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);



              // echo $_query['base'];die();
                $Where = $this->GenerujWarunkiDzienny('client');
                $_result =  $this->Baza->GetRows("SELECT k.* FROM orderplus_klient k
                                                        LEFT JOIN orderplus_uzytkownik ou ON(ou.id_uzytkownik = k.dodal_uzytkownik)
                                                        LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = ou.id_oddzial)
                                                        $Where ORDER BY k.data_utworzenia, k.nazwa");

                $temp_data = array();

                // Przepisanie danych do tabeli tymczasowej gdzie beda wyswietlane dane dla poszczegolnych uzytkownikow
                for($i=0;$i<count($_result);$i++)
                {
                        if(isset($temp_data[$Users[$result[$i]['dodal_uzytkownik']]]) === false )
                        {
                            $_id = $temp_data[$Users[$result[$i]['dodal_uzytkownik']]]['end'] = 0;

                        }
                        else
                        {
                            $_id = ++$temp_data[$Users[$result[$i]['id_uzytkownik']]]['end'];
                        }
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['nazwa'] = $_result[$i]['nazwa'];
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['telefon'] = $_result[$i]['telefon'];
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['miasto'] = $_result[$i]['miasto'];
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['kod_pocztowy'] = $_result[$i]['kod_pocztowy'];
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['adres'] = $_result[$i]['adres'];
                        $temp_data[$Users[$result[$i]['dodal_uzytkownik']]][$_id]['data_utworzenia'] = $_result[$i]['data_utworzenia'];

                }
                $start = 2;
                foreach($temp_data as $login => $data)
                {

                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start)->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                                  ->setCellValue('A'.$start, $login);
                    $start++;
                    $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.($start), 'Data dodania')
                                    ->setCellValue('B'.($start), 'Nazwa')
                                    ->setCellValue('C'.($start), 'Telefon')
                                    ->setCellValue('D'.($start), 'Miasto')
                                    ->setCellValue('E'.($start), 'Kod Pocztowy')
                                    ->setCellValue('F'.($start), 'Adres');
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$start.':G'.$start)->getFont()->setBold(true);
                    $start++;
                    for($i=0;$i <= $data['end']; $i++)
                    {
                        $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A'.($start), $data[$i]['data_utworzenia'])
                                    ->setCellValue('B'.($start), $data[$i]['nazwa'])
                                    ->setCellValue('C'.($start), $data[$i]['telefon'])
                                    ->setCellValue('D'.($start), $data[$i]['miasto'])
                                    ->setCellValue('E'.($start), $data[$i]['kod_pocztowy'])
                                    ->setCellValue('F'.($start), $data[$i]['adres']);
                        $start++;
                    }
                    $start++;

                }
               //echo '<pre>';
               // print_r($temp_data);
               // die();
        ///////////////////////////////////////////////////////////////////////////////////////


                // Nag��wki klient�w
                $_start_row=$start+2;
                $objPHPExcel->getActiveSheet()->getRowDimension($_start_row)->setRowHeight(30);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('EEEEEEEE');
                $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)
                        ->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFont()->setSize(12);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$_start_row.':G'.$_start_row)
                            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.$_start_row, 'Data dodania')
                                        ->setCellValue('B'.$_start_row, 'Nazwa')
                                        ->setCellValue('C'.$_start_row, 'Telefon')
                                        ->setCellValue('D'.$_start_row, 'Miasto')
                                        ->setCellValue('E'.$_start_row, 'Kod pocztowy')
                                        ->setCellValue('F'.$_start_row, 'Adres')
                                        ->setCellValue('G'.$_start_row, 'Uzytkownik');
                $_start_row++;
                for($i=0;$i<count($_result);$i++)
                {
                        // Wy�wietlanie klientow
                        $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.($_start_row+$i), $_result[$i]['data_utworzenia'])
                                        ->setCellValue('B'.($_start_row+$i), $_result[$i]['nazwa'])
                                        ->setCellValue('C'.($_start_row+$i), $_result[$i]['telefon'])
                                        ->setCellValue('D'.($_start_row+$i), $_result[$i]['miasto'])
                                        ->setCellValue('E'.($_start_row+$i), $_result[$i]['kod_pocztowy'])
                                        ->setCellValue('F'.($_start_row+$i), $_result[$i]['adres'])
                                        ->setCellValue('G'.($_start_row+$i), $_result[$i]['login']);

                        $objPHPExcel->getActiveSheet()->getStyle('A'.($_start_row+$i).':G'.($_start_row+$i))
                                ->getAlignment()->setWrapText(true);
                }


                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->getActiveSheet()->setTitle('Raport klientow MEPP');
                $objPHPExcel->setActiveSheetIndex(0);



                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="raport_dopisanych_klientow_mepp.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
        }

        function RaportKlienciBaza(){
            if($this->Uzytkownik->IsAdmin() || $_SESSION['uprawnienia_id'] == 2){
                include(SCIEZKA_INCLUDE."PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("MEPP")
                                                                         ->setLastModifiedBy("MEPP")
                                                                         ->setTitle("Baza klientów MEPP");
                $ActiveSheet = $objPHPExcel->getActiveSheet();
                $ActiveSheet->getStyle('A1:J1')->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('EEEEEEEE');
                $ActiveSheet->getStyle('A1:J1')
                        ->getAlignment()->setWrapText(true);
                $ActiveSheet->getStyle('A1:J1')->getFont()->setSize(12);

                $ActiveSheet->getStyle('A1:J1')->getFont()->setBold(true);
                $ActiveSheet->getStyle('A1:J1')
        ->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ActiveSheet->getColumnDimension('A')->setWidth(30);
                $ActiveSheet->getColumnDimension('B')->setWidth(15);
                $ActiveSheet->getColumnDimension('C')->setWidth(30);
                $ActiveSheet->getColumnDimension('D')->setWidth(30);
                $ActiveSheet->getColumnDimension('E')->setWidth(30);
                $ActiveSheet->getColumnDimension('F')->setWidth(15);
                $ActiveSheet->getColumnDimension('G')->setWidth(15);
                $ActiveSheet->getColumnDimension('H')->setWidth(20);

                $ActiveSheet->getRowDimension('1')->setRowHeight(30);

                $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A1', 'Nazwa')
                                        ->setCellValue('B1', 'kraj')
                                        ->setCellValue('C1', 'adres')
                                        ->setCellValue('D1', 'telefon')
                                        ->setCellValue('E1', 'e-mail')
                                        ->setCellValue('F1', 'branża')
                                        ->setCellValue('G1', 'opiekun')
                                        ->setCellValue('H1', 'Oddział')
                                        ->setCellValue('I1', 'ostatnie zdarzenie')
                                        ->setCellValue('J1', 'następne przypomnienie');

                /*dane na temat klienta*/
                $_customers = $this->Baza->GetRows("SELECT k.*, kk.kod, kk.nazwa_kraju, b.branza, u.login, od.nazwa as od_nazwa
                                        FROM orderplus_klient k
                                        LEFT JOIN kod_kraju kk ON(kk.id = k.kod_kraju_id)
                                        LEFT JOIN branza b ON(b.id = k.branza_crm_id)
                                        LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = k.id_uzytkownik)
                                        LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = k.id_oddzial)
                                        WHERE k.usuniety = '0'".($_SESSION['uprawnienia_id'] == 2 ? " AND od.id_oddzial = '{$_SESSION['id_oddzial']}'" : "")."
                                        ORDER BY k.nazwa ASC");
                // wyciągnięcie klientów pana S.Jurzysty = 16, kpasierb = 50
                if($_GET['dev'] == "dev"){
                    $_customers = $this->Baza->GetRows("SELECT k.*, kk.kod, kk.nazwa_kraju, b.branza, u.login, od.nazwa as od_nazwa
                                        FROM orderplus_klient k
                                        LEFT JOIN kod_kraju kk ON(kk.id = k.kod_kraju_id)
                                        LEFT JOIN branza b ON(b.id = k.branza_crm_id)
                                        LEFT JOIN orderplus_klient_opiekun_handlowy koh ON(koh.id_klient = k.id_klient)
                                        LEFT JOIN orderplus_uzytkownik u ON(u.id_uzytkownik = koh.id_uzytkownik)
                                        LEFT JOIN orderplus_oddzial od ON(od.id_oddzial = k.id_oddzial)
                                        WHERE k.usuniety = '0' AND koh.id_uzytkownik = '50'
                                        ORDER BY k.nazwa ASC");
                }
                $_start_row=3;

                for($i=0;$i<count($_customers);$i++)
                {
                        $_last_event = $this->Baza->GetData("SELECT z.temat, z.data_zakonczenia FROM zdarzenia z
                                                                LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                                                WHERE z.data_zakonczenia is not NULL AND pz.id_klient = '{$_customers[$i]['id_klient']}'
                                                                ORDER BY z.data_zakonczenia DESC LIMIT 1");
                        $_next_event = $this->Baza->GetData("SELECT z.temat, z.data_poczatek, z.data_przypomnienia FROM zdarzenia z
                                                                LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = z.id)
                                                                WHERE z.data_zakonczenia IS NULL AND pz.id_klient = '{$_customers[$i]['id_klient']}'
                                                                ORDER BY z.data_poczatek, z.data_przypomnienia DESC LIMIT 1"); 

                        $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue('A'.($_start_row+$i), $_customers[$i]['nazwa'])
                                        ->setCellValue('B'.($_start_row+$i), $_customers[$i]['nazwa_kraju'].' ['.$_customers[$i]['kod'].']')
                                        ->setCellValue('C'.($_start_row+$i), $_customers[$i]['adres'].', '.$_customers[$i]['kod_pocztowy'].' '.$_customers[$i]['miasto'])
                                        ->setCellValue('D'.($_start_row+$i), 'tel: '.$_customers[$i]['telefon'])
                                        ->setCellValue('E'.($_start_row+$i), $_customers[$i]['emaile'])
                                        ->setCellValue('F'.($_start_row+$i), $_customers[$i]['branza'])
                                        ->setCellValue('G'.($_start_row+$i), $_customers[$i]['login'])
                                        ->setCellValue('H'.($_start_row+$i), $_customers[$i]['od_nazwa'])
                                        ->setCellValue('I'.($_start_row+$i), ($_last_event[0] ? $_last_event['temat'].' ['.$_last_event['data_zakonczenia'].']' : 'brak zadania'))
                                        ->setCellValue('J'.($_start_row+$i), ($_next_event[0] ? $_next_event['temat'].' ['.($_next_event['data_przypomnienia'] ? $_next_event['data_przypomnienia'] : $_next_event['data_poczatek']).']' : 'brak zadania'))
                                        ;

                        $ActiveSheet->getStyle('A'.($_start_row+$i).':J'.($_start_row+$i))
                                ->getAlignment()->setWrapText(true);
                }


                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $ActiveSheet->setTitle('Baza klientów MEPP');
                $objPHPExcel->setActiveSheetIndex(0);



                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="baza_klientow_mepp.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }
        }

        function TabelaRozliczenXLS(){
            $Modul = new TabelaRozliczenNowa($this->Baza, $this->Uzytkownik, "tabela_rozliczen_nowa", null);
            $Pola = $Modul->PobierzListeElementow(array(), true);
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Elementy[] = $Element;
            } 
            $Elementy = $Modul->DodatkoweFiltryDoKolumnXLS($Elementy);            
            $Elementy = $Modul->ObrobkaDanychXLS($Elementy);
            $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
            $Idx = count($Pola);
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Tabela rozliczen");
            $ActiveSheet = $objPHPExcel->getActiveSheet();
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getAlignment()->setWrapText(true);
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setSize(12);

            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setBold(true);
            $Idx2 = 0;
            foreach($Pola as $Pole => $Nazwa){
                if(in_array($Pole, array("id_klient", "id_kierowca", "id_przewoznik", "miejsce_zaladunku", "odbiorca"))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(30);
                }else if(in_array($Pole, array("id_oddzial", "ilosc_km", "typ_serwisu"))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(12);
                }else{
                    $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(20);
                }
                $ActiveSheet->setCellValue("{$Kolumny[$Idx2]}1", $Nazwa['naglowek']);
                $Idx2++;
            }
            $_start_row=2;

            for($i=0;$i<count($Elementy);$i++){
                $Idx2 = 0;
                foreach($Pola as $Pole => $Nazwa){
                    if(isset($Nazwa['type']) && $Nazwa['type'] == "date"){
                        $Elementy[$i][$Pole] = ($Elementy[$i][$Pole] == "0000-00-00" ? "" : $Elementy[$i][$Pole]);
                    }
                    $ActiveSheet->setCellValue($Kolumny[$Idx2].($_start_row+$i), (isset($Nazwa['elementy']) ? $Nazwa['elementy'][$Elementy[$i][$Pole]] : $Elementy[$i][$Pole]));
                    $Idx2++;
                }
                $ActiveSheet->getStyle('A'.($_start_row+$i).":{$Kolumny[$Idx]}".($_start_row+$i))->getAlignment()->setWrapText(true);
            }


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $ActiveSheet->setTitle('Tabela rozliczen');
            //$objPHPExcel->setActiveSheetIndex(0);



            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="tabela_rozliczen.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        function TabelaRozliczenMorskieXLS(){
            $Modul = new TabelaRozliczenMorskie($this->Baza, $this->Uzytkownik, $this->Parametr, null);
            $Users = UsefullBase::GetUsers($this->Baza);
            $Pola = $Modul->PobierzListeElementow(array(), true);
            $Pola['koszty']['naglowek'] = "Przewoźnik";
            unset($Pola['id_uzytkownik']);
            unset($Pola['marza']);
            $Pola['koszty_kwota'] = array('naglowek' => 'Koszt');
            $Pola['marza'] = array("naglowek" => "Marża");
            $Pola['id_uzytkownik'] = array("naglowek" => "Zlecenie wystawił", 'elementy' => $Users);
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Elementy[] = $Element;
            }
            $Elementy = $Modul->ObrobkaDanychXLS($Elementy, $Pola);
            $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
            $Idx = count($Pola);
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Tabela rozliczen morskie");
            $ActiveSheet = $objPHPExcel->getActiveSheet();
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getAlignment()->setWrapText(true);
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setSize(12);

            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setBold(true);
            $Idx2 = 0;
            foreach($Pola as $Pole => $Nazwa){
                if(in_array($Pole, array("id_klient_shipper", "id_klient_consignee", "id_przewoznik_agent", 'numer_zlecenia'))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(30);
                }else if(in_array($Pole, array("id_oddzial", "ilosc_km", "typ_serwisu"))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(12);
                }else{
                    $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(20);
                }
                $ActiveSheet->setCellValue("{$Kolumny[$Idx2]}1", $Nazwa['naglowek']);
                $Idx2++;
            }
            $_start_row=2;
            $i = 0;
            foreach($Elementy as $Element){
                $Rows = count($Element['koszty_lista']);
                $Idx2 = 0;
                $MergeRows = false;
                if($Rows > 1){
                    $MergeRows = true;
                }
                foreach($Pola as $Pole => $Nazwa){
                    if($MergeRows && $Pole != "koszty" && $Pole != "koszty_kwota"){
                        $ActiveSheet->mergeCells($Kolumny[$Idx2].($_start_row+$i).":".$Kolumny[$Idx2].($_start_row+$i+$Rows));
                        $Element['koszty'] = $Element['koszty_lista'][0]['przewoznik'];
                        $Element['koszty_kwota'] = $Element['koszty_lista'][0]['kwota'];
                    }
                    if(isset($Nazwa['type']) && $Nazwa['type'] == "date"){
                        $Element[$Pole] = ($Element[$Pole] == "0000-00-00" ? "" : $Element[$Pole]);
                    }
                    $ActiveSheet->setCellValue($Kolumny[$Idx2].($_start_row+$i), trim($Element[$Pole]));
                    $Idx2++;
                }
                $ActiveSheet->getStyle('A'.($_start_row+$i).":{$Kolumny[$Idx]}".($_start_row+$i))->getAlignment()->setWrapText(true);
                $i++;
                if($Rows > 1){
                    foreach($Element['koszty_lista'] as $DaneKoszt){
                        $ActiveSheet->setCellValue("G".($_start_row+$i), trim($DaneKoszt['przewoznik']));
                        $ActiveSheet->setCellValue("H".($_start_row+$i), trim($DaneKoszt['kwota']));
                        $i++;
                    }
                }
            }

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $ActiveSheet->setTitle('Tabela rozliczen morskich');
            //$objPHPExcel->setActiveSheetIndex(0);



            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="tabela_rozliczen_morskie.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

          function TabelaRozliczenLotniczeXLS(){
            $Modul = new TabelaRozliczenLotnicze($this->Baza, $this->Uzytkownik, $this->Parametr, null);
            $Users = UsefullBase::GetUsers($this->Baza);
            $Pola = $Modul->PobierzListeElementow(array(), true);
            $Pola['koszty']['naglowek'] = "Przewoźnik";
            unset($Pola['id_uzytkownik']);
            unset($Pola['marza']);
            $Pola['koszty_kwota'] = array('naglowek' => 'Koszt');
            $Pola['marza'] = array("naglowek" => "Marża");
            $Pola['id_uzytkownik'] = array("naglowek" => "Zlecenie wystawił", 'elementy' => $Users);
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Elementy[] = $Element;
            }
            $Elementy = $Modul->ObrobkaDanychXLS($Elementy, $Pola);
            $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
            $Idx = count($Pola);
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Tabela rozliczen lotnicze");
            $ActiveSheet = $objPHPExcel->getActiveSheet();
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getAlignment()->setWrapText(true);
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setSize(12);

            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setBold(true);
            $Idx2 = 0;
            foreach($Pola as $Pole => $Nazwa){
                if(in_array($Pole, array("id_klient_shipper", "id_klient_consignee", "id_przewoznik_agent", 'numer_zlecenia'))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(30);
                }else if(in_array($Pole, array("id_oddzial", "ilosc_km", "typ_serwisu"))){
                   $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(12);
                }else{
                    $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(20);
                }
                $ActiveSheet->setCellValue("{$Kolumny[$Idx2]}1", $Nazwa['naglowek']);
                $Idx2++;
            }
            $_start_row=2;
            $i = 0;
            foreach($Elementy as $Element){
                $Rows = count($Element['koszty_lista']);
                $Idx2 = 0;
                $MergeRows = false;
                if($Rows > 1){
                    $MergeRows = true;
                }
                foreach($Pola as $Pole => $Nazwa){
                    if($MergeRows && $Pole != "koszty" && $Pole != "koszty_kwota"){
                        $ActiveSheet->mergeCells($Kolumny[$Idx2].($_start_row+$i).":".$Kolumny[$Idx2].($_start_row+$i+$Rows));
                        $Element['koszty'] = $Element['koszty_lista'][0]['przewoznik'];
                        $Element['koszty_kwota'] = $Element['koszty_lista'][0]['kwota'];
                    }
                    if(isset($Nazwa['type']) && $Nazwa['type'] == "date"){
                        $Element[$Pole] = ($Element[$Pole] == "0000-00-00" ? "" : $Element[$Pole]);
                    }
                    $ActiveSheet->setCellValue($Kolumny[$Idx2].($_start_row+$i), trim($Element[$Pole]));
                    $Idx2++;
                }
                $ActiveSheet->getStyle('A'.($_start_row+$i).":{$Kolumny[$Idx]}".($_start_row+$i))->getAlignment()->setWrapText(true);
                $i++;
                if($Rows > 1){
                    foreach($Element['koszty_lista'] as $DaneKoszt){
                        $ActiveSheet->setCellValue("G".($_start_row+$i), trim($DaneKoszt['przewoznik']));
                        $ActiveSheet->setCellValue("H".($_start_row+$i), trim($DaneKoszt['kwota']));
                        $i++;
                    }
                }
            }

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $ActiveSheet->setTitle('Tabela rozliczen lotnicze');
            //$objPHPExcel->setActiveSheetIndex(0);



            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="tabela_rozliczen_lotnicze.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        
        function PlatnosciMorskieXLS(){
            $Przewoznicy = UsefullBase::GetPrzewoznicy($this->Baza);
            $Klienci = UsefullBase::GetKlienci($this->Baza);
            $Waluty = UsefullBase::GetWaluty($this->Baza);
            $Modul = new PlatnosciMorskie($this->Baza, $this->Uzytkownik, "platnosci_morskie", null);
            $Pola = $Modul->PobierzListeElementow(array(), true);
            $Elementy = array();
            while ($Element = $this->Baza->GetRow()) {
                $Elementy[] = $Element;
            } 
            $Elementy = $Modul->ObrobkaDanychLista($Elementy);
            $Elementy = $Modul->DodatkoweFiltryDoKolumnXLS($Elementy);
            $Kolumny = array(   "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
                                "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP", "AQ", "AR", "AS", "AT", "AU", "AV", "AW", "AX", "AY", "AZ");
            $Idx = count($Pola);
            include(SCIEZKA_INCLUDE."PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("MEPP")->setLastModifiedBy("MEPP")->setTitle("Tabela rozliczen");
            $ActiveSheet = $objPHPExcel->getActiveSheet();
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('EEEEEEEE');
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getAlignment()->setWrapText(true);
            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setSize(12);

            $ActiveSheet->getStyle("A1:{$Kolumny[$Idx]}1")->getFont()->setBold(true);
            $Idx2 = 0;
            foreach($Pola as $Pole => $Nazwa){
                if(in_array($Pole, array('numer_zlecenia', 'faktura_wlasna', 'id_klient', 'id_przewoznik', 'faktura_przewoznik'))){
                    $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(30);
                }else{
                    $ActiveSheet->getColumnDimension($Kolumny[$Idx2])->setWidth(20);
                }
                $ActiveSheet->setCellValue("{$Kolumny[$Idx2]}1", str_replace("<br />", " ", (is_array($Nazwa) ? $Nazwa['naglowek'] : $Nazwa)));
                $Idx2++;
            }
            $_start_row=2;

            foreach($Elementy as $dane){
                $Idx2 = 0;
                $Faktury = $dane['faktury'];
                $Koszty = $dane['koszty'];
                $Check = array(count($Faktury),count($Koszty));
                $Rows = max($Check);
                $i = 0;
                $ActiveSheet->setCellValue($Kolumny[0].($_start_row), $dane['numer_zlecenia']);
                $ActiveSheet->setCellValue($Kolumny[1].($_start_row), (isset($Faktury[$i]) ? $Faktury[$i]['numer'] : ""));
                $ActiveSheet->setCellValue($Kolumny[2].($_start_row), (isset($Faktury[$i]) ? $Faktury[$i]['data_wystawienia'] : ""));
                $ActiveSheet->setCellValue($Kolumny[3].($_start_row), (isset($Faktury[$i]) ? $Klienci[$Faktury[$i]['id_klienta']] : ""));
                if(isset($Faktury[$i])){
                    $Pozycje = mysql_query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '{$Faktury[$i]['id_faktury']}'");
                    $PosMany = 0;
                    $PosManyWaluta = 0;
                    while($Pos = mysql_fetch_array($Pozycje)){
                        if($Waluty[$Faktury[$i]['id_waluty']] == "PLN"){
                            $PosMany += $Pos['brutto'];
                        }else{
                            $PosMany += $Pos['brutto'] * $Faktury[$i]['kurs'];
                        }
                        $PosManyWaluta += $Pos['brutto'];
                    }
                    $ActiveSheet->setCellValue($Kolumny[4].($_start_row), number_format($PosManyWaluta,2,',',' ')." {$Waluty[$Faktury[$i]['id_waluty']]}");
                    if($Waluty[$Faktury[$i]['id_waluty']] != "PLN"){
                        $ActiveSheet->setCellValue($Kolumny[5].($_start_row), number_format($PosMany,2,',',' ')." PLN");
                    }
                    if(!in_array($Faktury[$i]['id_faktury'], $this->UzyteFaktury)){
                        $this->UzyteFaktury[] = $Faktury[$i]['id_faktury'];
                        $this->Sumowanie['stawka_klient'] += $PosMany;
                    }
                }
                $ActiveSheet->setCellValue($Kolumny[6].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['data_wplywu'] != "0000-00-00" ? $Faktury[$i]['data_wplywu'] : ""));
                $ActiveSheet->setCellValue($Kolumny[7].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['termin_platnosci'] != "0000-00-00" ? $Faktury[$i]['termin_platnosci'] : ""));
                $ActiveSheet->setCellValue($Kolumny[8].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Faktury[$i]['rzeczywista_zaplata'] : ""));
                $ActiveSheet->setCellValue($Kolumny[9].($_start_row), (isset($Faktury[$i]) ? Usefull::PokazOpoznienie($Faktury[$i]['termin_platnosci'], $Faktury[$i]['rzeczywista_zaplata'], true) : ""));
                $ActiveSheet->setCellValue($Kolumny[10].($_start_row), (isset($Koszty[$i]) ? $Przewoznicy[$Koszty[$i]['id_przewoznik']] : ""));
                $ActiveSheet->setCellValue($Kolumny[11].($_start_row), (isset($Koszty[$i]) ? $Koszty[$i]['nr_faktury'] : ""));
                if(isset($Koszty[$i])){
                    $Brutto1 = $Koszty[$i]['koszt_kwota_1']*(1+(intval($Koszty[$i]['stawka_vat'])/100));
                    $Brutto2 = $Koszty[$i]['koszt_kwota_2']*(1+(intval($Koszty[$i]['stawka_vat_2'])/100));
                    $Brutto = $Brutto1 + $Brutto2;
                    $Kwota = ($Koszty[$i]['waluta'] > 1 ? $Brutto * $Koszty[$i]['kurs'] : $Brutto);
                    $ActiveSheet->setCellValue($Kolumny[12].($_start_row), number_format($Brutto,2,',',' ')." {$Waluty[$Koszty[$i]['waluta']]}");
                    if($Waluty[$Koszty[$i]['waluta']] != "PLN"){
                        $ActiveSheet->setCellValue($Kolumny[13].($_start_row), number_format($Kwota,2,',',' ')." PLN");
                    }
                    if(!in_array($Koszty[$i]['id_koszt'], $this->UzyteKoszty)){
                        $this->UzyteKoszty[] = $Koszty[$i]['id_koszt'];
                        $this->Sumowanie['stawka_przewoznik'] += $Kwota;
                    }
                }
                $ActiveSheet->setCellValue($Kolumny[14].($_start_row), (isset($Koszty[$i]) && $Koszty[$i]['termin_platnosci'] != "0000-00-00" ? $Koszty[$i]['termin_platnosci'] : ""));
                $ActiveSheet->setCellValue($Kolumny[15].($_start_row).(isset($Koszty[$i]) && $Koszty[$i]['planowana_zaplata_przew'] != "0000-00-00" ? $Koszty[$i]['planowana_zaplata_przew'] : ""));
                $ActiveSheet->setCellValue($Kolumny[16].($_start_row).(isset($Koszty[$i]) && $Koszty[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Koszty[$i]['rzeczywista_zaplata'] : ""));
                $ActiveSheet->setCellValue($Kolumny[17].($_start_row), (isset($Koszty[$i]) ? Usefull::PokazOpoznienie($Koszty[$i]['termin_platnosci'], $Koszty[$i]['rzeczywista_zaplata'], true) : ""));
                $ActiveSheet->setCellValue($Kolumny[18].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['data_wystawienia'] && $Faktury[$i]['data_wplywu'] != "0000-00-00" ? Usefull::ObliczIloscDniMiedzyDatami($Faktury[$i]['data_wystawienia'], $Faktury[$i]['data_wplywu']) : ""));
                $ActiveSheet->getStyle('A'.($_start_row).":{$Kolumny[18]}".($_start_row))->getAlignment()->setWrapText(true);
                $_start_row++;
                
                if($Rows > 1){
                    for($i = 1; $i < $Rows; $i++){
                        $ActiveSheet->setCellValue($Kolumny[1].($_start_row), (isset($Faktury[$i]) ? $Faktury[$i]['numer'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[2].($_start_row), (isset($Faktury[$i]) ? $Faktury[$i]['data_wystawienia'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[3].($_start_row), (isset($Faktury[$i]) ? $Klienci[$Faktury[$i]['id_klienta']] : ""));
                        if(isset($Faktury[$i])){
                            $Pozycje = mysql_query("SELECT * FROM orderplus_sea_orders_faktury_pozycje WHERE id_faktury = '{$Faktury[$i]['id_faktury']}'");
                            $PosMany = 0;
                            $PosManyWaluta = 0;
                            while($Pos = mysql_fetch_array($Pozycje)){
                                if($Waluty[$Faktury[$i]['id_waluty']] == "PLN"){
                                    $PosMany += $Pos['brutto'];
                                }else{
                                    $PosMany += $Pos['brutto'] * $Faktury[$i]['kurs'];
                                }
                                $PosManyWaluta += $Pos['brutto'];
                            }
                            $ActiveSheet->setCellValue($Kolumny[4].($_start_row), number_format($PosManyWaluta,2,',',' ')." {$Waluty[$Faktury[$i]['id_waluty']]}");
                            if($Waluty[$Faktury[$i]['id_waluty']] != "PLN"){
                                $ActiveSheet->setCellValue($Kolumny[5].($_start_row), number_format($PosMany,2,',',' ')." PLN");
                            }
                            if(!in_array($Faktury[$i]['id_faktury'], $this->UzyteFaktury)){
                                $this->UzyteFaktury[] = $Faktury[$i]['id_faktury'];
                                $this->Sumowanie['stawka_klient'] += $PosMany;
                            }
                        }
                        $ActiveSheet->setCellValue($Kolumny[6].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['data_wplywu'] != "0000-00-00" ? $Faktury[$i]['data_wplywu'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[7].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['termin_platnosci'] != "0000-00-00" ? $Faktury[$i]['termin_platnosci'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[8].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Faktury[$i]['rzeczywista_zaplata'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[9].($_start_row), (isset($Faktury[$i]) ? Usefull::PokazOpoznienie($Faktury[$i]['termin_platnosci'], $Faktury[$i]['rzeczywista_zaplata'], true) : ""));
                        $ActiveSheet->setCellValue($Kolumny[10].($_start_row), (isset($Koszty[$i]) ? $Przewoznicy[$Koszty[$i]['id_przewoznik']] : ""));
                        $ActiveSheet->setCellValue($Kolumny[11].($_start_row), (isset($Koszty[$i]) ? $Koszty[$i]['nr_faktury'] : ""));
                        if(isset($Koszty[$i])){
                            $Brutto1 = $Koszty[$i]['koszt_kwota_1']*(1+(intval($Koszty[$i]['stawka_vat'])/100));
                            $Brutto2 = $Koszty[$i]['koszt_kwota_2']*(1+(intval($Koszty[$i]['stawka_vat_2'])/100));
                            $Brutto = $Brutto1 + $Brutto2;
                            $Kwota = ($Koszty[$i]['waluta'] > 1 ? $Brutto * $Koszty[$i]['kurs'] : $Brutto);
                            $ActiveSheet->setCellValue($Kolumny[12].($_start_row), number_format($Brutto,2,',',' ')." {$Waluty[$Koszty[$i]['waluta']]}");
                            if($Waluty[$Koszty[$i]['waluta']] != "PLN"){
                                $ActiveSheet->setCellValue($Kolumny[13].($_start_row), number_format($Kwota,2,',',' ')." PLN");
                            }
                            if(!in_array($Koszty[$i]['id_koszt'], $this->UzyteKoszty)){
                                $this->UzyteKoszty[] = $Koszty[$i]['id_koszt'];
                                $this->Sumowanie['stawka_przewoznik'] += $Kwota;
                            }
                        }
                        $ActiveSheet->setCellValue($Kolumny[14].($_start_row), (isset($Koszty[$i]) && $Koszty[$i]['termin_platnosci'] != "0000-00-00" ? $Koszty[$i]['termin_platnosci'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[15].($_start_row).(isset($Koszty[$i]) && $Koszty[$i]['planowana_zaplata_przew'] != "0000-00-00" ? $Koszty[$i]['planowana_zaplata_przew'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[16].($_start_row).(isset($Koszty[$i]) && $Koszty[$i]['rzeczywista_zaplata'] != "0000-00-00" ? $Koszty[$i]['rzeczywista_zaplata'] : ""));
                        $ActiveSheet->setCellValue($Kolumny[17].($_start_row), (isset($Koszty[$i]) ? Usefull::PokazOpoznienie($Koszty[$i]['termin_platnosci'], $Koszty[$i]['rzeczywista_zaplata'], true) : ""));
                        $ActiveSheet->setCellValue($Kolumny[18].($_start_row), (isset($Faktury[$i]) && $Faktury[$i]['data_wystawienia'] && $Faktury[$i]['data_wplywu'] != "0000-00-00" ? Usefull::ObliczIloscDniMiedzyDatami($Faktury[$i]['data_wystawienia'], $Faktury[$i]['data_wplywu']) : ""));
                        $ActiveSheet->getStyle('A'.($_start_row).":{$Kolumny[18]}".($_start_row))->getAlignment()->setWrapText(true);
                        $_start_row++;
                    }
                }
            }


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $ActiveSheet->setTitle('Platnosci morskie');
            //$objPHPExcel->setActiveSheetIndex(0);



            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="tabela_rozliczen.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        function TestXLS(){
            	/*tworzenie pliku excel*/
                include(SCIEZKA_INCLUDE."PHPExcel.php");
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("MEPP")
                                             ->setLastModifiedBy("MEPP")
                                             ->setTitle("Test");


                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->getActiveSheet()->setTitle('Raport klientow MEPP');
                $objPHPExcel->setActiveSheetIndex(0);
                $gdImage = imagecreatefromjpeg('/images/logo-faktury.jpg');
                // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('Sample image');$objDrawing->setDescription('Sample image');
                $objDrawing->setImageResource($gdImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                //$objDrawing->setHeight(150);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                //$objDrawing->setPath('./images/paid.png');
                $objDrawing->setCoordinates('B15');
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="test_xls.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
        }

}
?>
