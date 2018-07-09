<?php
/**
 * Moduł faktury
 *
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Faktury extends ModulBazowy {
    public $Klienci;
    public $Waluty;
    function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
        parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
        if($Parametr == "faktury_nowe"){
            $this->LinkPowrotu = "?modul=tabela_rozliczen_nowa";
            $this->PrzyciskiFormularza['anuluj']['link'] = $this->LinkPowrotu;
        }
        $this->Tabela = 'faktury';
        $this->PoleID = 'id_faktury';
        $this->PoleNazwy = 'numer';
        $this->Nazwa = 'Faktury';
        $this->IloscNaStrone = 50;
        $this->CzySaOpcjeWarunkowe = true;
        $this->Klienci = UsefullBase::GetKlienci($this->Baza);
        $this->Filtry[] = array("opis" => "Pokaż tylko klienta", "nazwa" => "id_klienta", "typ" => "lista", "opcje" => $this->Klienci, 'domyslna' => ' - wybierz klienta - ');
        $this->Filtry[] = array("opis" => "Szukaj NIP", "nazwa" => "nip_search", "typ" => "tekst");
    }

    function &GenerujFormularz($Wartosci, $Mapuj = false) {
        if(isset($_POST['OrdersIDs'])) {
            $stawkaKlient = $this->Baza->GetValue("SELECT sum(stawka_klient) as stawka_klient FROM orderplus_zlecenie WHERE id_zlecenie IN ({$_POST['OrdersIDs']})", "zlecenie_id");
        }

        if(isset($_GET['act']) && $_GET['act'] == "afteradd"){
            $this->PrzyciskiFormularza['zapisz']['src'] = "dalej.gif";
        }
        $Formularz = parent::GenerujFormularz($Wartosci, $Mapuj);
        $Formularz->DodajPole('id_klienta', 'lista', 'Klient', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Klienci, 'wybierz' => true, 'atrybuty' => array('onchange' => 'ValueChange("OpcjaFormularza","przeladuj")')));
        $Formularz->DodajPole('id_zlecenia', 'podzbiór', 'Zlecenie<br /><small>Można zaznaczyć kilka zleceń<br />(trzymając wciśnięty przycisk CTRL)</small>', array('tabelka' => Usefull::GetFormStandardRow(), 'wybierz' => true, 'domyslna' => '--- brak zlecenia ---'));
        $Formularz->DodajPole('szablon_faktura', 'lista', 'Szablon faktury', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array('PL' => 'PL', 'ENG' => 'ENG')));
        $Formularz->DodajPole('firma_wystaw', 'lista', 'Sprzedawca', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(2 => 'MEPP Sp. z o.o.')));
        $Formularz->DodajPole('id_oddzial', 'lista', 'Oddział', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetOddzialy($this->Baza)));
        if($this->WykonywanaAkcja != "dodawanie"){
            $Formularz->DodajPole('numer', 'tekst', 'Numer faktury', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 150px;')));
        }
        $Formularz->DodajPole('data_wystawienia', 'tekst_data', 'Data wystawienia', array('tabelka' => array('tr_start' => 1, 'td_start' => 1, 'th_show' => 1), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('miejsce_wystawienia', 'tekst', null, array('tabelka' => array('td_end' => 1, 'tr_end' => 1), 'atrybuty' => array('style' => 'width: 150px;'), 'opis_dodatkowy_przed' => '<span style="padding-left: 20px;">Miejsce wystawienia: </span>'));
        $Formularz->DodajPole('data_sprzedazy', 'tekst_data', 'Data sprzedaży (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('termin_platnosci', 'tekst_data', 'Termin płatności (RRRR-MM-DD)', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('id_formy', 'lista', 'Forma płatności', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->Baza->GetOptions("SELECT id_formy, forma FROM faktury_formy_platnosci")));
        $Formularz->DodajPole('id_waluty', 'lista', 'Waluta', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => UsefullBase::GetWaluty($this->Baza)));
        $Formularz->DodajPole('kurs', 'tekst', 'Kurs', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 50px;')));
        $Formularz->DodajPole('pozycje', 'pozycje_faktury', 'Pozycje na fakturze', array('tabelka' => Usefull::GetFormStandardRow()));
        if(isset($_POST['OrdersIDs']))  $Formularz->DodajPole('suma', 'tekst', 'Suma', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 150px;', 'value'=> $stawkaKlient, 'stan'=>'readonly')));
        $Formularz->DodajPole('wplacono', 'tekst', 'Wpłacono', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'width: 100px;')));
        $Formularz->DodajPole('status', 'podzbiór_radio', 'Status', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => array(1 => 'faktura opłacona', 0 => 'faktura nieopłacona')));
        $Formularz->DodajPole('uwagi', 'tekst_dlugi', 'Uwagi', array('tabelka' => Usefull::GetFormStandardRow(), 'atrybuty' => array('style' => 'height: 150px;')));
        if(is_array($Wartosci)){
            $Values = $Formularz->ZwrocWartosciPol($Wartosci, $Mapuj);
            if($Values['id_klienta'] > 0 || isset($_POST['OrdersIDs'])){
                if($this->WykonywanaAkcja == "dodawanie" && !isset($_POST['OrdersIDs'])){
                    $Formularz->UstawOpcjePola('id_zlecenia', "elementy",  $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie z WHERE id_klient = '{$Values['id_klienta']}' AND id_faktury = '0' AND ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) ORDER BY numer_zlecenia_krotki DESC"), false);
                }elseif(isset($_POST['OrdersIDs'])){
                    //echo "SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie z WHERE id_klient = '{$Values['id_klienta']}' AND (id_faktury = '0' OR id_faktury = '{$_GET['id']}') AND ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) ORDER BY numer_zlecenia_krotki DESC"; exit();
                    //$Formularz->UstawOpcjePola('id_zlecenia', "elementy",  $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie z WHERE id_klient = '{$Values['id_klienta']}' AND (id_faktury = '0' OR id_faktury = '{$_GET['id']}') AND ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) ORDER BY numer_zlecenia_krotki DESC"), false);
                    $Formularz->UstawOpcjePola('id_zlecenia', "elementy",  $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie z WHERE id_klient = '732' AND (id_faktury = '0' OR id_faktury = '3199') AND ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) ORDER BY numer_zlecenia_krotki DESC"), false);
                }else{
                    $Formularz->UstawOpcjePola('id_zlecenia', "elementy",  $this->Baza->GetOptions("SELECT id_zlecenie, numer_zlecenia FROM orderplus_zlecenie z WHERE id_klient = '{$Values['id_klienta']}' AND (id_faktury = '0' OR id_faktury = '{$_GET['id']}') AND ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) ORDER BY numer_zlecenia_krotki DESC"), false);
                }
            }

        }
        if($this->WykonywanaAkcja != "szczegoly"){
            $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
        }
        return $Formularz;
    }

    function AkcjaDodawanie() {
        $DodajOdRazu = false;
        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $Zlecenie = $this->Baza->GetData("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie = '{$_GET['id']}'");
            $ZlecenieIds = array($_GET['id']);
            $ClientID = $Zlecenie['id_klient'];
            $DodajOdRazu = true;
        }
        if(isset($_POST['OrdersIDs']) && $_POST['OrdersIDs'] != ""){
            $ids = explode(",", $_POST['OrdersIDs']);
            $ClientID = false;
            $ZlecenieIds = array();
            foreach($ids as $ZlecID){
                $CID = $this->Baza->GetValue("SELECT id_klient FROM orderplus_zlecenie WHERE id_zlecenie = '$ZlecID'");
                if($ClientID == false){
                    $ClientID = $CID;
                }
                if($ClientID == $CID){
                    $ZlecenieIds[] = $ZlecID;
                }
            }
            $DodajOdRazu = true;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$DodajOdRazu) {
            $Formularz = $this->GenerujFormularz($_POST, true);
            $PolaWymagane = $Formularz->ZwrocPolaWymagane();
            $PolaZdublowane = $Formularz->ZwrocPolaNieDublujace();
            $OpcjaFormularza = (isset($_POST['OpcjaFormularza']) ? $_POST['OpcjaFormularza'] : 'zapisz');
            $Wartosci = $Formularz->ZwrocWartosciPol($_POST);
            if ($OpcjaFormularza == 'zapisz'){
                echo "<div style='clear: both;'></div>\n";
                if($this->SprawdzDane($Wartosci) && $this->SprawdzPliki($Formularz->ZwrocDanePrzeslanychPlikow()) && $this->SprawdzPolaWymagane($PolaWymagane, $Wartosci) && $this->SprawdzPolaNieDublujace($PolaZdublowane, $Wartosci)){
                    if ($this->ZapiszDaneElementu($Formularz, $Wartosci, $Formularz->ZwrocDanePrzeslanychPlikow())) {
                        $this->ShowOK();
                        return;
                    }
                    else {
                        Usefull::ShowKomunikatError('<b>Wystąpił problem. Dane nie zostały zapisane.</b><br/>'.$this->Baza->GetLastErrorDescription());
                    }
                }else{
                    Usefull::ShowKomunikatError('<b>'.$this->Error.'</b>');
                }
            }
            $Wartosci = $this->AkcjaPrzeladowanie($Wartosci, $Formularz);
            $Formularz = $this->AkcjaPrzeladowanieFormularz($Wartosci, $Formularz);
            foreach($this->PolaWymaganeNiewypelnione as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            foreach($this->PolaZdublowane as $NazwaPola){
                $Formularz->UstawOpcjePola($NazwaPola, 'atrybuty', Usefull::WstawienieDoTablicy('style','background-color: #FFC0C0;',$Formularz->ZwrocOpcjePola($NazwaPola,'atrybuty', false)),false);
            }
            $Formularz->Wyswietl($Wartosci, false);
        }
        elseif($DodajOdRazu){
            $Dane = $this->PobierzDaneDoFakturyZbiorczej();
            $Formularz = $this->GenerujFormularz($Dane, false);
            //var_dump($Dane); exit();
            $Formularz->Wyswietl($Dane, false);
        }
        else {
            $DaneDomyslne = $this->PobierzDaneDomyslne();
            $Formularz = $this->GenerujFormularz($DaneDomyslne, false);
            $Formularz->Wyswietl($DaneDomyslne, false);
        }
    }


    function PobierzDaneDoFakturyZbiorczej() {

        $Dane['id_zlecenia'] = $this->Baza->GetValues("SELECT id_zlecenie FROM orderplus_zlecenie WHERE id_zlecenie IN ({$_POST['OrdersIDs']})");
        $Dane['id_klienta'] = $this->Baza->GetValue("SELECT id_klient FROM orderplus_zlecenie WHERE id_zlecenie IN ({$_POST['OrdersIDs']}) LIMIT 1");
        $Dane['miejsce_wystawienia'] = "Warszawa";
        $Dane['data_wystawienia'] = $this->Dzis;
        $Dane['szablon_faktura'] = "PL";
        $Dane['id_oddzial'] = 2;
        $Dane['pozycje'][0] = array('lp' => 1);
        $Dane['firma_wystaw'] = 2;
        $Dane['id_waluty'] = $this->Baza->GetValue("SELECT id_waluty FROM orderplus_zlecenie oz LEFT JOIN faktury_waluty fw ON fw.waluta = oz.waluta WHERE id_zlecenie IN (4495,4496,4497,4498) LIMIT 1");


        //echo "SELECT * FROM $this->Tabela WHERE $this->PoleID = '$ID'";
        /*$Dane = $this->Baza->GetData("SELECT * FROM orderplus_zlecenie WHERE id_zlecenie IN ({$_POST['OrdersIDs']}) LIMIT 1");
        $ClientDane = $this->Baza->GetData("SELECT * FROM orderplus_klient WHERE id_klient = '{$Dane['id_klient']}'");
        $this->ZleceniaDoFaktury = $this->Baza->GetOptions("SELECT z.id_zlecenie, z.numer_zlecenia FROM orderplus_zlecenie z WHERE ((z.ost_korekta = 1) OR (z.ost_korekta = 0 AND z.korekta = 0)) AND z.id_klient='{$Dane['id_klient']}' AND id_faktury = '0' AND waluta = '{$Dane['waluta']}'");
        $Faktura['data_wystawienia'] = $this->Dzis;
        $Faktura['miejsce_wystawienia'] = "Warszawa";
        $Faktura['data_sprzedazy'] = $Dane['termin_rozladunku'];
        $Faktura['termin_platnosci'] = date('Y-m-d', strtotime($Faktura['data_wystawienia'].'+'.$Dane['termin_platnosci_dni'].' days'));
        $Faktura['zlecenia_faktura'][] = $this->Baza->GetValues("SELECT id_zlecenie FROM orderplus_zlecenie WHERE id_zlecenie IN ({$_POST['OrdersIDs']})");
        $Faktura['firma_wystaw'] = $Dane['firma_wystaw'];

        $Faktura['szablon_faktura'] = ($ClientDane['siedziba_id'] == 2 ? "ENG" : "PL");
        $Faktura['waluta'] = ($ClientDane['waluta_fakturowania'] != "" ? $ClientDane['waluta_fakturowania'] : $Dane['waluta']);
        $Faktura['kurs'] = UsefullBase::PobierzKursDoFaktury($this->Baza, $Dane['waluta'], $Faktura['waluta'], $Dane['termin_zaladunku'], $Dane['id_klient']);

        $Dane   = $Faktura;
        */

        return $Dane;
    }

    function GenerujWarunki($AliasTabeli = null) {
        $Where = $this->DomyslnyWarunek();
        if (isset($_SESSION['Filtry']) && count($_SESSION['Filtry'])) {
            for ($i = 0; $i < count($this->Filtry); $i++) {
                $Pole = $this->Filtry[$i]['nazwa'];
                if (isset($_SESSION['Filtry'][$Pole])) {
                    $Wartosc = $_SESSION['Filtry'][$Pole];
                    if($this->Filtry[$i]['typ'] == "lista"){
                        $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole = '$Wartosc'";
                    }else{
                        if($Pole == "nip_search"){
                            $Clients = UsefullBase::GetKlienciByNip($Baza, $Wartosc);
                            $Clients[] = -1;
                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."id_klient IN(".imlode(",",$Clients).")";
                        }else{
                            $Where .= ($Where != '' ? ' AND ' : '').($AliasTabeli ? "$AliasTabeli." : '')."$Pole LIKE '%$Wartosc%'";
                        }
                    }
                }
            }
        }
        return ($Where != '' ? "WHERE $Where" : '');
    }

    function DomyslnyWarunek(){
        if($this->Uzytkownik->CheckNoOddzial()){
            $Klienci = array_keys(UsefullBase::GetKlienciAccessOddzial($this->Baza, $this->Uzytkownik));
        }
        return "f.data_wystawienia >= '{$_SESSION['okresStart']}-01' AND f.data_wystawienia <= '{$_SESSION['okresEnd']}-31'".($this->Uzytkownik->CheckNoOddzial() ? " AND (z.id_oddzial = '{$_SESSION['id_oddzial']}' OR k.id_klient IN(".implode(',',$Klienci)."))" : "");
    }

    function PobierzListeElementow($Filtry = array()) {
        $this->Waluty = UsefullBase::GetWaluty($this->Baza);
        $Wynik = array(
            "numer" => 'Numer',
            "data_wystawienia" => array('naglowek' => 'Data wystawienia', 'td_styl' => 'text-align: center;'),
            "miejsce_wystawienia" => array('naglowek' => 'Miejsce wystawienia', 'td_styl' => 'text-align: center;'),
            "data_sprzedazy" => array('naglowek' => 'Data sprzedaży', 'td_styl' => 'text-align: center;'),
            "termin_platnosci" => array('naglowek' => 'Termin płatności', 'td_styl' => 'text-align: center;'),
            "netto" => array('naglowek' => 'Netto', 'td_styl' => 'text-align: center;'),
            "brutto" => array('naglowek' => 'Brutto', 'td_styl' => 'text-align: center;')
        );
        return $Wynik;
    }

    function AkcjaLista($Filtry = array()){
        $this->WykonajAkcjeDodatkowa();
        $Pola = $this->PobierzListeElementow($Filtry);
        $Puste = array();
        $AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($Puste, true);
        ?>
        <form name="" action="drukuj_faktury_zbiorczo.php" method="post">
            <table class="lista">
                <tr>
                    <th class='licznik'>Lp</th>
                    <?php
                    foreach ($Pola as $NazwaPola => $Opis) {
                        ?>
                        <?php
                        $Styl = '';
                        if(is_array($Opis)){
                            $Styl = (isset($Opis['styl']) ? " style='{$Opis['styl']}'" : '');
                            $Opis = $Opis['naglowek'];
                        }
                        echo "<th$Styl>";
                        $SortHow = (!isset($_GET['sort']) ? "ASC" : ($_GET['sort'] != $NazwaPola ? "ASC" : ((!isset($_GET['sort_how']) || $_GET['sort_how'] == "DESC") ? "ASC" : "DESC")));
                        echo "<a href='?modul=$this->Parametr&akcja=$this->WykonywanaAkcja&sort=$NazwaPola&sort_how=$SortHow'>$Opis</a>";
                        echo "</th>";
                        ?>
                        <?php
                    }
                    foreach($AkcjeNaLiscie as $Actions){
                        if($Actions['title'] == 'Usuń specyfikacje') echo "<th class='ikona'><img class='ikonka' src='images/buttons/add_grey.png' title='{$Actions['title']}' alt='Dodaj/usuń specyfikację'></th>";
                        else echo "<th class='ikona'><img class='ikonka' src='images/buttons/{$Actions['img']}_grey.png' title='{$Actions['title']}' alt='{$Actions['title']}'></th>";
                    }
                    ?>
                </tr>
                <?php
                $Faktury = $this->PobierzFaktury();
                if($this->ParametrPaginacji == 0){
                    $Biezaca = 0;
                    $Strona = 0;
                }else{
                    $Biezaca = $this->IloscNaStrone * $this->ParametrPaginacji;
                    $Strona = $this->ParametrPaginacji;
                }
                $BiezacaEnd = $Biezaca+$this->IloscNaStrone;
                $IloscNewsow = count($Faktury);
                $this->IleStronPaginacji = ceil($IloscNewsow / $this->IloscNaStrone);
                $Licznik = $Strona * $this->IloscNaStrone + 1;
                for($i = $Biezaca; $i < $BiezacaEnd; $i++){
                    if(isset($Faktury[$i])){
                        $this->ShowTR($Pola, $Faktury[$i], $Licznik, $AkcjeNaLiscie);
                        $Licznik++;
                    }
                }
                ?>
                <tr>
                    <th class='licznik'>&nbsp;</th>
                    <?php
                    foreach ($Pola as $NazwaPola => $Opis) {
                        echo "<th>&nbsp;</th>";
                    }
                    echo "<th class='ikona' colspan='3'><input type='submit' value='drukuj zaznaczone' class='form-button' /></th>";
                    echo "<th>&nbsp;</th>";
                    echo "<th>&nbsp;</th>";
                    ?>
                </tr>

            </table>
        </form>
        <?php
        echo("<table class='paginacja_table'>");
        echo("<tr>");
        echo("<td>");
        Usefull::ShowPagination("?modul=$this->Parametr".(isset($_GET['sort']) ? "&sort={$_GET['sort']}" : "").(isset($_GET['sort_how']) ? "&sort_how={$_GET['sort_how']}" : ""), $this->ParametrPaginacji, 10, $this->IleStronPaginacji);
        echo("</td>");
        echo("</tr>");
        echo("</table>");
    }

    function ShowTR($Pola, $faktura, $Licznik, $AkcjeNaLiscie){
        $TabPozycje = ($faktura['typek'] == "morska" ? "orderplus_sea_orders_faktury_pozycje" : ($faktura['typek'] == "lotnicza" ? "orderplus_air_orders_faktury_pozycje"  : "faktury_pozycje"));
        $this->Baza->Query("SELECT *, SUM(netto) as suma_netto, SUM(brutto) as suma_brutto FROM $TabPozycje WHERE id_faktury = {$faktura['id_faktury']} GROUP BY vat DESC");
        $faktura['netto'] = 0;
        $faktura['brutto'] = 0;
        while($pozycje = $this->Baza->GetRow()){
            $faktura['brutto'] += $pozycje['suma_brutto'];
            $faktura['netto'] += $pozycje['suma_netto'];
        }
        $faktura['brutto'] = number_format($faktura['brutto'], 2, ',', ' ');
        $faktura['netto'] = number_format($faktura['netto'], 2, ',', ' ');
        $KolorWiersza = ($Licznik % 2 != 0 ? '#FFFFFF' : '#F6F6F6');
        echo("<tr style='background-color: $KolorWiersza;'>");
        echo("<td class='licznik'>$Licznik</td>");
        foreach ($Pola as $Nazwa => $Opis) {
            $Styl = "";
            if(is_array($Opis)){
                $Styl = (isset($Opis['td_styl']) ? " style='{$Opis['td_styl']}'" : '');
                if(isset($Opis['elementy'])){
                    $faktura[$Nazwa] = $Opis['elementy'][$Element[$Nazwa]];
                }
                if(isset($Opis['type']) && $Opis['type'] == "date"){
                    $faktura[$Nazwa] = ($Element[$Nazwa] == "0000-00-00" ? "&nbsp;" : $Element[$faktura]);
                }
            }
            $this->ShowRecord($faktura, $Nazwa, $Styl);
        }
        if($this->CzySaOpcjeWarunkowe){
            $AkcjeNaLiscie = $this->PobierzAkcjeNaLiscie($faktura);
        }
        $this->ShowActionsList($AkcjeNaLiscie, $faktura);
        echo("</tr>");
    }

    function ShowActionsList($AkcjeNaLiscie, $Element){
        $PaginParam = ($this->ParametrPaginacji > 0 ? "&pagin=$this->ParametrPaginacji" : "");
        foreach ($AkcjeNaLiscie as $Actions){
            echo("<td class='ikona'>");
            if(!isset($Actions['hidden']) || !$Actions['hidden']){
                if(isset($Actions['img'])){
                    if(!in_array($Element[$this->PoleID], $this->ZablokowaneElementyIDs)){
                        if($Actions['akcja']){
                            echo "<a href=\"?modul=$this->Parametr&akcja={$Actions['akcja']}&id={$Element[$this->PoleID]}$PaginParam\"><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                        }else if($Actions['akcja_href']){
                            echo "<a href=\"{$Actions['akcja_href']}id={$Element[$this->PoleID]}\" target='_blank'><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                        }else if($Actions['akcja_link']){
                            echo "<a href=\"{$Actions['akcja_link']}\"".($Actions['target'] ? " target='_blank'" : "")."><img src=\"images/buttons/{$Actions['img']}.png\" onmouseover='this.src=\"images/buttons/{$Actions['img']}_hover.png\"' onmouseout='this.src=\"images/buttons/{$Actions['img']}.png\"' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\"></a>";
                        }else{
                            echo "<img src=\"images/buttons/{$Actions['img']}.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                        }
                    }else{
                        echo "<img src=\"images/buttons/{$Actions['img']}_grey.png\" class='ikonka' title=\"{$Actions['title']}\" alt=\"{$Actions['title']}\">";
                    }
                }else{
                    if($Actions['type'] == "checkbox"){
                        echo "<input type='checkbox' name='Drukuj[{$Actions['typek']}][]' value='{$Actions['id']}' />";
                    }else{
                        echo "&nbsp;";
                    }
                }
            }
            echo "</td>\n";
        }
    }

    function PobierzFaktury(){
        $Where = $this->GenerujWarunki("f");
        $this->Baza->Query("SELECT DISTINCT(f.id_faktury), f.* FROM faktury f
                                    LEFT JOIN orderplus_zlecenie z ON(z.id_faktury = f.id_faktury)
                                    LEFT JOIN orderplus_klient k ON(k.id_klient = f.id_klienta)
                                    $Where ORDER BY f.autonumer DESC, f.id_faktury DESC");

        while($faktura = $this->Baza->GetRow()){
            $faktura['typek'] = "normal";
            $Faktury[] = $faktura;
            $Numerki[] = $faktura['autonumer'];
        }

        $this->Baza->Query("SELECT * FROM orderplus_sea_orders_faktury f
                                LEFT JOIN orderplus_sea_orders z ON(z.id_zlecenie = f.id_zlecenia)
                                LEFT JOIN orderplus_klient k ON(k.id_klient = f.id_klienta)
                                $Where ORDER BY f.autonumer DESC, f.id_faktury DESC");
        while($faktura = $this->Baza->GetRow()){
            $faktura['typek'] = "morska";
            $Faktury[] = $faktura;
            $Numerki[] = $faktura['autonumer'];
        }
        $this->Baza->Query("SELECT * FROM orderplus_air_orders_faktury f
                                LEFT JOIN orderplus_air_orders z ON(z.id_zlecenie = f.id_zlecenia)
                                LEFT JOIN orderplus_klient k ON(k.id_klient = f.id_klienta)
                                $Where ORDER BY f.autonumer DESC, f.id_faktury DESC");
        while($faktura = $this->Baza->GetRow()){
            $faktura['typek'] = "lotnicza";
            $Faktury[] = $faktura;
            $Numerki[] = $faktura['autonumer'];
        }
        array_multisort($Numerki, SORT_DESC, $Faktury);
        return $Faktury;
    }

    function PobierzDaneElementu($ID){
        $Dane = parent::PobierzDaneElementu($ID);
        $Dane['id_zlecenia'] = $this->Baza->GetValues("SELECT id_zlecenie FROM orderplus_zlecenie WHERE id_faktury = '$ID'");
        $Dane['pozycje'] = $this->Baza->GetRows("SELECT * FROM faktury_pozycje WHERE id_faktury = '$ID' ORDER BY id_pozycji");
        return $Dane;
    }

    function PobierzAkcjeNaLiscie($Dane = array()){
        $Akcje = array();
        if($Dane['specyfikacja_id'] != 0)    $Akcje[] = array('img' => "printer_button", 'title' => "Drukuj", "akcja_link" => ($Dane['typek'] == "morska" ? "drukuj_fakture_morska?id={$Dane[$this->PoleID]}&soid={$Dane['id_zlecenie']}" : ($Dane['typek'] == "lotnicza" ? "drukuj_fakture_lotnicze?id={$Dane[$this->PoleID]}&aoid={$Dane['id_zlecenie']}" : "drukuj_fakture.php?id={$Dane[$this->PoleID]}&spec=1")));
        else    $Akcje[] = array('img' => "printer_button", 'title' => "Drukuj", "akcja_link" => ($Dane['typek'] == "morska" ? "drukuj_fakture_morska?id={$Dane[$this->PoleID]}&soid={$Dane['id_zlecenie']}" : ($Dane['typek'] == "lotnicza" ? "drukuj_fakture_lotnicze?id={$Dane[$this->PoleID]}&aoid={$Dane['id_zlecenie']}" : "drukuj_fakture.php?id={$Dane[$this->PoleID]}")));

        $Akcje[] = array('type' => 'checkbox', 'typek' => $Dane['typek'], 'id' => $Dane[$this->PoleID]);
        $Akcje[] = array('img' => "edit_button", 'title' => "Edycja", "akcja_link" => ($Dane['typek'] == "morska" ? "?modul=faktury_morskie&akcja=edycja&id={$Dane[$this->PoleID]}&soid={$Dane['id_zlecenie']}" : ($Dane['typek'] == "lotnicza" ? "?modul=faktury_lotnicze&akcja=edycja&id={$Dane[$this->PoleID]}&aoid={$Dane['id_zlecenie']}" : "?modul=faktury&akcja=edycja&id={$Dane[$this->PoleID]}")));

        if($Dane['specyfikacja_id'] != 0) $Akcje[] = array('img' => "delete_button", 'title' => "Usuń specyfikacje", "akcja_link" => "?modul=faktury&akcja=usun_specyfikacje&id={$Dane[$this->PoleID]}");
        else    $Akcje[] = array('img' => "add_specification", 'title' => "Dodaj specyfikacje", "akcja_link" => "?modul=faktury&akcja=dodaj_specyfikacje&id={$Dane[$this->PoleID]}");

        $Akcje[] = array('img' => "delete_button", 'title' => "Kasowanie", "akcja_link" => ($Dane['typek'] == "morska" ? "?modul=faktury_morskie&akcja=kasowanie&id={$Dane[$this->PoleID]}" : ($Dane['typek'] == "lotnicza" ? "?modul=faktury_lotnicze&akcja=kasowanie&id={$Dane[$this->PoleID]}" : "?modul=faktury&akcja=kasowanie&id={$Dane[$this->PoleID]}")));
        //var_dump($Dane);
        return $Akcje;
    }

    function PobierzDaneDomyslne() {
        $DaneDomyslne['miejsce_wystawienia'] = "Warszawa";
        $DaneDomyslne['data_wystawienia'] = $this->Dzis;
        $DaneDomyslne['szablon_faktura'] = "PL";
        $DaneDomyslne['id_oddzial'] = 2;
        $DaneDomyslne['pozycje'][0] = array('lp' => 1);
        $DaneDomyslne['firma_wystaw'] = 2;
        return $DaneDomyslne;
    }

    function ShowRecord($Element, $Nazwa, $Styl){
        if($Nazwa == "numer" || $Nazwa == "termin_platnosci"){
            $statkolor = ($Element['status'] == 1 ? '' : ' style="color: #9a0000; text-align: center;"');
            echo("<td$statkolor>{$Element[$Nazwa]}</td>");
        }else if($Nazwa == "netto" || $Nazwa == "brutto"){
            echo "<td$Styl><nobr>\n";
            echo $Element[$Nazwa]." <small>".$this->Waluty[$Element['id_waluty']]."</small>";
            echo "</nobr></td>\n";
        }else{
            echo("<td$Styl>".stripslashes($Element[$Nazwa])."</td>");
        }
    }

    function AkcjaDodajSpecyfikacje(){
        $this->Baza->Query("UPDATE faktury SET specyfikacja_id = '1' WHERE id_faktury = '{$_GET['id']}'");
        Usefull::ShowKomunikatOK("<b>Specyfikacja dodana.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
    }

    function AkcjaUsunSpecyfikacje(){
        $this->Baza->Query("UPDATE faktury SET specyfikacja_id = '0' WHERE id_faktury = '{$_GET['id']}'");
        Usefull::ShowKomunikatOK("<b>Specyfikacja usunięta.</b><br/><br/><a href=\"$this->LinkPowrotu\"><img src='images/ok.gif' title='Powrót' alt='Powrót' style='display: inline; vertical-align: middle;'></a>");
    }

    function AkcjaDrukuj($ID, $Akcja){
        if($this->SprawdzUprawnienie("faktury")){
            if($Akcja == "wydruk" || $Akcja == "wydruk-new"){
                $Zap = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
                                                        LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
                                                        LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
                                                        LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
                                                        WHERE f.id_faktury = '$ID'".($ID > 17162 || $ID == 17153 ? " ORDER BY p.id_pozycji ASC" : "");
                $faktura = $this->Baza->GetData($Zap);

                if($faktura['szablon_faktura'] == 'ENG'){
                    include(SCIEZKA_INCLUDE."faktura_lang/eng.php");
                }else{
                    include(SCIEZKA_INCLUDE."faktura_lang/pl.php");
                }
                # zmiana szablonu faktury od 26.09.2012 #
                if($faktura['data_wystawienia'] >= '2012-09-26' || $Akcja == "wydruk-new"){
                    $all_pozycje_baza = $this->Baza->GetRows($Zap);
                    $pogrupowane_pozycje = array();
                    $idx = 0;
                    $k = 1;
                    foreach($all_pozycje_baza as $pozycje_z_bazy){
                        $pogrupowane_pozycje[$idx][] = $pozycje_z_bazy;
                        if($k % 9 == 0){
                            $idx++;
                        }
                        $k++;
                    }
                    $dane_konta = $this->getAccount($faktura);
                    foreach($pogrupowane_pozycje as $key => $Pozycje){
                        $next = $key+1;
                        $lp = 1 + ($key * 9);
                        $koniec_faktury = (isset($pogrupowane_pozycje[$next]) && count($pogrupowane_pozycje[$next]) > 0 ? false : true);
                        /*include(SCIEZKA_SZABLONOW."druki/drukuj-fakture-new-design.tpl.php");*/
                        include(SCIEZKA_SZABLONOW."druki/drukuj-fakture.tpl.php");
                    }
                }else{
                    include(SCIEZKA_SZABLONOW."druki/drukuj-fakture.tpl.php");
                }
            }else if($Akcja == "wydruk_zbiorczy"){
                if(isset($_POST['FakturyIDs'])){
                    $ids = explode(",", $_POST['FakturyIDs']);
                    foreach($ids as $id){
                        $this->AkcjaDrukuj($id, "wydruk");
                    }
                }
                if(count($_POST['Drukuj']['normal']) > 0){
                    foreach($_POST['Drukuj']['normal'] as $id){
                        $this->AkcjaDrukuj($id, "wydruk");
                        $Zap = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
                                                        LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
                                                        LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
                                                        LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
                                                        WHERE f.id_faktury = '$id'".($id > 17162 || $id == 17153 ? " ORDER BY p.id_pozycji ASC" : "");
                        $faktura = $this->Baza->GetData($Zap);
                        if(($faktura['specyfikacja_id']) != 0){
                            $_GET['ids'] = $faktura['id_faktury'];
                            $spec = new Specyfikacja($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $spec->AkcjaDrukuj(1, "wydruk");
                        }else{
                            $_GET['ids'] = 0;
                        }
                    }
                }

                if(count($_POST['Drukuj']['morska']) > 0){
                    $FakturyMorskie = new FakturyMorskie($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                    foreach($_POST['Drukuj']['morska'] as $id){
                        $FakturyMorskie->AkcjaDrukuj($id, "wydruk_zbiorczy");
                        $Zap = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
                                                        LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
                                                        LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
                                                        LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
                                                        WHERE f.id_faktury = '$id'".($id > 17162 || $id == 17153 ? " ORDER BY p.id_pozycji ASC" : "");
                        $faktura = $this->Baza->GetData($Zap);
                        if(($faktura['specyfikacja_id']) != 0){
                            $_GET['ids'] = $faktura['id_faktury'];
                            $spec = new Specyfikacja($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $spec->AkcjaDrukuj(1, "wydruk");
                        }else{
                            $_GET['ids'] = 0;
                        }
                    }
                }
                if(count($_POST['Drukuj']['lotnicza']) > 0){
                    $FakturyLotnicze = new FakturyLotnicze($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                    foreach($_POST['Drukuj']['lotnicza'] as $id){
                        $FakturyLotnicze->AkcjaDrukuj($id, "wydruk_zbiorczy");
                        $Zap = "SELECT fw.waluta, fw.kurs, fp.forma, fp.forma_en, p.*, f.* FROM faktury f
                                                        LEFT JOIN faktury_pozycje p ON f.id_faktury = p.id_faktury
                                                        LEFT JOIN faktury_formy_platnosci fp ON f.id_formy = fp.id_formy
                                                        LEFT JOIN faktury_waluty fw ON f.id_waluty = fw.id_waluty
                                                        WHERE f.id_faktury = '$id'".($id > 17162 || $id == 17153 ? " ORDER BY p.id_pozycji ASC" : "");
                        $faktura = $this->Baza->GetData($Zap);
                        if(($faktura['specyfikacja_id']) != 0){
                            $_GET['ids'] = $faktura['id_faktury'];
                            $spec = new Specyfikacja($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
                            $spec->AkcjaDrukuj(1, "wydruk");
                        }else{
                            $_GET['ids'] = 0;
                        }
                    }
                }
            }else if($Akcja == "zestawienie"){
                include(SCIEZKA_SZABLONOW."druki/zestawienie-faktur.tpl.php");
            }
        }
    }

    function ShowNaglowekDrukuj($Akcja){
        if($Akcja == "zestawienie"){
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_raporty.tpl.php');
        }else{
            include(SCIEZKA_SZABLONOW.'naglowek_drukuj_faktura.tpl.php');
        }
    }

    function AkcjaPrzeladowanie($Wartosci, $Formularz, $ID = null){
        $Wartosci['pozycje'] = $_POST['Faktura']['Pos'];
        return $Wartosci;
    }

    function ShowBigButtonActions($ID){
        echo "<div style='margin-bottom: 10px; padding: 10px; width: 98%; float: left; border-bottom: 1px solid #bbce00;'>";
        if(is_null($ID)){
            echo("<div style='float: left; display: inline;'>");
            echo "<a href='zestawienie_faktur.php' target='_blank' class='form-button'>Zestawienie faktur</a>";
            echo ("</div>");
        }
        include(SCIEZKA_SZABLONOW."nav.tpl.php");
        echo "</div>\n";
        if($this->WykonywanaAkcja != "dodawanie" && $this->WykonywanaAkcja != "specyfikacja" && is_null($ID)){
            include(SCIEZKA_SZABLONOW."filters.tpl.php");
        }
        echo "<div style='clear: both'></div>\n";
    }

    function UsunElement($ID){
        if($this->Baza->Query("DELETE FROM faktury WHERE id_faktury = '$ID'")){
            $this->Baza->Query("DELETE FROM faktury_pozycje WHERE id_faktury = '$ID'");
            $this->Baza->Query("UPDATE orderplus_zlecenie SET id_faktury = '0', fifo = null WHERE id_faktury = '$ID'");
            return true;
        }
        return false;
    }

    function ZapiszDaneElementu($Formularz, $Wartosci, &$PrzeslanePliki = null, $ID = null) {
        $Zlecenia = $Wartosci['id_zlecenia'];
        unset($Wartosci['id_zlecenia']);
        $Wartosci['wplacono'] = ($Wartosci['wplacono'] != "" ?  str_replace(",",".",$Wartosci['wplacono']) : "");
        if ($ID) {
            $AutoN = explode("/", $Wartosci['numer']);
            $Wartosci['autonumer'] = $AutoN[0];
            $Zapytanie = $this->Baza->PrepareUpdate($this->Tabela, $Wartosci, array($this->PoleID => $ID));
        }
        else {
            $Wartosci = $this->GenerujNumer($Wartosci);
            $Zapytanie = $this->Baza->PrepareInsert($this->Tabela, $Wartosci);
        }

        if ($this->Baza->Query($Zapytanie)) {
            if (!$ID) {
                $ID = $this->Baza->GetLastInsertID();
            }
            $this->ID = $ID;
            if($this->Baza->Query("DELETE FROM faktury_pozycje WHERE id_faktury = '$ID'")){
                $this->Baza->Query("UPDATE orderplus_zlecenie SET id_faktury = '0', fifo = null WHERE id_faktury = '$ID'");
                foreach($Zlecenia as $Value){
                    $UpdateZlecenie['id_faktury'] = $ID;
                    //$UpdateZlecenie['kurs'] = $Wartosci['kurs'];
                    $UpdateZlecenie['termin_wlasny'] = $Wartosci['termin_platnosci'];
                    //$UpdateZlecenie['stawka_vat_klient'] = $Wartosci['vat'];
                    $UpdateZlecenie['faktura_wlasna'] = $Wartosci['numer'];
                    $DataWplywu = $this->Baza->GetValue("SELECT data_wplywu FROM orderplus_zlecenie WHERE id_zlecenie = '$Value'");
                    $UpdateZlecenie['fifo'] = Usefull::ObliczIloscDniMiedzyDatami($Wartosci['data_wystawienia'], $DataWplywu);
                    //przypisuje w tabeli orderplus_zlecenia id_faktury bierzącej faktury
                    $ZapytanieZlecenie = $this->Baza->PrepareUpdate("orderplus_zlecenie", $UpdateZlecenie, array('id_zlecenie' => $Value));
                    # var_dump($ZapytanieZlecenie);
                    $this->Baza->Query($ZapytanieZlecenie);
                }
                foreach($_POST['Faktura']['Pos'] as $Pozycja){
                    unset($Pozycja['lp']);
                    $Pozycja['id_faktury'] = $ID;
                    $Zapytanie = $this->Baza->PrepareInsert("faktury_pozycje", $Pozycja);
                    $this->Baza->Query($Zapytanie);
                }
            }
            return true;
        }
        return false;
    }

    function GenerujNumer($Wartosci){
        $dataExp = explode("-", $Wartosci['data_wystawienia']);
        $autorok = $dataExp[0];
        $automiesiac = $dataExp[1];
        $autonumer = $this->Baza->GetValue("SELECT MAX(autonumer) FROM faktury WHERE firma_wystaw = '{$Wartosci['firma_wystaw']}' AND data_wystawienia >= '2013-01-11' AND data_wystawienia LIKE '$autorok-$automiesiac-%' AND id_oddzial = '{$Wartosci['id_oddzial']}'");
        $autonumer++;
        $OddzialDane = $this->Baza->GetData("SELECT * FROM orderplus_oddzial WHERE id_oddzial = '{$Wartosci['id_oddzial']}'");

        if($autonumer <= 9){
            $autonumer_f = sprintf('%02d',$autonumer);
        }else{
            $autonumer_f = $autonumer;
        }

        $Wartosci['numer'] = "$autonumer_f/$automiesiac/$autorok";
        $Wartosci['autonumer'] = $autonumer;
        return $Wartosci;
    }

    function WyswietlAJAX($Akcja){
        if($Akcja == "get-action-list"){
            $Akcje = array();
            $specyfikacja = $this->Baza->GetValue("SELECT specyfikacja_id FROM faktury where id_faktury = '{$_POST['id']}' LIMIT 1");
            if($specyfikacja != 0)    $Akcje[] = array('title' => "Drukuj", "akcja_href" => "drukuj_fakture.php?spec=1&", "_blank" => true);
            else    $Akcje[] = array('title' => "Drukuj", "akcja_href" => "drukuj_fakture.php?", "_blank" => true);
            $Akcje[] = array('title' => "Edycja", "akcja_href" => "?modul=faktury_nowe&akcja=edycja&");
            $Akcje[] = array('title' => "Kasowanie", "akcja_href" => "?modul=faktury_nowe&akcja=kasowanie&");
            $this->ShowActionInPopup($Akcje, $_POST['id']);
        }
    }

    function ShowOK(){
        if(isset($_GET['act']) && $_GET['act'] == "afteradd"){
            $ZlecenieID = $this->Baza->GetValue("SELECT id_zlecenie FROM orderplus_zlecenie WHERE id_faktury = '{$_GET['id']}' LIMIT 1");
            //echo "SELECT id_zlecenie FROM orderplus_zlecenie WHERE id_faktury = '{$_GET['id']}' LIMIT 1";
            Usefull::RedirectLocation("?modul=platnosci_nowe&akcja=edycja&id=$ZlecenieID");
            ////parent::ShowOK();
        }else{
            parent::ShowOK();
        }
    }

    function getAccount($faktura){
        $dane_konta = array();
        if($faktura['id_faktury'] > 18720){
            $dane_konta['bank'] = array("Bank PKO BP S.A.", "ul. Światowida 47", "03-144 Warszawa");
            $dane_konta['swift'] = "BPKOPLPW";
            $dane_konta['pln'] = "PL87102010420000800203009800";
            $dane_konta['eur'] = "PL15102010420000810203017837";
        }else{
            $dane_konta['bank'] = array("Bank BPH S.A.", "ul. Targowa 41", "03-728 Warszawa");
            $dane_konta['swift'] = "BPHKPLPK";
            $dane_konta['pln'] = "PL58106000760000320001361929";
            $dane_konta['eur'] = "PL49106000760000330000656852";
        }
        $dane_konta['usd'] = "PL54106000760000330000699442";
        return $dane_konta;
    }
}
?>
