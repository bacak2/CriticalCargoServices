<?php
    $Klienci = UsefullBase::GetKlienciAccessOddzial($this->Baza, $this->Uzytkownik);
    $Klienci = $this->Baza->GetOptions("SELECT id_klient, nazwa  FROM orderplus_klient $warunex ORDER BY nazwa");
    $Klienci = Usefull::PolaczDwieTablice(array(0 => ' -- Wybierz --'), $Klienci);
    $Klasy = UsefullBase::GetPrzewoznikClass($this->Baza);
    $PrzewoznicyArr = UsefullBase::GetPrzewoznicyWithClass($this->Baza);
    $Przewoznicy = Usefull::PolaczDwieTablice(array(0 => array('nazwa' => ' -- Wybierz -- ')), $PrzewoznicyArr);
    $Terms = UsefullBase::GetTerms($this->Baza);
    $Terms = Usefull::PolaczDwieTablice(array(0 => ' -- Wybierz -- '), $Terms);
    $Carriers = UsefullBase::GetCarriers($this->Baza);
    $TakNie = Usefull::GetTakNie2();
?>