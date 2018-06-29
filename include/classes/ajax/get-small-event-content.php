<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    $Panel->WyswietlAJAX("Zdarzenia", "get-small-event-content-id");
?>
