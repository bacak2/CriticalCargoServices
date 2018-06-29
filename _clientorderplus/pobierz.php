<?php
switch ($_GET['nazwa']) {
	case 'krs':
		$Plik = 'pliki/KRS.pdf';
		break;
	case 'nip':
		$Plik = 'pliki/NIP.pdf';
		break;
	case 'regon':
		$Plik = 'pliki/REGON.pdf';
		break;
	default:
		exit();
}
header('Content-type: application/pdf');
header("Content-Disposition: attachment; filename=\"{$_GET['nazwa']}.pdf\"");
header('Content-Length: '.filesize($Plik));
readfile($Plik);
?> 