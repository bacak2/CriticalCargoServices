<?php
/**
 * Instalacja zmian
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright           Copyright (c) 2004-2012 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Install {

        private $Baza = null;

	function __construct($BazaParametry) {
		$DBConnectionSettings = new DBConnectionSettings($BazaParametry);
		$this->Baza = new DBMySQL($DBConnectionSettings);
                $this->Baza->EnableLog();
		$this->Uzytkownik = new Uzytkownik($this->Baza, 'artdesign_users', null, null, null);
	}

        function MakeInstall(){
            error_reporting(E_ERROR);
            echo "########## ROZPOCZECIE INSTALOWANIA #############<br /><br />";
            if($_GET['mode'] == "przenies_userow"){
                $this->PrzeniesUzytkownikowDoOrdera();
            }
            if($_GET['mode'] == "generuj_hashe"){
                $this->GenerujHashe();
            }
            if($_GET['mode'] == "nadaj_uprawnienia"){
                $this->NadajUprawnieniaDoNowychModulow();
            }
            if($_GET['mode'] == "dostosuj_nipy"){
                $this->DostosujNipy();
            }
            
            if($_GET['mode'] == "przenies_klientow"){
                $this->PrzeniesKlientowDoOrdera();
            }
            
             if($_GET['mode'] == "przenies_mapery"){
                $this->PrzeniesMapery();
             }
             if($_GET['mode'] == "przenies_powiazania"){
                    $this->PrzeniesPowiazania();
             }
             if($_GET['mode'] == "przenies_kontakty"){
                $this->PrzeniesOsKontaktowe();
             }
             if($_GET['mode'] == "przenies_logowania"){
                $this->PrzeniesLogowania();
             }
             if($_GET['mode'] == "przenies_dodanie_klientow"){
                $this->PrzeniesDodanieKlientow();
             }
            if($_GET['mode'] == "klient_status"){
                $this->AkutalizujStatusyKlientowZZamowien();
            }
            if($_GET['mode'] == "aktualizujNipy"){
                $this->AktualizujNipy();
            }
            if($_GET['mode'] == "dostosujOpiekunow"){
                $this->DostosujOpiekunow();
            }
            #$this->Priorytety();
            #$this->PrzeniesUprawnieniaDoKlientow();
            #$this->PrzeniesMiastaKlientow();
            $this->Faktury();
            echo "########## ZAKONCZONO INSTALOWANIE ZMIAN #############<br /><br />";
        }

        function MapujOddzial($OddzialID){
            $Oddzialy[2] = 2;
            $Oddzialy[3] = 1;
            $Oddzialy[4] = 3;
            $Oddzialy[6] = 4;
            return $Oddzialy[$OddzialID];
        }

        function GenerujHashe(){
            if($this->Baza->Query("UPDATE orderplus_uzytkownik SET hash = md5(CONCAT(login,'389^&ashrtyfjsd72sofmndbfvuin9d789indb782bft56'))")){
                echo "########## WYGENEROWANO HASHE #############<br /><br />";
            }
        }

        function NadajUprawnieniaDoNowychModulow(){
            if($this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',baza_klientow,klienci_raporty,klienci_potwierdzenia') WHERE uprawnienia LIKE '%klienci%'")
                    && $this->Baza->Query("UPDATE orderplus_uzytkownik SET uprawnienia = CONCAT(uprawnienia,',rozliczenia,klienci_raporty,klienci_potwierdzenia') WHERE uprawnienia LIKE '%tabela_rozliczen%'")
                    && $this->Baza->Query("UPDATE orderplus_uprawnienia SET uprawnienia = CONCAT(uprawnienia,',baza_klientow,klienci_raporty,klienci_potwierdzenia') WHERE uprawnienia LIKE '%klienci,%'")
                    && $this->Baza->Query("UPDATE orderplus_uprawnienia SET uprawnienia = CONCAT(uprawnienia,',rozliczenia,klienci_raporty,klienci_potwierdzenia') WHERE uprawnienia LIKE '%tabela_rozliczen,%'")
                    ){
                echo "########## NADANO UPRAWNIENIA DO NOWYCH MODULOW #############<br /><br />";
            }
        }

        function PrzeniesUzytkownikowDoOrdera(){
            $this->Baza->Query("DELETE FROM orderplus_mapuj_uzytkownicy");
            $Uzytkownicy = $this->Baza->GetRows("SELECT * FROM uzytkownicy");
            foreach($Uzytkownicy as $User){
                $Save = array();
                $UserIstnieje = $this->Baza->GetValue("SELECT id_uzytkownik FROM orderplus_uzytkownik WHERE login = '{$User['login']}'");
                $Save['email'] = $User['mail'];
                if($UserIstnieje){
                    $Zapytanie = $this->Baza->PrepareUpdate("orderplus_uzytkownik", $Save, array('id_uzytkownik' => $UserIstnieje));
                }else{
                    $Save['uprawnienia_id'] = $User['Prawa_id'];
                    $Save['login'] = $User['login'];
                    $Save['haslo'] = "qwerty";
                    $Save['haslo_hash'] = md5("qwerty");
                    $Save['hash'] = md5($Save['login']."389^&ashrtyfjsd72sofmndbfvuin9d789indb782bft56");
                    $Save['id_oddzial'] = $this->MapujOddzial($this->Baza->GetValue("SELECT Oddzialy_id FROM oddzial_uzytkownik WHERE Uzytkownicy_id = '{$User['id']}'"));
                    $Zapytanie = $this->Baza->PrepareInsert("orderplus_uzytkownik", $Save);
                }
                $this->Baza->Query($Zapytanie);
                if(!$UserIstnieje){
                    $UserIstnieje = $this->Baza->GetLastInsertId();
                }
                $this->Baza->Query("INSERT INTO orderplus_mapuj_uzytkownicy SET Uzytkownicy_id = '{$User['id']}', id_uzytkownik = '$UserIstnieje'");
            }
            echo "########## PRZENIESIONO USERÓW #############<br /><br />";
        }

        function DostosujNipy(){
            echo "########## DOSTOSOWANIE NIPÓW DO SPRAWDZENIA #############<br /><br />";
            $KlienciOrder = $this->Baza->GetRows("SELECT * FROM orderplus_klient WHERE nip_inty = ''");
            foreach($KlienciOrder as $Kli){
                $NIP = preg_replace('|[^a-zA-Z0-9ĄĆĘŁŃÓŚŹŻąęćłńóśżź]|', '', $Kli['nip']);
                $this->Baza->Query("UPDATE orderplus_klient SET nip_inty = '$NIP' WHERE id_klient = '{$Kli['id_klient']}'");
            }
        }

        function PrzeniesUprawnieniaDoKlientow(){
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Oddzialy = $this->Baza->GetOptions("SELECT Klienci_id, Oddzialy_id FROM oddzial_klient");
            $Klienci = $this->Baza->GetRows("SELECT * FROM klienci");
            foreach($Klienci as $Klient){
                if(key_exists($Klient['id'], $Client)){

                    $this->Baza->Query("UPDATE orderplus_klient_oddzial SET id_oddzial = '".$this->MapujOddzial($Oddzialy[$Klient['id']])."' WHERE id_klient = '{$Client[$Klient['id']]}' AND id_oddzial = '{$Oddzialy[$Klient['id']]}'");
                }
            }
        }

        function PrzeniesMiastaKlientow(){
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Klienci = $this->Baza->GetRows("SELECT * FROM klienci");
            foreach($Klienci as $Klient){
                if(key_exists($Klient['id'], $Client)){
                    $this->Baza->Query("UPDATE orderplus_klient SET miejscowosc = '{$Klient['miasto']}' WHERE id_klient = '{$Client[$Klient['id']]}' AND miejscowosc = ''");
                }
            }
        }

        function PrzeniesKlientowDoOrdera(){


            $this->Baza->Query("DELETE FROM orderplus_mapuj_klienci");
            
            $Branze = $this->Baza->GetOptions("SELECT Klienci_id, Branza_id FROM branza_klient");
            $Grupy = $this->Baza->GetOptions("SELECT Klienci_id, Grupy_firm_id FROM grupa_klient");
            $Userzy = $this->Baza->GetOptions("SELECT Klienci_id, Uzytkownicy_id FROM klient_uzytkownik");
            $Oddzialy = $this->Baza->GetOptions("SELECT Klienci_id, Oddzialy_id FROM oddzial_klient");
            $Klienci = $this->Baza->GetRows("SELECT * FROM klienci");
            foreach($Klienci as $Klient){
                $Save = array();
                $UserIstnieje = $this->UstalIdKlientaWOrder($Klient['nip']);
                $Save['branza_crm_id'] = $Branze[$Klient['id']];
                $Save['grupa_id'] = $Grupy[$Klient['id']];
                $Save['id_uzytkownik'] = $Userzy[$Klient['id']];
                $Save['id_oddzial'] = $this->MapujOddzial($Oddzialy[$Klient['id']]);
                $Save['ostatnio_edytowal'] = $Klient['ostatnio_edytowal'];
                $Save['telefon'] = $Klient['telefon'];
                $Save['zone'] = $Klient['zone'];
                $Save['potencjal_id'] = $Klient['Potencjal_id'];
                $Save['kod_kraju_id'] = $Klient['Kod_kraju_id'];
                $Save['data_utworzenia'] = $Klient['data_utworzenia'];
                $Save['usuniety'] = ($Klient['usuniety'] == "tak" ? 1 : 0);
                if($UserIstnieje){
                    $Zapytanie = $this->Baza->PrepareUpdate("orderplus_klient", $Save, array('id_klient' => $UserIstnieje));
                }else{
                    $Save['miejscowosc'] = $Klient['miejscowosc'];
                    $Save['nazwa'] = $Klient['nazwa'];
                    $Save['adres'] = $Klient['adres'];
                    $Save['kod_pocztowy'] = $Klient['kod_pocztowy'];
                    $Save['nip'] = $Klient['nip'];
                    $Save['emaile'] = $Klient['mail'];
                    $Save['telefon'] = $Klient['telefon'];
                    $Zapytanie = $this->Baza->PrepareInsert("orderplus_klient", $Save);
                }
                $this->Baza->Query($Zapytanie);
                if(!$UserIstnieje){
                    $UserIstnieje = $this->Baza->GetLastInsertId();
                }
                $this->Baza->Query("INSERT INTO orderplus_mapuj_klienci SET Klienci_id = '{$Klient['id']}', id_klient = '$UserIstnieje'");
            }
        }

        function UstalIdKlientaWOrder($nip){
            if($nip == ""){
                return false;
            }
		$NIP = preg_replace('|[^a-zA-Z0-9ĄĆĘŁŃÓŚŹŻąęćłńóśżź]|', '', $nip);
		return $this->Baza->GetValue("SELECT id_klient FROM orderplus_klient WHERE nip_inty = '$NIP'");
	}

        function PrzeniesMapery(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Dostepy = $this->Baza->GetRows("SELECT * FROM klient_uzytkownik_dostep");
            $this->Baza->Query("DELETE FROM orderplus_klient_uzytkownik_dostep");
            foreach($Dostepy as $Dane){
                $Save['id_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Save['id_klient'] = $Client[$Dane['Klienci_id']];
                $Zap = $this->Baza->PrepareInsert("orderplus_klient_uzytkownik_dostep", $Save);
                $this->Baza->Query($Zap);
            }
            
        }

        function PrzeniesLogowania(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Dostepy = $this->Baza->GetRows("SELECT * FROM logowania");
            foreach($Dostepy as $Dane){
                $Save['id_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Zap = $this->Baza->PrepareUpdate("logowania", $Save, array('id' => $Dane['id']));
                $this->Baza->Query($Zap);
            }

        }

         function PrzeniesOsKontaktowe(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Dostepy = $this->Baza->GetRows("SELECT * FROM osoby_kontaktowe");
            foreach($Dostepy as $Dane){
                $Save['id_klient'] = $Client[$Dane['Klienci_id']];
                $Zap = $this->Baza->PrepareUpdate("osoby_kontaktowe", $Save, array('id' => $Dane['id']));
                $this->Baza->Query($Zap);
            }

        }

        function PrzeniesPowiazania(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Powiazania = $this->Baza->GetRows("SELECT * FROM powiazania_zdarzenia WHERE Zdarzenia_id < 10000");
            foreach($Powiazania as $Dane){
                $Save['id_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Save['id_klient'] = $Client[$Dane['Klienci_id']];
                $Zap2 = $this->Baza->PrepareUpdate("powiazania_zdarzenia", $Save, array('Zdarzenia_id' => $Dane['Zdarzenia_id']));
                $this->Baza->Query($Zap2);
            }
            $Powiazania = $this->Baza->GetRows("SELECT * FROM powiazania_zdarzenia WHERE Zdarzenia_id < 22000 && Zdarzenia_id >= 10000");
            foreach($Powiazania as $Dane){
                $Save['id_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Save['id_klient'] = $Client[$Dane['Klienci_id']];
                $Zap2 = $this->Baza->PrepareUpdate("powiazania_zdarzenia", $Save, array('Zdarzenia_id' => $Dane['Zdarzenia_id']));
                $this->Baza->Query($Zap2);
            }
            $Powiazania = $this->Baza->GetRows("SELECT * FROM powiazania_zdarzenia WHERE Zdarzenia_id >= 22000");
            foreach($Powiazania as $Dane){
                $Save['id_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Save['id_klient'] = $Client[$Dane['Klienci_id']];
                $Zap2 = $this->Baza->PrepareUpdate("powiazania_zdarzenia", $Save, array('Zdarzenia_id' => $Dane['Zdarzenia_id']));
                $this->Baza->Query($Zap2);
            }
        }

        function PrzeniesDodanieKlientow(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Client = $this->Baza->GetOptions("SELECT Klienci_id, id_klient FROM orderplus_mapuj_klienci");
            $Dostepy = $this->Baza->GetRows("SELECT * FROM klient_uzytkownik_dodanie");
            foreach($Dostepy as $Dane){
                $ClientID = $Client[$Dane['Klienci_id']];
                $Save['dodal_uzytkownik'] = $User[$Dane['Uzytkownicy_id']];
                $Zap = $this->Baza->PrepareUpdate("orderplus_klient", $Save, array('id_klient' => $ClientID));
                $this->Baza->Query($Zap);
            }
        }

        function AkutalizujStatusyKlientowZZamowien(){
            $Clients = $this->Baza->GetValues("SELECT id_klient FROM orderplus_zlecenie");
            foreach($Clients as $CID){
                $this->Baza->Query("UPDATE orderplus_klient SET klient_status = '1' WHERE id_klient = '$CID'");
            }
        }

        function AktualizujNipy(){
            $Clients = $this->Baza->GetOptions("SELECT id_klient, nip FROM orderplus_klient");
            foreach($Clients as $CID => $Nip){
                $NowyNip = Usefull::NipValidate($Nip);
                $this->Baza->Query("UPDATE orderplus_klient SET nip = '$NowyNip' WHERE id_klient = '$CID'");
            }
        }

        function DostosujOpiekunow(){
            $User = $this->Baza->GetOptions("SELECT Uzytkownicy_id, id_uzytkownik FROM orderplus_mapuj_uzytkownicy");
            $Clients = $this->Baza->GetRows("SELECT id_klient, id_uzytkownik, id_oddzial FROM orderplus_klient");
            foreach($Clients as $Dane){ 
                $this->Baza->Query("INSERT INTO orderplus_klient_opiekun_handlowy SET id_klient = '{$Dane['id_klient']}', id_uzytkownik = '{$User[$Dane['id_uzytkownik']]}'");
                $this->Baza->Query("INSERT INTO orderplus_klient_opiekun_operacyjny SET id_klient = '{$Dane['id_klient']}', id_uzytkownik = '{$User[$Dane['id_uzytkownik']]}'");
                if($Dane['id_oddzial'] == 0){
                    for($i = 0; $i < 5; $i++){
                        $this->Baza->Query("INSERT INTO orderplus_klient_oddzial SET id_klient = '{$Dane['id_klient']}', id_oddzial = '$i'");
                    }
                }else{
                    $this->Baza->Query("INSERT INTO orderplus_klient_oddzial SET id_klient = '{$Dane['id_klient']}', id_oddzial = '{$Dane['id_oddzial']}'");
                }
            }
            $Clients2 = $this->Baza->GetRows("SELECT id_klient, id_uzytkownik FROM orderplus_klient_uzytkownik_dostep");
            foreach($Clients2 as $Dane){
                $this->Baza->Query("INSERT INTO orderplus_klient_opiekun_operacyjny SET id_klient = '{$Dane['id_klient']}', id_uzytkownik = '{$Dane['id_uzytkownik']}'");
            }
        }

        function Priorytety(){
            $Zdarzenia = $this->Baza->GetValues("SELECT id FROM zdarzenia WHERE Priorytet_id = '0'");
            foreach($Zdarzenia as $ZID){
                $Pr = $this->Baza->GetValue("SELECT zd.Priorytet_id FROM zdarzenia zd LEFT JOIN powiazania_zdarzenia pz ON(pz.Zdarzenia_id = zd.id) WHERE pz.id_kolejnego_zdarzenia=$ZID");
                $this->Baza->Query("UPDATE zdarzenia SET Priorytet_id = '$Pr' WHERE id = '$ZID'");
            }
        }

        function Faktury(){
            $Faki = $this->Baza->GetOptions("SELECT id_faktury, id_zlecenia FROM faktury");
            foreach($Faki as $ID => $ZlecID){
                mysql_query("UPDATE orderplus_zlecenie SET id_faktury = '$ID' WHERE id_zlecenie = '$ZlecID' AND id_faktury = '0'");
            }
        }

}
?>