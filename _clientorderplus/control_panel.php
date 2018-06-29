<?php

function WyswietlKomunikat($Tresc, $Link, $CzyWTabelce = false) {
   if ($CzyWTabelce) {
      echo('<tr bgcolor="#FFFFFF"><td>');
   }
?>
<center>
	<br>
	<br>
	<br>
	<b><?php echo($Tresc); ?></b><br>
	<br>
	<a href="<?php echo($Link); ?>"><img src="images/ok.gif" border="0"></a><br>
	<br>
	<br>
</center>
<?php
if ($CzyWTabelce) {
   echo('</td></tr>');
}
}

$NazwaAkcji = 'Panel Administracyjny';

$modul = (isset($_REQUEST['modul']) ? $_REQUEST['modul'] : "zlecenia");
$akcja = (isset($_REQUEST['akcja']) ? $_REQUEST['akcja'] : "lista");

$Moduly = array(
'zlecenia' => 'Zlecenia',
'klient_potwierdzenia' => 'Potwierdzenia',
'klient_raporty' => 'Sprawdź przesyłkę',
'faktury' => 'Faktury',

);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td style="text-align: left; padding: 4px;">
            <div style="display: block; width: 325px; float: left;"><img src="images/logo.png" alt="MEPP Plus" /></div>
            <div style="display: block; width: 30%; float: right; text-align: right"><form action="." method="post"><input type="hidden" name="logout" value="tak"><input type="image" src="images/wyloguj.gif" alt="" height="17" width="72" border="0"></form></div>
        </td>
    </tr>
    <tr>
        <td align="center" valign="middle" class="border-main" id="border-main">
            <table  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                <tr>
                    <td class="naglowek" colspan="2"></td>
                </tr>
                <tr>
                    <td colspan='2' class='boki' style='background-color: #bcce00;'>
                        <?php include("menu.php");?>
                    </td>
                </tr>
                <tr>
                    <td>

			<table width="100%" border="0" cellspacing="0" cellpadding="5">
				<tr>
					<td class="boki" align="left" valign="top">
					<?php

					   $plik_do_wstawienia = $modul . "_" . $akcja . ".php";

					   if (file_exists($plik_do_wstawienia))
					   {
					      include ($plik_do_wstawienia);
					   } else {
					      print("<center><br><br><p>ARTplus - projekty internetowe<br><br><br></center>");
					   }

					?>

					</td>
				</tr>
			</table>


		</td>
            </tr>
     </table>
        </td>
    </tr>
    <tr>
            <td><p class="logowanie_dol">powered by <a class="log" target="_blank" href="http://www.artplus.pl">ARTplus</a> for Critical Cargo and Freight Services</p></td>
    </tr>
</table>