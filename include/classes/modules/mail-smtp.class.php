<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer//src/SMTP.php';
/**
 * Moduł wysyłki emaili przez SMTP
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class MailSMTP {
	public $Mail;
    public $Errors = false;
	
	function __construct() {
            ///include("vendor/phpmailer/phpmailer/src/PHPMailer.php");
            $this->Mail = new PHPMailer();
            $this->Mail->MailerDebug = 4;
            //$this->PluginDir = "";
            $this->Mail->isSMTP();
            $this->Mail->From = "noreply@orderplus.critical-cs.com"; //adres naszego konta
            $this->Mail->FromName = "Critical CS Order System";//nagłówek From
            //$this->Mail->Host = "smtp.googlemail.com";//adres serwera SMTP

            /*$this->Mail->Host = "mail.orderplus.critical-cs.com";//adres serwera SMTP
            $this->Mail->Port = 587;
            $this->Mail->Mailer = "smtp";
            $this->Mail->Username = "noreply@orderplus.critical-cs.com";//nazwa użytkownika
            $this->Mail->Password = "9DHsdua4";//nasze hasło do konta SMTP
            */

            ///// nowe dane
            $this->Mail->Host = "s16.hekko.pl";//adres serwera SMTP
            $this->Mail->Port = 465;
            $this->Mail->Mailer = "smtp";
            $this->Mail->Username = "noreply@orderplus.critical-cs.com";
            $this->Mail->Password = "bhrk5E75";
            $this->Mail->SMTPSecure = "ssl";

            $this->Mail->SMTPAuth = true;
            //$this->Mail->SMTPSecure = "tls";
            $this->Mail->SetLanguage("pl", "language/");
	}
	
	function encodeSlowo($s) {
		return "=?utf-8?B?" . base64_encode($s) . "?=";
	}
        
        function SetEmail($Email){
            $this->Email = $Email;
        }

        function SendEmail($Mail, $Tytul, $Tresc, $Attachements = false, $IsHTML = true){
            $Tytul = $this->encodeSlowo($Tytul);
            $this->Mail->Subject = $Tytul;
            $this->Mail->CharSet = "utf-8";
            $this->Mail->IsHTML($IsHTML);
            $this->Mail->Body = $Tresc;
            $this->Mail->AddAddress($Mail);
            if(is_array($Attachements)){
                foreach($Attachements as $name => $Att){
                    $this->Mail->AddAttachment($Att, $name);
                }
            }
            if($this->Mail->Send()){
                $this->Mail->ClearAddresses();
                return true;
            }
            return false;
        }

        function GetErrors(){
            return $this->Errors;
        }
        
        function _AddReplyTo($address, $name = '') {
            $this->Mail->AddReplyTo($address, $name);
        }
        
        function SendMailsZdarzenie($user_id, $Tresc){
            $OD_user = $this->Baza->GetValue("SELECT email FROM orderplus_uzytkownik WHERE id_uzytkownik = '{$_SESSION['id_uzytkownik']}'");
            $DO_user = $this->Baza->GetValue("SELECT email FROM orderplus_uzytkownik WHERE id_uzytkownik = '$user_id'");
            /*title*/
            $title='Critical CS CRM: Wiadomość od '.$_SESSION['login'];
            //$this->Email = $OD_user;
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
