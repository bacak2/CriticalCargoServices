<?php
session_start();
setlocale(LC_ALL, 'pl_PL.UTF-8');
ini_set('error_log', '_error_log/error.log');

if($_SESSION['login'] == "artplusadmin"){
    ini_set('display_errors', '1');
    error_reporting(E_ALL ^E_NOTICE ^E_WARNING);
}else{
    ini_set('display_errors', '1');
    error_reporting(E_ALL ^E_NOTICE ^E_WARNING ^E_DEPRECATED ^E_STRICT);
}
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
?>