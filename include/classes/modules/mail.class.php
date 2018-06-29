<?php
/**
 * Moduł wysyłki emaili
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class Mail {
	private $Baza = null;
	private $Email;
	
	function __construct($Baza) {
            $this->Baza = $Baza;
            $this->Email = "Critical CS Order System <office@critical-cs.com>";
            //$this->Email = "MEPP Order System <sebastian.jurzysta@meppeurope.com>";
	}

        function SetEmail($Email){
            $this->Email = $Email;
        }
	
	function GetHeaders(){
		$Header = "MIME-Version: 1.0\n";
		$Header .= "From: $this->Email\n";
		$Header .= "Content-type: text/html; charset=utf-8\n";
		$Header .= "Reply-To: $this->Email\n";
		return $Header;
	}
        
	function encodeSlowo($s) {
		return "=?utf-8?B?" . base64_encode($s) . "?=";
	}

        function SendEmail($Mail, $Tytul, $Tresc){
            $Tytul = $this->encodeSlowo($Tytul);
            if(mail($Mail, $Tytul, $Tresc, $this->GetHeaders())){
                return true;
            }
            return false;
        }

        function SendMailsZdarzenie($user_id, $Tresc){
            $OD_user = $this->Baza->GetValue("SELECT email FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$_SESSION['id_uzytkownik']}'");
            $DO_user = $this->Baza->GetValue("SELECT email FROM orderplus_uzytkownik WHERE id_uzytkownik = '$user_id'");
            /*title*/
            $title='Mepp CRM: Wiadomość od '.$_SESSION['login'];
            $this->Email = $OD_user;
            $this->SendEmail($DO_user, $title, $Tresc);
        }

        function SendReminders(){
            $datatime = date("Y-m-d H:i:s");
            $time = date("H:i:s");
            $Reminders = $this->Baza->GetRows("SELECT id, temat, data_poczatek, data_przypomnienia, przypomnienie_mail_godzina FROM zdarzenia
                                                WHERE przypomnienie_mail = '1' AND przypomnienie_mail_wyslano = '0'
                                                AND ((data_poczatek <= '$datatime' AND data_przypomnienia is null) OR data_przypomnienia <= '$datatime')
                                                AND przypomnienie_mail_godzina <= '$time'");
            if($Reminders){
                foreach($Reminders as $Remind){
                    $Powiazane = $this->Baza->GetData("SELECT id_uzytkownik, id_klient FROM powiazania_zdarzenia WHERE Zdarzenia_id = '{$Remind['id']}'");
                    $Klient = $this->Baza->GetValue("SELECT identyfikator FROM orderplus_klient WHERE id_klient = '{$Powiazane['id_klient']}'");
                    if($Klient == ""){
                        $Klient = $this->Baza->GetValue("SELECT nazwa FROM orderplus_klient WHERE id_klient = '{$Powiazane['id_klient']}'");
                    }
                    $User = $this->Baza->GetValue("SELECT email FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$Powiazane['id_uzytkownik']}'");
                    $Tresc = "<b>Przypomnienie o wykonaniu zadania:</b><br /><br />Temat: {$Remind['temat']}<br />Klient: $Klient<br />";
                    $Tresc .= "Data przypomnienia: ".($Remind['data_przypomnienia'] !== null ? substr($Remind['data_przypomnienia'],0,10) : substr($Remind['data_poczatek'],0,10))." {$Remind['przypomnienie_mail_godzina']}";
                    if($User != ""){
                        if($this->SendEmail($User, "Critical CS CRM: Przypomnienie o zadaniu", $Tresc)){
                            $this->Baza->Query("UPDATE zdarzenia SET przypomnienie_mail_wyslano = '1' WHERE id = '{$Remind['id']}'");
                        }
                    }
                }
            }
        }
}
?>
