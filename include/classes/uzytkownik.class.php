<?php
/**
 * Zarządzanie użytkownikami aplikacji
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Zarządzanie użytkownikami aplikacji
 *
 */
class Uzytkownik {

	/**
	 * Tekst służący do generowania klucza identyfikującego użytkownika.
	 * UWAGA! Po zmianie wartości, założone już konta przestaną funkcjonować!
	 *
	 */
	const HASH = '389^&ashrtyfjsd72sofmndbfvuin9d789indb782bft56';
	
	private $Baza = null;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o kontach użytkowników.
	 *
	 * @var string
	 */
	private $TabelaUzytkownik;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które można wykonać poprzez aplikację.
	 *
	 * @var string
	 */
	private $TabelaOperacja;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególny uzytkownik.
	 *
	 * @var string
	 */
	private $TabelaUprawnieniaUzytkownika;
	/**
	 * Nazwa tabeli w bazie danych przechowująca dane o operacjach które może wyknać poszczególna grupa.
	 * 
	 * @var string
	 */
	private $TabelaUprawnieniaGrupy;
	private $PoleLogin;
	private $PoleHash;
	private $PoleHaslo;	
	private $PoleID;
	private $PoleUprawnienia;
	private $PoleStatus;
        private $LastID;
        private $OdswiezUprawnienia = true;
        public $DaneKlienta = array();

