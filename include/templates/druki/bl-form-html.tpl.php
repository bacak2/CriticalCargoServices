<?php
    $Print = false;
    $Zapis = false;
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $Wartosci = $_POST;
        if(isset($_POST['sea_order_id'])){
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
    }
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("textarea").hide();
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

    function SubmitKonosament(){
        $("textarea").each(function(){
            name_id = $(this).attr('name');
            value_set = $("#"+name_id).html();
            $(this).html(value_set);
        });
        document.formek.submit();
    }

</script>
<div id="print" style="width:793px; margin: 8px auto 8px auto; font-size: 12px; text-align:left; padding-right: 7px;">
    <div class="to-print-2" style="margin: 0;">
        <a href="javascript:HideBg()" id="hide-bg-trigger" >ukryj tło</a><a href="javascript:ShowBg()" id="show-bg-trigger" style="display: none;">pokaż tło</a> | <a href="javascript:window.print()">drukuj</a> | <a href="javascript:window.close()">wyjdź</a>
    </div>
</div>
<form action="" method="post" name="formek" id="formek" style="margin:0; padding:0;">
    <div id="print" style="width:793px; margin: 16px auto 8px auto; font-size: 12px; text-align:left; padding-right: 7px;">
        Konosament dla zlecenia: <?php FormularzSimple::PoleSelect("sea_order_id", $SOI, $Wartosci['sea_order_id']); ?><?php FormularzSimple::PoleButton("zapisz_konosament", "Zapisz konosament", "onclick='SubmitKonosament();'"); ?><?php echo ($Zapis ? "Konosament został zapisany!" : ""); ?>
    </div>
