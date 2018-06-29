<?php
/**
 * Moduł klienci - kontakty
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Kontakty extends ModulBazowy {
    
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'osoby_kontaktowe';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'imie_nazwisko';
            $this->Nazwa = 'Kontakty';
            $this->CzySaOpcjeWarunkowe = true;
            $this->LinkPowrotu = "?modul=$this->Parametr&cid={$_GET['cid']}";
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
	}

        function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('imie_nazwisko', 'tekst', 'Osoba kontaktowa', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('stanowisko', 'tekst', 'Stanowisko', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('mail', 'tekst', 'E-mail', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('telefon', 'tekst', 'Telefon', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('id_klient', 'hidden', null);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			"imie_nazwisko" => 'Imię i nazwisko',
                        "stanowisko" => 'Stanowisko',
                        "telefon" => 'Telefon',
                        "mail" => 'Mail'
		);
		$this->Baza->Query("SELECT * FROM $this->Tabela WHERE id_klient = '{$_GET['cid']}' ORDER BY imie_nazwisko");
		return $Wynik;
	}

        function PobierzDaneDomyslne(){
            return array('id_klient' => $_GET['cid']);
        }

	function WyswietlAjax($Akcja){
            if($Akcja == "add-kontakt"){
                $this->ZapiszKontaktAjax($_POST);
            }
            if($Akcja == "add-os-kontakowa"){
                include(SCIEZKA_SZABLONOW."forms/osoba-kontaktowa-form.tpl.php");
            }
            if($Akcja == "save-os-kontakowa"){
                if($_POST['id_klient'] > 0){
                    $this->ZapiszKontaktAjaxDodajWiersz($_POST);
                }else{
                    $this->ZapiszKontaktAjaxDodajWierszSesja($_POST);
                }
            }
            if($Akcja == "del-os-kontaktowa"){
                if($_POST['cli'] > 0){
                    $this->UsunElement($_POST['id']);
                }else{
                    unset($_SESSION['Kontakt'][$this->SID][$_POST['id']]);
                }
            }
        }

        function ZapiszKontaktAjaxDodajWiersz($Values){
            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Values);
            if($this->Baza->Query($Zapytanie)){
                $ID = $this->Baza->GetLastInsertId();
                $Person = $this->PobierzDaneElementu($ID);
                include(SCIEZKA_SZABLONOW."forms/osoba-kontaktowa-row.tpl.php");
            }
        }

        function ZapiszKontaktAjaxDodajWierszSesja($Values){
            $SID = session_id();
            if(!isset($_SESSION['KontaktIdx'][$this->SID])){
                $_SESSION['KontaktIdx'][$this->SID] = 0;
            }else{
                $_SESSION['KontaktIdx'][$this->SID]++;
            }
            $Ktory = $_SESSION['KontaktIdx'][$this->SID];
            $Values['id'] = $Ktory;
            $_SESSION['Kontakt'][$this->SID][$Ktory] = $Values;
            $Person = $Values;
            include(SCIEZKA_SZABLONOW."forms/osoba-kontaktowa-row.tpl.php");
        }

        function ZapiszKontaktAjax($Values){
            $Save['id_klient'] = $Values['customer_id'];
            $Save['mail'] = $Values['mail'];
            $Save['imie_nazwisko'] = $Values['os_kontaktowa'];
            $Save['telefon'] = $Values['telefon'];
            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Save);
            if($this->Baza->Query($Zapytanie)){
                $status = "ok";
            }else{
                $status = "fail";
            }
            $Klienci = new Klienci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            $Klienci->InfoAboutCustomer($Values['customer_id'], $status);
        }

        function AkcjaDodawanie($ID) {
            $Klient = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$_GET['cid']}'");
            echo "<div style='clear: both;'></div>\n";
            echo "<div class='form-title'>$Klient - nowy kontakt";
            echo "</div>\n";
            parent::AkcjaDodawanie($ID);
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(isset($_GET['cid'])){
                    echo("<div style='float: left; display: inline;' id='zaczep'>");
                        echo "<a href='?modul=klienci&akcja=szczegoly&id={$_GET['cid']}' class='form-button'>wróć do klienta</a>";
                    echo ("</div>");
                }
            echo "</div>\n";
            if(!in_array($this->WykonywanaAkcja, array("dodawanie","dodaj_import","dodaj_export")) && is_null($ID) && !isset($_GET['did'])){
                include(SCIEZKA_SZABLONOW."filters.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
        }

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
		$Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja_link" => "?modul=$this->Parametr&akcja=edycja&id={$Dane['id']}&cid={$_GET['cid']}");
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja_link" => "?modul=$this->Parametr&akcja=edycja&id={$Dane['id']}&cid={$_GET['cid']}");
		}
		return $Akcje;
	}
}
?>