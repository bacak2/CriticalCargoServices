<?php
session_start();
if (!isset($_SESSION['client_login'])) {
   include('index.php');
   die();
}
require_once('baza.php');
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
    $zlecenie = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_zlecenie_klient WHERE id_zlecenie = '$id'"));
}else if(isset($_GET['real_id']) && is_numeric($_GET['real_id'])){
    $id = $_GET['real_id'];
    $zlecenie = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie = '$id'"));
    $zlecenieKlient = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_zlecenie_klient WHERE real_id = '$id'"));
    $zlecenie->ladunek_niebezpieczny = $zlecenieKlient->ladunek_niebezpieczny;
}
$klient = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_klient WHERE id_klient = '{$_SESSION['zalogowany_id']}'"));
include("../zlecenia_lang/pl_utf.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<style type="text/css">
	<!--
		@page {
			size: portrait;
			margin: 1cm;
			font-family: Verdana, Arial, Helvetica, sans-serif;
    		font-size: 8pt;
		}

		html {
			margin: 0px;
			padding: 0px;
		}

		body {
			margin: 0px;
			padding: 0px;
			font-family: Verdana, Arial, Helvetica, sans-serif;
    		font-size: 8pt;
			text-align: center;
		}
		.table_okno_duze , .table_okno_male{
			border:1px solid #000000;
			padding: 2px 2px;
		}
		div {
			margin: 30px 0 30px 0;
		}
		
		td.title {
    border: 1px solid #000;
    background: #ffcd00;
    font-style: italic;
    font-weight: 700;
    padding: 2px;
    padding-left: 10px;
	-->
	</style>
</head>
<body>
<div style="width:580px; margin: 0 auto 0 auto; text-align: left;">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="left"><img src="../../images/logo-new.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/></td>
			<td align="center" valign="middle">
                            Critical Cargo and Freight Services Sp. z o.o.<br /> 
al. Solidarności 115/2<br />
00-140 Warszawa, Poland<br />
NIP: PL 525-258-15-65
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="left" width="50%" style="vertical-align: top;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
                                            <?php
                                                $DaneOddzialu[1] = array('siedziba' => 'Wrocław', 'tel' => 'tel. +48&nbsp;693233314<br />tel. +48&nbsp;530989969');
                                                $DaneOddzialu[2] = array('siedziba' => 'Warszawa', 'tel' => 'tel. +48&nbsp;22&nbsp;330-81-21<br />fax +48&nbsp;22&nbsp;398-79-07');
                                                $DaneOddzialu[3] = array('siedziba' => 'Poznań', 'tel' => 'tel +48&nbsp;61&nbsp;6417592<br />fax +48&nbsp;61&nbsp;6417594');
                                                $DaneOddzialu[4] = array('siedziba' => 'Gdynia', 'tel' => '');
                                            ?>
						<td style="vertical-align: top;">
                                                    <?php
                                                        echo $DaneOddzialu[$klient->id_oddzial]['siedziba'];
                                                    ?>
						</td>
						<td style="vertical-align: top;">
                                                    <?php
                                                        echo $DaneOddzialu[$klient->id_oddzial]['tel'];
                                                    ?>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" width="50%" style="vertical-align: top;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td style="vertical-align: top;">
							E-MAIL:
						</td>
						<td style="vertical-align: top;">
							office@critical-cs.com
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top;">
							WWW:
						</td>
						<td style="vertical-align: top;">
							www.critical-cs.pl
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table><br /><br />
	<div align="center" style="font-weight: bold;">
		<?php echo "ZLECENIE SPEDYCYJNE ".($zlecenie->numer_zlec_klienta); ?><br />
		<?php echo DNIA.($zlecenie->data_zlecenia); ?>
	</div>
	<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<tr><td align="left" width="270" class="title">1.<?php echo MIEJSCE_ZALADUNKU; ?></td><td width="40"></td><td align="left" width="270" class="title">2. <?php echo ODBIORCA; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270">
		<?php
                    print nl2br($zlecenie->miejsce_zaladunku);
		?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270">
		<?php
                    print nl2br($zlecenie->odbiorca);
		?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td align="left" width="270" class="title">3. <?php echo DATA_ZALADUNKU; ?></td><td width="40">&nbsp;</td><td align="left" width="270" class="title">4. <?php echo DATA_ROZLADUNKU; ?></td></tr>
		<tr><td class="table_okno_male" align="left" width="270"><?php print $zlecenie->termin_zaladunku." ".$zlecenie->godzina_zaladunku; ?>&nbsp;</td><td width="40"></td><td class="table_okno_male" align="left" width="270"><?php print $zlecenie->termin_rozladunku." ".$zlecenie->godzina_rozladunku; ?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
		<tr><td align="left" width="270" class="title">5. <?php echo OPIS_LADUNKU; ?></td><td width="40"></td><td align="left" width="270" class="title">6. <?php echo UWAGI; ?></td></tr>
		<tr><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie->opis_ladunku); ?>&nbsp;</td><td width="40"></td><td class="table_okno_duze" align="left" width="270"><?php print nl2br($zlecenie->ladunek_niebezpieczny);?>&nbsp;</td></tr>
		<tr><td align="left" width="270">&nbsp;</td><td width="40">&nbsp;</td><td align="left" width="270">&nbsp;</td></tr>
	</table>
        <br /><br />
        <b>CENA NETTO: <?php echo $zlecenie->stawka_klient." ".$zlecenie->waluta; ?></b>
        <br /><br /><br /><br />
</div>
</body>
</html>