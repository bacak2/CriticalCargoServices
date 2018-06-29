<?php
    include("configure.php");
    $_SESSION['TabelaRozliczenWidok'][$_POST['param']] = ($_POST['check'] ? true : false);
?>