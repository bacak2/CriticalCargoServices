<?php
session_start();
setlocale(LC_ALL, 'pl_PL.UTF-8');
error_reporting(E_ERROR);
ini_set('error_log', '../../_error_log/error.log');
define('SCIEZKA_INCLUDE', '../../include/');
define('SCIEZKA_KLAS', SCIEZKA_INCLUDE.'classes/');
define('SCIEZKA_SZABLONOW', SCIEZKA_INCLUDE.'templates/');
define('SCIEZKA_MODULOW', SCIEZKA_KLAS.'modules/');
define('SCIEZKA_FORMULARZY', SCIEZKA_KLAS.'forms/');
define('SCIEZKA_DANYCH', '../../data/');
define('SCIEZKA_DANYCH_MODULOW', SCIEZKA_DANYCH.'modules/');

include("../../include/db_access.php");
include("../../include/classes.php");
?>