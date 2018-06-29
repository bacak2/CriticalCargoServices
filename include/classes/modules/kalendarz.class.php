<?php
/**
 * Moduł kalendarza
 * 
 * @author		Michal Grzegorzek <asrael.nwn@gmail.com>
 * @copyright	Copyright (c) 2004-2008 ARTplus
 * @version		1.0
 */
class Kalendarz {
	protected $calendar_box_view;
        /*widok kalendarza - dzienny czy miesięczny*/
        protected $calendar_view;
        /*typ wyświetlania kalendarza*/
        protected $view_type;
        /*informacje nt. aktualnej daty bądź daty, która została przekazana*/
        protected $current_date;
        /*tablica $_GET*/
        protected $get=array();
        protected $dane = array();
        protected $months = array('','styczeń','luty','marzec','kwiecień','maj','czerwiec','lipiec','sierpień','wrzesień','październik','listopad','grudzień');
        protected $days = array('poniedziałek','wtorek','środa','czwartek','piątek','sobota','niedziela'); 

        function __construct($Values) {
            $this->validateGetParam($Values);
            $this->get=$Values;
            /*ustawianie przekazanych zmiennych*/
            $this->current_date['month']=intval(($this->get['month']===false ? date('n') : $this->get['month']));
            $this->current_date['year']=intval(($this->get['year']===false ? date('Y') : $this->get['year']));
            $this->current_date['day']=intval((($this->get['year']===false || $this->get['month']===false) ? date('j') : 1));

            /*aktualny dzień, ustawiony tylko, gdy został wybrany aktualny dzień*/
            $this->current_date['active']=($this->current_date['year']==date('Y') && $this->current_date['month']==date('n') ? date('j') : false);

            /*data do wyświetlenia w timestampie*/
            $_date_to_display=mktime(0,0,0,$this->current_date['month'],$this->current_date['day'],$this->current_date['year']);

            /*pobieranie aktualnej daty*/
                    /*wybrany tydzień, bądź tydzień aktualnej daty*/
            $this->current_date['week']=intval(($this->get['week']===false ? date('W',$_date_to_display) : $this->get['week']));
                    /*numer tygodnia dla pierwszego dnia w miesiącu*/
            $this->current_date['first_week']=intval(date('W',mktime(0,0,0,$this->current_date['month'],1,$this->current_date['year'])));
                    /*dzień tygodnia dla aktualnie wybranej daty*/
            $this->current_date['week_day']=intval(date('w',$_date_to_display));
                    /*ilość dni w miesiącu*/
            $this->current_date['month_days']=intval(date('t',$_date_to_display));

            /*przepisywanie aktualnej daty do widoku*/
            $this->dane['current']=array(
                                        'month'		=>	$this->months[intval($this->current_date['month'])],
                                        'month_int'	=>	$this->current_date['month'],
                                        'year'		=>	$this->current_date['year'],
                                        'week'		=>	$this->current_date['week']
                                                                                    );

            /*tablica z miesiącami*/
            $this->dane['month_array'] = $this->months;
            switch($this->view_type){
                    case 'week':
                            $this->setWeek();
                            break;
                    case 'month':
                            $this->setMonth();
                            break;
                    default:
                            $this->setMonth();
                            $this->view_type = 'month';
                            break;
            }
            
	}

        function ShowCalendar(){
            include(SCIEZKA_SZABLONOW."calendar/calendar-box.tpl.php");
        }

        function ShowCalendarViev(){
            /*tworzenie kalendarza*/
            switch($this->view_type){
                case 'week':
                       include(SCIEZKA_SZABLONOW."calendar/calendar_week.tpl.php");
                        break;
                case 'month':
                        include(SCIEZKA_SZABLONOW."calendar/calendar_month.tpl.php");
                        break;
            }
        }

