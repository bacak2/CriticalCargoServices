<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->WyswietlAJAX("XML", "klienci_bez_zadan", "klienci");
?>
