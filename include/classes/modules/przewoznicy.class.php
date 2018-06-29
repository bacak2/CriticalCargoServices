<?php
/**
 * Moduł przewoźników
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2011 Asrael
 * @package		Panelplus
 * @version		1.0
 */

class Przewoznicy extends ModulBazowy {

        public $SaveNip;
	function __construct(&$Baza, $Uzytkownik, $Parametr, $Sciezka) {
            parent::__construct($Baza, $Uzytkownik, $Parametr, $Sciezka);
            $this->Tabela = 'orderplus_przewoznik';
            $this->PoleID = 'id_przewoznik';
            $this->PoleNazwy = 'identyfikator';
            $this->Nazwa = 'Przewoźnik';
            $this->Filtry[] = array('opis' => 'NIP', 'typ' => 'tekst', 'nazwa' => 'nip');
            $this->Filtry[] = array('opis' => 'Nazwa', 'typ' => 'tekst', 'nazwa' => 'nazwa');
	}

	function &GenerujFormularz() {
            $Formularz = parent::GenerujFormularz();
            $Formularz->DodajPole('nazwa', 'tekst', 'Nazwa przewoźnika', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('identyfikator', 'tekst', 'Identyfiaktor', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('dane_firmy', 'tekst_dlugi', 'Dane firmy', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('nip', 'tekst', 'NIP', array('tabelka' => Usefull::GetFormStandardRow()));
            $Formularz->DodajPole('klasa', 'przewoznik_klasa', 'Kategoria', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => PrzewoznicyKlasy::GetClasses($this->Baza)));
            $Formularz->DodajPole('emaile', 'tekst_dlugi', 'Adresy e-mail<br>(rozdzielane przecinkiem)', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "dodawanie"){
                //$Formularz->DodajPole('domyslny_kierowca', 'domyslny_kierowca', 'Domyślny kierowca', array('tabelka' => Usefull::GetFormStandardRow(), 'elementy' => $this->GetKierowcy($_GET['id'])));
            }
            $Formularz->DodajPole('komentarz', 'tekst_dlugi', 'Komentarz', array('tabelka' => Usefull::GetFormStandardRow()));
            if($this->WykonywanaAkcja != "szczegoly"){
                $Formularz->DodajPole('zapisz', 'zapisz_anuluj', '', array("tabelka" => Usefull::GetFormButtonRow(), "elementy" => $this->PrzyciskiFormularza));
            }
            return $Formularz;
	}
	
	function PobierzListeElementow($Filtry = array()) {
		$Wynik = array(
			"nazwa" => 'Przewoźnik',
                        "identyfikator" => 'Identyfikator',
                        "klasa_id" => array('naglowek' => 'Status', 'elementy' => PrzewoznicyKlasy::GetClassesNames($this->Baza))
		);
                $Where = $this->GenerujWarunki();
		$this->Baza->Query("SELECT $this->PoleID, identyfikator, nazwa, klasa_id FROM $this->Tabela $Where ORDER BY $this->PoleNazwy");
		return $Wynik;
	}

        function GetKierowcy($PrzewoznikID){
            $Kierowcy = new Kierowcy($this->Baza, $this->Uzytkownik, $this->Parametr, $this->Sciezka);
            return $Kierowcy->GetKierowcyByPrzewoznik($PrzewoznikID);
        }

        function PobierzDodatkoweDane($Typ, $Pole, $Dane){
            switch($Typ){
                case "przewoznik_klasa":
                    $Dane[$Pole]['klasa_id'] = $Dane['klasa_id'];
                    $Dane[$Pole]['powod_zakazu'] = $Dane['powod_zakazu'];
                    break;
            }
            return $Dane;
        }

        function ZapiszDaneSzczegolowe($Wartosci, $Typ, $Pole){
            switch($Typ){
                case "przewoznik_klasa":
                    $Wartosci['klasa_id'] = (isset($Wartosci[$Pole]['klasa_id']) ?  $Wartosci[$Pole]['klasa_id'] : 4);
                    $Wartosci['powod_zakazu'] = ($Wartosci['klasa_id'] == 4 ? $Wartosci[$Pole]['powod_zakazu'] : "");
                    unset($Wartosci[$Pole]);
                    break;
            }
            return $Wartosci;
        }

        function GetList() {
            return $this->Baza->GetOptions("SELECT $this->PoleID, nazwa FROM $this->Tabela ORDER BY nazwa");
        }

        function ShowOK(){
            Usefull::ShowKomunikatOK('<b>Rekord został zapisany</b><br/><br/><a href="'.$this->LinkPowrotu.'"><img src="images/ok.gif" title="Powrót" alt="Powrót" style="display: inline; vertical-align: middle;"></a>&nbsp;&nbsp;&nbsp;<a href="?modul=zlecenia&akcja=dodawanie&pid='.$this->ID.'"><img src="images/nowe_zlecenie.gif" title="Nowe zlecenie" alt="Nowe zlecenie" style="display: inline; vertical-align: middle;"></a>');
        }

        function WyswietlAJAX($Akcja){
            if($Akcja == "get-action-list"){
                $Akcje[] = array('title' => "Edycja", "akcja_href" => "?modul=przewoznicy&akcja=edycja&");
                $this->ShowActionInPopup($Akcje, $_POST['id']);
            } 
        }

        function ObrobkaDanychLista($Elementy){
            if(!isset($_SESSION['Filtry']) || count($_SESSION['Filtry']) == 0) {
                $Elementy = array();
            }
            return $Elementy;
        }

        function ShowPaginacjaTable(){
            if(!isset($_SESSION['Filtry']) || count($_SESSION['Filtry']) == 0) {
                ?>
                <div style="width: 100%; margin: 20px 0px; text-align: center; color: #BBCE00; font-weight: bold; font-size: 13px;">
                    Proszę użyć wyszukiwarki.
                </div>
                <?php
            }
        }

        function SprawdzDane($Wartosci, $ID){
            $CheckNip = preg_replace("/[^a-zA-Z0-9]/", "", $Wartosci['nip']);
            if(strlen($CheckNip) == 0){
                $this->Error = "Proszę wprowadzić NIP";
                return false;
            }
            $NipKod = preg_replace("/[^A-Z]/", "", strtoupper($CheckNip[0].$CheckNip[1]));
            if(strlen($NipKod) != 2){
                $this->Error = "Błędny numer NIP";
                return false;
            }
            if($NipKod == "PL"){
                $NipNumer = preg_replace("/[^0-9]/", "", substr($CheckNip, 2, strlen($CheckNip)));
                if(strlen($NipNumer) != 10){
                    $this->Error = "Błędny numer NIP";
                    return false;
                }
            }else{
                $NipNumer = substr($CheckNip, 2, strlen($CheckNip));
            }
            $this->SaveNip = $NipKod.$NipNumer;
            return true;
        }

        function OperacjePrzedZapisem($Wartosci){
            $Wartosci['nip'] = $this->SaveNip;
            return $Wartosci;
        }

        function AkcjeNiestandardowe($ID){
            if($this->WykonywanaAkcja == "scalanie"){
                if($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['Scalanie'] == "Scal"){
                    $_POST['zostaje']['scalony'] = 2;
                    $_POST['zostaje']['dodane_idki'] = implode(",", $_POST['scalam']);
                    $PozostajeUpdate = $this->Baza->PrepareUpdate("orderplus_przewoznik", $_POST['zostaje'], array('id_przewoznik' => $_POST['pozostaje_id']));
                    $this->Baza->Query($PozostajeUpdate);
                    $IdScalonego = $_POST['pozostaje_id'];
                    foreach($_POST['scalam'] as $UsunId){
                        $Usuniecie = "DELETE FROM orderplus_przewoznik WHERE id_przewoznik = '$UsunId'";
                        $this->Baza->Query($Usuniecie);
                        $ZmienZlecenia = "UPDATE orderplus_zlecenie SET id_przewoznik = '$IdScalonego' WHERE id_przewoznik = '$UsunId'";
                        $this->Baza->Query($ZmienZlecenia);
                        $ZmienZleceniaSea = "UPDATE orderplus_sea_orders_zlecenia SET id_przewoznik_to = '$IdScalonego' WHERE id_przewoznik_to = '$UsunId'";
                        $this->Baza->Query($ZmienZleceniaSea);
                        $ZmienZleceniaAir = "UPDATE orderplus_air_orders_zlecenia SET id_przewoznik_to = '$IdScalonego' WHERE id_przewoznik_to = '$UsunId'";
                        $this->Baza->Query($ZmienZleceniaAir);
                        $ZmienSeaOrders = "UPDATE orderplus_sea_orders SET id_przewoznik_agent = '$IdScalonego' WHERE id_przewoznik_agent = '$UsunId'";
                        $this->Baza->Query($ZmienSeaOrders);
                        $ZmienSeaOrders2 = "UPDATE orderplus_sea_orders SET inland_carrier_id = '$IdScalonego' WHERE inland_carrier_id = '$UsunId'";
                        $this->Baza->Query($ZmienSeaOrders2);
                        $ZmienAirOrders = "UPDATE orderplus_air_orders SET id_przewoznik_agent = '$IdScalonego' WHERE id_przewoznik_agent = '$UsunId'";
                        $this->Baza->Query($ZmienAirOrders);
                        $ZmienSeaOrdersKoszty = "UPDATE orderplus_sea_orders_koszty SET id_przewoznik = '$IdScalonego' WHERE id_przewoznik = '$UsunId'";
                        $this->Baza->Query($ZmienSeaOrdersKoszty);
                        $ZmienAirOrdersKoszty = "UPDATE orderplus_air_orders_koszty SET id_przewoznik = '$IdScalonego' WHERE id_przewoznik = '$UsunId'";
                        $this->Baza->Query($ZmienAirOrdersKoszty);
                    }
                    $this->Baza->EnableLog(false);
                }
                $Przewoznik = $this->Baza->GetRows("SELECT * FROM orderplus_przewoznik WHERE scalony = 0"); 
                foreach($Przewoznik as $Przew){
                    $CheckNip = preg_replace("/[^0-9]/", "", $Przew['nip']);
                    echo "SELECT * FROM orderplus_przewoznik WHERE nip LIKE '%$CheckNip%' AND id_przewoznik != '{$Przew['id_przewoznik']}'<br />";
                    $Szukaj = $this->Baza->GetRows("SELECT * FROM orderplus_przewoznik WHERE nip LIKE '%$CheckNip%' AND id_przewoznik != '{$Przew['id_przewoznik']}'");
                    if($Szukaj != false){
                        $IleZlecen = $this->Baza->GetValue("SELECT count(*) FROM orderplus_zlecenie WHERE id_przewoznik = '{$Przew['id_przewoznik']}'");
                        ?>
                            <form action="" method="post">
                            <table class="lista">
                                <tr style="background-color: #FFF;">
                                    <td><?php echo $IleZlecen; ?></td>
                                    <td><input type="text" name="pozostaje_id" style="width: 50px;" value="<?php echo $Przew['id_przewoznik']; ?>"></td>
                                    <td><input type="text" name="zostaje[nazwa]" style="width: 200px;" value="<?php echo $Przew['nazwa']; ?>"></td>
                                    <td><input type="text" name="zostaje[identyfikator]" style="width: 200px;" value="<?php echo $Przew['identyfikator']; ?>"></td>
                                    <td><textarea name="zostaje[dane_firmy]" style="height: 200px;"><?php echo $Przew['dane_firmy']; ?></textarea></td>
                                    <td><input type="text" name="zostaje[nip]" value="<?php echo $Przew['nip']; ?>"></td>
                                    <td><textarea name="zostaje[emaile]" style="height: 200px;"><?php echo $Przew['emaile']; ?></textarea></td>
                                    <td><input type="text" name="zostaje[domyslny_kierowca]" style="width: 50px;" value="<?php echo $Przew['domyslny_kierowca']; ?>"></td>
                                    <td><input type="text" name="zostaje[klasa_id]" style="width: 50px;" value="<?php echo $Przew['klasa_id']; ?>"></td>
                                    <td><textarea name="zostaje[komentarz]" style="height: 200px;"><?php echo $Przew['komentarz']; ?></textarea></td>
                                    <td><textarea name="zostaje[powod_zakazu]" style="height: 200px;"><?php echo $Przew['powod_zakazu']; ?></textarea></td>
                                </tr>
                                <?php
                                    foreach($Szukaj as $Dane){
                                        $IleZlecen = $this->Baza->GetValue("SELECT count(*) FROM orderplus_zlecenie WHERE id_przewoznik = '{$Dane['id_przewoznik']}'");
                                        ?>
                                        <tr style="background-color: #FFF;">
                                            <td><?php echo $IleZlecen; ?></td>
                                            <td><input type="text" name="scalam[]" style="width: 50px;" value="<?php echo $Dane['id_przewoznik']; ?>"></td>
                                            <td><?php echo $Dane['nazwa']; ?></td>
                                            <td><?php echo $Dane['identyfikator']; ?></td>
                                            <td><?php echo nl2br($Dane['dane_firmy']); ?></td>
                                            <td><?php echo $Dane['nip']; ?></td>
                                            <td><?php echo nl2br($Dane['emaile']); ?></td>
                                            <td><?php echo $Dane['domyslny_kierowca']; ?></td>
                                            <td><?php echo $Dane['klasa_id']; ?></td>
                                            <td><?php echo nl2br($Dane['komentarz']); ?></td>
                                            <td><?php echo nl2br($Dane['powod_zakazu']); ?></td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </table>
                                <input type="submit" name="Scalanie" value="Scal" />
                            </form>
                        <?php
                        break;
                    }else{
                        $this->Baza->Query("UPDATE orderplus_przewoznik SET scalony = '1' WHERE id_przewoznik = '{$Przew['id_przewoznik']}'");
                    }
                }
            }else{
		$this->AkcjaLista();
            }
	}
}
?>
