<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Action = (isset($_GET['ev']) ? "get-event-content-id" : "get-event-content");
    $Panel->WyswietlAJAX("Zdarzenia", $Action);
?>
