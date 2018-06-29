<?php
/**
 * Moduł klienci - raporty
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class KlienciRaportyMorskie extends ModulBazowy {
        public $Emaile;
        public $ZleceniaKlienta;
        public $TabelaZlecenia;
        public $TabelaKontenery;
        public $HashPrefix;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_sea_klient_raport';
            $this->TabelaZlecenia = 'orderplus_sea_klient_raport_zlecenie';
            $this->TabelaKontenery = 'orderplus_sea_klient_raport_zlecenie_cont';
            $this->PoleID = 'raport_id';
            $this->PoleNazwy = 'raport_date';
            $this->Nazwa = 'Raporty';
            $this->CzySaOpcjeWarunkowe = true;
            $this->PrzyciskiFormularza['zapisz'] = array('etykieta' => 'Wyślij', 'src' => 'wyslij.gif', 'type' => 'button');
            $this->HashPrefix = "SeaRaportKlient";
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('klient_id', 'lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->GetKlienci(), 'wybierz' => true, 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza", "klient_change")')));
            $Formularz = $this->FormDodajPoleZlecenia($Formularz);
            $Formularz->DodajPole('nowe_zlecenia', 'dodaj_zlecenie_do_raportu', 'Dodaj nowe zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->ZleceniaKlienta));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function FormDodajPoleZlecenia($Formularz){
            $Formularz->DodajPole('statusy', 'zlecenia_w_raporcie_sea', 'Zlecenia w raporcie', array('tabelka' => Usefull::GetFormStandardRow()));
            return $Formularz;
        }

        function &GenerujFormularzWyslij() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('email', 'wyslij_raport', null, array('tabelka' => Usefull::GetFormWithoutTHRow(), 'elementy' => $this->Emaile));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                $Akcje[] = array('img' => "copy_button", 'title' => "Duplikacja", "akcja" => "duplikacja");
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podgląd", "akcja_link" => "raports/raport_morski.php?check={$Dane["hash"]}");
                $Akcje[] = array('img' => "mail_button", 'title' => "Wyślij", "akcja" => "wyslij");
                $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
		return $Akcje;
	}

        function DomyslnyWarunek(){
            return ($this->Uzytkownik->IsAdmin() == false ? "(id_oddzial IN(".implode(",",$_SESSION['oddzialy_dostep']).") OR id_oddzial = '0')" : "");
        }
	
	function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			$this->PoleNazwy => 'Data wysłania',
                        "klient_id" => array('naglowek' => 'Klient', 'elementy' => $this->GetKlienci())
		);
                $Where = $this->GenerujWarunki();
		$this->Baza->Query($this->QueryPagination("SELECT * FROM $this->Tabela k $Where ORDER BY $this->PoleNazwy DESC",$this->ParametrPaginacji));
		return $Wynik;
	}

        function GetKlienci(){
            $Klienci = new Klienci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $Klienci->GetListAktywni();
        }

        function GetEmaileKlienta($ID){
            $Klienci = new Klienci($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $Klienci->GetEmaile($ID);
        }

        function PobierzDaneElementu($ID, $Typ = null) {
            $Dane = parent::PobierzDaneElementu($ID, $Typ);
            $this->Emaile = $this->GetEmaileKlienta($Dane['klient_id']);
            if(!$this->Emaile){
                $this->Emaile = array();
            }else{
                $this->Emaile = explode(",", $this->Emaile);
            }
            $Emaile = array();
            foreach($this->Emaile as $Email){
                $Emaile[] = trim($Email);
            }
            $this->Emaile = $Emaile;
            $this->Emaile = array_unique($this->Emaile);
            return $Dane;
        }

        function PobierzZlecenia($ID){
            $Zlecenia = $this->Baza->GetResultAsArray("SELECT id_zlecenie, numer_zlecenia as numer, zlecenie_status as status, mode, podjecie, etd, rtd, eta, rta FROM $this->TabelaZlecenia tz JOIN  orderplus_sea_orders z ON(z.id_zlecenie = tz.zlecenie_id) WHERE $this->PoleID = '$ID'", "id_zlecenie");
            $Zlecenia = $this->PobierzKontenery($Zlecenia);
            return $Zlecenia;
        }

        function SprawdzDane($Wartosci, $ID) {
            if(isset($Wartosci['email'])){
                if(count($Wartosci['email']['lista']) == 0 && $Wartosci['email']['email_dodatkowy'] == ""){
                    $this->Error = "Dane zostały zapisane. Nie wybrano emaila do wysyłki";
                    return true;
                }
            }
            return true;
        }

        function AkcjaDodawanie() {
                $DodajOdRazu = false;
                if(isset($_GET['id']) && is_numeric($_GET['id'])){
                    $Zlecenie = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '{$_GET['id']}'");
                    $ZlecenieIds = array($_GET['id']);
                    $ClientID = $Zlecenie['nabywca_id'];
                    $DodajOdRazu = true;
                }
                if(isset($_POST['OrdersIDs']) && $_POST['OrdersIDs'] != ""){
                    $ids = explode(",", $_POST['OrdersIDs']);
                    $ClientID = false;
                    $ZlecenieIds = array();
                    foreach($ids as $ZlecID){
                        $ZlecDane = $this->Baza->GetData("SELECT * FROM orderplus_sea_orders WHERE id_zlecenie = '$ZlecID'");
                        if($ClientID == false){
                            $ClientID = $ZlecDane['nabywca_id'];
                        }else if($ClientID == false && $ZlecDane['id_klient_shipper'] > 0){
                            $ClientID = $ZlecDane['id_klient_shipper'];
                        }else if($ClientID == false && $ZlecDane['id_klient_consignee'] > 0){
                            $ClientID = $ZlecDane['id_klient_consignee'];
                        }
                        $ZlecenieIds[] = $ZlecID;
                    }
                    $DodajOdRazu = true;
                }
		if ($_SERVER['REQUEST_METHOD'] == 'POST' || $DodajOdRazu) {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if (in_array($OpcjaFormularza, array('zapisz', 'zapisz_zmiany', 'dodaj_nowe')) || $DodajOdRazu){
                                echo "<div style='clear: both;'></div>\n";
                                if($DodajOdRazu){
                                    $Wartosci['klient_id'] = $ClientID;
                                    $Wartosci["nowe_zlecenia"] = $ZlecenieIds;
                                }else{
                                    $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                                }
				if($this->SprawdzDane($Wartosci) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
                                        $Wartosci[$this->PoleNazwy] = date("Y-m-d H:i:s");
                                        $Wartosci['id_uzytkownik'] = $this->UserID;
                                        $Wartosci['id_oddzial'] = $_SESSION['id_oddzial'];
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
                                            $Hash = md5($this->HashPrefix.$this->ID);
                                            $Update['hash'] = $Hash;
                                            $ZapUpd = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $this->ID));
                                            $this->Baza->Query($ZapUpd);
                                            if($OpcjaFormularza == "dodaj_nowe" || $DodajOdRazu){
                                                $this->ZapiszZleceniaDoRaportu($Wartosci, $this->ID);
                                                $this->AktualizujSeaOrder($_POST['SeaOrder']);
                                                echo "<script type='text/javascript'>\n";
                                                    echo "window.location.href = '?modul=$this->Parametr&akcja=edycja&id=$this->ID';"; 
                                                echo "</script>\n";
                                            }else{
                                                echo "<script type='text/javascript'>\n";
                                                    echo "window.location.href = '?modul=$this->Parametr&akcja=wyslij&id=$this->ID';";
                                                echo "</script>\n";
                                            }
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}else{
                            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                            $this->ZleceniaKlienta = $this->GetZlecenia($Wartosci['klient_id']);
                            $Formularz->UstawOpcjePola("nowe_zlecenia", "elementy", $this->ZleceniaKlienta, false);
                        }
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
                        $DaneDomyslne = $this->PobierzDaneDomyslne();
                        $this->ZleceniaKlienta = $this->GetZlecenia($Wartosci['klient_id']);
			$Formularz = $this->GenerujFormularz();
			$Formularz->Wyswietl($DaneDomyslne, false);
		}
	}

        function AkcjaEdycja($ID) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
                                echo "<div style='clear: both;'></div>\n";
                                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
				if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
                                        unset($Wartosci['statusy']);
                                        $Wartosci[$this->PoleNazwy] = date("Y-m-d H:i:s");
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow(), $ID)) {
						echo "<script type='text/javascript'>\n";
                                                    echo "window.location.href = '?modul=$this->Parametr&akcja=wyslij&id=$ID';";
                                                echo "</script>\n";
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem! Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}else{
                            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                            $this->ZleceniaKlienta = $this->GetZlecenia($Wartosci['klient_id']);
                            $Formularz->UstawOpcjePola("nowe_zlecenia", "elementy", $this->ZleceniaKlienta, false);
                        }
                        if($OpcjaFormularza == "dodaj_nowe"){
                            $this->ZapiszZleceniaDoRaportu($Wartosci, $ID);
                        }
                        if($OpcjaFormularza == "zapisz_zmiany"){
                            foreach($_POST["UsunZlecenia"] as $ZID){
                                $this->Baza->Query("DELETE FROM $this->TabelaZlecenia WHERE $this->PoleID = '$ID' AND zlecenie_id = '$ZID'");
                                $this->Baza->Query("DELETE FROM $this->TabelaKontenery WHERE $this->PoleID = '$ID' AND zlecenie_id = '$ZID'");
                            }
                            
                            $this->Baza->Query("DELETE FROM $this->TabelaKontenery WHERE $this->PoleID = '$ID'");
                            foreach($Wartosci['statusy'] as $ZID => $Contenery){
                                foreach($Contenery as $ContNumber => $Status){
                                    $Upd2 = array($this->PoleID => $ID, "zlecenie_id" => $ZID);
                                    $Upd2['cont_status'] = $Status;
                                    $Upd2['cont_number'] = $ContNumber;
                                    $Zap3 = $this->Baza->PrepareInsert($this->TabelaKontenery, $Upd2);
                                    $this->Baza->Query($Zap3);
                                }
                            }
                            $this->AktualizujSeaOrder($_POST['SeaOrder']);
                        }
                        $Wartosci['statusy'] = $this->PobierzZlecenia($ID);
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
                        $this->ShowTitleDiv($ID, $_POST);
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
                    $Dane = $this->PobierzDaneElementu($ID);
                    $Dane['statusy'] = $this->PobierzZlecenia($ID);
                    $this->ZleceniaKlienta = $this->GetZlecenia($Dane['klient_id']);
                    $Formularz = $this->GenerujFormularz($Dane);
                    $this->ShowTitleDiv($ID, $Dane);
                    $Formularz->Wyswietl($Dane, false);
		}
	}
        
        function AkcjaDuplikacja($ID) {
                $DodajOdRazu = false;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularz($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if (in_array($OpcjaFormularza, array('zapisz', 'zapisz_zmiany', 'dodaj_nowe')) || $DodajOdRazu){
                                echo "<div style='clear: both;'></div>\n";
                                if($DodajOdRazu){
                                    $Wartosci['klient_id'] = $ClientID;
                                    $Wartosci["nowe_zlecenia"] = $ZlecenieIds;
                                }else{
                                    $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                                }
				if($this->SprawdzDane($Wartosci) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
                                        $Wartosci[$this->PoleNazwy] = date("Y-m-d H:i:s");
                                        $Wartosci['id_uzytkownik'] = $this->UserID;
                                        $Wartosci['id_oddzial'] = $_SESSION['id_oddzial'];
					if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
                                            $Hash = md5($this->HashPrefix.$this->ID);
                                            $Update['hash'] = $Hash;
                                            $ZapUpd = $this->Baza->PrepareUpdate($this->Tabela, $Update, array($this->PoleID => $this->ID));
                                            $this->Baza->Query($ZapUpd);
                                            if($OpcjaFormularza == "dodaj_nowe" || $DodajOdRazu){
                                                $this->ZapiszZleceniaDoRaportu($Wartosci, $this->ID);
                                                $this->AktualizujSeaOrder($_POST['SeaOrder']);
                                                echo "<script type='text/javascript'>\n";
                                                    echo "window.location.href = '?modul=$this->Parametr&akcja=edycja&id=$this->ID';"; 
                                                echo "</script>\n";
                                            }else{
                                                echo "<script type='text/javascript'>\n";
                                                    echo "window.location.href = '?modul=$this->Parametr&akcja=wyslij&id=$this->ID';";
                                                echo "</script>\n";
                                            }
					}
					else {
						Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}else{
                            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
                            $this->ZleceniaKlienta = $this->GetZlecenia($Wartosci['klient_id']);
                            $Formularz->UstawOpcjePola("nowe_zlecenia", "elementy", $this->ZleceniaKlienta, false);
                        }
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
                        $Dane = $this->PobierzDaneElementu($ID);
                        $Dane['statusy'] = $this->PobierzZlecenia($ID);
                        $this->ZleceniaKlienta = $this->GetZlecenia($Dane['klient_id']);
                        $Formularz = $this->GenerujFormularz($Dane);
                        $this->ShowTitleDiv($ID, $Dane);
                        $Formularz->Wyswietl($Dane, false);
		}
	}

        function ZapiszZleceniaDoRaportu($Wartosci, $ID){
            foreach($Wartosci["nowe_zlecenia"] as $ZID){
                $Save[$this->PoleID] = $ID;
                $Save['zlecenie_id'] = $ZID;
                $Zap = $this->Baza->PrepareInsert($this->TabelaZlecenia, $Save);
                $this->Baza->Query($Zap);
            }
        }

        function ZapiszDaneSzczegolowe($Wartosci, $Typ, $Pole){
            switch($Typ){
                case "dodaj_zlecenie_do_raportu":
                case "zlecenia_w_raporcie":
                    unset($Wartosci[$Pole]);
                    break;
            }
            return $Wartosci;
        }

        function AkcjeNiestandardowe($ID){
            switch($this->WykonywanaAkcja){
                case "wyslij":
                    $this->AkcjaWyslij($ID);
                    break;
            }
	}

        function AkcjaWyslij($ID){
            $Dane = $this->PobierzDaneElementu($ID);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$Formularz = $this->GenerujFormularzWyslij($_POST);
			$PolaWymagane = $Formularz->ZwrocPolaWymagane();
			$PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
			$OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
			if ($OpcjaFormularza == 'zapisz') {
                                echo "<div style='clear: both;'></div>\n";
                                $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
				if($this->SprawdzDane($Wartosci, $ID) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci, $ID)){
					if ($this->WyslijRaport($Formularz, $Wartosci, $ID)) {
						Usefull::ShowKomunikatOK('<b>E-mail został wysłany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>');
						return;
					}
					else {
						Usefull::ShowKomunikatError('<b>Błąd. E-mail nie został wysłany.</b><br/>'.$this->Baza->GetLastErrorDescription());
					}
				}else{
					Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
				}
			}
			foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
			foreach($this->PolaZdublowane as $NazwaPola){
				$Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
			}
                        $this->ShowTitleDiv($ID, $_POST);
			$Formularz->Wyswietl($Wartosci, false);
		}
		else {
			$Formularz = $this->GenerujFormularzWyslij($Dane);
                        $this->ShowTitleDiv($ID, $Dane);
			$Formularz->Wyswietl($Dane, false);
		}
        }

        function WyslijRaport($Formularz, $Wartosci, $ID){
            $Hash = md5($this->HashPrefix.$ID);
            $this->Baza->Query("UPDATE $this->Tabela SET hash = '$Hash' WHERE $this->PoleID = '$ID'");
            $Mail = new MailSMTP($this->Baza);
            $tresc_maila = $this->GetEmailTresc($Hash);
            $SendEmails = "";
            $NoError = true;
            foreach($Wartosci['email']['lista'] as $Email){
                if (!$Mail->SendEmail($Email, $this->GetEmailTitle(), $tresc_maila)){
                        $NoError = false;
                }else{
                        $SendEmails .= ($SendEmails != "" ? ", $Email" : $Email);
                }
            }
            if($Wartosci['email']['email_dodatkowy'] != ""){
                    $Email = $Wartosci['email']['email_dodatkowy'];
                    if (!$Mail->SendEmail($Email, $this->GetEmailTitle(), $tresc_maila)){
                        $NoError = false;
                    }else{
                        $SendEmails .= ($SendEmails != "" ? ", $Email" : $Email);
                    }
            }
            $this->Baza->Query("UPDATE $this->Tabela SET send_email = '$SendEmails' WHERE $this->PoleID = '$ID'");
            if(!$NoError){
                return false;
            }
            return true;
        }

        function GetEmailTitle(){
            return "MEPP TRACKING";
        }

        function GetEmailTresc($Hash){
            $tresc_maila = "Please find current status of delivery your shipments<br /><br />Po klinknieciu w poniższy link znajdziecie Państwo aktualny raport ze statusami przesyłek.<br /><br />";
            $tresc_maila .= "<a href='http://plus.meppeurope.com/raports/raport_morski.php?check=$Hash'>http://plus.meppeurope.com/raports/raport_morski.php?check=$Hash</a><br /><br />\n";
            $tresc_maila .= "Thank you for using our service.<br />Dziękujemy za skorzystanie z naszych usług.<br /><br />\n";
            $tresc_maila .= "MEPP Europe Team<br /><br />\n";
            $tresc_maila .= "Please don't replay on this mail.<br />If you have any urgent request,don't hesitate contact us 24-hour customer service availability.<br />Mobile: +48 697233235<br />mailto: operational@meppeurope.com<br /><br />\n";
            $tresc_maila .= "Prosimy nie odpowiadać na poniższego maila.<br />W przypadku pilnych zapytań, pozostajemy do Państwa dyspozycji całą dobę.<br />tel kom: +48 697233235<br />e-mail: operational@meppeurope.com<br /><br />\n";
            return $tresc_maila;
        }
        
        function AkcjaDrukuj($ID){
            $Raport = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE hash = '{$_GET['check']}'");
            if($Raport){
                $Zlecenia = $this->Baza->GetResultAsArray("SELECT zl.*, pzl.zlecenie_status FROM $this->TabelaZlecenia pzl
                                                            JOIN  orderplus_sea_orders zl ON(zl.id_zlecenie = pzl.zlecenie_id)
                                                            WHERE $this->PoleID = '{$Raport[$this->PoleID]}'", "id_zlecenie");
                $Zlecenia = $this->PobierzKontenery($Zlecenia);
                $Client = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Raport['klient_id']}'");
                $Size = UsefullBase::GetSizes($this->Baza);
                $Types = UsefullBase::GetTypes($this->Baza);
                include(SCIEZKA_SZABLONOW."druki/raport_morski_klient.tpl.php");
            }
        }

        function &PobierzDaneDomyslne() {
            $Dane = array('klient_id' => 0);
            
            return $Dane;
	}
        
        function GetZlecenia($ClientID){
            return $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_sea_orders WHERE ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0)) AND nabywca_id = '$ClientID' ORDER BY data_zlecenia DESC");
        }
        
        function PobierzKontenery($Zlecenia){
            foreach($Zlecenia as $zlec_id => $dane){
                if($dane['mode'] == "LCL"){
                    $Zlecenia[$zlec_id]['kontenery'] = $this->Baza->GetRows("SELECT sol.*, st.cont_status FROM orderplus_sea_orders_lcl sol LEFT JOIN $this->TabelaKontenery st ON(st.cont_number = order_fcl_id AND st.zlecenie_id = '$zlec_id') WHERE sol.id_zlecenie = '$zlec_id'");
                }else{
                    $Zlecenia[$zlec_id]['kontenery'] = $this->Baza->GetRows("SELECT sol.*, st.cont_status FROM orderplus_sea_orders_fcl sol LEFT JOIN $this->TabelaKontenery st ON(st.cont_number = order_fcl_id AND st.zlecenie_id = '$zlec_id') WHERE sol.id_zlecenie = '$zlec_id'");
                }
            }
            return $Zlecenia;
        }
        
        function AktualizujSeaOrder($SeaOrderData){
            foreach($SeaOrderData as $order_id => $order_data){
                $zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders", $order_data, array('order_id' => $order_id));
                $this->Baza->Query($zapytanie);
            }
        }
}
?>