        function validateGetParam(&$_get){
            /*rok*/
                if(isset($_get['year']))
                {	/*walidacja przesłanej zmiennej*/
                        if(!preg_match('/^2[0-9]{3}$/',$_get['year']))
                        {	$_get['year']=false;	}
                }
                else
                {	$_get['year']=false;	}

                /*miesiąc*/
                if(isset($_get['month']))
                {	/*walidacja przesłanej zmiennej*/
                        if(!preg_match('/^1?[0-9]$/',$_get['month']) || intval($_get['month'])<1 || intval($_get['month'])>12)
                        {	$_get['month']=false;	}
                }
                else
                {	$_get['month']=false;	}

                /*tydzień*/
                if(isset($_get['week']))
                {	/*walidacja przesłanej zmiennej*/
                        if(!preg_match('/^[1-5]?[0-9]$/',$_get['week']) || intval($_get['week'])<1 || intval($_get['week'])>53)
                        {	$_get['week']=false;	}
                }
                else
                {	$_get['week']=false;	}

                /*typ wyświetlania kalendarza*/
                if(isset($_get['calendar_view']))
                {	switch($_get['calendar_view'])
                        {	case 'week':
                                        $this->view_type='week';

                                        if($_get['week']!==false)
                                        {	/*obliczenie aktualnego miesiąca*/
                                                $result=$this->getDateFromWeek($_get['year'],$_get['week']);
                                                $_get['month']=$result['current_month'];
                                        }
                                        else
                                        {	$_get['month']=false;	}

                                        break;
                                case 'month':
                                default:
                                        $this->view_type='month';
                                        break;
                        }
                }
                else $this->view_type='month';
        }

        function setMonth(){
                /*tworzenie dni kalendarza*/
                $_days=array();
                        /*określenie, w który dzień tygodnia wypadł pierwszy dzień miesiąca*/
                $_first_day=($this->current_date['day']===1 ? $this->current_date['week_day'] : intval(date('w',mktime(0,0,0,$this->current_date['month'],1,$this->current_date['year']))));
                $_first_day=($_first_day==0 ? 7 : $_first_day);
                        /*puste dni (początek)*/
                for($i=1;$i<$_first_day;$i++)
                {	$_days[$i]=false;	}
                        /*pierwszy dzień miesiąca*/
                if(isset($_days[1])===false )
                {	$_days[1]=1;	}
                else
                {	$_days[]=1;	}

                        /*dni miesiąca*/
                for($i=1;$i<$this->current_date['month_days'];$i++)
                {	$_days[]=$i+1;	}
                        /*puste dni (koniec)*/
                while((count($_days)%7)!==0)
                {	$_days[]=false;	}

                /*zapisywanie dni*/
                $this->dane['days']['active']=$this->current_date['active'];
                $this->dane['days']['desc']=$this->days;
                $this->dane['days']['days']=$_days;

                /*nagłówek*/
                $this->dane['current']['desc']=ucfirst($this->dane['current']['month']).' '.$this->dane['current']['year'];

                /*numer pierwszego tygodnia*/
                $this->dane['first_week'] = $this->current_date['first_week'];

                /*aktualna data*/
                $this->dane['current_year']=$this->current_date['year'];
                $this->dane['current_month']=$this->current_date['month'];

                /*linki*/
                $this->dane['link']=$this->makeLink('month');
        }
        /*---------------------------------------------*/

        /*ustawienie wyświetlania miesiąca
          -----------------------------------------------
        */
        function setWeek(){

                /*zapisywanie dni*/
                $this->dane['days']['active']=$this->current_date['year'].'-'.$this->current_date['month'].'-'.($this->current_date['active']<10 ? '0' : '').$this->current_date['active'];
                // $this->days['days']=$_days;
                        /*daty wyświetlanych dni*/
                $result=$this->getDateFromWeek($this->current_date['year'],$this->current_date['week']);
                $this->dane['days']['head']['date']=$result['days'];
                        /*nazwa słowna wyświetlanych dni*/
                $this->dane['days']['head']['desc']=$this->days;
                        /*tablica z nazwami miesiący*/
                $this->dane['days']['month_desc']=$this->months;

                /*nagłówek*/
                $this->dane['current']['desc']=$this->current_date['week'].' tydzień '.$this->dane['current']['year'];

                /*linki*/
                $this->dane['link']=$this->makeLink('week');
        }