	/**
	 * Konstruktor
	 *
	 */
	function __construct(&$Baza, $TabelaUzytkownik, $TabelaOperacja = null, $TabelaUprawnieniaUzytkownika = null, $TabelaUprawnieniaGrupy = null) {
		$this->Baza = $Baza;
		$this->TabelaUzytkownik = $TabelaUzytkownik;
		$this->TabelaOperacja = $TabelaOperacja;
		$this->TabelaUprawnieniaUzytkownika = $TabelaUprawnieniaUzytkownika;
		$this->TabelaUprawnieniaGrupy = $TabelaUprawnieniaGrupy;
		$this->PoleLogin = "login";
		$this->PoleHash = "hash";
		$this->PoleHaslo = "haslo_hash";
		$this->PoleID = "id_uzytkownik";
		$this->PoleStatus = "blokada";
		$this->PoleUprawnienia = "uprawnienia_id";
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i haśle.
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function CzyIstnieje($Login, $Haslo){
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHaslo = '$Haslo' AND $this->PoleStatus = '0'"));
	}

	/**
	 * Sprawdza czy istnieje użytkownik o podanym loginie i kluczu hash.
	 *
	 * @param string $Login
	 * @param string $Hash
	 * @return boolean
	 */
	function CzyIstniejeHash($Login, $Hash) {
		return (1 == $this->Baza->GetValue("SELECT count(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '$Login' AND $this->PoleHash = '$Hash'"));
	}

	/**
	 * Wyznacza klucz hash użytkownika na podstawie loginu i stałej
	 *
	 * @param string $Login
	 * @return string
	 */
	function WyznaczHash($Login) {
		return md5($Login.self::HASH);
	}

	/**
	 * Sprawdza czy aktualny użytkownik jest zalogowany.
	 *
	 * @return boolean
	 */
	function CzyZalogowany() {
		if (isset($_SESSION['login']) && isset($_SESSION['hash']) && ($_SESSION['hash'] == $this->WyznaczHash($_SESSION['login'])) && $this->CzyIstniejeHash($_SESSION['login'], $_SESSION['hash'])) {
                        if($this->OdswiezUprawnienia){
                            $DaneKlienta = $this->Baza->GetData("SELECT * FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '{$_SESSION['login']}' AND $this->PoleHash = '{$_SESSION['hash']}'");
                            //$Uprawnienia = $this->Baza->GetValue("SELECT uprawnienia FROM orderplus_uprawnienia WHERE uprawnienia_id = '{$DaneKlienta[$this->PoleUprawnienia]}'");
                            $this->DaneKlienta['uprawnienia_id'] = $DaneKlienta[$this->PoleUprawnienia];
                            $this->DaneKlienta['uprawnienia'] = $DaneKlienta['uprawnienia'];
                            $_SESSION['uprawnienia_id'] = $this->DaneKlienta['uprawnienia_id'];
                            $_SESSION['uprawnienia'] = $this->DaneKlienta['uprawnienia'];
                            $_SESSION['aplikacja_dostep'] = unserialize($DaneKlienta['aplikacja_dostep']);
                            $_SESSION['raporty_dostep'] = unserialize($DaneKlienta['raporty_dostep']);
                            $_SESSION['oddzialy_dostep'] = unserialize($DaneKlienta['oddzialy_dostep']);
                        }
                        if(count($_SESSION['aplikacja_dostep']) == 1){
                            $_SESSION['Aplikacja'] = $_SESSION['aplikacja_dostep'][0];
                        }
			return true;
		}
		return false;
	}

	/**
	 * Loguje użytkownika do aplikacji
	 *
	 * @param string $Login
	 * @param string $Haslo
	 * @return boolean
	 */
	function Zaloguj($Login, $Haslo) {
		if($Login != '' && $Haslo != '' && $this->CzyIstnieje($Login, md5($Haslo))) {
                    $_SESSION['login'] = $Login;
                    $_SESSION['hash'] = $this->WyznaczHash($Login);
                    $DaneKlienta = $this->Baza->GetData("SELECT * FROM $this->TabelaUzytkownik WHERE $this->PoleLogin = '{$_SESSION['login']}' AND $this->PoleHash = '{$_SESSION['hash']}'");
                    $_SESSION['id_uzytkownik'] = $DaneKlienta['id_uzytkownik'];
                    $_SESSION['id_oddzial'] = $DaneKlienta['id_oddzial'];
                    $_SESSION['nazywasie'] = $DaneKlienta['imie']." ".$DaneKlienta['nazwisko'];
                    $_SESSION['oddzial'] = $DaneKlienta['oddzial'];
                    //$Uprawnienia = $this->Baza->GetValue("SELECT uprawnienia FROM orderplus_uprawnienia WHERE uprawnienia_id = '{$DaneKlienta[$this->PoleUprawnienia]}'");
                    $this->DaneKlienta['uprawnienia_id'] = $DaneKlienta[$this->PoleUprawnienia];
                    $this->DaneKlienta['uprawnienia'] = $DaneKlienta['uprawnienia'];
                    $_SESSION['uprawnienia_id'] = $this->DaneKlienta['uprawnienia_id'];
                    $_SESSION['uprawnienia'] = $this->DaneKlienta['uprawnienia'];
                    $_SESSION['aplikacja_dostep'] = unserialize($DaneKlienta['aplikacja_dostep']);
                    $_SESSION['raporty_dostep'] = unserialize($DaneKlienta['raporty_dostep']);
                    $_SESSION['oddzialy_dostep'] = unserialize($DaneKlienta['oddzialy_dostep']);
                    $_SESSION['okresStart'] = date("Y-m");
                    $_SESSION['okresEnd'] = date("Y-m");
                    $this->Baza->Query("INSERT INTO logowania SET id_uzytkownik = '{$_SESSION['id_uzytkownik']}', login = now(), logout = null");
                    if(count($_SESSION['aplikacja_dostep']) == 1){
                        $_SESSION['Aplikacja'] = $_SESSION['aplikacja_dostep'][0];
                    }
                    return true;
		}
		return false;
	}

	/**
	 * Wylogowuje użytkownika z aplikacji.
	 *
	 */
	function Wyloguj()
	{
                $this->Baza->Query("UPDATE logowania SET logout = now() WHERE id_uzytkownik = '{$_SESSION['id_uzytkownik']}' ORDER BY login DESC LIMIT 1");
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
	}

	/**
	 * Sprawdza czy aktualny użytkownik posiada podane uprawnienie.
	 *
	 * @param string $Uprawnienie
	 * @return boolean
	 */
	function SprawdzUprawnienie($Uprawnienie, $TablicaUprawnien) {
            if(in_array($_SESSION['uprawnienia_id'], array(1,4)) || (count($TablicaUprawnien) == 1 && $TablicaUprawnien[0] == "*")){
                    return true;
            }else{
                    return in_array($Uprawnienie, $TablicaUprawnien);
            }
	}
	
	/**
	 * Dodaje użytkownika do aplikacji.
	 *
	 * @param array Dane z formularza
	 * @return boolean
	 */
	function Dodaj($Dane) {
                $UprawnieniaKolumn = $Dane['uprawnienia_kolumn'];
                unset($Dane['uprawnienia_kolumn']);
		$Dane[$this->PoleHaslo] = md5($Dane['pass']);
                $Dane['haslo'] = $Dane['pass'];
		unset($Dane['pass']);
		$Dane[$this->PoleHash] = $this->WyznaczHash($Dane[$this->PoleLogin]);
                $Dane['uprawnienia'] = $this->ZapiszUprawnienia($Dane['uprawnienia']);
                $Dane = $this->UstawUprawnienia($Dane);
		$Zapytanie = $this->Baza->PrepareInsert($this->TabelaUzytkownik, $Dane);
		if($this->Baza->Query($Zapytanie)) {
                    $ID = $this->Baza->GetLastInsertId();
                    $this->ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID);
                    return true;
		}
	}
	
