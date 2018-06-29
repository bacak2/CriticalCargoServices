<?php
/**
 * Cronjobsy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class Cronjobs {
	private $Baza = null;
	
	function __construct($Baza) {
            $this->Baza = $Baza;
	}

        function WykonajAkcjeNocne(){
            //$this->GenerujOpoznieniePrzewoznika();
            //$this->GenerujOpoznienieKlienta();
        }

        /** funkcja uzyta do obliczenia Fifo przy zmianie z oblicznia w locie na trzymane w bazie **/
        function GenerujFifo(){
            $Zlecenia = $this->Baza->GetRows("SELECT data_wplywu, id_zlecenie, id_faktury FROM orderplus_zlecenie WHERE id_faktury > 0 AND data_wplywu != '0000-00-00' AND fifo IS NULL");
            foreach($Zlecenia as $Zlec){
                $Faktura = $this->Baza->GetValue("SELECT data_wystawienia FROM faktury WHERE id_faktury = '{$Zlec['id_faktury']}'");
                $Save['fifo'] = Usefull::ObliczIloscDniMiedzyDatami($Faktura, $Zlec['data_wplywu']);
                $Zapytanie = $this->Baza->PrepareUpdate("orderplus_zlecenie", $Save, array('id_zlecenie' => $Zlec['id_zlecenie']));
                echo $Zapytanie."<br />";
                //$this->Baza->Query($Zapytanie);
            }
        }

        function GenerujOpoznieniePrzewoznika(){
            $Zlecenia = $this->Baza->GetRows("SELECT rzecz_zaplata_przew, termin_przewoznika, id_zlecenie FROM orderplus_zlecenie WHERE rzecz_zaplata_przew != '0000-00-00' AND opoznienie_przewoznik IS NULL");
            foreach($Zlecenia as $Zlec){
                if($Zlec['rzecz_zaplata_przew'] > $Zlec['termin_przewoznika']){
                    $Data = explode("-",$Zlec['termin_przewoznika']);
                    $Data2 = explode("-",$Zlec['rzecz_zaplata_przew']);
                    $date2 = mktime(0,0,0,$Data[1],$Data[2],$Data[0]);
                    $date1 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
                    $dateDiff = $date1 - $date2;
                    $fullDays = floor($dateDiff/(60*60*24));
                    $Save['opoznienie_przewoznik'] = $fullDays;
                    $Zapytanie = $this->Baza->PrepareUpdate("orderplus_zlecenie", $Save, array('id_zlecenie' => $Zlec['id_zlecenie']));
                    echo $Zapytanie."<br />";
                    $this->Baza->Query($Zapytanie);
                }
            }
        }

        function GenerujOpoznienieKlienta(){
            $Zlecenia = $this->Baza->GetRows("SELECT rzecz_zaplata_klienta, termin_wlasny, id_zlecenie FROM orderplus_zlecenie WHERE rzecz_zaplata_klienta != '0000-00-00' AND opoznienie_klient IS NULL");
            foreach($Zlecenia as $Zlec){
                if($Zlec['rzecz_zaplata_klienta'] > $Zlec['termin_wlasny']){
                    $Data = explode("-",$Zlec['termin_wlasny']);
                    $Data2 = explode("-",$Zlec['rzecz_zaplata_klienta']);
                    $date2 = mktime(0,0,0,$Data[1],$Data[2],$Data[0]);
                    $date1 = mktime(0,0,0,$Data2[1],$Data2[2],$Data2[0]);
                    $dateDiff = $date1 - $date2;
                    $fullDays = floor($dateDiff/(60*60*24));
                    $Save['opoznienie_klient'] = $fullDays;
                    $Zapytanie = $this->Baza->PrepareUpdate("orderplus_zlecenie", $Save, array('id_zlecenie' => $Zlec['id_zlecenie']));
                    echo $Zapytanie."<br />";
                    $this->Baza->Query($Zapytanie);
                }
            }
        }
}
?>