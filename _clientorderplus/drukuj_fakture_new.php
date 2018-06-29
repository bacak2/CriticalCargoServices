<?php
include("../include/classes/modules/usefull.class.php");
function Waluta($kwotka)
{
   return number_format($kwotka, 2, ',', ' ');
}

function WyswietlFormatWaluty($Kwota, $Waluta){
            if($Waluta == "EUR"){
                return "&euro;".number_format($Kwota,2,"."," ");
            }else{
                return number_format($Kwota,2,"."," ")." <small>$Waluta</small>";
            }
        }

session_start();
include('baza.php');
include('functions.php');
extract($_GET);
extract($_POST);
extract($_SESSION);
ini_set('display_errors', '1');
error_reporting(E_ERROR);

$z0 = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
       LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
       LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
       LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
       WHERE f.id_faktury = $id AND id_klienta = '{$_SESSION['zalogowany_id']}'";

$w0 = mysql_query($z0);
if(mysql_num_rows($w0) > 0){
        $faktura = mysql_fetch_array($w0);

        if($faktura['szablon_faktura'] == 'ENG'){
           include("../faktura_lang/eng.php");
        }
        else {
           include("../faktura_lang/pl_utf.php");
        }
}
        ?>
		
		<html>
        <head>
             <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <style>
body, html
{
   text-align: center;
   margin: 0;
   padding: 0;
}
.layout
{
   width: 740px;
   border: 0px solid black;
   margin: 0 auto;
   padding: 0;
   page-break-after: always;
}

.layout_new_design
{
   width: 909px;
   border: 0px solid black;
   margin: 0 auto;
   padding: 0;
   font-family: Arial;
   color: #231f20; 
   height: 1286px;
}

.layout_new_design table td{
    font-family: Arial;
   color: #231f20;
}

h1
{
   font-family: arial;
   font-size: 20px;
   padding: 0;
   margin: 5px 5px 10px 5px;
}
h2
{
   font-family: arial;
   font-size: 18px;
   padding: 0;
   margin: 2px 5px 8px 5px;
}
h3
{
   font-family: arial;
   font-size: 13px;
   padding: 0;
   margin: 2px 5px 4px 5px;
   text-align: right;
}
h4
{
   background-color: #eeeeee;
   padding: 4px 2px 4px 2px;
   color: black;
   font-weight: bold;
   font-size: 15px;
   width: 350px;
   margin-bottom: 10px;
}
table
{
   font-size: 12px;
   font-family: arial;
}
.dane_nabywcy
{
   font-size: 12px;
   font-family: helvetica;
}

table#pozycje-table th{
    background-color: #3d3e3d;
    color: #FFF;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
} 

table#pozycje-table td{
    border-top: 1px solid #929494;
    color: #231f20; 
    font-weight: bold;
    font-size: 15px;
    text-align: center;
}

table#pozycje-table tr{
    height: 30px;
}

table#morska-info tr{
    height: 24px;
}

table#morska-info td, table#morska-info th{
    height: 24px;
    border-top: 1px solid #929397;
    border-bottom: 1px solid #929397;
}

table#morska-info th{
    width: 120px;
    color: #FFF;
    background-color: #929397;
    text-align: right;
    font-weight: bold;
}

table#morska-info td{
    width: 333px;
    text-align: center;
    font-weight: bold;
}

@media print
       {
	     tr#print {display:none}
		 }
