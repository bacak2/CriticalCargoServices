<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("../include/modules.php");
$Panel->WyswietlDrukuj("ZleceniaMorskie", "podglad_przewoznik");
?>
