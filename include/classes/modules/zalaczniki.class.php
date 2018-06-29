<?php
/**
 * Moduł załączniki
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Zalaczniki extends ModulBazowy {
        public $ClientID;
        public $EventID;
    
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'zalaczniki';
            $this->PoleID = 'id';
            $this->PoleNazwy = 'opis';
            $this->Nazwa = 'Załączniki';
            $this->EventID = (isset($_GET['event']) ? $_GET['event'] : false);
            $KlientNazwa = "";
            $TematNazwa = "";
            if($this->EventID){
                $TematNazwa = $this->Baza->GetValue("SELECT temat FROM zdarzenia WHERE id = '$this->EventID'");
                $this->ClientID = $this->Baza->GetValue("SELECT id_klient FROM powiazania_zdarzenia WHERE Zdarzenia_id = '$this->EventID'");
            }else{
                $this->ClientID = (isset($_GET['cid']) ? $_GET['cid'] : false);
            }
            $KlientNazwa = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '$this->ClientID'");
            $this->NazwaElementu = $KlientNazwa.($TematNazwa != "" ? ": <b>$TematNazwa </b>" : "");
            $this->KatalogDanych = "files/";
            $this->LinkPowrotu = "?modul=$this->Parametr".$this->AddLinkParams();
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
	}

        function AddLinkParams(){
            return ($this->EventID ? "&event=$this->EventID" : "&cid=$this->ClientID");
        }

        function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('klient_temat', 'tekstowo', 'Klient'.($this->EventID ? ": temat zadania" : ""), array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('opis', 'tekst', 'Opis pliku', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('link', 'obraz', 'Załącznik', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('Zdarzenia_id', 'hidden', null);
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function WyswietlAjax($Akcja){
            if($Akcja == "add-zalacznik"){
                include(SCIEZKA_SZABLONOW."forms/zalacznik-form.tpl.php");
            }
            if($Akcja == "del-zalacznik"){
                $this->KatalogDanych = "../../../files/";
                if($_POST['cli'] > 0){
                    $this->UsunElement($_POST['id']);
                }else{
                    unlink($this->KatalogDanych.$_SESSION['Zalacznik'][$this->SID][$_POST['id']]['link']);
                    unset($_SESSION['Zalacznik'][$this->SID][$_POST['id']]);
                }
            }
        }

        function ZapiszZalacznikFromClient($ID = 0){
           if (is_uploaded_file($_FILES['zalacznik_add']['tmp_name'])){
                if($this->SprawdzPliki($_FILES['zalacznik_add'], true)){
                    $Plik = $this->KatalogDanych.$_FILES['zalacznik_add']['name'];
                    $Sciezka = dirname($Plik);
                    $StaryUmask = umask(0);
                    if (!file_exists($Sciezka)) {
                            mkdir($Sciezka, 0777, true);
                    }
                    if (move_uploaded_file($_FILES['zalacznik_add']['tmp_name'], $Plik)) {
                        chmod($Plik, 0777);
                        $Save['link'] = $_FILES['zalacznik_add']['name'];
                        $Save['opis'] = ($_POST['zalacznik_add_opis'] == "" ? $_FILES['zalacznik_add']['name'] : $_POST['zalacznik_add_opis']);
                        if($ID > 0){
                            $Save['id_klient'] = $ID;
                            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Save);
                            $this->Baza->Query($Zapytanie);
                        }else{
                            if(!isset($_SESSION['ZalacznikIdx'][$this->SID])){
                                $_SESSION['ZalacznikIdx'][$this->SID] = 0;
                            }else{
                                $_SESSION['ZalacznikIdx'][$this->SID]++;
                            }
                            $Ktory = $_SESSION['ZalacznikIdx'][$this->SID];
                            $Save['id'] = $Ktory;
                            $_SESSION['Zalacznik'][$this->SID][$Ktory] = $Save;
                        }
                    }else{
                        $this->Error = "Wystąpił problem z przesłaniem pliku";
                        return false;
                    }
                    umask($StaryUmask);
                }else{
                    return false;
                }
            }else{
                $this->Error = "Nie przesłano pliku";
                return false;
            }
            return true;
        }

        function GenerujWarunki($AliasTabeli = null) {
		if($this->EventID){
                    $Where = "Zdarzenia_id = '$this->EventID'";
                }else{
                    $Events = $this->Baza->GetValues("SELECT Zdarzenia_id FROM powiazania_zdarzenia WHERE id_klient = '$this->ClientID'");
                    $Events[] = -1;
                    $Where = "Zdarzenia_id IN(".implode(",",$Events).") OR (Zdarzenia_id = 0 AND id_klient = '$this->ClientID')";
                }
		return ($Where != '' ? "WHERE $Where" : '');
	}

        function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			"opis" => nl2br($this->NazwaElementu." [załączniki]")
		);
                $Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT * FROM $this->Tabela $Where ORDER BY $this->PoleID");
		return $Wynik;
	}

        function PobierzZalacznikiKlienta($ID = 0){
            if($ID > 0){
                $this->ClientID = $ID;
                $Where = $this->GenerujWarunki();
                return $this->Baza->GetRows("SELECT id, opis FROM $this->Tabela $Where ORDER BY $this->PoleID");
            }else{
                return $_SESSION['Zalaczniki'][$this->SID];
            }
        }

        function ShowTH($NazwaPola, $Styl, $Opis){
            echo "<th style='text-align: left'>$Opis</th>";
        }

        function PobierzAkcjeNaLiscie($Dane = array(), $TH = false){
		$Akcje = array();
		if(!in_array($this->Parametr,$this->ModulyBezKasowanie)){
			$Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja_link" => "?modul=$this->Parametr&akcja=kasowanie".$this->AddLinkParams());
		}
		return $Akcje;
	}

        function PobierzDaneDomyslne() {
            return array('klient_temat' => $this->NazwaElementu, 'Zdarzenia_id' => $this->EventID);
        }

        function ShowBigButtonActions($ID){
            echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
                if(isset($_GET['event'])){
                    echo("<div style='float: left; display: inline;' id='zaczep'>");
                        echo "<a href='?modul=zdarzenia&akcja=szczegoly&id={$_GET['event']}' class='form-button'>wróć do zadania</a>";
                    echo ("</div>");
                }
            echo "</div>\n";
            if(!in_array($this->WykonywanaAkcja, array("dodawanie","dodaj_import","dodaj_export")) && is_null($ID) && !isset($_GET['did'])){
                include(SCIEZKA_SZABLONOW."filters.tpl.php");
            }
            echo "<div style='clear: both'></div>\n";
        }

        function ShowRecord($Element, $Nazwa, $Styl){
            echo("<td$Styl><a href='download-file.php?attach={$Element[$this->PoleID]}'>".stripslashes($Element[$Nazwa])."</a></td>");
        }

        function UsunElement($ID) {
            $File = $this->Baza->GetValue("SELECT link FROM $this->Tabela WHERE id = '$ID'");
            unlink($this->KatalogDanych.$File);
            return $this->Baza->Query("DELETE FROM $this->Tabela WHERE id = '$ID'");
        }

        function SprawdzPliki($Pliki, $FromClient = false){
            if(!isset($Pliki['link']) && !$FromClient){
                $this->Error = "Nie przesłano pliku";
                return false;
            }
            if($this->Baza->GetValue("SELECT count(*) FROM $this->Tabela WHERE link = '{$Pliki['name']}'") > 0){
                $this->Error = "Istnieje już plik o takiej nazwie";
                return false;
            }
            if(!$this->checkMeppExt($Pliki['type'])){
                $this->Error = "Niepoprawny format pliku";
                return false;
            }
            return true;
        }


        function  AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null) {
            $Wartosci['klient_temat'] = $this->NazwaElementu;
            return $Wartosci;
        }

        function checkMeppExt($Type){
                $_mepp_file_ext=array(
                        'application/msword',
                        'application/pdf',
                        'application/rtf',
                        'application/vnd',
                        'application/x-mswrite',
                        'application/x-shockwave-flash',
                        'image',
                        'text'
                                );

                if(in_array($Type,$_mepp_file_ext))
                {	/*poprawny typ pliku*/
                        return true;
                }

                $_temp=explode('/',$Type);
                if(in_array($_temp[0],$_mepp_file_ext))
                {	/*poprawny typ pliku*/
                        return true;
                }

                $_temp=explode('.',$Type);
                if(in_array($_temp[0],$_mepp_file_ext))
                {	/*poprawny typ pliku*/
                        return true;
                }

                /*niepoprawny typ pliku*/
                return false;
        }

        function WykonajOperacjePoZapisie($Wartosci, $ID){
            $this->Baza->Query("UPDATE zdarzenia SET zalacznik='tak' WHERE id = '{$Wartosci['Zdarzenia_id']}'");
        }

        function JakiBlad(){
            return $this->Error;
        }
}
?>