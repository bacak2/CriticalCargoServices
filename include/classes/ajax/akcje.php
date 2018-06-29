<?php
    include("configure.php");
    $Panel = new Panel($BazaParametry);
    if($_GET['type'] == "klient"){
        $Panel->WyswietlAJAX("Klienci", "get-action-list");
    }
    if($_GET['type'] == "przewoznik"){
        $Panel->WyswietlAJAX("Przewoznicy", "get-action-list"); 
    }
    if($_GET['type'] == "edytowali"){
        $Panel->WyswietlAJAX("Zlecenia", "get-edytowali");
    }
    if($_GET['type'] == "zlecenie"){ 
        $Panel->WyswietlAJAX("Zlecenia", "get-action-list");
    }
    if($_GET['type'] == "faktura"){
        $Panel->WyswietlAJAX("Faktury", "get-action-list");
    }
    if($_GET['type'] == "platnosci"){
        $Panel->WyswietlAJAX("Platnosci", "get-action-list");
    }
    if($_GET['type'] == "faktura-morska"){
        $Panel->WyswietlAJAX("FakturyMorskie", "get-action-list");
    }
    if($_GET['type'] == "faktura-lotnicza"){
        $Panel->WyswietlAJAX("FakturyLotnicze", "get-action-list");
    }
?>