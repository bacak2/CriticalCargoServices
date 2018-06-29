<?php
/**
 * Obsługa akcji panelu
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>; Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */
class Panel {

        private $Baza = null;
        private $Menu = null;
        private $Uzytkownik = null;
        private $Moduly = array();
        private $TablicaUprawnienia = array();
        public $ModulyBezDodawania = array('platnosci', 'zlecenia_klient', 'tabela_rozliczen', 'platnosci_morskie', 'tabela_rozliczen_morskie', 'tabela_rozliczen_lotnicze', 'tabela_rozliczen_nowa',
                                                'tabela_rozliczen_moja', 'zlecenia_morskie', 'zlecenia_lotnicze', 'zdarzenia', 'zlecenia_lotnicze_zlec', 'raporty','logowania', 'day_raport', 'client_raport', 'kontakty', 'zalaczniki');
        public $Aplikacja = "ORDER";

	function __construct($BazaParametry) {
		$DBConnectionSettings = new DBConnectionSettings($BazaParametry);
		$this->Baza = new DBMySQL($DBConnectionSettings);
                if($_SESSION['login'] == "artplusadmin"){
                    #$this->Baza->EnableLog();
                }
		$this->Uzytkownik = new Uzytkownik($this->Baza, 'orderplus_uzytkownik', null, null, null);
		if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['logowanie']) && (!$this->Uzytkownik->CzyZalogowany())) {
			$this->Uzytkownik->Zaloguj($_POST['pp_login'],$_POST['pp_haslo']);
		}
		$this->TablicaUprawnienia = $this->Uzytkownik->ZwrocTabliceUprawnien();
		if(isset($_POST['wyloguj'])) {
			$this->Uzytkownik->Wyloguj();
		}
		$this->Menu = new Menu();
                if($_GET['dev'] == "dev"){
                    $ZleceniaWDniu = $this->Baza->GetRows("SELECT * FROM orderplus_zlecenie WHERE kurs_przewoznik = 0 AND waluta != 'PLN' AND termin_zaladunku <= '2012-03-19'");
                    foreach($ZleceniaWDniu as $Zlec){
                        $WstawKurs = $this->Baza->GetValue("SELECT ".strtolower($Zlec['waluta'])." FROM orderplus_kurs WHERE data_publikacji <= '{$Zlec['termin_zaladunku']}' ORDER BY data_publikacji DESC LIMIT 1");
                        $this->Baza->Query("UPDATE orderplus_zlecenie SET kurs_przewoznik = '$WstawKurs' WHERE id_zlecenie = '{$Zlec['id_zlecenie']}'");
                    }
                }
	}

        function UstawAplikacje($Apl){
            $this->Aplikacja = $Apl;
        }

	function DodajModul($NazwaKlasy, $Parametr, $Nazwa, $Nadrzedny = null, $Ukryty = false) {
                if(!$this->Uzytkownik->IsAdmin() && $Ukryty && $this->Uzytkownik->SprawdzUprawnienie($Nadrzedny, $this->TablicaUprawnienia)){
                    $this->TablicaUprawnienia[] = $Parametr;
                }
		if($this->Menu->SprawdzUprawnienie($Parametr, $this->TablicaUprawnienia)){
                    $this->Moduly[$Parametr] = $NazwaKlasy;
                    $this->Menu->DodajModul($Parametr, $Nazwa, $Nadrzedny, $Ukryty, $this->Aplikacja);
		}		
	}
	
	function GetClassName($Parametr){
		return $this->Moduly[$Parametr];
	}

	function Wyswietl() {
		include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
		if ($this->Baza->Connected()) {
			if($this->Uzytkownik->CzyZalogowany()) {
                                $this->Menu->WyznaczSciezke($this->TablicaUprawnienia);
				$this->Menu->WczytajTabliceUprawnien($this->TablicaUprawnienia);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left; padding: 4px;">
                <div style="display: block; width: 325px; float: left;"><img src="images/logo.png" alt="MEPP Plus" /></div>
                <div style="display: block; width: 20%; float: left; color: #BBCE00;">
                    <?php
                        if(count($_SESSION['aplikacja_dostep']) > 1){
                    ?>
                    <form action="." method="post">
                        <b>Przejdź do aplikacji:</b>
                            <select name="program" class="tabelka" onchange="this.form.submit();">
                                <option value="ORDER"<?php echo ($_SESSION['Aplikacja'] == "ORDER" ? " selected" : ""); ?>>  Orderplus  </option>
                                <option value="CRM"<?php echo ($_SESSION['Aplikacja'] == "CRM" ? " selected" : ""); ?>>  CRM  </option>
                            </select>
                    </form>
                    <?php
                        }
                    ?>
                </div>
                <div style="display: block; width: 30%; float: right; text-align: right"><form action="." method="post"><input type="hidden" name="wyloguj" value="1"><input type="image" src="images/wyloguj.gif" alt="" height="17" width="72" border="0"></form></div>
            </td>
        </tr>
	<tr>
		<td align="center" valign="middle" class="border-main" id="border-main">
			<table  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
				<tr>
					<td class="naglowek" colspan="2">
                                            <table cellpadding="3" cellpadding="5" border="0">
                                                <tr>
                                                    <td style="padding-left: 100px;">
						<?php
                                                    if($_GET['akcja'] != "dodawanie" && is_null($_GET['id'])){
                                                        if(!in_array($this->Menu->AktywnyModul(), $this->ModulyBezDodawania)){
                                                             echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodawanie'><img src=\"images/buttons/add_top.png\"\ alt=\"Dodaj\" onmouseover='this.src=\"images/buttons/add_top_hover.png\"' onmouseout='this.src=\"images/buttons/add_top.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "tabela_rozliczen" && is_null($_GET['id'])){
                                                            echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=specyfikacja'><img src=\"images/buttons/add_specification.png\"\ alt=\"Generuj specyfikacje\" onmouseover='this.src=\"images/buttons/add_specification_hover.png\"' onmouseout='this.src=\"images/buttons/add_specification.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "tabela_rozliczen_nowa" && is_null($_GET['id'])){
                                                            echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodawanie'><img src=\"images/buttons/add_top.png\"\ alt=\"Dodaj\" onmouseover='this.src=\"images/buttons/add_top_hover.png\"' onmouseout='this.src=\"images/buttons/add_top.png\"'></a>&nbsp;&nbsp;&nbsp;");
                                                            echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=specyfikacja'><img src=\"images/buttons/add_specification.png\"\ alt=\"Generuj specyfikacje\" onmouseover='this.src=\"images/buttons/add_specification_hover.png\"' onmouseout='this.src=\"images/buttons/add_specification.png\"'></a>");
                                                        }
                                                        if(($this->Menu->AktywnyModul() == "zlecenia_morskie" || $this->Menu->AktywnyModul() == "zlecenia_lotnicze") && is_null($_GET['id'])){
                                                            echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodaj_import'><img src=\"images/buttons/so_add_imp.png\"\ alt=\"Dodaj SO import\" onmouseover='this.src=\"images/buttons/so_add_imp_hover.png\"' onmouseout='this.src=\"images/buttons/so_add_imp.png\"'></a>&nbsp;&nbsp;&nbsp;");
                                                            echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodaj_export'><img src=\"images/buttons/so_add_exp.png\"\ alt=\"Dodaj SO export\" onmouseover='this.src=\"images/buttons/so_add_exp_hover.png\"' onmouseout='this.src=\"images/buttons/so_add_exp.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "kontakty" && is_null($_GET['id']) && isset($_GET['cid'])){
                                                             echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodawanie&cid={$_GET['cid']}'><img src=\"images/buttons/add_button.png\"\ alt=\"Dodaj\" onmouseover='this.src=\"images/buttons/add_button_hover.png\"' onmouseout='this.src=\"images/buttons/add_button.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "zalaczniki" && isset($_GET['event'])){
                                                             echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodawanie&event={$_GET['event']}'><img src=\"images/buttons/add_button.png\"\ alt=\"Dodaj\" onmouseover='this.src=\"images/buttons/add_button_hover.png\"' onmouseout='this.src=\"images/buttons/add_button.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "zdarzenia" && isset($_GET['cid'])){
                                                             echo("<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=dodawanie&cid={$_GET['cid']}'><img src=\"images/buttons/add_button.png\"\ alt=\"Dodaj\" onmouseover='this.src=\"images/buttons/add_button_hover.png\"' onmouseout='this.src=\"images/buttons/add_button.png\"'></a>");
                                                        }
                                                        if($this->Menu->AktywnyModul() == "klienci" && is_null($_GET['id'])){
                                                             echo("&nbsp;&nbsp;&nbsp;<a href='?modul=".$this->Menu->AktywnyModul()."&akcja=import'><img src=\"images/buttons/csv_button.png\"\ alt=\"Import z pliku CSV\" onmouseover='this.src=\"images/buttons/csv_button_hover.png\"' onmouseout='this.src=\"images/buttons/csv_button.png\"'></a>");
                                                        }
                                                    }
                                                    if (isset($_POST['okresStart']) && isset($_POST['okresEnd'])) {
                                                        $_SESSION['okresStart'] = $_POST['okresStart'];
                                                        $_SESSION['okresEnd'] = $_POST['okresEnd'];
                                                    }
                                                 ?>
                                             </td>
                                              <td style="padding-left: 100px;" class="naglowek">
                                                <?php
                                                    if($_SESSION['Aplikacja'] == "ORDER"){
                                                        echo("<form action=\"\" method=\"post\">");
                                                        echo("Miesiące od <select name=\"okresStart\" size=\"1\" style='margin: 0 5px;' class='tabelka' >");
                                                        $rok = substr($_SESSION['okresStart'], 0, 4);
                                                        $miesiac = substr($_SESSION['okresStart'], 5, 2);
                                                        for ($delta = -6; $delta <= 6; $delta++) {
                                                           $data = mktime(0, 0, 0, $miesiac + $delta, 1, $rok);
                                                           $data_RRRR_MM = date('Y-m', $data);
                                                           $data_MM_RRRR = date('m-Y', $data);
                                                           echo("<option value='$data_RRRR_MM'".($_SESSION['okresStart'] == $data_RRRR_MM ? ' selected="selected"' : '').">$data_MM_RRRR</option>");
                                                        }
                                                        echo("</select> do <select name=\"okresEnd\" size=\"1\" style='margin: 0 5px;' class='tabelka' >");
                                                        $rok = substr($_SESSION['okresEnd'], 0, 4);
                                                        $miesiac = substr($_SESSION['okresEnd'], 5, 2);
                                                        for ($delta = -6; $delta <= 6; $delta++) {
                                                           $data = mktime(0, 0, 0, $miesiac + $delta, 1, $rok);
                                                           $data_RRRR_MM = date('Y-m', $data);
                                                           $data_MM_RRRR = date('m-Y', $data);
                                                           echo("<option value='$data_RRRR_MM'".($_SESSION['okresEnd'] == $data_RRRR_MM ? ' selected="selected"' : '').">$data_MM_RRRR</option>");
                                                        }
                                                        echo("</select> <input type=\"image\" value=\"wybierz\" src='images/filtruj.gif' style='vertical-align: middle;' /></form>");
                                                    }
                                                ?>&nbsp;
                                                    </td>
                                                </tr>
                                             </table>
					</td>
				</tr>
				
