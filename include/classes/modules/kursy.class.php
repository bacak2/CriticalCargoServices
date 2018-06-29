<?php
/**
 * Moduł pobierający kursy
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class Kursy {
	private $Baza = null;
	
	function __construct($Baza) {
            $this->Baza = $Baza;
	}

        function PobierzDzisiejszeKursy(){
            $Dzis = date("Y-m-d");
            $DataZBazy = $this->Baza->GetValue("SELECT data_publikacji FROM orderplus_kurs WHERE data_publikacji='$Dzis'");
            if(!$DataZBazy || $_GET['act'] == "dev"){
                $plik = "xml_comp/h.xml";
                $link = "http://www.nbp.pl/kursy/kursya.html";
                $this->vWritePageToFile($link, $plik);
                $uchwyt = fopen($plik, 'r+');
                while (!feof ($uchwyt)){
                    $link .= fread($uchwyt, 4096);
                }
                fclose($uchwyt);
                $link = 'http://www.nbp.pl/kursy/'.substr(strstr($link, 'xml/'), '0', '19');
                $this->vWritePageToFile($link, $plik);
                $uchwyt = fopen($plik, 'r+');
                $tresc = '';
                while (!feof ($uchwyt)){
                    $tresc .= fread($uchwyt, 4096);
                }
                fclose($uchwyt);

               $strona_xml = str_replace('      ', '', $tresc);
               $strona_xml = str_replace('   ', '', $strona_xml);
               $strona_xml = str_replace("\n", '', $strona_xml);

               // wyciagam numer tabeli
               $nr_tab = explode('<numer_tabeli>', $strona_xml);
               $nr_tab2 = explode('</numer_tabeli>', $nr_tab[1]);
               $nr_tabeli = trim($nr_tab2[0]);
               // wyciagam date
               $temp1 = explode('<data_publikacji>', $nr_tab2[1]);
               $temp2 = explode('</data_publikacji>', $temp1[1]);
               $DataPublikacji = trim($temp2[0]);
                  // wyciagam waluty
                  //$Waluty = array('USD' => 'dolar', 'EUR' => 'euro', 'GBP' => 'funt', 'RUB' => 'rubel', 'JPY' => 'jen');
                  $Waluty = array('USD' => 'dolar', 'EUR' => 'euro');
                  foreach($Waluty as $klucz => $wartosc)
                  {
                     $tab1 = explode($klucz, $strona_xml);
                     $tab2 = explode('<kurs_sredni>', $tab1[1]);
                     $war = trim($tab2[1]);
                     $tab_waluta = '';
                     $tab_waluta .= $war[0];
                     $tab_waluta .= '.';
                     $tab_waluta .= $war[2];
                     $tab_waluta .= $war[3];
                     $tab_waluta .= $war[4];
                     $tab_waluta .= $war[5];
                     ${$wartosc} = trim($tab_waluta);
                  }
                  if($DataPublikacji == $Dzis){
                      if($dolar > 0 && $euro > 0){
                        $this->WstawDoTablicy("orderplus_kurs", $DataPublikacji, $nr_tabeli, $dolar, $euro, "NBP");
                      }
                  }
            }
        }

        function PobierzDzisiejszeKursyBPH($Sciezka){
            $Dzis = date("Y-m-d");
            for($i=4;$i>1;$i--){ 
                $DataZBazy = $this->Baza->GetValue("SELECT data_publikacji FROM orderplus_kurs_bph WHERE data_publikacji='$Dzis'");
                if(!$DataZBazy){
                    $plik = $Sciezka."xml_comp/h.xml";
                    $link = "http://www.bph.pl/bphportal/ratesToCsv?lang=pl&pageId=734&date=$Dzis&index=4";
                    $this->vWritePageToFile($link, $plik);
                    $uchwyt = fopen($plik, 'r+');
                    $tresc = '';
                    while (!feof ($uchwyt)){
                        $tresc .= fread($uchwyt, 4096);
                    }
                    fclose($uchwyt);
                    $Wiersze = explode("\n", $tresc);
                    foreach($Wiersze as $Wiersz){
                        $Dane = explode(",", $Wiersz);
                        if($Dane[1] == "1 USD"){
                            $dolar = $Dane[3];
                        }
                        if($Dane[1] == "1 EUR"){
                            $euro = $Dane[3];
                        }
                    }
                    if($dolar > 0 && $euro > 0){
                        $this->WstawDoTablicy("orderplus_kurs_bph", $Dzis, "", $dolar, $euro, "KOM");
                    }
                }else{
                    break;
                }
            }
        }

        function WstawDoTablicy($Tabela, $DataPublikacji, $NrTabeli, $dolar, $euro, $Bank = "NBP"){
            $Dzien = date("l", strtotime($DataPublikacji));
            $CzyJestWBazie = $this->Baza->GetValue("SELECT data_publikacji FROM $Tabela WHERE data_publikacji='$DataPublikacji'");
            if(!$CzyJestWBazie){
                $z = "INSERT INTO $Tabela SET data_publikacji='$DataPublikacji', nr_tabeli = '$NrTabeli', dzien = '$Dzien', pln='1.0000', usd='$dolar', eur='$euro'";
                if($this->Baza->Query($z)){
                    $this->AktualizujKursyWZleceniach($DataPublikacji, $dolar, $euro, $Bank);
                }
            }else{
                $z = "UPDATE $Tabela SET data_publikacji='$DataPublikacji', nr_tabeli = '$NrTabeli', dzien = '$Dzien', pln='1.0000', usd='$dolar', eur='$euro' WHERE data_publikacji='$DataPublikacji'";
                $this->Baza->Query($z);
            }
            echo "POBRANO KURS";
        }

        function vWritePageToFile( $sHTMLpage, $sTxtfile ) {
            $sh = curl_init( $sHTMLpage );
            $hFile =  fopen( $sTxtfile, 'w+' );
            curl_setopt( $sh, CURLOPT_FILE, $hFile );
            curl_setopt( $sh, CURLOPT_HEADER, 0 );
            curl_exec( $sh );
            curl_close($sh );
            fclose($hFile );
        }

        function AktualizujKursyWZleceniach($DataPublikacji, $USD, $EUR, $Bank = "NBP"){
            $TabelaKurs = ($Bank == "KOM" ? "orderplus_kurs_bph" : "orderplus_kurs");
            $OstatniKurs = $this->Baza->GetValue("SELECT MAX(data_publikacji) FROM $TabelaKurs WHERE data_publikacji < '$DataPublikacji'");
            for($Date = $DataPublikacji; $Date >= $OstatniKurs; $Date = $NewDate){
                $Dzien = date("l", strtotime($Date));
                $WstawUSD = $USD;
                $WstawEUR = $EUR;
                $KursZPiatkuR = $this->Baza->GetData("SELECT * FROM $TabelaKurs WHERE data_publikacji <= '$Date' ORDER BY data_publikacji DESC LIMIT 1");
                $WstawUSD = $KursZPiatkuR['usd'];
                $WstawEUR = $KursZPiatkuR['eur'];
                $ZleceniaWDniu = $this->Baza->GetRows("SELECT * FROM orderplus_zlecenie WHERE termin_zaladunku = '$Date' AND (kurs = 0 OR kurs_przewoznik = 0) AND waluta != 'PLN'");
                if($ZleceniaWDniu){
                    foreach($ZleceniaWDniu as $Zlec){
                        $WstawKurs = ($Zlec['waluta'] == "USD" ? $WstawUSD : $WstawEUR);
                        if($Zlec['kurs'] <= 0 && $this->Baza->GetValue("SELECT kurs_waluty_bank FROM orderplus_klient WHERE id_klient = '{$Zlec['id_klient']}'") == $Bank){
                            $this->Baza->Query("UPDATE orderplus_zlecenie SET kurs = '$WstawKurs' WHERE id_zlecenie = '{$Zlec['id_zlecenie']}'");
                        }
                        if($Bank == "NBP" && $Zlec['kurs_przewoznik'] <= 0){
                            $this->Baza->Query("UPDATE orderplus_zlecenie SET kurs_przewoznik = '$WstawKurs' WHERE id_zlecenie = '{$Zlec['id_zlecenie']}'");
                        }
                    }
                }
                $NewDate = date("Y-m-d", strtotime($Date."-1 days"));
            }
        }

	
}
?>