        function getDateFromWeek($_year,$_week){
            /*aktualny rok*/
                $_year=intval(($_year===false ? date('Y') : $_year));
                $_week=intval($_week);
                /*walidacja tygodnia*/
                if($_week==53)
                {	/*sprawdzanie, czy dany rok rzeczywiście ma 53 tygodnie*/
                        if(intval(date('W',mktime(0,0,0,12,31,$_year)))!=53)
                        {	$_week=52;	}
                }

                /*którego dnia wypada pierwszy dzień roku*/
                $_first_day_year=intval(date('N',mktime(0,0,0,1,1,$_year)));

                /*dzień roku dla pierwszego dnia wybranego tygodnia (wstępny)*/
                $_week_day=(($_week-1)*7)+1;

                /*tablica z dniami miesięcy*/
                $_month_array=array();
                for($i=1;$i<13;$i++)
                {	$_month_array[$i]=date('t',mktime(0,0,0,$i,1,$_year));
                }

                /*obliczanie ilości dni*/
                $_month_of_the_week=1;
                $_temp_sum=0;
                for($i=1;$i<13;$i++)
                {	if(($_temp_sum+intval(date('t',mktime(0,0,0,$i,1,$_year))))>$_week_day)
                        {	/*mamy już poszukiwany miesiąc*/
                                break;
                        }
                        $_temp_sum+=intval(date('t',mktime(0,0,0,$i,1,$_year)));

                        $_month_of_the_week++;
                }

                /*dzień dla pierwszego dnia wybranego tygodnia (in progress)*/
                $_week_day=$_week_day-$_temp_sum;
                $_month_to_return=$_month_of_the_week;

                $content='';

                /*sprawdzanie, czy rok zaczyna się 1-go tygodnia, czy 5n-tego poprzedniego roku*/
                if($_week==52 || $_week==53)
                {	$_month_of_the_week=12;

                        if(intval(date('W',mktime(0,0,0,1,1,$_year+1)))!==1)
                        {
                                // $content.='aaaa';
                                if(intval(date('W',mktime(0,0,0,1,1,$_year+1)))==53 && $_week==52)
                                {	$_week_day=intval(date('t',mktime(0,0,0,12,1,$_year)))-(intval(date('N',mktime(0,0,0,1,1,$_year+1)))-2)-7;	}
                                else
                                $_week_day=intval(date('t',mktime(0,0,0,12,1,$_year)))-(intval(date('N',mktime(0,0,0,1,1,$_year+1)))-2);
                                $_month_to_return=$_month_of_the_week;
                        }
                        else
                        {
                                // $content.='bbbb'.' '.intval(date('W',mktime(0,0,0,12,31,$_year))).' '.($_year+1);
                                if(intval(date('W',mktime(0,0,0,12,31,$_year)))==1)
                                {
                                        // $content.=')aaaa'.$_week_day.' '.intval(date('W',mktime(0,0,0,12,30,$_year)));
                                        if(intval(date('W',mktime(0,0,0,12,30,$_year)))==1)
                                        {	$_week_day+=(8-$_first_day_year)-7;	}
                                        else
                                        {	if($_first_day_year==1)
                                                {	$_week_day+=(8-$_first_day_year)-7;	}
                                                else
                                                {	$_week_day+=(8-$_first_day_year);	}
                                        }
                                }
                                else
                                $_week_day+=(8-$_first_day_year);
                        }
                }
                elseif(intval(date('W',mktime(0,0,0,1,1,$_year)))===1)
                {
                        if($_first_day_year!==1)
                        {	/*pierwszy dzień roku nie jest pierwszym dniem tygodnia*/
                                if($_week===1)
                                {	/*pierwszy tydzień*/
                                        // $content.='ccccc';
                                        $_week_day=intval(date('t',mktime(0,0,0,12,1,$_year-1)))-($_first_day_year-2);
                                        $_month_of_the_week=12;
                                }
                                else
                                {	/*pozostałe tygodnie*/
                                        // $content.='dddd';
                                        $_week_day-=($_first_day_year-1);
                                }
                        }

                }
                else
                {
                        // $content.='eeee';
                        $_week_day+=(8-$_first_day_year);

                        if($_week_day>intval(date('t',mktime(0,0,0,$_month_of_the_week,1,$_year))))
                        {	$_week_day-=intval(date('t',mktime(0,0,0,$_month_of_the_week,1,$_year)));
                                $_month_of_the_week++;
                                $_month_to_return=$_month_of_the_week;
                        }
                }

                // echo $_month_to_return.' '.$_month_of_the_week.' '.$_week_day.' '.$_week.' '.$_year.'<br>';
                /*jeżeli po całej transforamcji okazało się, że pierwszym dniem jest dzień 0*/
                if($_week_day<1)
                {	$_week_day=intval(date('t',mktime(0,0,0,$_month_of_the_week-1,1,$_year)))+$_week_day;
                        $_month_of_the_week--;
                        $_month_to_return=$_month_of_the_week;
                }

                // echo $_month_to_return.' '.$_month_of_the_week.' '.$_week_day.'<br>';
                // $content.= '<br>miesiąc: '.$_month_of_the_week.', dzień: '.$_week_day.', tydzień: '.$_week.', pierwszy dzień roku: '.$_first_day_year.'<br>';
                // $content.='<br>'.$_week_day.'-'.Models_Ab_Date::getMonth($_month_of_the_week).'-'.$_year.'<br>';
                /*daty dni*/
                $result=array();
                $result['days'][1]=$_year.'-'.$_month_of_the_week.'-'.($_week_day<10 ? '0' : '').$_week_day;

                while(count($result['days'])<7)
                {	if($_week_day==$_month_array[$_month_of_the_week])
                        {	/*następny miesiąc*/
                                if($_month_of_the_week==12)
                                {	/*następny rok*/
                                        ++$_year;
                                        $_month_of_the_week=1;
                                }
                                else
                                {	++$_month_of_the_week;	}

                                $_week_day=1;
                        }
                        else
                        {	++$_week_day;	}

                        /*rok*/
                        $result['days'][]=$_year.'-'.$_month_of_the_week.'-'.($_week_day<10 ? '0' : '').$_week_day;
                }

                $result['current_month']=$_month_to_return;
                $result['days']['var']=$content;
                return $result;
        }

