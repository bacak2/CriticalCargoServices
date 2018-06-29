<?php
/**
 * Moduł klienci - raporty
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class KlienciRaporty extends ModulBazowy {
        public $Emaile;
        public $ZleceniaKlienta;
        public $TabelaZlecenia;
        public $HashPrefix;
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_klient_raport';
            $this->TabelaZlecenia = 'orderplus_klient_raport_zlecenie';
            $this->PoleID = 'raport_id';
            $this->PoleNazwy = 'raport_date';
            $this->Nazwa = 'Raporty';
            $this->CzySaOpcjeWarunkowe = true;
            $this->PrzyciskiFormularza['zapisz'] = array('etykieta' => 'Wyślij', 'src' => 'wyslij.gif', 'type' => 'button');
            $this->HashPrefix = "RaportKlient";
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('klient_id', 'lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->GetKlienci(), 'wybierz' => true, 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza", "klient_change")')));
            $Formularz = $this->FormDodajPoleZlecenia($Formularz);
            //$Formularz->DodajPole('wybierz_jezyk', 'wybierz_jezyk_maila', 'Wybierz język', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(1 => 'polski', 2 => 'czeski')));
            $Formularz->DodajPole('nowe_zlecenia', 'dodaj_zlecenie_do_raportu', 'Dodaj nowe zlecenia', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->ZleceniaKlienta));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function FormDodajPoleZlecenia($Formularz){
            $Formularz->DodajPole('statusy', 'zlecenia_w_raporcie', 'Zlecenia w raporcie', array('tabelka' => Usefull::GetFormStandardRow()));
            return $Formularz;
        }

        function &GenerujFormularzWyslij() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('email', 'wyslij_raport', null, array('tabelka' => Usefull::GetFormWithoutTHRow(), 'elementy' => $this->Emaile));
            $Formularz->DodajPole('tekst','wybierz_jezyk', null, array('tabelka' => Usefull::GetFormWithoutTHRow(), 'elementy' => $this->Emaile));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podgląd", "akcja_link" => "raports/raport.php?check={$Dane["hash"]}");
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
            return $this->Baza->GetResultAsArray("SELECT zlecenie_id, numer_zlecenia as numer, zlecenie_status as status FROM $this->TabelaZlecenia tz JOIN orderplus_zlecenie z ON(z.id_zlecenie = tz.zlecenie_id) WHERE $this->PoleID = '$ID'", "zlecenie_id");
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
                    $Zlecenie = $this->Baza->GetData("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie = '{$_GET['id']}'");
                    $ZlecenieIds = array($_GET['id']);
                    $ClientID = $Zlecenie['id_klient'];
                    $DodajOdRazu = true;
                }
                if(isset($_POST['OrdersIDs']) && $_POST['OrdersIDs'] != ""){
                    $ids = explode(",", $_POST['OrdersIDs']);
                    $ClientID = false;
                    $ZlecenieIds = array();
                    foreach($ids as $ZlecID){
                        $CID = $this->Baza->GetValue("SELECT id_klient FROM orderplus_zlecenie WHERE id_zlecenie = '$ZlecID'");
                        if($ClientID == false){
                            $ClientID = $CID;
                        }
                        if($ClientID == $CID){
                            $ZlecenieIds[] = $ZlecID;
                        }
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
                            $this->ZleceniaKlienta = UsefullBase::GetZlecenia($this->Baza, $Wartosci['klient_id']);
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
                        $this->ZleceniaKlienta = UsefullBase::GetZlecenia($this->Baza);
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
                            $this->ZleceniaKlienta = UsefullBase::GetZlecenia($this->Baza, $Wartosci['klient_id']);
                            $Formularz->UstawOpcjePola("nowe_zlecenia", "elementy", $this->ZleceniaKlienta, false);
                        }
                        if($OpcjaFormularza == "dodaj_nowe"){
                            $this->ZapiszZleceniaDoRaportu($Wartosci, $ID);
                        }
                        if($OpcjaFormularza == "zapisz_zmiany"){
                            foreach($_POST["UsunZlecenia"] as $ZID){
                                    mysql_query("DELETE FROM $this->TabelaZlecenia WHERE $this->PoleID = '$ID' AND zlecenie_id = '$ZID'");
                            }
                            foreach($Wartosci['statusy'] as $ZID => $Status){
                                $Upd2['zlecenie_status'] = $Status;
                                $Zap3 = $this->Baza->PrepareUpdate($this->TabelaZlecenia, $Upd2, array($this->PoleID => $ID, "zlecenie_id" => $ZID));
                                $this->Baza->Query($Zap3);
                            }
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
                    $this->ZleceniaKlienta = UsefullBase::GetZlecenia($this->Baza, $Dane['klient_id']);
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
					if ($this->WyslijRaport($Formularz, $Wartosci, $ID, $_POST['lang'])) {
						Usefull::ShowKomunikatOK('<b>E-mail został wysłany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>');
						return;
                                                //var_dump($_POST['lang']);
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
                    //var_dump($Dane);
			$Formularz = $this->GenerujFormularzWyslij($Dane);
                        $this->ShowTitleDiv($ID, $Dane);
                        
			$Formularz->Wyswietl($Dane, false);
                        
                        //echo '<pre>';
                        //var_dump($Formularz);
                        //echo '</pre>';
		}
        }

        function WyslijRaport($Formularz, $Wartosci, $ID, $lang = 'en'){
            $Hash = md5($this->HashPrefix.$ID);
            $this->Baza->Query("UPDATE $this->Tabela SET hash = '$Hash' WHERE $this->PoleID = '$ID'");
            $Mail = new MailSMTP($this->Baza);
            $tresc_maila = $this->GetEmailTresc($Hash, $lang);
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
            return "CRITICAL-CS TRACKING";
        }

        function GetEmailTresc($Hash, $lang='en'){
            if($lang == 'cz') {
                $tresc_maila = "Please find current status of delivery your shipments<br /><br />";
                $tresc_maila .= "<a href='http://orderplus.critical-cs.com/raports/raport.php?lang=$lang&check=$Hash'>Otwórz raport</a><br /><br />\n";
                $tresc_maila .= "Thank you for using our service.<br /><br />\n";
                $tresc_maila .= "Critical Cargo Services Team<br /><br />\n";
            } else {
                $tresc_maila = "Please find current status of delivery your shipments<br /><br />Po klinknieciu w poniższy link znajdziecie Państwo aktualny raport ze statusami przesyłek.<br /><br />";
                $tresc_maila .= "<a href='http://orderplus.critical-cs.com/raports/raport.php?lang=$lang&check=$Hash'>Otwórz raport</a><br /><br />\n";
                $tresc_maila .= "Thank you for using our service.<br />Dziękujemy za skorzystanie z naszych usług.<br /><br />\n";
                $tresc_maila .= "Critical Cargo Services Team<br /><br />\n";
                $tresc_maila .= "Please don't replay on this mail.<br />If you have any urgent request,don't hesitate contact us 24-hour customer service availability.<br />Mobile: +48 669 609 004<br />mailto: office@critical-cs.com<br /><br />\n";
                $tresc_maila .= "Prosimy nie odpowiadać na poniższego maila.<br />W przypadku pilnych zapytań, pozostajemy do Państwa dyspozycji całą dobę.<br />tel kom: +48 669 609 004<br />e-mail: office@critical-cs.com<br /><br />\n";
            }
            
            return $tresc_maila;
        }
        
        function AkcjaDrukuj($ID){
            $Raport = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE hash = '{$_GET['check']}'");
            $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
            if($Raport){
                $Zlecenia = $this->Baza->GetResultAsArray("SELECT zl.*, pzl.zlecenie_status FROM orderplus_klient_raport_zlecenie pzl
                                                            JOIN orderplus_zlecenie zl ON(zl.id_zlecenie = pzl.zlecenie_id)
                                                            WHERE $this->PoleID = '{$Raport[$this->PoleID]}'", "id_zlecenie");
                $Client = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Raport['klient_id']}'");
                $Typy = UsefullBase::GetTypySerwisu($this->Baza);
                
                include(SCIEZKA_SZABLONOW."druki/raport_klient_".$lang.".tpl.php");
                
            }
        }

        function &PobierzDaneDomyslne() {
            $Dane = array('klient_id' => 0);
            
            return $Dane;
	}
}
?>
