<div style="width: 19cm; margin: 1cm auto 1cm auto; text-align: left;">
    <style>
        table.ramka td, table.ramka th {
            border: 1px solid #CCCCCC;
        }
        td, th {
            margin: 0;
            padding: 5px;
            vertical-align: top;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
    </style>
	<span style='font-size: 11pt; font-weight: bold;'><?php echo $Wartosci['tytul'] ?></span>
	<br />
	<br />
    <?php if(isset($_GET['spec'])){ ?>
        <table style='font-size: 14pt; font-weight: bold; text-align: center;'>
            <tr><td>Specification</td></tr>
        </table>
        <br />
    <?php }?>
	<table class="ramka">
	<thead>
		<tr>
			<th>Lp.</th>
			<th style="width: 130px;">Date of loading</th>
			<th style="width: 130px;">Date of unloading</th>
			<th>Place of loading</th>
			<th colspan="2">Place of unloading</th>
            <?php if(isset($_GET['spec'])){
                $fakturaCurrency = $this->Baza->GetData("SELECT waluta FROM faktury LEFT JOIN faktury_waluty ON faktury.id_waluty = faktury_waluty.id_waluty WHERE id_faktury = {$_GET['id']}");
                if ($fakturaCurrency['waluta'] == "PLN") echo '<th>Amount in PLN</th>';
                else echo '<th>Amount in EUR</th>';
                }
                else echo '<th>Amount</th>';
            ?>
            <th>Order number</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$Wartosci['nowe_zlecenia'][] = 0;
			$Licznik = 1;
            if($_POST == null)  $Zlecenia = $this->Baza->GetResultAsArray("SELECT * FROM orderplus_zlecenie WHERE id_faktury = {$_GET['id']}", "id_zlecenie");
            elseif(isset($_GET['ids'])) $Zlecenia = $this->Baza->GetResultAsArray("SELECT * FROM orderplus_zlecenie WHERE id_faktury = {$_GET['ids']}", "id_zlecenie");
            else  $Zlecenia = $this->Baza->GetResultAsArray("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie IN(".implode(",",$Wartosci['nowe_zlecenia']).")", "id_zlecenie");
            $Sumuj = 0;
            $kursFaktury = $this->Baza->GetValue("SELECT kurs FROM faktury WHERE id_faktury = {$_GET['id']}", "id_faktury");

        foreach($Zlecenia as $ZleID => $Zle){
				echo "<tr>\n";
					echo "<td>$Licznik</td>\n";
					echo "<td>{$Zle['termin_zaladunku']}</td>\n";
					echo "<td>{$Zle['termin_rozladunku']}</td>\n";
					echo "<td>".nl2br($Zle['adres_zaladunku'])."</td>\n";
					echo "<td colspan='2'>".nl2br($Zle['adres_rozladunku'])."</td>\n";

					if(isset($_GET['spec'])){
                        echo "<td>";
                        if ($fakturaCurrency['waluta'] == "PLN") {
                            $StawkaPLN = $Zle['stawka_klient'] * $kursFaktury;
                            echo number_format($StawkaPLN, 2, ".", "");
                        } else {
                            $StawkaEUR = $Zle['stawka_klient'];
                            echo number_format($StawkaEUR, 2, ".", "");
                        }

                    }
                    else {
                        echo "<td style='vertical-align: bottom;'>";
                        if ($Zle['waluta'] == "PLN") {
                            echo $Zle['stawka_klient'] . "&nbsp;PLN;";
                            $StawkaPLN = $Zle['stawka_klient'];
                        } else {
                            $StawkaEUR = $Zle['stawka_klient'];
                            echo $Zle['stawka_klient'] . "&nbsp;{$Zle['waluta']};<br />";
                            $StawkaPLN = $Zle['stawka_klient'] * $Zle['kurs'];
                            echo number_format($StawkaPLN, 2, ".", "") . "&nbsp;PLN;";
                        }
                    }
					echo "</td>\n";
                    echo "<td>".nl2br($Zle['nr_zlecenia_klienta'])."</td>\n";
				echo "</tr>\n";
				$Licznik++;
				$Sumuj += round($StawkaPLN,2);
				$SumujEUR += round($StawkaEUR,2);
			}
			$VAT = $Sumuj * ($Wartosci['stawka_vat']/100);
			$Brutto = $Sumuj + $VAT;
		?>
        <tr>
            <td colspan="5" style="border: none;"></td>
            <td style="border: none;">Total:</td>
            <td style="border: none;">
                <?php
                    if($fakturaCurrency['waluta'] == "PLN") echo number_format($Sumuj,2,".","")."&nbsp;PLN";
                    else echo number_format($SumujEUR,2,".","")."&nbsp;EUR";
                ?>
            </td>
        </tr>
		<!--tr><td colspan="8" class='przerwa'></td></tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>NETTO</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($Sumuj,2,".","")."&nbsp;zł"; ?></td>
		</tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>VAT amount</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($VAT,2,".","")."&nbsp;zł"; ?></td>
		</tr>
		<tr>
			<td colspan="6" style='border: 0;'>&nbsp;</td>
			<td style='font-weight: bold;'>BRUTTO</td>
			<td style='font-weight: bold; text-align: right;'><?php echo number_format($Brutto,2,".","")."&nbsp;zł"; ?></td>
		</tr-->
        </table>
</div>