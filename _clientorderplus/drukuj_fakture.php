<?php

function Waluta($kwotka)
{
   return number_format($kwotka, 2, ',', ' ');
}

session_start();
include('baza.php');
include('functions.php');
extract($_GET);
extract($_POST);
extract($_SESSION);

$z0 = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
       LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
       LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
       LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
       WHERE f.id_faktury = $id AND id_klienta = '{$_SESSION['zalogowany_id']}'";

$w0 = mysql_query($z0);
if(mysql_num_rows($w0) > 0){
        $faktura = mysql_fetch_object($w0);

        if($faktura->szablon_faktura == 'ENG'){
           include("../faktura_lang/eng.php");
        }
        else {
           include("../faktura_lang/pl_utf.php");
        }

        ?>


        <html>
        <head>
             <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <style>
        body, html
        {
           text-align: center;
        }
        .layout
        {
           width: 740px;
           border: 0px solid black;
           margin: 0 auto;
           padding: 0;
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
        @media print
               {
                     * {
                        background-color: white !important;
                        background-image: none !important;
                        }
                     tr#print {display:none}
                         }
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
        $w = mysql_query("SELECT * FROM faktury_wystawiajacy");
        if($wystaw = mysql_fetch_object($w))
        {
           echo "<img src=\"images/$wystaw->ikonka\" style=\"margin-left: 10px;\"/>";
           if($faktura->firma_wystaw == 1){
              echo '<br />'.$Lang['TEL'].' +48 22 219 55 80';
           }else{
              echo '<br />'.$Lang['TEL'].' +48 22 323 10 12';
           }
           echo ", fax +48 22 330 81 25<br />www.meppeurope.com.";
        }
        ?>
              </td>
              <td width="50%" valign="middle">
              <h1><u><?php echo $Lang['FAKTURA_VAT'] ?></u></h1>
              <h2><?php echo $Lang['NR']." ". $faktura->numer ?></h2>
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
              <td align=\"center\"><b>$faktura->data_wystawienia</b></td>
              <td align=\"center\"><b>$faktura->miejsce_wystawienia</b></td>
              <td align=\"center\"><b>$faktura->data_sprzedazy</b></td>
              <td align=\"center\"><b>$faktura->termin_platnosci</b></td>
              <td align=\"center\"><b>";
              if($faktura->szablon_faktura == 'ENG')
              echo $faktura->forma_en;
              else
              echo $faktura->forma;
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
                                                if($faktura->firma_wystaw == 1){
                                        ?>
                                                Critical Cargo and Freight Services Sp. z o.o.<br />
                                                al. Solidarności 115/2<br />
                                                00-140 Warszawa, Poland<br />
                                                <?php echo $Lang['NIP']; ?> PL 525-258-15-65<br />
                                        <?php
                                                }else{
                                        ?>
                                                Critical Cargo and Freight Services Sp. z o.o.<br />
                                                al. Solidarności 115/2<br />
                                                00-140 Warszawa, Poland<br />
                                                <?php echo $Lang['NIP']; ?> PL 525-258-15-65<br />
                                        <?php
                                                }
                                        ?>
                                        <b>
                                                        <?php
                                                                echo "<br />".$Lang['NUMER_BANKU']."<br />";
                                                                echo "Bank BPH S.A.<br />";
                                                                echo "ul. Targowa 41 03-728 Warszawa<br />";
                                                                if($faktura->szablon_faktura == 'ENG'){
								if($faktura->firma_wystaw == 1){
									echo "SWIFT: BPHKPLPK<br />";
									echo "IBAN PL: PL32106000760000330000640183<br>";
								}else{
									echo "SWIFT: BPHKPLPK<br />";
                                                                        if($faktura->waluta == "USD"){
                                                                            echo "IBAN PL: PL54106000760000330000699442<br />";
                                                                        }else{
                                                                            echo "IBAN PL: PL49106000760000330000656852<br />";
                                                                        }
								}
							}else{
								if($faktura->firma_wystaw == 1){
									echo "04 1060 0076 0000 3260 0148 5014<br />";
                                                                        if($faktura->waluta == "EUR"){
                                                                            echo "Konto EUR:<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                            echo "IBAN PL: PL32106000760000330000640183<br>";
                                                                        }else if($faktura->waluta == "USD"){

                                                                        }
								}else{
									echo "58 1060 0076 0000 3200 0136 1929<br />";
                                                                        if($faktura->waluta == "EUR"){
                                                                            echo "Konto EUR:<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                            echo "IBAN PL: PL49106000760000330000656852<br />";
                                                                        }else if($faktura->waluta == "USD"){
                                                                            echo "Konto USD:<br />";
                                                                            echo "SWIFT: BPHKPLPK<br />";
                                                                            echo "IBAN PL: PL54106000760000330000699442<br />";
                                                                        }
								}
							}
                                                        ?>
                                        </b>
        <?php

        $w = mysql_query("SELECT * FROM faktury_wystawiajacy");
        if($wystaw = mysql_fetch_object($w))
        {
          # echo nl2br($wystaw->opis);
        }
        ?>

                 </div>
              </td>
              <td width="50%" valign="top">
                 <h4><?php echo $Lang['NABYWCA'] ?></h4>
                 <div class="dane_nabywcy">
        <?php
        $klient = mysql_fetch_object(mysql_query("SELECT * FROM orderplus_klient WHERE id_klient = '$faktura->id_klienta'"));
        echo($klient->nazwa); ?><br />
                                        <?php echo($klient->adres); ?>, <?php echo($klient->kod_pocztowy); ?> <?php echo($klient->miejscowosc); ?><br />
                                        <?php echo $Lang['NIP']; ?> <?php echo ($klient->nip); ?><br>
                                        <br />
                 </div>
              </td>
           </tr>
           <tr>
           <td colspan="2"><br />
           <?php
              $uwagi = stripslashes($faktura->uwagi);
              if($faktura->szablon_faktura == 'ENG')
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
        while($pozycje = mysql_fetch_object($w0))
        {
           echo "
           <tr>
           <td align=\"center\" style=\"height: 20px;\">$lp</td>
           <td align=\"center\">";
           if($faktura->szablon_faktura == 'ENG')
           {
              echo str_replace('Wewnątrzwspólnotowa usługa spedycyjna', 'Intraeuropean Forwarding Service', $pozycje->opis);
           }
           else
           {
              echo $pozycje->opis;
           }
           echo "</td>
           <td align=\"center\">$pozycje->pkwiu</td>
           <td align=\"center\">$pozycje->ilosc</td>
           <td align=\"center\">$pozycje->jednostka</td>
           <td align=\"center\">". Waluta($pozycje->netto_jednostki) ." <small>$faktura->waluta</small></td>
           <td align=\"center\">". Waluta($pozycje->netto) ."  <small>$faktura->waluta</small></td>
           <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw"))  ? "%" : "")."</td>
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
           echo "
           <tr>
           <td align=\"center\" style=\"height: 20px;\"></td>
           <td align=\"center\">". Waluta($pozycje->suma_netto) ." <small>$faktura->waluta</small></td>
           <td align=\"center\">$pozycje->vat".(!in_array(strtolower($pozycje->vat), array("np", "zw")) ? "%" : "")."</td>
           <td align=\"center\">". Waluta($pozycje->suma_kwot_vat) ." <small>$faktura->waluta</small></td>
           <td align=\"center\">". Waluta($pozycje->suma_brutto) ." <small>$faktura->waluta</small></td>
           </tr>";
        }


        echo "<tr>
           <td align=\"right\" style=\"height: 20px;\"><b>".$Lang['RAZEM'] ." </b></td>
           <td align=\"center\" style=\"border-top: 1px solid black;\">". Waluta($suma_netto) ." <small>$faktura->waluta</small></td>
           <td align=\"center\" style=\"border-top: 1px solid black;\">&nbsp;</td>
           <td align=\"center\" style=\"border-top: 1px solid black;\">". Waluta($suma_kwot_vat) ." <small>$faktura->waluta</small></td>
           <td align=\"center\" style=\"border-top: 1px solid black;\">". Waluta($suma_brutto) ." <small>$faktura->waluta</small></td>
           </tr>";
        echo "</table>";





        echo "<table style=\"font-size: 16px; margin: 22px 0 20px 0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
           <tr>
              <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['DO_ZAPLATY'] ."</td>
              <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>". Waluta($suma_brutto) ." <small>$faktura->waluta</small></b></td>
           </tr>
           <tr>
              <td align=\"right\" style=\"font-size: 14px; width: 100px; height: 30px;\">". $Lang['SLOWNIE'] ."</td>
              <td align=\"left\">&nbsp;&nbsp;&nbsp;<b>";
           echo KwotaSlownie($suma_brutto, $faktura->waluta, $faktura->szablon_faktura);
           echo "</b></td>
           </tr>";
        if($faktura->wplacono != null)
        {
           $pozostalo = $suma_brutto - $faktura->wplacono;
           echo "<tr>
              <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\"><br /><br />Wpłacono:</td>
              <td align=\"left\" style=\"font-size: 14px;\"><br /><br />&nbsp;&nbsp;&nbsp;<b>". Waluta($faktura->wplacono) ." <small>$faktura->waluta</small></b></td>
           </tr>
           <tr>
              <td align=\"right\" style=\"font-size: 12px; width: 100px; height: 30px;\">Pozostało:</td>
              <td align=\"left\" style=\"font-size: 14px;\">&nbsp;&nbsp;&nbsp;<b>". Waluta($pozostalo) ." $faktura->waluta</b>
              &nbsp;&nbsp;&nbsp;". KwotaSlownie($pozostalo, $faktura->waluta, $faktura->szablon_faktura) ."</td>
           </tr>";
        }
        ?>



        <table  style="margin: 50px 0 0 0" width="100%" cellpadding="3" cellspacing="10">
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
                 $podpis_wystawcy = mysql_result(mysql_query("SELECT osoba FROM faktury_wystawiajacy LIMIT 1"), 0, 0);
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
                if($faktura->szablon_faktura == "PL"){
                        echo "<br /><br /><br />
                        <span style='font-size: 8pt;'>
                        Zgodnie z art. 7 ustawy o terminach zapłaty w transakcjach handlowych,
                        jeżeli dłużnik w określonym w umowie nie dokona zapłaty na rzecz
                        wierzyciela, zobowiązany jest on do zapłaty wierzycielowi, bez odrębnego
                        wezwania, odsetek w wysokości odsetek od zaległości
                        podatkowych.<br />
                        Ta faktura jest także wezwaniem do zapłaty w rozumieniu art. 476 KC.</span>";
                }
        ?>

        </div>
        </body>

        </html>
 <?php
}else{
	echo "Błędna operacja!";
	exit;
}
?>