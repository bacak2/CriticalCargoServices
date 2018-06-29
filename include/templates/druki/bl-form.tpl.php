<?php
    $Print = false;
    $Zapis = false;
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $Wartosci = $_POST;
        if(isset($_POST['zapisz_konosament'])){
            unset($Wartosci['zapisz_konosament']);
            if($this->Baza->GetValue("SELECT count(*) FROM orderplus_sea_orders_konosament WHERE sea_order_id = '{$Wartosci['sea_order_id']}'") > 0){
                $Zapytanie = $this->Baza->PrepareUpdate("orderplus_sea_orders_konosament", $Wartosci, array('sea_order_id' => $Wartosci['sea_order_id']));
            }else{
                $Zapytanie = $this->Baza->PrepareInsert("orderplus_sea_orders_konosament", $Wartosci);
            }
            if(!$this->Baza->Query($Zapytanie)){
                echo $this->Baza->GetLastErrorDescription();
            }else{
                $Zapis = true;
            }
            $SOI_id = $Wartosci['sea_order_id'];
        }
    }
    if($_SERVER['REQUEST_METHOD'] == "POST" || $_GET['act'] == "print"){
        $Print = true;
        ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    $("textarea").hide();
                    $(".input_class").hide();
                    $("div.show").show();
                    $(".to-print-2").show();
                    $(".to-print").hide();
                });

                function HideBg(){
                    $("#konosament-bg").css("background", "none");
                    $("#hide-bg-trigger").css('display', 'none');
                    $("#show-bg-trigger").css('display', 'inline');
                }

                function ShowBg(){
                    $("#konosament-bg").css("background", "url('images/bl-bg.png') no-repeat");
                    $("#hide-bg-trigger").css('display', 'inline');
                    $("#show-bg-trigger").css('display', 'none');
                }
            </script>
        <?php
    }
?>
<div id="print" style="width:793px; margin: 8px auto 8px auto; font-size: 12px; text-align:left; padding-right: 7px;">
    <div class="to-print-2" style="display: none; margin: 0;">
        <a href="javascript:HideBg()" id="hide-bg-trigger" >ukryj tło</a><a href="javascript:ShowBg()" id="show-bg-trigger" style="display: none;">pokaż tło</a> | <a href="javascript:window.print()">drukuj</a> | <a href="javascript:PrintBack()"><?php echo (isset($_GET['id']) ? "przejdź" : "cofnij") ?> do edycji</a> | <a href="javascript:window.close()">wyjdź</a>
    </div>
    <div class="to-print" style="margin: 0;">
        <a href="javascript:GoToPrint2()">podgląd wydruku</a> | <a href="javascript:window.close()">wyjdź</a>
    </div>
</div>
<form action="" method="post" name="formek" id="formek" style="margin:0; padding:0;">
    <div id="print" style="width:793px; margin: 16px auto 8px auto; font-size: 12px; text-align:left; padding-right: 7px;">
        Konosament dla zlecenia: <?php FormularzSimple::PoleSelect("sea_order_id", $SOI, $Wartosci['sea_order_id']); ?><?php FormularzSimple::PoleSubmit("zapisz_konosament", "Zapisz konosament"); ?><?php echo ($Zapis ? "Konosament został zapisany!" : ""); ?>
    </div>