        function makeLink($_type)
        {	$_get_part='?';
                $coma=false;

                foreach($this->get as $key => $value)
                {	if($key=='year' || $key=='month' || $key=='week' || $key=='calendar_view')
                        {	/*pomijanie wartości dotyczących kalendarza*/
                                continue;
                        }
                        $_get_part.=($coma ? '&' : '').$key.'='.$value;

                        if($coma===false)
                        {	$coma=true;	}
                }

                $_get_part.=($_get_part=='?' ? '' : '&');

                        /*zmiana typu wyświetlania kalendarza*/
                if($_type=='month')
                {	/*sprawdzanie, czy jest dzisiejszy dzień w wybranym miesiącu*/
                        if($this->current_date['active']!==false)
                        $this->current_date['week']=intval(date('W',mktime(0,0,0,$this->current_date['month'],$this->current_date['active'],$this->current_date['year'])));
                }
                $_set=$_get_part.'calendar_view='.($_type=='month' ? 'week' : 'month').'&year='.$this->current_date['year'].($_type=='month' ? '&week='.$this->current_date['week'] : '&month='.$this->current_date['month']);
                        /*aktualna data*/
                $_current=$_get_part;
                $_current_get='&year='.date('Y').($_type=='week' ? '&week='.date('W') : '&month='.date('n'));

                        /*dodawanie do linku informacji o typie kalendarza*/
                $_get_part.=(isset($this->get['calendar_view']) ? 'calendar_view='.$this->get['calendar_view'].'&' : '');

                switch($_type)
                {	case 'week':
                                        /*prev*/
                                $_year_prev=($this->current_date['week']==1 ? $this->current_date['year']-1 : $this->current_date['year']);
                                if($this->current_date['week']==1)
                                {	if(intval(date('W',mktime(0,0,0,12,31,$this->current_date['year']-1)))===1)
                                        {	$_week_prev=52;	}
                                        else
                                        {	$_week_prev=intval(date('W',mktime(0,0,0,12,31,$this->current_date['year']-1)));	}
                                }
                                else
                                {	$_week_prev=$this->current_date['week']-1;	}

                                $_prev=$_get_part.'year='.$_year_prev.'&week='.$_week_prev;

                                        /*next*/
                                if($this->current_date['week']>51)
                                {	if(intval(date('W',mktime(0,0,0,12,31,$this->current_date['year'])))===53)
                                        {	$_week=($this->current_date['week']==52 ? 53 : 1);
                                                $_year=($_week==53 ? $this->current_date['year'] : $this->current_date['year']+1);
                                        }
                                        else
                                        {	$_week=1;
                                                $_year=$this->current_date['year']+1;
                                        }
                                }
                                else
                                {	$_week=$this->current_date['week']+1;
                                        $_year=$this->current_date['year'];
                                }

                                $_next=$_get_part.'year='.$_year.'&week='.$_week;
                                break;


                        case 'month':
                                        /*rok*/
                                $_prev=$_get_part.'year='.($this->current_date['month']==1 ? $this->current_date['year']-1 : $this->current_date['year']);
                                $_next=$_get_part.'year='.($this->current_date['month']==12 ? $this->current_date['year']+1 : $this->current_date['year']);
                                        /*miesiąc*/
                                $_prev.='&month='.($this->current_date['month']==1 ? 12 : $this->current_date['month']-1);
                                $_next.='&month='.($this->current_date['month']==12 ? 1 : $this->current_date['month']+1);

                                        /*do linków z tygodniami*/
                                $this->dane['week_link'] = $_get_part.'calendar_view=week&year='.$this->current_date['year'].'&week=';
                                break;

                }

                return array('prev'=>$_prev,'next'=>$_next,'set'=>array('link'=>$_set,'type'=>$_type),'current'=>$_current,'current_get'=>$_current_get);
        }

