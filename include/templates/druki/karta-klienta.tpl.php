<div style="width: 19cm; margin: 0 auto; text-align: left;">
    <img src="images/logo-new.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/>
    <br /><br />
    <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold; color: #000; padding: 6px 0px; text-align: center; font-size: 14px;">
        KARTA KLIENTA
    </div>
    <br /><br />
    <table border="0" cellpadding="5" cellspacing="0" id="karta_klienta">
        <?php
            foreach($PoleNaKarcie['standard'] as $Pole => $Etykieta){
                if($Pole == "os_kontaktowe"){
                    ?>
                    <tr>
                        <th><?php echo $Etykieta; ?></th>
                        <td>
                            <?php
                                $Wartosc = $Dane[$Pole];
                                include(SCIEZKA_SZABLONOW."forms/osoby-kontaktowe-dane.tpl.php");
                            ?>
                        </td>
                    </tr>
                    <?php
                }else{
                ?>
                <tr>
                    <th><?php echo $Etykieta; ?></th>
                    <td><?php echo $Dane[$Pole]; ?></td>
                </tr>
                <?php
                }
            }
        ?>
            <tr>
                <th><br />Fakturowanie</th>
                <td>&nbsp;</td>
            </tr>
        <?php
            foreach($PoleNaKarcie['fakturowanie'] as $Pole => $Etykieta){

                ?>
                <tr>
                    <td><?php echo $Etykieta; ?></td>
                    <td><?php echo $Dane[$Pole]; ?></td>
                </tr>
                <?php
            }
            if(isset($PoleNaKarcie['info_specjalne'])){
                ?>
                    <tr>
                        <th><br />Instrukcje specjalne</th>
                        <td>&nbsp;</td>
                    </tr>
                <?php
                foreach($PoleNaKarcie['info_specjalne'] as $Pole => $Etykieta){
                ?>
                <tr>
                    <td><?php echo $Etykieta; ?></td>
                    <td><?php echo $Dane[$Pole]; ?></td>
                </tr>
                <?php
                }
            }
        ?>

         <tr>
            <th><br />Data: <?php echo date("d.m.Y"); ?></th>
            <td style="font-weight: bold;"><br />Podpis Specjalisty ds. Sprzedaży................................................</td>
         </tr>
         <tr>
            <th><br />Data: <?php echo date("d.m.Y"); ?></th>
            <td style="font-weight: bold;"><br />Podpis Agenta ds. Frachtów.......................................................</td>
         </tr>
         <tr>
            <th><br />Data: <?php echo date("d.m.Y"); ?></th>
            <td style="font-weight: bold;"><br />Podpis Kierownika Komórki Organizacyjnej..................................</td>
         </tr>
    </table>
</div>