<div id="konosament-bg" style="width:793px; height: 1122px; margin: 0 auto 0 auto; text-align: left; background: url('images/bl-bg.png') no-repeat; position: relative;">
    <div class="editable textarea" style="width: 297px; height: 74px; top: 62px; left: 84px;">
        <textarea name="consignor"><?php echo $Wartosci['consignor']; ?></textarea>
        <div class="show"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['consignor'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 297px; height: 74px; top: 160px; left: 84px;">
        <textarea name="consigned_to"><?php echo $Wartosci['consigned_to']; ?></textarea>
        <div class="show"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['consigned_to'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 297px; height: 64px; top: 253px; left: 84px;">
        <textarea name="notify_address"><?php echo $Wartosci['notify_address']; ?></textarea>
        <div class="show"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['notify_address'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 333px; left: 267px;">
        <textarea name="place_of_receipt" style="height: 16px;"><?php echo $Wartosci['place_of_receipt']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['place_of_receipt'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 367px; left: 84px;">
        <textarea name="ocean_vessel" style="height: 16px;"><?php echo $Wartosci['ocean_vessel']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['ocean_vessel'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 367px; left: 267px;">
        <textarea name="pol" style="height: 16px;"><?php echo $Wartosci['pol']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['pol'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 401px; left: 84px;">
        <textarea name="podis" style="height: 16px;"><?php echo $Wartosci['podis']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['podis'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 401px; left: 267px;">
        <textarea name="pod" style="height: 16px;"><?php echo $Wartosci['pod']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['pod'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 130px; height: 20px; top: 47px; left: 576px;">
        <textarea name="fbl_number" style="height: 16px;"><?php echo $Wartosci['fbl_number']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['fbl_number'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 680px; height: 320px; top: 440px; left: 84px;">
        <textarea name="content" id="content"><?php echo $Wartosci['content']; ?></textarea>
        <div class="show"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['content'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 210px; height: 20px; top: 824px; left: 87px;">
        <textarea name="declaration_of_interest" style="height: 16px;"><?php echo $Wartosci['declaration_of_interest']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['declaration_of_interest'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 208px; height: 20px; top: 827px; left: 541px;">
        <textarea name="declared_value" style="height: 16px;"><?php echo $Wartosci['declared_value']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['declared_value'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 239px; height: 20px; top: 962px; left: 89px;">
        <textarea name="freight_amount" style="height: 16px;"><?php echo $Wartosci['freight_amount']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['freight_amount'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 150px; height: 20px; top: 962px; left: 340px;">
        <textarea name="freight_payable" style="height: 16px;"><?php echo $Wartosci['freight_payable']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['freight_payable'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 247px; height: 20px; top: 962px; left: 503px;">
        <textarea name="place_and_date_of_issue" style="height: 16px;"><?php echo $Wartosci['place_and_date_of_issue']; ?></textarea>
        <div class="show" style="height: 16px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['place_and_date_of_issue'])); ?></div>
    </div>
    <div class="editable inputek" style="width: 14px; height: 14px; top: 1003px; left: 86px;">
        <input type="text" name="cargo_not_covered" style="height: 10px; width: 10px;" class="input_class" value="<?php echo $Wartosci['cargo_not_covered']; ?>" />
        <div class="show" style="height: 10px; width: 10px; margin-top: 0px; margin-left: 3px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['cargo_not_covered'])); ?></div>
    </div>
    <div class="editable inputek" style="width: 14px; height: 14px; top: 1003px; left: 152px;"> 
        <input type="text" name="cargo_covered" style="height: 10px; width: 10px;" class="input_class" value="<?php echo $Wartosci['cargo_covered']; ?>" />
        <div class="show" style="height: 10px; width: 10px; margin-top: 0px; margin-left: 3px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['cargo_covered'])); ?></div>
    </div>
    <div class="editable inputek" style="width: 127px; height: 16px; top: 1003px; left: 349px;">
        <input type="text" name="number_of_original_fbls" style="height: 12px;" class="input_class" value="<?php echo $Wartosci['number_of_original_fbls']; ?>" />
        <div class="show" style="height: 12px;"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['number_of_original_fbls'])); ?></div>
    </div>
    <div class="editable textarea" style="width: 232px; height: 71px; top: 1024px; left: 258px;">
        <textarea name="for_delivery_of_goods"><?php echo $Wartosci['for_delivery_of_goods']; ?></textarea>
        <div class="show"><?php echo nl2br(str_replace(" ", "&nbsp;", $Wartosci['for_delivery_of_goods'])); ?></div>
    </div>
    <div class="editable textarea" style="top: 150px; left: 479px;">
        <img src="images/logo-trans.png" alt="Logo" style="border: 0; margin: 0; vertical-align: middle; display: inline;"/><br /><br />
        Critical Cargo and Freight Services sp. z o. o.<br />
        ul. Solidarności 115 lok. 2<br />
        00-140<br />
        Warszawa,<br />
        tel. +48 730 730 590<br /><br />
        <u>www.critical-cs.com</u>
    </div>
</div>
</form>
<div style="width:793px; height: 1122px; margin: 0 auto 0 auto; text-align: left; background: url('images/bl-bg-2.jpg') no-repeat; page-break-before: always;">

</div>