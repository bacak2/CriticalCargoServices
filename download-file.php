<?php
include("configure.php");
$File = new Download($BazaParametry);
$File->GetFile($_GET);

?>
