<?php
	include("configure.php");
	$Panel = new Panel($BazaParametry);
	include("../../include/modules.php");
	$Panel->WyswietlAjax($Panel->GetClassName($_GET['modul']), "tinymce_list", $_GET['modul']);	
?>
