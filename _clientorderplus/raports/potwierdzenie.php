<?php
session_start();
error_reporting(0);
require_once('../baza.php');
require_once('../functions.php');
$hash = $_GET['check'];

$Potwierdzenie = GetRow("SELECT * FROM orderplus_klient_potwierdzenie WHERE hash = '$hash' AND klient_id = '{$_SESSION['zalogowany_id']}'");

if($Potwierdzenie){
	$IDQ = mysql_query("SELECT zl.* FROM orderplus_klient_potwierdzenie_zlecenie pzl
							JOIN orderplus_zlecenie zl ON(zl.id_zlecenie = pzl.zlecenie_id)
							WHERE potwierdzenie_id = '{$Potwierdzenie['potwierdzenie_id']}'");
	$Zlecenia = array();
	while($IDR = mysql_fetch_array($IDQ)){
		$Zlecenia[$IDR['id_zlecenie']] = $IDR;
	}
	$Client = GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Potwierdzenie['klient_id']}'");
	$Typy = GetTypySerwisu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Potwierdzenie zlecenia</title>
	<link href="style.css" rel="stylesheet" media="screen">
</head>
<body>
<div style="width:800px; margin: 0 auto 0 auto; text-align: left;">
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="width: 40%;"><img src="../../images/logo-new.png" alt="Logo" /></td>
			<td style="width: 60%; font-weight: bold; font-size: 13px;"><br /><br /><div class='inline' style='width: 100px;'>Date & Time:</div> <div class='data bordered inline'><?php echo str_replace(" ", " godz. ", date("d.m.Y H:i", strtotime($Potwierdzenie['potwierdzenie_date']))); ?></div></td>
		</tr>
	</table>
	<br /><br />
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="width: 60%; font-size: 16px; color: #40A0FF;"><strong>Forwarding order confirmation for<br /><span style="font-size: 12px;">Potwierdzenie przyjęcia zlecenia spedycyjnego  dla firmy</span></strong></td>
			<td style="width: 40%; font-weight: bold; font-size: 17px; color: #0060E0;"><?php echo $Client; ?></td>
		</tr>
		<tr>
			<td style="font-size: 13px; font-weight: bold;" colspan="2">
				<br />
				We would like to you inform that we have accepted your order for organizing transport<br />
				<span style="font-size: 10px;">Uprzejmie informujemy, że przyjęliśmy do realizacji zlecenie na zorganizowanie transportu:</span><br />
				
			</td>
		</tr>
	</table>
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%; border-collapse: collapse;">
		<tr>
			<th class="small bordered">No.</th>
			<th class="small bordered">Order No.</th>
			<th class="small bordered">Truck No.</th>
			<th class="small bordered">Loading place</th>
			<th class="small bordered">Delivery place</th>
			<th class="small bordered">Loading date</th>
			<th class="small bordered">Unloading date</th>
			<th class="small bordered">Shipment</th>
			<th class="small bordered">Type of service</th>
			<th class="small bordered">Rate</th>
		</tr>
		<?php
			$Lp = 1;
			foreach($Zlecenia as $Dane){
				echo "<tr>\n";
					$TruckNr = GetValue("SELECT rejestracja FROM orderplus_kierowca WHERE id_kierowca = '{$Dane['id_kierowca']}'");
					echo "<td class='small bordered white'>$Lp</td>";
					echo "<td class='small bordered white'>{$Dane['numer_zlecenia_krotki']}</td>";
					echo "<td class='small bordered white'><nobr>{$Dane['kierowca_dane_nr_rejestracyjny']}</nobr></td>";
					echo "<td class='small bordered white'>{$Dane['miejsce_zaladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['odbiorca']}</td>";
					echo "<td class='small bordered white'>{$Dane['termin_zaladunku']} {$Dane['godzina_zaladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['termin_rozladunku']} {$Dane['godzina_rozladunku']}</td>";
					echo "<td class='small bordered white'>{$Dane['opis_ladunku']}</td>";
					echo "<td class='small bordered white'>{$Typy[$Dane['typ_serwisu']]}</td>";
					echo "<td class='small bordered white'><nobr>{$Dane['stawka_klient']} {$Dane['waluta']}</nobr></td>";
				echo "</tr>\n";
				$Lp++;
			}
		?>
	</table>
	
</div>
</body>
</html>
<?php
}else{
	echo "Błędna operacja!";
	exit;
}
?>