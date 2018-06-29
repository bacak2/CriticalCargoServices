<?php
/**
 * Moduł noty obciążeniowe
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class NotyObciazeniowe extends ModulBazowy {
        public $Klienci;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_noty';
            $this->PoleID = 'id_noty';
            $this->PoleNazwy = 'nr_noty';
            $this->Nazwa = 'Noty';
            $this->Klienci = UsefullBase::GetKlienci($this->Baza);
            $this->Filtry[] = array("opis" => "Pokaż tylko klienta", "nazwa" => "id_klienta", "typ" => "lista", "opcje" => $this->Klienci, 'domyslna' => ' - wybierz klienta - ');
            $this->Filtry[] = array("opis" => "Szukaj NIP", "nazwa" => "nip_search", "typ" => "tekst");
            $this->Filtry[] = array("opis" => "Pokaż noty", "nazwa" => "status", "typ" => "lista", "opcje" => array('pay' => 'zapłacone', 'nopay' => 'niezapłacone'), 'domyslna' => ' - wszystkie - ');
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('id_klienta', 'lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Klienci, 'wybierz' => true));
            $Formularz->DodajPole('nr_noty', 'tekst', 'Numer noty', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('szablon_nota', 'lista', 'Szablon noty', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => Usefull::GetNotyLangs()));
            $Formularz->DodajPole('nazwa_naleznosci', 'tekst_dlugi', 'Treść należności', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 100px;')));
            $Formularz->DodajPole('kwota_waluta', 'tekst', 'Należność', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;'), 'decimal' => true));
            $Formularz->DodajPole('waluta', 'lista', null, array('tabelka' => array('td_end' => 1, 'tr_end' => 1), 'elementy' => Usefull::GetWaluty()));
            $Formularz->DodajPole('kurs', 'kurs', 'Kurs<br /><small>Jeżeli kwota obciążenia jest w walucie obcej</small>', array('tabelka' => Usefull::GetFormStandardRow(), 'decimal' => true));
            $Formularz->DodajPole('data_wystawienia', 'tekst_data', 'Data wystawienia', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('miejsce_wystawienia', 'tekst', null, array('tabelka' => array('td_end' => 1, 'tr_end' => 1), 'atrybuty' => array('style' => 'width: 150px;'), 'opis_dodatkowy_przed' => '<span style="padding-left: 20px;">Miejsce wystawienia: </span>'));
            $Formularz->DodajPole('termin_platnosci', 'tekst_data', 'Termin płatności', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('data_zaplaty', 'tekst_data', 'Data zapłaty', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
            $Formularz->DodajPole('status', 'podzbiór_radio', 'Status', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(1 => 'Nota opłacona', 0 => 'Nota nieopłacona')));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function GenerujWarunki($AliasTabeli = null) {
		$Where = $this->DomyslnyWarunek();
		if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
			for ($i = 0; $i < count($this->Filtry); $i++) {
				$Pole = $this->Filtry[$i]['nazwa'];
				if (isset($_SESSION['Filtry'][$Pole])) {
                                    $Wartosc = $_SESSION['Filtry'][$Pole];
                                    if($this->Filtry[$i]['typ'] == "lista"){
                                        if($Pole == "status"){
                                            $Wartosc = ($Wartosc == "pay" ? 1 : 0);
                                        }
                                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                                    }else{
                                        if($Pole == "nip_search"){
                                            $Clients = UsefullBase::GetKlienciByNip($Baza, $Wartosc);
                                            $Clients[] = -1;
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."id_klient IN(".imlode(",",$Clients).")";
                                        }else{
                                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                                        }
                                    }
				}
			}
		}
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function DomyslnyWarunek(){
            return "data_wystawienia >= '{$_SESSION['okresStart']}-01' AND data_wystawienia <= '{$_SESSION['okresEnd']}-31'";
        }
	
	function PobierzListeElementow($Filtry = array()) {
                $Wynik = array(
			"nr_noty" => 'Numer',
                        "id_klienta" => array('naglowek' => 'Klient', 'elementy' => $this->Klienci),
                        "data_wystawienia" => array('naglowek' => 'Data wystawienia', 'td_styl' => 'text-align: center;'),
                        "miejsce_wystawienia" => array('naglowek' => 'Miejsce wystawienia', 'td_styl' => 'text-align: center;'),
                        "kwota" => array('naglowek' => 'Kwota', 'td_styl' => 'text-align: center;'),
                        "termin_platnosci" => array('naglowek' => 'Termin płatności', 'td_styl' => 'text-align: center;'),
                        "data_zaplaty" => array('naglowek' => 'Data zapłaty', 'td_styl' => 'text-align: center;')
		);
		$Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT * FROM $this->Tabela a $Where ORDER BY id_noty DESC", $this->ParametrPaginacji, 30));
		return $Wynik;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "printer_button", 'title' => "Drukuj", "akcja_href" => "drukuj_note.php?");
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie", 'hidden' => (!$Dane[$this->PoleID] || $Dane[$this->PoleID] > 1 ? false : true));
		return $Akcje;
	}

        function  PobierzDaneDomyslne() {
            $autorok = date('Y');
            $automiesiac = date("m");
            $autonumer = $this->Baza->GetValue("SELECT MAX(autonumer) FROM orderplus_noty WHERE data_wystawienia LIKE '$autorok%'");
            $autonumer++;
            return array('nr_noty' => "$autonumer/$automiesiac/$autorok/NO");
        }

        function ShowRecord($Element, $Nazwa, $Styl){
            if($Nazwa == "nr_noty"){
                  $statkolor = ($Element['status'] == 1 ? '' : ' style="color: #9a0000"');
                  $statbold = ($Element['status'] == 1 ? "<strong>".stripslashes($Element[$Nazwa])."</strong>" : stripslashes($Element[$Nazwa]));
                echo("<td$statkolor>$statbold</td>");
            }else if($Nazwa == "kwota"){
                echo "<td><nobr>\n";
                if($Element['waluta'] == 'PLN'){
                    echo $Element['kwota_pln']." ".$Element['waluta'];
                }else{
                    echo $Element['kwota_waluta']." ".$Element['waluta']."<br />".$Element['kwota_pln']." PLN";
                }
                echo "</nobr></td>\n";
            }else if($Nazwa == "data_zaplaty"){
                echo "<td$Styl>";
                    if(!(int)($Element['data_zaplaty'])){
                        echo("Jeszcze nie zapłacono");
                    }else{
                        echo "<strong>{$Element[$Nazwa]}</strong>";
                    }
                echo "</td>";
            }else{
                echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
            }
        }

        function AkcjaDrukuj($ID){
            if($this->SprawdzUprawnienie("noty")){
                $nota = $this->PobierzDaneElementu($ID);
                $Client = UsefullBase::GetDaneKlienta($this->Baza, $nota['id_klienta']);
                if($nota['szablon_nota'] == "PL"){
                    include(SCIEZKA_INCLUDE."faktura_lang/pl.php");
                }else{
                    include(SCIEZKA_INCLUDE."faktura_lang/eng.php");
                }
                include(SCIEZKA_SZABLONOW."druki/nota-obciazeniowa.tpl.php");
            }
        }

        function ShowNaglowekDrukuj(){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_nota.tpl.php');
        }

        function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
            if($_POST['OpcjaFormularza'] == "pobierz_kurs"){
                $Wartosci['kurs'] = UsefullBase::PobierzKursZDnia($this->Baza, $Wartosci['data_wystawienia'], $Wartosci['waluta'], $Wartosci['id_klienta']);
                if(!$Wartosci['kurs']){
                    $Wartosci['kurs'] = "0.0000";
                }
                
            }
            return $Wartosci;
        }

        function OperacjePrzedZapisem($Wartosci){
            if($Wartosci['waluta'] == "PLN"){
                $Wartosci['kurs'] = 1.0000;
            }
            $Wartosci['kwota_pln']  = number_format($Wartosci['kwota_waluta'] * $Wartosci['kurs'], 2, '.', '');
            return $Wartosci;
        }
}
?>
