<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->WyswietlAJAX("XML", "raport_dopisanych", "client_raport");
?>