<div id="konosament-bg" style="width:793px; height: 1122px; margin: 0 auto 0 auto; text-align: left; background: url('images/bl-bg.png') no-repeat; position: relative;">
    <div class="editable textarea" style="width: 297px; height: 74px; top: 62px; left: 84px;">
        <div contenteditable="true" id="consignor"><?php echo stripslashes($Wartosci['consignor']); ?></div>
        <textarea name="consignor" id="consignor-txt"><?php echo stripslashes($Wartosci['consignor']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 297px; height: 74px; top: 160px; left: 84px;">
        <div contenteditable="true" id="consigned_to"><?php echo stripslashes($Wartosci['consigned_to']); ?></div>
        <textarea name="consigned_to" id="consigned_to-txt"><?php echo stripslashes($Wartosci['consigned_to']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 297px; height: 64px; top: 253px; left: 84px;">
        <div contenteditable="true" id="notify_address"><?php echo stripslashes($Wartosci['notify_address']); ?></div>
        <textarea name="notify_address" id="notify_address-txt"><?php echo stripslashes($Wartosci['notify_address']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 333px; left: 267px;">
        <div contenteditable="true" id="place_of_receipt" style="height: 16px;"><?php echo stripslashes($Wartosci['place_of_receipt']); ?></div>
        <textarea name="place_of_receipt" id="place_of_receipt-txt"><?php echo stripslashes($Wartosci['place_of_receipt']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 367px; left: 84px;">
        <div contenteditable="true" id="ocean_vessel" style="height: 16px;"><?php echo stripslashes($Wartosci['ocean_vessel']); ?></div>
        <textarea name="ocean_vessel" id="ocean_vessel-txt"><?php echo stripslashes($Wartosci['ocean_vessel']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 367px; left: 267px;">
        <div contenteditable="true" id="pol" style="height: 16px;"><?php echo stripslashes($Wartosci['pol']); ?></div>
        <textarea name="pol" id="pol-txt"><?php echo stripslashes($Wartosci['pol']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 401px; left: 84px;">
        <div contenteditable="true" id="podis" style="height: 16px;"><?php echo stripslashes($Wartosci['podis']); ?></div>
        <textarea name="podis" id="podis-txt"><?php echo stripslashes($Wartosci['podis']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 140px; height: 20px; top: 401px; left: 267px;">
        <div contenteditable="true" id="pod" style="height: 16px;"><?php echo stripslashes($Wartosci['pod']); ?></div>
        <textarea name="pod" id="pod-txt"><?php echo stripslashes($Wartosci['pod']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 130px; height: 20px; top: 47px; left: 576px;">
        <div contenteditable="true" id="fbl_number" style="height: 16px;"><?php echo stripslashes($Wartosci['fbl_number']); ?></div>
        <textarea name="fbl_number" id="fbl_number-txt"><?php echo stripslashes($Wartosci['fbl_number']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 680px; height: 320px; top: 440px; left: 84px;">
        <div contenteditable="true" id="content"><?php echo stripslashes($Wartosci['content']); ?></div>
        <textarea name="content" id="content-txt"><?php echo stripslashes($Wartosci['content']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 210px; height: 20px; top: 824px; left: 87px;">
        <div contenteditable="true" id="declaration_of_interest" style="height: 16px;"><?php echo stripslashes($Wartosci['declaration_of_interest']); ?></div>
        <textarea name="declaration_of_interest" id="declaration_of_interest-txt"><?php echo stripslashes($Wartosci['declaration_of_interest']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 208px; height: 20px; top: 827px; left: 541px;">
        <div contenteditable="true" id="declared_value" style="height: 16px;"><?php echo stripslashes($Wartosci['declared_value']); ?></div>
        <textarea name="declared_value" id="declared_value-txt"><?php echo stripslashes($Wartosci['declared_value']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 239px; height: 20px; top: 962px; left: 89px;">
        <div contenteditable="true" id="freight_amount" style="height: 16px;"><?php echo stripslashes($Wartosci['freight_amount']); ?></div>
        <textarea name="freight_amount" id="freight_amount-txt"><?php echo stripslashes($Wartosci['freight_amount']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 150px; height: 20px; top: 962px; left: 340px;">
        <div contenteditable="true" id="freight_payable" style="height: 16px;"><?php echo stripslashes($Wartosci['freight_payable']); ?></div>
        <textarea name="freight_payable" id="freight_payable-txt"><?php echo stripslashes($Wartosci['freight_payable']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 247px; height: 20px; top: 962px; left: 503px;">
        <div contenteditable="true" id="place_and_date_of_issue" style="height: 16px;"><?php echo stripslashes($Wartosci['place_and_date_of_issue']); ?></div>
        <textarea name="place_and_date_of_issue" id="place_and_date_of_issue-txt"><?php echo stripslashes($Wartosci['place_and_date_of_issue']); ?></textarea>
    </div>
    <div class="editable inputek" style="width: 14px; height: 14px; top: 1003px; left: 86px;">
        <input type="text" name="cargo_not_covered" style="height: 10px; width: 10px; padding-left: 2px;  border: 0;" class="input_class" value="<?php echo stripslashes($Wartosci['cargo_not_covered']); ?>" />
    </div>
    <div class="editable inputek" style="width: 14px; height: 14px; top: 1003px; left: 152px;"> 
        <input type="text" name="cargo_covered" style="height: 10px; width: 10px; padding-left: 2px; border: 0;" class="input_class" value="<?php echo stripslashes($Wartosci['cargo_covered']); ?>" />
    </div>
    <div class="editable inputek" style="width: 127px; height: 16px; top: 1003px; left: 349px;">
        <div contenteditable="true" id="number_of_original_fbls" style="height: 12px;"><?php echo stripslashes($Wartosci['number_of_original_fbls']); ?></div>
        <textarea name="number_of_original_fbls" id="number_of_original_fbls-txt"><?php echo stripslashes($Wartosci['number_of_original_fbls']); ?></textarea>
    </div>
    <div class="editable textarea" style="width: 232px; height: 71px; top: 1024px; left: 258px;">
        <div contenteditable="true" id="for_delivery_of_goods"><?php echo stripslashes($Wartosci['for_delivery_of_goods']); ?></div>
        <textarea name="for_delivery_of_goods" id="for_delivery_of_goods-txt"><?php echo stripslashes($Wartosci['for_delivery_of_goods']); ?></textarea>
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
<script type="text/javascript">
    CKEDITOR.disableAutoInline = true;
    CKEDITOR.inline( 'consignor' );
    CKEDITOR.inline( 'consigned_to' );
    CKEDITOR.inline( 'notify_address' );
    CKEDITOR.inline( 'place_of_receipt' );
    CKEDITOR.inline( 'ocean_vessel' );
    CKEDITOR.inline( 'pol' );
    CKEDITOR.inline( 'podis' );
    CKEDITOR.inline( 'pod' );
    CKEDITOR.inline( 'fbl_number' );
    CKEDITOR.inline( 'content' );
    CKEDITOR.inline( 'declaration_of_interest' );
    CKEDITOR.inline( 'declared_value' );
    CKEDITOR.inline( 'freight_amount' );
    CKEDITOR.inline( 'freight_payable' );
    CKEDITOR.inline( 'place_and_date_of_issue' );
    CKEDITOR.inline( 'number_of_original_fbls' );
    CKEDITOR.inline( 'for_delivery_of_goods' );
</script>