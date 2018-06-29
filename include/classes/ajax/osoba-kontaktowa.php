<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAJAX("Kontakty", $_GET['action']);
?>
