<?php
include("configure.php");
ini_set("max_execution_time", 300);
set_time_limit(300);
$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->WyswietlAJAX("XML", "tabela_rozliczen", "tabela_rozliczen");
?>
