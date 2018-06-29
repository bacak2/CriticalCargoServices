<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="20"></td>
		<td>
			<br><b>Wybierz dzia³</b>:<br>
			<br>
<?php
foreach ($Moduly as $Parametr => $Opis) {
?>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="35"><img src="images/button_in.gif" alt="" height="23" width="23" border="0"></td>
					<td><a href="?modul=<?php echo($Parametr); ?>" class="modul"><?php echo($Opis); ?></a></td>
					<td align="right" valign="middle" width="43"><img src="images/button_out.gif" alt="" height="28" width="46" border="0"></td>
				</tr>
			</table>
			<br>
<?php
}
?>
		</td>
	</tr>
</table>
