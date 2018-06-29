<?php
    if(count($Wartosc) > 0){
        ?>
        <table class="osoby_kontaktowe_tabelka"><tbody>
                <tr class="first">
                    <th>Imię i nazwisko</th><th>Telefon</th><th>E-mail</th>
                </tr>
        <?php
        foreach($Wartosc as $Person){
?>
        <tr><td><?php echo $Person['imie_nazwisko']; ?> <span style="color:#555;font-size:.8em;">[ <?php echo $Person['stanowisko']; ?> ]</span></td><td><?php echo $Person['telefon']; ?>&nbsp;</td><td><?php echo $Person['mail']; ?> </td></tr>
<?php
        }
        ?>
        </table>
        <?php
    }else{
        echo "brak";
    }
?>