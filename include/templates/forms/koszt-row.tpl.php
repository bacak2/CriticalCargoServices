<?php
$Idx = $_POST['next'];
$Form = new FormularzSimple();
$FCL = array();
$Klasy = UsefullBase::GetPrzewoznikClass($this->Baza);
$PrzewoznicyArr = UsefullBase::GetPrzewoznicyWithClass($this->Baza);
$Przewoznicy = Usefull::PolaczDwieTablice(array(0 => array('nazwa' => ' -- Wybierz -- ')), $PrzewoznicyArr);
$Waluty = UsefullBase::GetWaluty($this->Baza);
?>
<tr id="<?php echo "position-row-$Idx"; ?>">
    <td style="border-bottom: 1px solid #888;">
        <?php
                echo ("<select name=\"Koszty[$Idx][id_przewoznik]\">");
                foreach($Przewoznicy as $PID => $PDane){
                    echo("<option value='$PID'".($FCL['id_przewoznik'] == $PID ? ' selected="selected"' : '')."".($PDane['klasa_id'] > 0 ? " style='background-color: {$Klasy[$PDane['klasa_id']]['klasa_color']};'" : "").">{$PDane['nazwa']} ".($PID > 0 ? "({$Klasy[$PDane['klasa_id']]['klasa_nazwa']})" : "")."</option>");
                }
            echo("</select>");
        ?></td>
    <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleInputText("Koszty[$Idx][opis]", $FCL['opis'], "style='width: 200px;'"); ?></td>
    <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Koszty[$Idx][koszt]", $FCL['koszt'], "style='width: 70px;'"); ?></td>
    <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleSelect("Koszty[$Idx][waluta]", $Waluty, $FCL['waluta']); ?></td>
    <td style="border-bottom: 1px solid #888;"><?php $Form->PoleInputText("Koszty[$Idx][kurs]", $FCL['kurs'], "style='width: 80px;'"); ?></td>
    <td style="border-bottom: 1px solid #888; background-color: #F0F0F0;"><?php $Form->PoleButton("Remove-$Idx", "Usun", "onclick='RemovePosition($Idx);'"); ?></td>
</tr>