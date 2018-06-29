<?php
    if(count($this->Filtry)){
        ?>
        <div style="clear: both;"></div>
        <?php
        $Form = new FormularzSimple();
        $Form->FormStart("filters", $this->LinkPowrotu);
        ?>
            <table cellpadding="5" cellspacing="0" border="0">
                <tr>
        <?php
        for ($i = 0; $i < count($this->Filtry); $i++) {
            if($this->Filtry[$i]['typ'] == "trasa"){
               echo "</tr><tr>";
            }
            echo "<td style='font-weight: bold; color: #bbce00;'>{$this->Filtry[$i]['opis']}:</td> ";
            echo "<td style='font-weight: bold; color: #bbce00;' ".($this->Filtry[$i]['typ'] == "trasa" ? "colspan='7'" : "").">";
            if($this->Filtry[$i]['typ'] == "lista"){
                $DomOp = (isset($this->Filtry[$i]['domyslna']) ? $this->Filtry[$i]['domyslna'] : "- wszystkie -");
                $Opcje = Usefull::PolaczDwieTablice(array("" => $DomOp), $this->Filtry[$i]['opcje']);
                $Form->PoleSelect("filtr_$i", $Opcje, $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']], "class='tabelka' style='margin-right: 8px;'");
            }
            if($this->Filtry[$i]['typ'] == "trasa"){
                $DomOp = (isset($this->Filtry[$i]['domyslna']) ? $this->Filtry[$i]['domyslna'] : "- wszystkie -");
                $Opcje = Usefull::PolaczDwieTablice(array("" => $DomOp), $this->Filtry[$i]['opcje']);
                $Form->PoleSelect("filtr_{$i}[kod_kraju_zaladunku]", $Opcje, $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['kod_kraju_zaladunku'], "class='tabelka' style='margin-right: 8px;'");
                echo " ==> ";
                $Form->PoleSelect("filtr_{$i}[kod_kraju_rozladunku]", $Opcje, $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']]['kod_kraju_rozladunku'], "class='tabelka' style='margin-left: 8px;'");
            }
            if($this->Filtry[$i]['typ'] == "tekst"){
                $Form->PoleInputText("filtr_$i", $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']], "class='tabelka' style='margin-right: 8px;'");
            }
            echo "</td>";
        }
        ?>
                </tr>
              <tr><td>&nbsp;</td><td>
        <?php
        $Form->PoleSubmit("szukaj", "Filtruj", "class='form-button rounded' style='margin-left: 10px; vertical-align: middle;'");
        $Form->PoleSubmit("czysc_filtry", "Usuń filtry", "class='form-button rounded' style='margin-left: 20px; vertical-align: middle;'");
        ?>
            </td></tr>
            </table>
        <?php
        $Form->FormEnd();
    }
?>