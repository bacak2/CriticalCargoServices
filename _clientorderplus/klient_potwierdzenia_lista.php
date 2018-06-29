<table cellpadding="5" cellspacing="0" border="0">
    <tr>
                <form action="" method="post">
                <?php
                echo '<tr><td style="font-weight: bold; color: #bbce00;">Filtruj wg zlecenia:&nbsp;</td>';
                echo "<td style='font-weight: bold; color: #bbce00;'><select name=\"filtr_zlecenie\" class='tabelka' style='margin-right: 8px;'>";
                echo "<option value=\"0\">---- wszystkie zlecenia ----</option>";
                if(isset($_POST['filtr_zlecenie'])){
                    $_SESSION['filtr_zlecenie'] = $_POST['filtr_zlecenie'];
                }else if(isset($_GET['filtr_zlecenie'])){
                    $_SESSION['filtr_zlecenie'] = $_GET['filtr_zlecenie'];
                }
                $w = mysql_query("SELECT * FROM orderplus_zlecenie WHERE id_klient = '{$_SESSION['zalogowany_id']}' AND ((ost_korekta = 1) OR (ost_korekta = 0 AND korekta = 0))  ORDER BY numer_zlecenia_krotki DESC, id_zlecenie DESC");
                while($zlec = mysql_fetch_object($w))
                {
                   $sel = $_SESSION['filtr_zlecenie'] == $zlec->id_zlecenie ? 'selected' : '';
                   echo "<option value=\"$zlec->id_zlecenie\" $sel>".OrderNumberForClient($zlec->numer_zlecenia)."</option>";
                }
                echo "</select>";
                echo '&nbsp;&nbsp;&nbsp;<input type="image" value="Filtruj" src="images/filtruj.gif" style="margin-left: 10px; vertical-align: middle;" /></td></tr>';
                ?>
                </form>
   </tr>
</table><br /><br />
<table class="lista">
        <tr>
            <th>Data wysłania</th>
            <th>Klient</th>
            <th class='ikona'><img src="images/buttons/podglad_potwierdzenie_button_grey.png" alt="Podgląd potwierdzenia" /></th>
        </tr>
<?php
$WHERE = "";
if(isset($_SESSION['filtr_zlecenie']) && $_SESSION['filtr_zlecenie'] > 0){
    $Potwierdzenia = array(-1);
    $ZapTest = mysql_query("SELECT potwierdzenie_id FROM orderplus_klient_potwierdzenie_zlecenie WHERE zlecenie_id = '{$_SESSION['filtr_zlecenie']}'");
    while($Z = mysql_fetch_object($ZapTest)){
        $Potwierdzenia[] = $Z->potwierdzenie_id;
    }
    $WHERE = " AND potwierdzenie_id IN(".implode(",",$Potwierdzenia).")";
}
$Klienci = GetOptions("SELECT id_klient, nazwa FROM orderplus_klient WHERE id_klient = '{$_SESSION['zalogowany_id']}'");
$wiersze = mysql_query("SELECT p.* FROM orderplus_klient_potwierdzenie p WHERE klient_id = '{$_SESSION['zalogowany_id']}' $WHERE ORDER BY potwierdzenie_date DESC");
$Licznik = 1;
while ($wiersz = mysql_fetch_array($wiersze)) {
    $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
    echo("<tr style='background-color: $KolorWiersza;'>");
    print ("<td>{$wiersz['potwierdzenie_date']}</td>");
    print ("<td>{$Klienci[$wiersz['klient_id']]}</td>");
    print ("<td class='ikona'><a href=\"raports/potwierdzenie.php?check={$wiersz["hash"]}\" class=\"akcja\" target='_blank'><img src=\"images/buttons/podglad_potwierdzenie_button.png\" onmouseover='this.src=\"images/buttons/podglad_potwierdzenie_button_hover.png\"' onmouseout='this.src=\"images/buttons/podglad_potwierdzenie_button.png\"'></a></td>");
    print ("</tr>");
    $Licznik++;
}
?>
</table>