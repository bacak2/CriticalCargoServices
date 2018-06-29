<?php
$dbh = mysql_connect("localhost", "ccfsadm_order", "ivLFA6Li") or die ('I cannot connect to the database because: '.mysql_error());
mysql_select_db("ccfsadm_order", $dbh);
mysql_query("SET NAMES 'utf8'");

$NoOddzial = array(1,4,5);
$SecondColor = "#e5f4fb";
$RamkaTabela = "#4679ac";

?>