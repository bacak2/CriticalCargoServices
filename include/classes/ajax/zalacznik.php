<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAJAX("Zalaczniki", $_GET['action']);
?>
