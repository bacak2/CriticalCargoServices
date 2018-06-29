<div class="layout">
<table width="100%" cellpadding="0" cellspacing="0">

<tr>

<td style='padding-bottom: 100px; vertical-align: top;'>

<table style="margin: 0px 0 12px 0" width="100%" cellpadding="0" cellspacing="0">
   <tr id="print">
		<td style="font-size: 12px; text-align:left;padding-right: 7px">

		<a href="javascript:window.print()">drukuj</a> | <a href="javascript:history.back(-1)">anuluj</a>
		</td>
	</tr>
           <tr>

      <td width="50%" align="right" colspan="2">
        <?php
            echo '<br /><strong>'.$nota['miejsce_wystawienia'].', '.$nota['data_wystawienia'].'</strong>';
            echo "<br /><br /><h3>".$Lang['ORYGINA']."</h3>";
            ?>
      </td>
   </tr>

   <tr>

     <td width="50%" valign="top" align="left">
            <br />
            <b>
            Critical Cargo and Freight Services sp. z o. o.<br />
            <?php
             
                 echo "ul. Solidarności 115/2, 00-140 Warszawa<br />";
             
            ?>
            POLAND, <?php echo $Lang['NIP']; ?> PL5252581565<br />
                <?php
                        echo "<br />".$Lang['NUMER_BANKU']."<br />";
                        echo "Bank PKO BP S.A.<br />";
                        echo "IBAN PL";
                        echo "<br />";
                        
                       
                        if($nota['szablon_nota'] == "PL"){
                            echo "PLN: PL25 1020 1042 0000 8802 0310 0443<br />";
                        }else {
                            echo "EUR: PL73 1020 1042 0000 8902 0310 0500<br />";
                        }
                        
                        echo "SWIFT: BPKOPLPW";
                ?>
            </b>

      </td>

      <td width="50%" valign="middle">
        <img src="images/logo-new-bw.png" style="margin-left: 10px;"/>
      <br />
      </td>
   </tr>

   <tr><td colspan="2" style="font-size: 8px; height: 3px; border-bottom: 1px solid black">&nbsp;</td></tr>

   <tr><td colspan="2"><br />
	<?php echo $Lang['NOTA_DLA'] ?>:<br /><br >
		<div style="margin-left: 30px;" class="dane_adresowe">
			<?php
                            echo $Client['nazwa'].'<br />'.
                            $Client['adres'].'<br />'.
                            $Client['kod_pocztowy'].' '.$Client['miejscowosc'].'<br /><br />'.NIP.' '.$Client['nip'];
			?>
		</div>
   </td>
   </tr>

   <tr><td colspan="2">
<br /><br />
   <center><h2><?php echo $Lang['NOTA_OB_NR']." ".$nota['nr_noty']; ?>
      </h2></center>
	  <br /><br />
	  <span style="font-size: 14px;">
              <?php
              if($nota['szablon_nota'] == "PL"){
                    $kwota = $nota['kwota_pln'].' PLN';
                    $value = $nota['kwota_pln'];
              }else{
                    #$kwota = $nota['kwota_pln'].' PLN';
                    $kwota = $nota['kwota_waluta'].' '.$nota['waluta'];
                    $value = $nota['kwota_waluta'];
              }
              echo nl2br($nota['nazwa_naleznosci']);

              ?>
          <br /><br />
          <b><?php echo $Lang['KWOTA_OBCIAZENIA'] ?>: </b> <?php echo $kwota; ?><br /><br />
          <b><?php echo $Lang['SLOWNIE'] ?></b> <?php echo Usefull::KwotaSlownie($value, ($nota['szablon_nota'] == "PL" ? 'PLN' : $nota['waluta']), $nota['szablon_nota']); ?>
	  <br /><br />
	  </span>
   </td>
   </tr>
</table>

</td>
</tr>
<tr>
<td>

<table  style="margin: 0px 0 0 0" width="100%" cellpadding="3" cellspacing="10">
		<tr>
			<td width="50%">&nbsp;</td>
			<td></td>
			<td width="50%" style="border-bottom: 1px dotted black;">&nbsp;</td>
		</tr>
		<tr style="text-align: center; font-size: 8pt;">
			<td></td>
			<td></td>
			<td><?php echo $Lang['PODPIS']; ?></td>
		</tr>
		<tr>
			<td colspan="3"><br /><br /></td>
		</tr>
	</table>
</td>
</tr>
</table>
</div>