<?php
    echo "<tr>";
        echo "<td colspan='2' class='boki' style='background-color: #bcce00;'>";
            $this->Menu->Wyswietl($this->TablicaUprawnienia);
        echo "</td>";
    echo "</tr>";
?>
				<tr>
					<td colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td class="boki" align="left" valign="top">
<?php

if(!$this->Menu->AktywnyModul()) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="20"></td>
		<td><br /><b>Wybierz dział</b>:<br />
		<br />
<?php
	$this->Menu->WyswietlModuly($this->TablicaUprawnienia);
?>
		</td>
	</tr>
</table>
<?php
}
else {
		if($this->Uzytkownik->SprawdzUprawnienie($this->Menu->AktywnyModul(), $this->TablicaUprawnienia)){
			$Modul = new $this->Moduly[$this->Menu->AktywnyModul()]($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
			$Modul->UstalUprawnienia($this->TablicaUprawnienia);
                        if($this->Menu->AktywnyModul() == "uzytkownicy"){
				$Modul->GenerujTabliceZakladek($this->Menu->ZwrocTabliceZakladek());
			}
			$Modul->Wyswietl($this->Menu->WykonywanaAkcja());
		}else{
			$Modul = new ModulZabroniony($this->Baza, $this->Uzytkownik, $this->Menu->AktywnyModul(), $this->Menu->ZwrocSciezke());
			$Modul->Wyswietl();
		}
}
?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
        <tr>
            <td><p class="logowanie_dol">powered by <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a> for Critical Cargo and Freight Services</td>
    </tr>
</table>
<?php
			}
			else {
				include(SCIEZKA_SZABLONOW.'logowanie.tpl.php');
			}
		}
		else {
			include(SCIEZKA_SZABLONOW.'przerwa_techniczna.tpl.php');
		}
                if($_SERVER['REQUEST_URI'] != $_SESSION['BACK_HREF']){
                    $_SESSION['BACK_HREF'] = $_SERVER['REQUEST_URI'];
                }
		include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}
	
	function WyswietlAJAX($Modul, $Action, $Parametr = null) {
		if ($this->Baza->Connected()) {
			if($this->Uzytkownik->CzyZalogowany()) {
				$Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
				$Modul->WyswietlAJAX($Action);
			}
		}
	}

        function WykonajCron($Modul, $Parametr = null) {
            if ($this->Baza->Connected()) {
                $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                $Modul->Cron();
            }
	}
	
	function WyswietlPopup($Modul, $Action, $Parametr = null) {
		include(SCIEZKA_SZABLONOW.'naglowek.tpl.php');
		if ($this->Baza->Connected()) {
			if($this->Uzytkownik->CzyZalogowany()) {
				$Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
				$Modul->WyswietlAJAX($Action);
			}
		}
		include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}

        function WyswietlDrukuj($Modul, $Akcja, $Parametr = null) {
            if ($this->Baza->Connected()) {
                    $Modul = new $Modul($this->Baza, $this->Uzytkownik, $Parametr, null);
                    $Modul->UstalUprawnienia($this->TablicaUprawnienia);
                    $Modul->ShowNaglowekDrukuj($Akcja);
                    $Modul->AkcjaDrukuj($_GET['id'], $Akcja);
            }
            include(SCIEZKA_SZABLONOW.'stopka.tpl.php');
	}
}
?>