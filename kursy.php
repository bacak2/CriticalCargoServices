<?php
ini_set('error_log', '_error_log/error.log');
ini_set('display_errors', '1');
error_reporting(E_ERROR);
define('SCIEZKA_OGOLNA', '');
define('SCIEZKA_INCLUDE', 'include/');
define('SCIEZKA_KLAS', SCIEZKA_INCLUDE.'classes/');
define('SCIEZKA_SZABLONOW', SCIEZKA_INCLUDE.'templates/');
define('SCIEZKA_MODULOW', SCIEZKA_KLAS.'modules/');
define('SCIEZKA_FORMULARZY', SCIEZKA_KLAS.'forms/');
define('SCIEZKA_DANYCH', $PathCron.'data/');
define('SCIEZKA_DANYCH_MODULOW', SCIEZKA_DANYCH.'modules/');
include("include/db_access.php");
include("include/classes.php");
$DBConnectionSettings = new DBConnectionSettings($BazaParametry);
$Baza = new DBMySQL($DBConnectionSettings);
$Kursy = new Kursy($Baza);
$Kursy->PobierzDzisiejszeKursy();
?>