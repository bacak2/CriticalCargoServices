<?php
    if(count($this->Filtry)){
        ?>
        <div style="clear: both;"></div>
        <?php
        $Form = new FormularzSimple();
        $Form->FormStart("filters", $this->LinkPowrotu);
        ?>
            <table cellpadding="5" cellspacing="0" border="0">
        <?php
        for ($i = 0; $i < count($this->Filtry); $i++) {
            echo ($i%3 == 0 ? "<tr>" : "")."<td style='font-weight: bold; color: #bbce00;'>".(isset($this->Filtry[$i]['opis']) ? "{$this->Filtry[$i]['opis']}:" : "&nbsp;")."</td> ";
            echo "<td style='font-weight: bold; color: #bbce00;'>";
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
            if($this->Filtry[$i]['typ'] == "data"){
                $Form->PoleInputText("filtr_$i", $_SESSION['Filtry'][$this->Filtry[$i]['nazwa']], "class='tabelka' style='margin-right: 8px;' onclick='javascript:showKal(document.filters.filtr_$i);'");
            }
            echo "</td>".($i%3 == 2 ? "</tr>" : "");
        }
        ?>
              <tr><td>&nbsp;</td><td>
        <?php
        $Form->PoleSubmitImage("szukaj", "search", "images/filtruj.gif", "style='margin-left: 10px; vertical-align: middle;'");
        ?>
            </td></tr>
            </table>
        <?php
        $Form->FormEnd();
    }
?>