	function Edytuj($ID, $Dane) {
            if(isset($Dane['pass']) && $Dane['pass'] != ""){
                $Dane[$this->PoleHaslo] = md5($Dane['pass']);
                $Dane['haslo'] = $Dane['pass'];
            }
            unset($Dane['pass']);
            $UprawnieniaKolumn = $Dane['uprawnienia_kolumn'];
            unset($Dane['uprawnienia_kolumn']);
            $Dane[$this->PoleHash] = $this->WyznaczHash($Dane[$this->PoleLogin]);
            $Dane['uprawnienia'] = $this->ZapiszUprawnienia($Dane['uprawnienia']);
            $Dane = $this->UstawUprawnienia($Dane);
            $Zapytanie = $this->Baza->PrepareUpdate($this->TabelaUzytkownik, $Dane, array($this->PoleID => $ID));
            if($this->Baza->Query($Zapytanie)) {
                $this->ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID);
                return true;
            }
            return false;
	}

        function ZapiszUprawnienia($Upr){
            $Uprawnienia = implode(',', $Upr['nad']);
            if(isset($Upr['pod'])){
                foreach($Upr['pod'] as $Param => $Pody){
                    foreach($Pody as $Pod){
                        if(!in_array($Param, $Upr['nad'])){
                            $Uprawnienia .= ($Uprawnienia != "" ? "," : "").$Param;
                        }
                        if($Pod == "tabela_rozliczen_nowa"){
                            $Uprawnienia .= ($Uprawnienia != "" ? "," : "")."zlecenia";
                        }
                        $Uprawnienia .= ($Uprawnienia != "" ? "," : "").$Pod;
                    }
                }
            }
            return $Uprawnienia;
        }

        function ZapiszUprawnieniaKolumn($UprawnieniaKolumn, $ID){
            $this->Baza->Query("DELETE FROM orderplus_uzytkownik_tabela_rozliczen WHERE id_uzytkownik = '$ID'");
            foreach($UprawnieniaKolumn as $Widok => $Kolumny){
                foreach($Kolumny as $Kolumna){
                    $Zap['id_uzytkownik'] = $ID;
                    $Zap['tabela_widok'] = $Widok;
                    $Zap['tabela_kolumna'] = $Kolumna;
                    $Zapytanie = $this->Baza->PrepareInsert("orderplus_uzytkownik_tabela_rozliczen", $Zap);
                    $this->Baza->Query($Zapytanie);
                }
            }
        }

        function UstawUprawnienia($Dane){
            if(!isset($Dane['aplikacja_dostep'])){
                $Dane['aplikacja_dostep'] = array();
            }
            $Dane['aplikacja_dostep'] = serialize($Dane['aplikacja_dostep']);
            if(!isset($Dane['raporty_dostep'])){
                $Dane['raporty_dostep'] = array();
            }
            $Dane['raporty_dostep'] = serialize($Dane['raporty_dostep']);
            if(!isset($Dane['oddzialy_dostep'])){
                $Dane['oddzialy_dostep'] = array();
            }
            $Dane['oddzialy_dostep'][] = $Dane['id_oddzial'];
            $Dane['oddzialy_dostep'] = serialize($Dane['oddzialy_dostep']);
            return $Dane;
        }

	/**
	 * Zmienia hasło podanego użytkownika.
	 *
	 * @param integer $ID
	 * @param string $HasloNowe
	 * @param string $HasloNowePowtorzenie
	 * @return boolean
	 */
	function ZmienHaslo($ID, $HasloNowe, $HasloNowePowtorzenie) {
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleHaslo='".md5($HasloNowe)."' WHERE $this->PoleID='$ID'")) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Usuwa konto użytkownika z aplikacji.
	 *
	 * @param integer $ID
	 * @return boolean
	 */
	function Usun($ID) {
		if($this->Baza->Query("DELETE from $this->TabelaUzytkownik WHERE $this->PoleID='$ID'")) {
			return true;
		}
		return false;
	}
	
	function Blokuj($ID){
		if($this->Baza->Query("UPDATE $this->TabelaUzytkownik SET $this->PoleStatus = 1-$this->PoleStatus WHERE $this->PoleID = '$ID'")){
			return true;
		}
		return false;
	}
	
	function CzyZablokowany($ID){
		if($this->Baza->GetValue("SELECT $this->PoleStatus FROM $this->TabelaUzytkownik WHERE $this->PoleID = '$ID'") == 1){
			return true;
		}
		return false;
	}
	
	function ZwrocTabliceUprawnien(){
		$Tab = array();
		$Tab = explode(",", $_SESSION['uprawnienia']);
		return $Tab;
	}
	
	function ZwrocIdUzytkownika($Login, $Hash) {
		return $this->Baza->GetValue("SELECT $this->PoleID FROM $this->TabelaUzytkownik WHERE $this->PoleLogin='$Login' AND $this->PoleHash='$Hash'");
	}
	
	function SprawdzPowtorzoneHaslo($HasloNowe, $HasloNowePowtorzenie){
		if(($HasloNowe != '') && ($HasloNowe == $HasloNowePowtorzenie)) {
			return true;
		}
		return false;		
	}
	
	function CzyNieZdublowanoLoginu($Login, $ID = null){
		if($this->Baza->GetValue("SELECT COUNT(*) FROM $this->TabelaUzytkownik WHERE $this->PoleLogin='$Login' ".(!is_null($ID) ? "AND $this->PoleID != '$ID'" : "")."") == 0) {
			return true;
		}
		return false;
	}

        function GetLastUserId(){
            return $this->Baza->GetLastInsertId();
        }

        function CheckNoOddzial(){
            $NoOddzial = array(1,4,5);
            if(in_array($_SESSION['uprawnienia_id'], $NoOddzial)){
                return false;
            }
            return true;
        }

        function IsAdmin(){
            if(in_array($_SESSION["uprawnienia_id"], array(1,4))){
               return true;
            }
            return false;
        }

        function MarzaAccess(){
            if(in_array($_SESSION["uprawnienia_id"], array(1,2,4))){
               return true;
            }
            return false;
        }

        function GetUprawnieniaID(){
            return $_SESSION['uprawnienia_id'];
        }

        function GetUprawnieniaBazowe($UprId){
            $Dane = $this->Baza->GetData("SELECT * FROM orderplus_uprawnienia WHERE uprawnienia_id = '$UprId'");
            return $Dane;
        }

        function DostepDoRaportu($Raport){
            if($this->IsAdmin() || in_array($Raport, $_SESSION['raporty_dostep'])){
                return true;
            }
            return false;
        }

}
?>
