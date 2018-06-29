<?php

class ZdarzeniaPrzepisz {
/*-----------------------------------------------------------------------------
								ARGUMENTY
  -----------------------------------------------------------------------------
*/
        public $Baza;
        function __construct($Baza) {
            $this->Baza = $Baza;
            $this->CheckSession();
	}
/*-----------------------------------------------------------------------------
								FUNKCJE PUBLIC
  -----------------------------------------------------------------------------
*/

function CheckSession(){
    $date = date("Y-m-d");
    if(isset($_SESSION['event_rewrite']) && $_SESSION['event_rewrite'] == $date){
        return;
    }
    $this->rewrite($date, $_SESSION['id_uzytkownik']);
}
/*funkcja zwracająca informacje na temat zdarzeń na wybrany dzień (lista po lewej stronie modułu zdarzeń)
  parameters:
	$_date		-	aktualna data
	$_user		-	dane użytkownika
  -----------------------------------------------
*/
function rewrite($_date,$_user)
{	/*id zdarzeń przeszłych, które nie zostały wykonane*/
        $_past_event = $this->Baza->GetRows("SELECT z.id FROM zdarzenia z, powiazania_zdarzenia pz
                                                WHERE pz.Zdarzenia_id = z.id AND pz.id_uzytkownik = '$_user'
                                                AND (z.data_poczatek < '$_date' AND (z.data_przypomnienia is null OR z.data_przypomnienia < '$_date') AND z.data_zakonczenia is null)
                                                AND z.specjalne = 'nie' ORDER BY z.Priorytet_id");
	/*liczba zaległych*/
        if($_past_event){
            $_event_number=count($_past_event);
            $_SESSION['event_rewrite'] = $_date;
            /*przepisywanie zadań na kolejne dni*/
            while($_event_number!=0)
            {	/*sprawdzanie, czy użytkownik nie zalogował się w weekend*/
                    if($this->isWeekendDay($_date))
                    {	/*data wypada w sobotę lub w niedzielę*/
                            $_date=$this->getNextDate($_date);
                    }

                    /*sprawdzanie ile zadań jest przypisanych na dany dzień*/
                    if(($_no_of_date_event=$this->getEventNumber($_date,$_user))<50)
                    {	/*w danym dniu jest mniej niż 50 zadań; przepisywanie odpowiedniej ilości zadań*/
                            $_free_event=50-$_no_of_date_event;

                            $_from=count($_past_event)-$_event_number;
                            $_event_number=$_event_number-$_free_event<0 ? 0 : $_event_number-$_free_event;
                            $_to=count($_past_event)-$_event_number;

                            $_in_query_part='(';

                            for($i=$_from;$i<$_to;$i++)
                            {	/*id elementów do przepisania*/
                                    $_in_query_part.=($i==$_from ? '' : ',').$_past_event[$i]['id'];
                            }
                            $_in_query_part.=')';

                            /*aktualizacja dni*/
                            $this->Baza->Query("UPDATE zdarzenia SET data_przypomnienia = '$_date' WHERE id IN$_in_query_part");
                    }

                    /*pobieranie następnego dnia*/
                    $_date=$this->getNextDate($_date);
            }
        }

}
/*---------------------------------------------*/

/*funkcja zwracająca następną datę (z wyłączeniem sobót i niedziel)
  parameters:
	$_date		-	aktualna data
  -----------------------------------------------
*/
function getNextDate($_date)
{	/*zmienne z informacjami na temat aktulnego dnia*/
	$_temp_date=explode('-',$_date);
	$_mktime=mktime(0,0,0,$_temp_date[1],$_temp_date[2],$_temp_date[0]);

	/*następny dzień*/
	$_next_day=array();

	/*dzień*/
	$_next_day[2]=( ($_temp_date[2]<date('t',$_mktime)) ? $_temp_date[2]+1 : 1 );

	/*miesiąc*/
	$_next_day[1]=( ($_next_day[2]<$_temp_date[2]) ? ($_temp_date[1]==12 ? 1 : $_temp_date[1]+1) : $_temp_date[1] );

	/*rok*/
	$_next_day[0]=( ($_next_day[1]<$_temp_date[1]) ? $_temp_date[0]+1 : $_temp_date[0] );

	/*sprawdzanie czy data nie wypada w sobotę lub niedzielę*/
	if($this->isWeekendDay($_next_day[0].'-'.$_next_day[1].'-'.$_next_day[2]))
	{	/*data wypada w sobotę lub w niedzielę*/
		return $this->getNextDate($_next_day[0].'-'.$_next_day[1].'-'.$_next_day[2]);
	}

	return $_next_day[0].'-'.$_next_day[1].'-'.$_next_day[2];
}
/*---------------------------------------------*/

/*zwracanie true, jeżeli przekazany dzień wypada w sobotę lub niedzielę
  parameters:
	$_date		-	aktualna data
  -----------------------------------------------
*/
function isWeekendDay($_date)
{	$_temp_date=explode('-',$_date);
	$_mktime=mktime(0,0,0,$_temp_date[1],$_temp_date[2],$_temp_date[0]);

	if(date('N',$_mktime)==6 || date('N',$_mktime)==7)
	{	/*dzień to sobota lub niedziela*/
		return true;
	}

	return false;
}
/*---------------------------------------------*/

/*zwracanie ilości zdarzeń dla danego użytkownika w danym dniu
  parameters:
	$_date		-	aktualna data
	$_id		-	id użytkownika, którego dotyczą zadania
  -----------------------------------------------
*/
function getEventNumber($_date,$_id)
{	/*pobieranie ilości zadań na dany dzień, dla danego użytkownika*/
        $ile = $this->Baza->GetValue("SELECT count(*) FROM zdarzenia z, powiazania_zdarzenia pz
                                            WHERE pz.Zdarzenia_id = z.id AND pz.id_uzytkownik = '$_id'
                                            AND ((z.data_poczatek = '$_date' AND z.data_przypomnienia is null) OR z.data_przypomnienia = '$_date') AND z.specjalne = 'nie'");
	return $ile;
}
/*---------------------------------------------*/

/*zwracanie ilości zdarzeń (wraz z dodatkowymi) dla danego użytkownika w danym dniu
  parameters:
	$_date		-	aktualna data
	$_id		-	id użytkownika, którego dotyczą zadania
  -----------------------------------------------
*/
function getAllEventNumber($_date,$_id)
{	$ile = $this->Baza->GetValue("SELECT count(*) FROM zdarzenia z, powiazania_zdarzenia pz
                                            WHERE pz.Zdarzenia_id = z.id AND pz.id_uzytkownik = '$_id'
                                            AND ((z.data_poczatek = '$_date' AND z.data_przypomnienia is null) OR z.data_przypomnienia = '$_date')");
	return $ile;
}
/*---------------------------------------------*/

/*-----------------------------------------------------------------------------
								FUNKCJE PROTECTED
  -----------------------------------------------------------------------------
*/

}
?>