h4 { background: #eee; }		 
		</style>
		</head>

		
		<body>
		
	<div class="layout">
		<table style="margin: 15px 0 12px 0" width="100%" cellpadding="0" cellspacing="0">
			<tr id="print">
				<td style="font-size: 12px; text-align:left;padding-right: 7px">
				<a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
				</td>
			</tr>
			<tr>
				<td width="50%" valign="middle">
				<?php


				echo "<img src=\"../images/logo-new-bw.png\" style=\"margin: 0 0 20px 10px;\"/>";
				
				if($faktura['firma_wystaw'] == 1){
					echo '<br />'.$Lang['TEL'].' +48 22 127 85 28';
					echo '<br />fax: +48 22 127 85 28';
					echo '<br />e-mail: office@critical-cs.com';
				}else{
					echo '<br />'.$Lang['TEL'].' +48 22 127 85 28';
					echo '<br />fax: +48 22 127 85 28';
					echo '<br />e-mail: office@critical-cs.com';
				}
				echo "<br />www.critical-cs.com";

				?>
				
				</td>
      
				<td width="50%" valign="middle">
				<h1><u><?php echo $Lang['FAKTURA_VAT'] ?></u></h1>
				<h2><?php echo $Lang['NR']." ". $faktura['numer'] ?></h2>
				<h3>
				
				<?php

				echo $Lang['ORYGINA'];
      
				?>
				</h3>
				</td>
			</tr>
			<tr><td colspan="2" style="font-size: 8px; height: 3px; border-bottom: 1px solid black">&nbsp;</td></tr>
		</table>
	

<?php

echo "
<table style=\"margin: 0px 0 10px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"center\">". $Lang['DATA_WYSTAWIENIA'] ."</td>
      <td align=\"center\">". $Lang['MIEJSCE_WYSTAWIENIA'] ."</td>
      <td align=\"center\">". $Lang['DATA_SPRZEDAZY'] ."</td>
      <td align=\"center\">". $Lang['TERMIN_PLATNOSCI'] ."</td>
      <td align=\"center\">". $Lang['FORMA_PLATNOSCI'] ."</td>
   </tr>
   <tr>
      <td align=\"center\"><b>{$faktura['data_wystawienia']}</b></td>
      <td align=\"center\"><b>".($faktura['szablon_faktura'] == 'ENG' ? str_replace("Warszawa", "Warsaw", $faktura['miejsce_wystawienia']) : $faktura['miejsce_wystawienia'])."</b></td>
      <td align=\"center\"><b>{$faktura['data_sprzedazy']}</b></td>
      <td align=\"center\"><b>{$faktura['termin_platnosci']}</b></td>
      <td align=\"center\"><b>";
      if($faktura['szablon_faktura'] == 'ENG')
      echo $faktura['forma_en'];
      else
      echo $faktura['forma'];
      echo
      "</b></td>
   </tr>
</table>";
?>


<table style="margin: 27px 0 30px 0" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%" valign="top">
			<h4><?php echo $Lang['SPRZEDAWCA'] ?></h4>
			<div class="dane_nabywcy">
         		<?php
					if($faktura['firma_wystaw'] == 1){
				?>
					Critical Cargo and Freight Services Sp. z o.o.<br /> 
al. Solidarności 115/2<br />
00-140 Warszawa, Poland<br />
NIP: PL 525-258-15-65
				<?php
					}else{
				?>
					Critical Cargo and Freight Services Sp. z o.o.<br />
al. Solidarności 115/2<br />
00-140 Warszawa, Poland<br />
NIP: PL 525-258-15-65
				<?php
					}
				?>

				<b>
				<?php
						
				echo "<br /><br />";
				echo "IBAN PL";
				echo "<br />";
                echo "PLN: PL25 1020 1042 0000 8802 0310 0443<br />";
                echo "EUR: PL73 1020 1042 0000 8902 0310 0500<br />";
				echo "SWIFT: BPKOPLPW<br />";
				
				?>
				</b>
			</div>
		</td>

		<td width="50%" valign="top">
			<h4><?php echo $Lang['NABYWCA']; ?></h4>
			<div class="dane_nabywcy">
			
			
			<?php
			$klient = mysql_fetch_array(mysql_query("SELECT * FROM orderplus_klient WHERE id_klient = '{$faktura['id_klienta']}'"));
			echo($klient['nazwa']); ?><br />


			<?php echo($klient['adres']); ?>,<br /> <?php echo($klient['kod_pocztowy']); ?> <?php echo($klient['miejscowosc']); ?><br />
			<?php echo $Lang['NIP']; ?> <?php echo ($klient['nip']); ?><br>
				<br />
         </div>
      </td>
   </tr>
   <tr>
   <td colspan="2"><br />
   <?php
      $uwagi = stripslashes($faktura['uwagi']);
      if($faktura['szablon_faktura'] == 'ENG')
      {
         $uwagi = str_replace('numer zlecenia klienta', 'Order No.', $uwagi);
      }
    echo $uwagi;
    ?> </td></tr>
</table>

<?php

echo "
<table style=\"margin: 10px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"center\" style=\"height: 30px; border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['LP'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\"><b>". $Lang['NAZWA'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['PKWIU_PKOB'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"30\"><b>". $Lang['ILOSC'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['JEDNOSTKA_MIARY'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['CENA_JEDNOSTKOWA'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>".$Lang['WARTOSC_SPRZEDAZY'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['VAT'] ."</b></td>
   </tr>";
   


$w0 = mysql_query($z0);
$lp = 1;
while($Pos = mysql_fetch_array($w0)){

echo "
   <tr>
   <td align=\"center\" style=\"height: 20px;\">$lp</td>
   <td align=\"center\">";
   
   if($faktura['szablon_faktura'] == 'ENG')
   {
      echo str_replace('Wewnątrzwspólnotowa usługa spedycyjna', 'Intraeuropean Forwarding Service', $Pos['opis']);
   }
   else
   {
      echo $Pos['opis'];
   }
   echo "</td>
   <td align=\"center\">{$Pos['pkwiu']}</td>
   <td align=\"center\">{$Pos['ilosc']}</td>
   <td align=\"center\">".($faktura['szablon_faktura'] == 'ENG' ? str_replace("szt", "pcs", $Pos['jednostka']) : $Pos['jednostka'])."</td>
   <td align=\"center\">". WyswietlFormatWaluty($Pos['netto_jednostki'], $faktura['waluta'])."</td>
   <td align=\"center\">".  WyswietlFormatWaluty($Pos['netto'], $faktura['waluta'])."</td>
   <td align=\"center\">{$Pos['vat']}".(!in_array(strtolower($Pos['vat']), array("np", "zw")) ? "%" : "")."</td>
   </tr>";
   $lp++;
}
echo "</table>";



echo "
<table style=\"margin: 30px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
      <td align=\"right\" style=\"height: 30px;\">". $Lang['OGOLEM'] ."</td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['WARTOSC_NETTO']."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"50\"><b>". $Lang['VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"130\"><b>". $Lang['KWOTA_VAT'] ."</b></td>
      <td align=\"center\" style=\"border-bottom: 1px solid black;\" width=\"150\"><b>". $Lang['WARTOSC_BRUTTO'] ."</b></td>
   </tr>";

   
$z1 = "SELECT *,
    SUM(netto) as suma_netto,
    SUM(kwota_vat) as suma_kwot_vat,
    SUM(brutto) as suma_brutto
    FROM faktury_pozycje WHERE id_faktury = $id GROUP BY vat DESC"; 
$w1 = mysql_query($z1);
$lp = 1;
$suma_netto = 0;
$suma_brutto = 0;
$suma_kwot_vat = 0;
while($pozycje = mysql_fetch_object($w1))
{
	
   $suma_brutto += $pozycje->suma_brutto;
   $suma_netto += $pozycje->suma_netto;
   $suma_kwot_vat += $pozycje->suma_kwot_vat;
   echo "<tr><td align=\"center\" style=\"height: 20px;\"></td>
   <td align=\"center\">".  WyswietlFormatWaluty($pozycje->suma_netto, $faktura['waluta'])."</td>
   <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : "")."</td>
   <td align=\"center\">".  WyswietlFormatWaluty($pozycje->suma_kwot_vat, $faktura['waluta'])."</td>
   <td align=\"center\">".  WyswietlFormatWaluty($pozycje->suma_brutto, $faktura['waluta'])."</td>
   </tr>";
}

echo "<tr>
   <td align=\"right\" style=\"height: 20px;\"><b>".$Lang['RAZEM'] ." </b></td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">". WyswietlFormatWaluty($suma_netto, $faktura['waluta'])."</td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">&nbsp;</td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">".  WyswietlFormatWaluty($suma_kwot_vat, $faktura['waluta'])."</td>
   <td align=\"center\" style=\"border-top: 1px solid black;\">".  WyswietlFormatWaluty($suma_brutto, $faktura['waluta'])."</td>
   </tr>";
echo "</table>";


echo "<table style=\"font-size: 16px; margin: 22px 0 20px 0; width: auto; float: right;\" cellpadding=\"0\" cellspacing=\"0\">";
if($faktura['status'] == 0){
   echo "<tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['DO_ZAPLATY'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>". WyswietlFormatWaluty($suma_brutto, $faktura['waluta'])."</b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['SLOWNIE'] ."</td>
      <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>";
   echo Usefull::KwotaSlownie($suma_brutto, $faktura['waluta'], $faktura['szablon_faktura']);
   echo "</b></td>
   </tr>";
if($faktura['wplacono'] > 0)
{
   $pozostalo = $suma_brutto - $faktura['wplacono'];
   echo "<tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />Wpłacono:</td>
      <td align=\"left\" style=\"font-size: 14px;\"><br /><br />&nbsp;&nbsp;&nbsp;<b>". WyswietlFormatWaluty($faktura['wplacono'], $faktura['waluta'])."</b></td>
   </tr>
   <tr>
      <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\">Pozostało:</td>
      <td align=\"left\" style=\"font-size: 14px;\">&nbsp;&nbsp;&nbsp;<b>". WyswietlFormatWaluty($pozostalo, $faktura['waluta'])."</b>
      &nbsp;&nbsp;&nbsp;". Usefull::KwotaSlownie($pozostalo, $faktura['waluta'], $faktura['szablon_faktura'])."</td>
   </tr>";
}
}else{
    echo "<tr>
       <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />&nbsp;</td>
      <td align=\"left\" style=\"font-size: 14px; height: 30px; font-weight: bold;\">".$Lang['OPLACONA']."</b></td>
   </tr></table>";
}
?>


<table  style="margin: 190px 0 0 0" width="100%" cellpadding="3" cellspacing="10">
		<tr>
			<td width="50%" style="border-bottom: 1px dotted black;">
			&nbsp;
			</td>
			<td>
			</td>
			<td width="50%" style="border-bottom: 1px dotted black;">
			&nbsp;
			</td>
		</tr>

<?php
         
		 $w2 = mysql_query("SELECT osoba FROM faktury_wystawiajacy LIMIT 1");
		 $podpis_wystawcy = mysql_result($w2);
         if($podpis_wystawcy != '')
         {
            echo '<tr height="20" style="text-align: center; font-size: 12px;">
			         <td><b>'. $podpis_wystawcy .'
				        </b></td>
			            <td>
			            </td>
			         <td>
				  &nbsp;
			   </td>
		    </tr>';

         }
		?>
		
		<tr style="text-align: center; font-size: 8pt;">
			<td>
				<?php echo $Lang['WYSTAWIENIE_FAKTURY'] ?>
			</td>
			<td>
			</td>
			<td>
				<?php echo $Lang['ODBIOR_FAKTURY'] ?>
			</td>
		</tr>
	</table>
	<?php
	
	if($faktura['szablon_faktura'] == "PL"){
		echo "<br /><br /><br />
		<span style='font-size: 8pt;'>
		Zgodnie z art. 7 ustawy o terminach zapłaty w transakcjach handlowych,
		jeżeli dłużnik w określonym w umowie nie dokona zapłaty na rzecz
		wierzyciela, zobowiązany jest on do zapłaty wierzycielowi, bez odrębnego
		wezwania, odsetek w wysokości odsetek od zaległości
		podatkowych.<br />VAT NP - Reverse Charge  |  0 % - Art 83 ust. 1.3 ustawy o podatku od  
towarów i usług <br />
		Ta faktura jest także wezwaniem do zapłaty w rozumieniu art. 476 KC.</span>";
	}
?>

</div>
</body>
</html>		
		