<?php
session_start();
error_reporting(0);
require_once('../baza.php');
require_once('../functions.php');
$hash = $_GET['check'];

$Raport = GetRow("SELECT * FROM orderplus_klient_raport WHERE hash = '$hash' AND klient_id = '{$_SESSION['zalogowany_id']}'");

if($Raport){
	$IDQ = mysql_query("SELECT zl.*, pzl.zlecenie_status FROM orderplus_klient_raport_zlecenie pzl
							JOIN orderplus_zlecenie zl ON(zl.id_zlecenie = pzl.zlecenie_id)
							WHERE raport_id = '{$Raport['raport_id']}'");
	$Zlecenia = array();
	while($IDR = mysql_fetch_array($IDQ)){
		$Zlecenia[$IDR['id_zlecenie']] = $IDR;
	}
	$Client = GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Raport['klient_id']}'");
	$Typy = GetTypySerwisu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Raport</title>
	<link href="style.css" rel="stylesheet" media="screen">
</head>
<body>
<div style="width:800px; margin: 0 auto 0 auto; text-align: left;">
	<table border="0" cellpadding="7" cellspacing="0" style="width: 100%;">
		<tr>
			<td style="width: 40%;" rowspan="2"><img src="../../images/logo-new.png" alt="Logo" /></td>
			<td style="width: 60%; font-weight: bold; font-size: 16px; color: #0060E0;"><br /><br /><i>Shipment location report for</i> <span style="font-size: 18px;"><?php echo $Client; ?></span></td>
		</tr>
		<tr>
			<td style="font-weight: bold; font-size: 13px;"><br /><br /><div class='inline' style='width: 100px;'>Date & Time:</div> <div class='data bordered inline'><?php echo str_replace(" ", " godz. ", date("d.m.Y H:i", strtotime($Raport['raport_date']))); ?></div></td>
		</tr>
	</table>
	<br /><br />
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
			<th class="small bordered">Actually status</th>
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
					echo "<td class='small bordered white'>{$Dane['zlecenie_status']}</td>";
				echo "</tr>\n";
				$Lp++;
			}
		?>
	</table>
	
	<br /><br /><br />
</div>
</body>
</html>
<?php
}else{
	echo "Błędna operacja!";
	exit;
}
?>