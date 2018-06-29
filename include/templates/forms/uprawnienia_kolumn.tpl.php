<script type="text/javascript" src="js/uprawnienia_kolumn.js"></script> 
<?php
    $Widoki = array(    "all" => "Wspólne",
                        "admin" => "Administracja",
                        "operacja" => "Operacja",
                        "platnosci" => "Płatności",
                    );
    foreach($Widoki as $Param => $Name){
        ?>
        <div style="float: left; width: 23%;">
        <?php
        print("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[$Param][]' value='widok'".(in_array($Param, $Wartosc) ? ' checked' : '')." class='widok_$Param' onclick='CheckWidokAll(this, \"$Param\")'>$Name<br />");
        foreach($this->Pola[$Nazwa]['opcje']['elementy'] as $Parametr => $Opis) {
            if(strstr($Opis['td_class'], $Param)){
                print("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[$Param][]' value='$Parametr'".(in_array($Parametr, $Wartosc) ? ' checked' : '')." class='$Param' style='margin-left: 15px;' onclick='CheckWidok(\"$Param\")'>{$Opis["naglowek"]}<br>");
            }
        }
        ?>
        </div>
        <?php
    }
?>