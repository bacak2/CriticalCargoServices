<?php
/**
 * Moduł specyfikacja do faktury
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Specyfikacja extends ModulBazowy {
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->PrzyciskiFormularza['zapisz']['etykieta'] = "Wyślij";
            $this->PrzyciskiFormularza['zapisz']['src'] = "wyslij.gif";
            $this->PrzyciskiFormularza['zapisz']['onclick'] = "GenerujSpecyfikacje();";
	}

	function &GenerujFormularz($Wartosci, $Mapuj = false) {
            $Kody = UsefullBase::GetCountryCodes($this->Baza);
            $Formularz = new Formularz($_SERVER['REQUEST_URI'], $this->LinkPowrotu, "tabela_rozliczen");
            $Formularz->DodajPole('tytul', 'tekst', 'Tytuł', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('klient_id', 'lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetKlienci($this->Baza), 'wybierz' => true, 'atrybuty' => array('onchange' => 'PrzeladujForm()')));
            $Formularz->DodajPole('kraj_1', 'lista', 'Zlecenia', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'elementy' => $Kody, 'wybierz' => true, 'domyslna' => ' -- wszystkie -- ','atrybuty' => array('onchange' => 'PrzeladujForm()'), 'opis_dodatkowy_przed' => '<b>Filtruj:</b><br />wg. kodu kraju załadunku '));
            $Formularz->DodajPole('kraj_2', 'lista', null, array('elementy' => $Kody, 'wybierz' => true, 'domyslna' => ' -- wszystkie -- ','atrybuty' => array('onchange' => 'PrzeladujForm()'), 'opis_dodatkowy_przed' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; wg. kodu kraju rozładunku '));
            $Formularz->DodajPole('nowe_zlecenia', 'podzbiór', null, array('tabelka' => array('tr_end' => 1, 'td_end' => 1), 'wybierz' => true, 'opis_dodatkowy_przed' => '<br /><br />', 'elementy' => array(), 'atrybuty' => array('size' => 6)));
            $Formularz->DodajPole('stawka_vat', 'tekst', 'Stawka VAT', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            if(is_array($Wartosci)){
                $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
                if($Values['klient_id'] > 0){
                    $Formularz->UstawOpcjePola('nowe_zlecenia', "elementy", UsefullBase::GetZleceniaByClientAndTrasa($this->Baza, $Values['klient_id'], $Values), false);
                }
            }
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function  PobierzDaneDomyslne() {
            return array('stawka_vat' => 23, 'kraj_1' => 0, 'kraj_2' => 0);
        }

        function AkcjaDrukuj($ID, $Akcja){
            $Formularz = $this->GenerujFormularz($_POST);
            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
            include(SCIEZKA_SZABLONOW."druki/specyfikacja.tpl.php");
        }

        function ShowNaglowekDrukuj($Akcja){
            include(SCIEZKA_SZABLONOW."naglowek_drukuj_specyfikacja.tpl.php");
        }
}
?>