        function getDays()
        {	$result=array();

                switch($this->view_type)
                {	case 'week':
                                /*wyświetlany jest kalendarz tygodniowy*/
                                for($i=1;$i<6;$i++)
                                {	/*budowanie dat danego tygodnia*/
                                        $_temp=explode('-',$this->dane['days']['head']['date'][$i]);
                                        $result[]=$_temp[0].'-'.($_temp[1] < 10 ? '0' : '').$_temp[1].'-'.$_temp[2];
                                }
                                break;
                        case 'month':
                                /*wyświetlany jest kalendarz miesięczny*/
                                for($i=1;$i<($this->current_date['month_days']+1);$i++)
                                {	/*budowanie dat danego miesiąca*/
                                        $result[]=$this->current_date['year'].'-'.($this->current_date['month'] < 10 ? '0' : '').$this->current_date['month'].'-'.($i < 10 ? '0' : '').$i;
                                }
                                break;
                }

                /*zwracanie dat*/
                return $result;
        }

        function getView(){
            return $this->view_type;
        }

        function setDayContent($_date,$_content){	
            switch($this->view_type)
                {	case 'week':
                                /*wyświetlany jest kalendarz tygodniowy*/
                                $_temp=explode('-',$_date);
                                $this->dane['days']['content'][$_temp[0].'-'.intval($_temp[1]).'-'.$_temp[2]]=$_content;
                                break;
                        case 'month':
                                /*wyświetlany jest kalendarz miesięczny*/
                                $_temp=explode('-',$_date);
                                $this->dane['days']['content'][intval($_temp[2])]=$_content;
                                break;
                }
        }
}
?>
