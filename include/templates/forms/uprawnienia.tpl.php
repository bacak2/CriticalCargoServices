<?php
    $Licz = 0;
    foreach($this->Pola[$Nazwa]['opcje']['elementy']['nad'] as $Parametr => $Opis) {
        if($Licz % 6  == 0){
            ?><div style="float: left; width: 23%;"><?php
        }
        print("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[nad][]' value='$Parametr'".(in_array($Parametr, $Wartosc) ? ' checked' : '').">$Opis<br>");
        if(isset($this->Pola[$Nazwa]['opcje']['elementy']['pod'][$Parametr])){
            foreach($this->Pola[$Nazwa]['opcje']['elementy']['pod'][$Parametr] as $Param => $PodOpis){
                print("<input type='checkbox' name='{$this->MapaNazw[$Nazwa]}[pod][$Parametr][]' value='$Param'".(in_array($Param, $Wartosc) ? ' checked' : '')." style='margin-left: 15px;'>$PodOpis<br>");
            }
        }
        if($Licz % 6  == 5){  
            ?></div><?php
        }
        $Licz++;
    }
    if($Licz % 6 != 0){
        ?></div><?php
    }

?>