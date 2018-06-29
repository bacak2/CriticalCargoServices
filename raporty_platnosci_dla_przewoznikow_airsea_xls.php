<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->WyswietlAJAX("XML", "raport_platnosci_airsea", "platnosci_morskie");
?>
