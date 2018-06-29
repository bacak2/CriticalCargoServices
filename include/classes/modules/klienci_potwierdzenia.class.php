<?php
/**
 * Moduł klienci - potwierdzenia
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class KlienciPotwierdzenia extends KlienciRaporty {
        
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_klient_potwierdzenie';
            $this->TabelaZlecenia = 'orderplus_klient_potwierdzenie_zlecenie';
            $this->PoleID = 'potwierdzenie_id';
            $this->PoleNazwy = 'potwierdzenie_date';
            $this->Nazwa = 'Potwierdzenia';
            $this->HashPrefix = "PotwierdzenieKlient";
            if($_GET['dev'] == "dev"){
                $Zlecenia = $this->Baza->GetRows("SELECT * FROM $this->TabelaZlecenia");
                foreach($Zlecenia as $Dane){
                    $OddzialID = $this->Baza->GetValue("SELECT id_oddzial FROM orderplus_zlecenie WHERE id_zlecenie = '{$Dane['zlecenie_id']}'");
                    if($OddzialID > 0){
                        $this->Baza->Query("UPDATE $this->Tabela SET id_oddzial = '$OddzialID' WHERE $this->PoleID = '{$Dane[$this->PoleID]}'");
                    }
                }
            }
	}

        function FormDodajPoleZlecenia($Formularz){
            $Formularz->DodajPole('statusy', 'zlecenia_w_raporcie', 'Zlecenia w potwierdzeniu', array('tabelka' => Usefull::GetFormStandardRow(), "no_status" => true));
            return $Formularz;
        }

        function PobierzAkcjeNaLiscie($Dane = array()){
		$Akcje = array();
                $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja" => "edycja");
                $Akcje[] = array('img' => "podglad_button", 'title' => "Podgląd", "akcja_link" => "raports/potwierdzenie.php?check={$Dane["hash"]}");
                $Akcje[] = array('img' => "mail_button", 'title' => "Wyślij", "akcja" => "wyslij");
                $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja" => "kasowanie");
		return $Akcje;
	}

        function PobierzZlecenia($ID){
            return $this->Baza->GetResultAsArray("SELECT zlecenie_id, numer_zlecenia as numer FROM $this->TabelaZlecenia tz JOIN orderplus_zlecenie z ON(z.id_zlecenie = tz.zlecenie_id) WHERE potwierdzenie_id = '$ID'", "zlecenie_id");
        }

        function GetEmailTitle(){
            return "CRITICAL-CS ORDER CONFIRMATION";
        }

        function GetEmailTresc($Hash, $lang='en'){
            if($lang == 'cz') {
                $tresc_maila = "Please find the confirmation for organizing transport<br /><br />";
                $tresc_maila .= "<a href='http://orderplus.critical-cs.com/raports/potwierdzenie.php?lang=$lang&check=$Hash'>Otwórz potwierdzenie</a><br /><br />\n";
                $tresc_maila .= "Thank you for using our service.<br /><br />\n";
                $tresc_maila .= "Critical Cargo Services Team<br /><br />\n";
            } else {
                $tresc_maila = "Please find the confirmation for organizing transport<br />Po kliknieciu w poniższy link zobaczą Państwo potwierdzenie przyjęcia zlecenia.<br /><br />";
                $tresc_maila .= "<a href='http://orderplus.critical-cs.com/raports/potwierdzenie.php?lang=$lang&check=$Hash'>Otwórz potwierdzenie</a><br /><br />\n";
                $tresc_maila .= "Thank you for using our service.<br />Dziękujemy za skorzystanie z naszych usług.<br /><br />\n";
                $tresc_maila .= "Critical Cargo Services Team<br /><br />\n";
                $tresc_maila .= "Please don't replay on this mail.<br />If you have any urgent request,don't hesitate contact us 24-hour customer service availability.<br />Mobile: +48 669 609 004<br />mailto: office@critical-cs.com<br /><br />\n";
                $tresc_maila .= "Prosimy nie odpowiadać na poniższego maila.<br />W przypadku pilnych zapytań, pozostajemy do Państwa dyspozycji całą dobę.<br />tel kom: +48 669 609 004<br />e-mail: office@critical-cs.com<br /><br />\n";
                $tresc_maila .= '<br /><br />Critical Cargo and Freight Services Sp. z o.o. <br />al. Solidarności 115/2 00-140 Warszawa,<br /> Poland<br />Telefon 24 H: + 48 669 609 004<br />NIP: PL 525-258-15-65'; 
            }
            
			
			return $tresc_maila;
        }

        function AkcjaDrukuj($ID){
            $Potwierdzenie = $this->Baza->GetData("SELECT * FROM $this->Tabela WHERE hash = '{$_GET['check']}'");
            $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
            if($Potwierdzenie){
                $Zlecenia = $this->Baza->GetResultAsArray("SELECT zl.* FROM orderplus_klient_potwierdzenie_zlecenie pzl
                                                                JOIN orderplus_zlecenie zl ON(zl.id_zlecenie = pzl.zlecenie_id)
                                                                WHERE potwierdzenie_id = '{$Potwierdzenie['potwierdzenie_id']}'", "id_zlecenie");
                $Client = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Potwierdzenie['klient_id']}'");
                $Typy = UsefullBase::GetTypySerwisu($this->Baza);
                include(SCIEZKA_SZABLONOW."druki/potwierdzenie_klient_".$lang.".tpl.php");
            }
        }
}
?>
