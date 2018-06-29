/*---------------------------------------------------------
		ŁADOWANIE USTAWIEŃ PANELU
  ---------------------------------------------------------
*/
manageEvent(window,'load',function()
{	/*-----------------------------------------------------
						GŁÓWNY KONTENER
	  -----------------------------------------------------
	*/
	manageEvent(document.getElementById('border-main'),'click',function()
	{	/*chowanie selectów kalendarza*/
		if(document.getElementById('go_to_chosen_day'))
		{	hideCalendarSelect('year');
			hideCalendarSelect('month');
		}
		
		/*zamykanie treści kalendarza (miesiąc)*/
		if(document.getElementById('calendar_content_from_ajax'))
		{	document.getElementById('calendar_content_from_ajax').parentNode.removeChild(document.getElementById('calendar_content_from_ajax'));
			clearInterval(ab_interval_calendar_content);
		}
		
		/*zamykanie wysuwanych menu (tydzień)*/
		if(document.getElementById('calendar'))
		{	/*wysówane menu (tydzień)*/
			for(i=1;i<32;i++)
			{	/*sprawdzanie, czy istenieje element*/
				temp_i=	i < 10 ? '0'+i : i;
				
				if(document.getElementById('date_'+temp_i))
				{	/*dodawanie rozsuwanego menu*/
					document.getElementById('menu_'+temp_i).style.display='none';
				}
			}
		}
	});
	/*---------------------------------------------------*/
	
	/*-----------------------------------------------------
						KALENDARZ
	  -----------------------------------------------------
	*/
	if(document.getElementById('date_field_1'))
	{	addCalendar('date_field_1');
	}
	if(document.getElementById('date_field_2'))
	{	addCalendar('date_field_2');
	}	
	if(document.getElementById('date_field_3'))
	{	addCalendar('date_field_3');
	}	
	
	if(document.getElementById('go_to_chosen_day'))
	{	/*istnieje div z wybieraniem daty*/
			/*akcje do select buttonów*/
		manageEvent(document.getElementById('year_picker'),'click',showHideCalendarSelect);
		manageEvent(document.getElementById('month_picker'),'click',showHideCalendarSelect);
		manageEvent(document.getElementById('year_chosen'),'click',showHideCalendarSelect);
		manageEvent(document.getElementById('month_chosen'),'click',showHideCalendarSelect);
			
			/*akcje do opcji buttonów*/
		day_that_is_today=new Date();
		for(i=1;i<13;i++)
		{	/*akcja do miesięcy*/
			manageEvent(document.getElementById('calendar_month_'+i),'click',setCalendarHiddenIput);
		}
		for(i=(day_that_is_today.getFullYear()-5);i<(day_that_is_today.getFullYear()+11);i++)
		{	/*akcja do miesięcy*/
			manageEvent(document.getElementById('calendar_year_'+i),'click',setCalendarHiddenIput);
		}
	}
	
	if(document.getElementById('calendar'))
	{	/*wysuwane menu (tydzień)*/
		for(i=1;i<32;i++)
		{	/*sprawdzanie, czy istenieje element*/
			temp_i=	i < 10 ? '0'+i : i;
			
			if(document.getElementById('date_'+temp_i))
			{	/*dodawanie rozsuwanego menu*/
				addCalendarSlideMenu(temp_i);
			}
		}
	}
	/*---------------------------------------------------*/
});
/*-------------------------------------------------------*/