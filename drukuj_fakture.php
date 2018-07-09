<?php
include("configure.php");

$Panel = new Panel($BazaParametry);
include("include/modules.php");
$Panel->WyswietlDrukuj("Faktury", "wydruk");
if(isset($_GET['spec'])){
    $Panel->WyswietlDrukuj("Specyfikacja", "wydruk");
}
?>
