<?php
/**
 * Moduł moja tabela rozliczeń
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class MojaTabelaRozliczen extends TabelaRozliczen {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);

	}

        function DomyslnyWarunek(){
            $InniUserzy = array($_SESSION['id_uzytkownik']);
            $Userzy = array();
            $UserzyFiltr = array();
            $this->Baza->Query("SELECT id_uzytkownik, imie, nazwisko, blokada FROM orderplus_uzytkownik WHERE id_oddzial = '{$_SESSION['id_oddzial']}'");
            while($InniUserzyW = $this->Baza->GetRow()){
                    $InniUserzy[] = $InniUserzyW['id_uzytkownik'];
                    $Userzy[$InniUserzyW['id_uzytkownik']] = $InniUserzyW['imie']." ".$InniUserzyW['nazwisko'];
                    if($InniUserzyW['blokada'] == 0){
                            $UserzyFiltr[$InniUserzyW['id_uzytkownik']] = $InniUserzyW['imie']." ".$InniUserzyW['nazwisko'];
                    }
            }
            return "((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND sea_order_id = '0' AND air_order_id = '0' AND termin_zaladunku >= '{$_SESSION['okresStart']}-01' AND termin_zaladunku <= '{$_SESSION['okresEnd']}-31' AND id_uzytkownik IN(".(implode(",", $InniUserzy)).")";
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(is_null($ID)){
                    echo("<div style='float: left; display: inline;'>");
                        echo "<div style='float: left; color: #bcce00; font-weight: bold;'>RAPORTY:<br /><br /></div>";
                        echo "<div style='clear: both;'></div>\n";
                        if($this->Uzytkownik->DostepDoRaportu('klient')){
                            echo "<a href='raporty.php?tryb=klientmojatabela' target='_blank' class='form-button'>klienci</a>";
                        }
                        if($this->Uzytkownik->DostepDoRaportu('trasy')){
                            echo "<a href='raporty3.php' target='_blank' class='form-button'>wg. tras</a>";
                        }
                    echo ("</div>");
                }
                include(SCIEZKA_SZABLONOW."nav.tpl.php");
            echo "</div>\n";
            if($this->WykonywanaAkcja != "dodawanie" && is_null($ID)){
                include(SCIEZKA_SZABLONOW."filters.tpl.php");
            }
        }

}
?>
