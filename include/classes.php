<?php
$PlikiKlas = array(
	'Panel' => SCIEZKA_KLAS.'panel.class.php',
        'Install' => SCIEZKA_KLAS.'install.class.php',
	'DBConnectionSettings' => SCIEZKA_KLAS.'mysql.class.php',
	'DBMySQL' => SCIEZKA_KLAS.'mysql.class.php',
	'DBQueryResult' => SCIEZKA_KLAS.'mysql.class.php',
	'Uzytkownik' => SCIEZKA_KLAS.'uzytkownik.class.php',
        'PHPExcel' => SCIEZKA_INCLUDE.'PHPExcel.php',
	'Menu' => SCIEZKA_KLAS.'menu.class.php',
	'Formularz' => SCIEZKA_FORMULARZY.'formularz.class.php',
	'FormularzSimple' => SCIEZKA_FORMULARZY.'forms.class.php',
	'ModulBazowy' => SCIEZKA_MODULOW.'modul_bazowy.class.php',
	'ModulPodrzedny' => SCIEZKA_MODULOW.'modul_podrzedny.class.php',
	'ModulPusty' => SCIEZKA_MODULOW.'modul_pusty.class.php',
	'ModulZabroniony' => SCIEZKA_MODULOW.'modul_zabroniony.class.php',
        'Usefull' => SCIEZKA_MODULOW.'usefull.class.php',
        'Mail' => SCIEZKA_MODULOW.'mail.class.php',
        'MailSMTP' => SCIEZKA_MODULOW.'mail-smtp.class.php',
        'XML' => SCIEZKA_MODULOW.'xml.class.php',
        'Cronjobs' => SCIEZKA_MODULOW.'cronjobs.class.php',
        'Download' => SCIEZKA_MODULOW.'download.class.php',
        'Kursy' => SCIEZKA_MODULOW.'kursy.class.php',
        'UsefullBase' => SCIEZKA_MODULOW.'usefull-base.class.php',
        'BL' => SCIEZKA_MODULOW.'bl.class.php',
        'Faktury' => SCIEZKA_MODULOW.'faktury.class.php',
        'FakturyMorskie' => SCIEZKA_MODULOW.'faktury_morskie.class.php',
        'FakturyLotnicze' => SCIEZKA_MODULOW.'faktury_lotnicze.class.php',
        'Logowania' => SCIEZKA_MODULOW.'logowania.class.php',
        'Kierowcy' => SCIEZKA_MODULOW.'kierowcy.class.php',
        'Klienci' => SCIEZKA_MODULOW.'klienci.class.php',
        'KlienciKoperty' => SCIEZKA_MODULOW.'klienci_koperty.class.php',
        'KlienciRaporty' => SCIEZKA_MODULOW.'klienci_raporty.class.php',
        'KlienciRaportyMorskie' => SCIEZKA_MODULOW.'klienci_raporty_morskie.class.php',
        'KlienciRaportyLotnicze' => SCIEZKA_MODULOW.'klienci_raporty_lotnicze.class.php',
        'KlienciPotwierdzenia' => SCIEZKA_MODULOW.'klienci_potwierdzenia.class.php',
        'NotyObciazeniowe' => SCIEZKA_MODULOW.'noty.class.php',
        'Platnosci' => SCIEZKA_MODULOW.'platnosci.class.php',
        'PlatnosciMorskie' => SCIEZKA_MODULOW.'platnosci_morskie.class.php',
        'PlatnosciLotnicze' => SCIEZKA_MODULOW.'platnosci_lotnicze.class.php',
        'Przewoznicy' => SCIEZKA_MODULOW.'przewoznicy.class.php',
        'PrzewoznicyKlasy' => SCIEZKA_MODULOW.'przewoznicy_klasy.class.php',
        'Punkty' => SCIEZKA_MODULOW.'punkty.class.php',
        'RaportyCRM' => SCIEZKA_MODULOW.'raporty_crm.class.php',
        'RaportyCRMDzienny' => SCIEZKA_MODULOW.'raporty_crm_dzienny.class.php',
        'RaportyCRMClient' => SCIEZKA_MODULOW.'raporty_crm_klient.class.php',
        'SeaOrders' => SCIEZKA_MODULOW.'sea_orders.class.php',
        'AirOrders' => SCIEZKA_MODULOW.'air_orders.class.php',
        'Szablon' => SCIEZKA_MODULOW.'szablon.class.php',
        'TabelaRozliczen' => SCIEZKA_MODULOW.'tabela_rozliczen.class.php',
        'TabelaRozliczenNowa' => SCIEZKA_MODULOW.'tabela_rozliczen_nowa.class.php',
        'TabelaRozliczenMorskie' => SCIEZKA_MODULOW.'tabela_rozliczen_morskie.class.php',
        'TabelaRozliczenLotnicze' => SCIEZKA_MODULOW.'tabela_rozliczen_lotnicze.class.php',
        'TabelaRozliczenRaporty' => SCIEZKA_MODULOW.'raporty_tabela_rozliczen.class.php',
        'MojaTabelaRozliczen' => SCIEZKA_MODULOW.'tabela_rozliczen_moja.class.php',
        'Specyfikacja' => SCIEZKA_MODULOW.'specyfikacja.class.php',
        'Uzytkownicy' => SCIEZKA_MODULOW.'uzytkownicy.class.php',
        'Zlecenia' => SCIEZKA_MODULOW.'zlecenia.class.php',
        'ZleceniaKlient' => SCIEZKA_MODULOW.'zlecenia_klient.class.php',
        'ZleceniaMorskie' => SCIEZKA_MODULOW.'zlecenia_morskie.class.php',
        'ZleceniaLotnicze' => SCIEZKA_MODULOW.'zlecenia_lotnicze.class.php',
        'Zdarzenia' => SCIEZKA_MODULOW.'zdarzenia.class.php',
        'ZdarzeniaPrzepisz' => SCIEZKA_MODULOW.'zdarzenia_przepis.class.php',
        'Oddzialy' => SCIEZKA_MODULOW.'oddzialy.class.php',
        'Kalendarz' => SCIEZKA_MODULOW.'kalendarz.class.php',
        'Kontakty' => SCIEZKA_MODULOW.'kontakty.class.php',
        'Zalaczniki' => SCIEZKA_MODULOW.'zalaczniki.class.php'
);

function __autoload($NazwaKlasy) {
	global $PlikiKlas;
	if (file_exists($PlikiKlas[$NazwaKlasy])) {
		require_once($PlikiKlas[$NazwaKlasy]);
	}
	else {
		require_once($PlikiKlas['ModulPusty']);
		error_log("WARNING: Nie znaleziono modulu $NazwaKlasy: Plik: {$PlikiKlas[$NazwaKlasy]}");
		eval("class $NazwaKlasy extends ModulPusty {}");
	}
}
?>