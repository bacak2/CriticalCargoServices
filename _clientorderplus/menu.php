<table cellpadding="0" cellspacing="0" id="menu">
    <tr>
<?php
foreach ($Moduly as $Parametr => $Opis) {
        if(isset($modul) && $Parametr == $modul) {
?>
            <td class="picked"><a href='?modul=<?php echo $Parametr; ?>'><div style='white-space: nowrap;'><?php echo $Opis; ?></div></a></td>
<?php
        }
        else {
?>
            <td><a href='?modul=<?php echo $Parametr; ?>'><div style='white-space: nowrap;'><?php echo $Opis; ?></div></a></td>
<?php
        }
}
?>
    </tr>
</table>