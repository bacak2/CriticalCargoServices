<?php

#            MODU� FAKTUR
# Marcin Romanowski 7 - 9 kwietnia 2008
#         (C) ARTplus 2008
#
#
# kopiowac pliki modulu [faktury_XXX.php] oraz
# images/prev.gif
# images/next.gif
# images/callendar.jpg
# images/dodaj_pozycje.gif:
# + CHMOD 0777 na images/
#
# kalendarz.js
#
# kopiowac tabele SQL z przedrostkiem [faktury_XXX + faktury]
#
?>

<table class="lista">

 <tr>
     <th>Lp.</th>
     <th>Numer</th>
     <th>Data wystawienia</th>
     <th>Miejsce wystawienia</th>
     <th>Data sprzedaży</th>
     <th>Termin płatności</th>
     <th>Netto</th>
     <th>Brutto</th>
     <th class="ikona"><img src="images/buttons/printer_button_grey.png" alt="Drukuj" /></th>
 </tr>


<?php

$z = "SELECT f.*, w.waluta FROM faktury f
      LEFT JOIN faktury_waluty w ON f.id_waluty = w.id_waluty
      LEFT JOIN orderplus_zlecenie z ON(z.id_zlecenie = f.id_zlecenia)
      WHERE f.id_klienta = '{$_SESSION['zalogowany_id']}'
      ORDER BY f.id_faktury DESC";
$lp = 1;

$w = mysql_query($z);

if($w)
{
   # wydruk listy
   $nr = 1;
   while($faktura = mysql_fetch_object($w))
   {
      $z1 = "SELECT *,
             SUM(netto) as suma_netto,
             SUM(brutto) as suma_brutto
             FROM faktury_pozycje WHERE id_faktury = $faktura->id_faktury GROUP BY vat DESC";
      $w1 = mysql_query($z1);
      $suma_netto = 0;
      $suma_brutto = 0;
      while($pozycje = mysql_fetch_object($w1))
      {
         $suma_brutto += $pozycje->suma_brutto;
         $suma_netto += $pozycje->suma_netto;
      }
      $suma_brutto = number_format($suma_brutto, 2, ',', ' ');
      $suma_netto = number_format($suma_netto, 2, ',', ' ');

      $KolorWiersza = ($nr % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
      $nr++;
       echo("<tr style='background-color: $KolorWiersza;'>");
      echo "<td class='licznik'>$lp</td>";
      $faktura->status == 1 ? $statkolor = '' : $statkolor = 'style="color: #9a0000"';
      echo "<td $statkolor>$faktura->numer</td>";
      echo "<td>$faktura->data_wystawienia</td>";
      echo "<td>$faktura->miejsce_wystawienia</td>";
      echo "<td>$faktura->data_sprzedazy</td>";
      echo "<td $statkolor>$faktura->termin_platnosci</td>";
      echo "<td style='text-align: center;'>$suma_netto <small>$faktura->waluta</small></td>";
      echo "<td style='text-align: center;'>$suma_brutto <small>$faktura->waluta</small></td>";
      if($faktura->data_wystawienia >= '2012-09-26'){
        echo "<td><a href='drukuj_fakture_new.php?id=$faktura->id_faktury'><img src=\"images/buttons/printer_button.png\" onmouseover='this.src=\"images/buttons/printer_button_hover.png\"' onmouseout='this.src=\"images/buttons/printer_button.png\"'></a></td>";
      }else{
          echo "<td><a href='drukuj_fakture.php?id=$faktura->id_faktury'><img src=\"images/buttons/printer_button.png\" onmouseover='this.src=\"images/buttons/printer_button_hover.png\"' onmouseout='this.src=\"images/buttons/printer_button.png\"'></a></td>";
      }
      echo "</tr>";
      ++$lp;
   }
}
   ?>
</table>