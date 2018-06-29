<?php
include("configure.php");

### Przekierowanie wynikające z błędnie wysłanego linku do kilku raportów ####
$Hash[] = "e33780cd078f57943669fccced844015";
$Hash[] = "4c554839da80be700bbc85061ae7fd30";
$Hash[] = "7d5d48484a1b880bedb19140eb2df359";
$Hash[] = "dd2f887cd6723067600eab423681143e";
$Hash[] = "fbb9c05933f2bb1983a7efdbdfdede4e";
$Hash[] = "eb65f75e7f4b969b38e68bc9ebe50795";
$Hash[] = "f876ee47081bdbd2438f84b5a0d80b49";
$Hash[] = "40cf09a813810fb03cda4d25d95a2ad5";
$Hash[] = "8165677c93a606912ddbcb12e32e7f20";
$Hash[] = "a9b8cc91dc5ccaee87bb47e839ab0a74";
$Hash[] = "ba9027429d4ac5209a4f3547ca9f0d91";
$Hash[] = "c5768813f86700a582123bc4a8b47ccb";
$Hash[] = "d1832ed6692b7996c9b6a8762dd08b3b";

$Panel = new Panel($BazaParametry);
include("../include/modules.php");
if(in_array($_GET['check'], $Hash)){
    $Panel->WyswietlDrukuj("KlienciRaporty");
}else{
    $Panel->WyswietlDrukuj("KlienciPotwierdzenia");
}
?>
