<?php
$tekst = "Skrypt uruchomiono ".date ( "c" )."\n"; 
$file = "plik.txt";
$fp = fopen($file, "a");
flock($fp, 2);
fwrite($fp, $tekst);
flock($fp, 3);
fclose($fp);
echo $tekst;
?>