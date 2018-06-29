<?php
    echo "<b>".($Wartosc['dodatkowe_ubezpieczenie'] == 1 ? "TAK" : "NIE")."</b> dodatkowe ubezpieczenie cargo<br />\n";
    echo "<b>".($Wartosc['dodatkowe_raporty'] == 1 ? "TAK" : "NIE")."</b> dodatkowe raporty o statusie przesyłki<br />\n";
    echo "<span ".($Wartosc['dodatkowe_raporty'] == 0 ? "style='display: none;'" : "").">{$Wartosc['dodatkowe_raporty_godziny']}</span><br />\n";
    echo "<b>TAK</b> akceptuje regulamin świadczenia usług spedycyjnych <br />\n";
?>
