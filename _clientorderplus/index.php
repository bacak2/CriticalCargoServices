<?php
	session_start();
	ini_set("error_log","log/blad.txt");
	#error_reporting(E_ALL | E_WARNING | E_NOTICE | E_PARSE);
	error_reporting(0);
	extract($_GET);
	extract($_POST);
	extract($_SESSION);
	require_once('baza.php');
	include("functions.php");
	function CzyUprawnionyDostep($Login) {
//		$Godzina = date('H:i');
//		$DzienTygodnia = date('w');
//		return (
//			in_array($Login, array('admin', 'ws')) ||
//			(($Login  == 'sobota') && ($DzienTygodnia == 6) && ($Godzina >= '10:00' && $Godzina <= '12:00')) ||
//			(($Login  != 'sobota') && ($DzienTygodnia >= 1 && $DzienTygodnia <= 5) && ($Godzina >= '06:00' && $Godzina <= '17:30'))
//		);
		return true;
	}

	// Wylogowanie
	if (isset($_SESSION['client_login']) && ((isset($_REQUEST['logout']) && ($_REQUEST['logout'] == "tak")) || (!CzyUprawnionyDostep($_SESSION['client_login'])))) {
		$_SESSION = array();
		session_unset();
		session_destroy();
		session_start();
	}

	// Logowanie
	if ((!isset($_SESSION['client_login'])) && isset($_POST['orderplus_login']) && ($_POST['orderplus_login'] != '') && isset($_POST['orderplus_haslo'])) {
		//Lukanie na juzera i haslo
		$hashpass = md5($_POST['orderplus_haslo']);
		$uzytkownicy = mysql_query ("SELECT * FROM orderplus_klient WHERE client_login='{$_POST['orderplus_login']}' AND client_haslo_hash='$hashpass'");
		if (mysql_num_rows($uzytkownicy)) {
			$uzytkownik = mysql_fetch_object($uzytkownicy);
			if (CzyUprawnionyDostep($uzytkownik->client_login)) {
				$_SESSION['zalogowany_id'] = $uzytkownik->id_klient;
				$_SESSION['client_login'] = $uzytkownik->client_login;
				$_SESSION['okres'] = date("Y-m");
			}
		}
		else {
			$Komunikat = 'Błędny login lub hasło.';
		}
	}
	include ("header.php");
	if (isset($_SESSION['client_login'])) {
		include("control_panel.php");
	}
	else {
		include ("formularz.php");

	}
	include ("footer.php");
?>
