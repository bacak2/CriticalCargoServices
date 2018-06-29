<div class="mepp_ajax_head_name">
    <?php echo $customer['nazwa']; ?>
</div>

<div class="mepp_ajax_head_date">
    <span>
        <?php echo $customer['potencjal']; ?>
    </span>
</div>
<?php
    if(isset($status) && ($status == "ok" || $status == "fail"))
    {
        ?>
        <div class="operation_status operation_<?php echo $status; ?>_status">
            <?php echo $status=='fail' ? 'Nie udało się dodać kontaktu.' : 'Dodano osobę kontaktową.';?>
        </div>
        <?php
    }
?>
<div class="mepp_ajax_description">
    <table>
        <tbody>
            <tr>
                <td class="desc">
                    Telefon:
                </td>
                <td>
                    <?php echo $customer['telefon']; ?>
                </td>
            </tr>
            <tr>
                <td class="desc">
                    E-mail:
                </td>
                <td>
                    <?php
                        echo $customer['mail'] ?
                            '<a href="mailto:'.$customer['mail'].'" class="ab_a" title="napisz do">'.$customer['mail'].'</a>' :
                            ''
                        ;
                    ?>
                </td>
            </tr>
            <tr>
                <td class="desc">
                    Adres:
                </td>
                <td>
                    <?php echo $customer['adres'].', '.$customer['kod_pocztowy'].' '.$customer['miasto']; ?>
                </td>
            </tr>
            <tr>
                <td class="desc">
                    NIP:
                </td>
                <td>
                    <?php echo $customer['nip']; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- osoby kontaktowe-->
<?php
    
    if(empty($kontakt) === false)
    {
        ?>
        <div class="mepp_ajax_content">
            <?php
                foreach($kontakt as $k)
                {
                    ?>
                    <div class="ajax_box_comment">
                        <div class="ajax_box_comment_kontakt_name">
                            <?php
                                echo $k['imie_nazwisko'];
                                echo $k['stanowisko'] ? '['.$k['stanowisko'].']' : '';
                            ?>
                        </div>
                        <table style="font-size: 0.9em;">
                            <tbody>
                                <tr>
                                    <td class="desc">
                                        Telefon:
                                    </td>
                                    <td>
                                        <?php echo $k['telefon']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="desc">
                                        E-mail:
                                    </td>
                                    <td>
                                        <?php 
                                            echo $k['email'] ?
                                                '<a href="mailto:'.$k['email'].'" class="ab_a" title="napisz do">'.$k['email'].'</a>' :
                                                ''
                                            ;
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
            ?>

        </div>
        <?php
    }
?>

<!-- nowy kontakt -->
<form action="" method="post" name="nowa_osoba_kontaktowa">
    <div id="nowa_osoba_kontaktowa" onclick="oskontFormPokaz()">dodaj osobę kontaktową</div>
    
    <div class="mepp_ajax_content" id="nowa_os_kontaktowa_form" style="display:none">
        <div class="ajax_box_comment">
            <div class="ajax_box_comment_kontakt_name">
                <input type="text" name="os_kontaktowa" value="" />
                <input type="hidden" name="customer_id" value="<?php echo $customer['id_klient']; ?>" />
            </div>
            <table style="font-size: 0.9em; width:350px;">
                <tbody>
                    <tr>
                        <td class="desc">
                            Telefon:
                        </td>
                        <td>
                            <input type="text" name="telefon" />
                        </td>
                    </tr>
                    <tr>
                        <td class="desc">
                            E-mail:
                        </td>
                        <td>
                            <input type="text" name="mail" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="mepp_ajax_button button_cols"
                                 style="margin:0px; margin-left: 140px;">
                                <button onclick="addOsobaKontakowaAjax('');return false;" title="dodaj" name="button"  class="form-button">dodaj</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>


<div class="mepp_ajax_button button_cols">
    <button onclick="window.location='<?php echo $link; ?>';return false;" title="przejdź" name="button" class="form-button">przejdź</button>
</div>

<a class="mepp_close_ajax_box_button" id="info_box_close" title="zamknij"></a>
