<?php
error_reporting(E_ALL ^ E_DEPRECATED);
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->Wyswietl();
?>
