<?php
    FormularzSimple::FormStart("", "?modul=$this->Parametr&pagin=0");
?>
<tr>
    <th class='licznik'>&nbsp;</th>
<?php
$Sorty = array("" => "---", "ASC" => "rosnąco", "DESC" => "malejąco");
foreach ($Pola as $NazwaPola => $Opis) {
    $Styl = (isset($Opis['td_class']) ? " class='{$Opis['td_class']}'" : "");
    if(isset($Filtry[$NazwaPola])){
        echo "<th style='text-align: right; vertical-align: top;'$Styl><nobr>\n";
        if(isset($Filtry[$NazwaPola]['wyszukaj'])){
            echo "Szukaj: ";
            FormularzSimple::PoleInputText("szukajka[$NazwaPola]", "");
            FormularzSimple::PoleSubmit("submit", "&raquo");
            echo "<br />";
        }
        if($Filtry[$NazwaPola]['type'] == "sort"){
            FormularzSimple::PoleSelect("sorty[$NazwaPola]", $Sorty, $_SESSION[$this->Parametr]['filtry_kolumn']['sorty'][$NazwaPola]);
            FormularzSimple::PoleSubmit("submit", "&raquo");
        }else if($Filtry[$NazwaPola]['type'] == "sort_table"){
            if($NazwaPola == "id_faktury"){
                foreach($Filtry[$NazwaPola]['elementy'] as $PoleFiltru => $Name){
                    $NewSorty = $Sorty;
                    $NewSorty[""] = $Name;
                    FormularzSimple::PoleSelect("sorty_table[$PoleFiltru]", $NewSorty, $_SESSION[$this->Parametr]['filtry_kolumn']['sorty_table'][$PoleFiltru]);
                    FormularzSimple::PoleSubmit("submit", "&raquo");
                    echo "<br />";
                }
            }else{
                FormularzSimple::PoleSelect("sorty_table[$NazwaPola]", $Sorty, $_SESSION[$this->Parametr]['filtry_kolumn']['sorty_table'][$NazwaPola]);
                FormularzSimple::PoleSubmit("submit", "&raquo");
            }
        }else{
            $Name = $Filtry[$NazwaPola]['type'] == "filtr_id" ? "filtry_id" : "filtry";
            FormularzSimple::PoleSelect("{$Name}[{$NazwaPola}]", Usefull::PolaczDwieTablice(array("" => "---"), $Filtry[$NazwaPola]['elementy']), $_SESSION[$this->Parametr]['filtry_kolumn'][$Name][$NazwaPola], $Filtry[$NazwaPola]['dodatki']);
            FormularzSimple::PoleSubmit("submit", "&raquo");
        }
        echo "</nobr></th>\n";
    }else{
        ?>
        <th<?php echo $Styl; ?>>&nbsp;</th>
        <?php
    }
}
foreach($AkcjeNaLiscie as $Actions){
    ?>
    <th>&nbsp;</th>
    <?php
}
?>
</tr>
<?php
    FormularzSimple::FormEnd();
?>