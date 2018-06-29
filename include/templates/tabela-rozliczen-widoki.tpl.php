<script type="text/javascript" src="js/uprawnienia_kolumn.js"></script>
<div style='margin-bottom: 10px; color: #bcce00; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00; border-top: 1px solid #bbce00;'>
    <div style="float: left;">
    <span style="color: #bcce00; font-weight: bold;">Widoki:</span><br />
    <?php
    $Widoki = array(    "all" => "Wspólne",
                        "admin" => "Administracja",
                        "operacja" => "Operacja",
                        "platnosci" => "Płatności",
                    );
    foreach($Widoki as $Param => $Name){
        if(!isset($_SESSION['TabelaRozliczenWidok'][$Param])){
            $_SESSION['TabelaRozliczenWidok'][$Param] = true;
        }
        if($this->Uzytkownik->IsAdmin() || in_array($Param, $Dostep)){
        ?>
            <input type="checkbox" name="Check" class="ChangeView" value="<?php echo $Param ?>" <?php echo ($_SESSION['TabelaRozliczenWidok'][$Param] ? "checked" : ""); ?> style="margin-left: 12px;" onclick="ShowColumns(this,'<?php echo $Param; ?>')" /> <?php echo $Name; ?>
    <?php
        }
    }
    ?>
    </div>
    <?php
        if($this->Uzytkownik->IsAdmin()){

    ?>
        <a href='tabela_rozliczen_xls.php' target='_blank' class='form-button' style="margin-left: 60px;">Generuj plik XLS</a>
    <?php
        }
    ?>
</div>
