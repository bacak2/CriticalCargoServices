/*zmienna przechowująca wskaźnik do wywoływanej w czasie funkcji*/
var ab_interval_calendar_content;
/*ilość przesłanych lini do wyświetlenia*/
var ab_calendar_content_lines;
/*aktualnie wyświetlana linia*/
var ab_calendar_content_current_line;
/*aktualnie wyświetlana linia*/
var ab_calendar_url;

/*-------------------------------------------------------------------
					KALENDARZ - WYŚWIETLANIE TYGODNIA
  -------------------------------------------------------------------
*/
/*funkcja podpinująca do elementów rozsuwane menu
  parameters:
	id_part	-	część id, która określa element, którego dotyczy akcja
  -----------------------------------------------
*/
function addCalendarSlideMenu(id_part)
{	manageEvent(document.getElementById('date_'+id_part),'click',showUpCalendarMenu);
}
/*---------------------------------------------*/

/*funkcja inicjująca rozwijajanie menu
  parameters:
	event	-	zdarzenie, które wywołało funkcję
  -----------------------------------------------
*/
function showUpCalendarMenu(event)
{	/*pobieranie obiektu i anulowanie zdarzenia*/
	main_element=returnEventObiect(event);
	cancelEvent(event);
	
	/*czyszczenie interwału, jeżeli jakaś funkcja jest w trakcie*/
	clearInterval(ab_interval_calendar_content);
	temp_array=main_element.id.split('_');
	
	if(document.getElementById('menu_'+temp_array[1]).style.display=='block')
	{	/*jeżeli element jest wyświetlony, to zostaje schowany*/
		document.getElementById('menu_'+temp_array[1]).style.display='none';
		return;
	}
	
	/*chowanie wszystkich pozostałych menu*/
	for(i=1;i<32;i++)
	{	/*sprawdzanie, czy istenieje element*/
		temp_i=	i < 10 ? '0'+i : i;
		
		if(document.getElementById('date_'+temp_i))
		{	/*dodawanie rozsuwanego menu*/
			document.getElementById('menu_'+temp_i).style.display='none';
		}
	}
	
	/*ustawianie początkowych wartości menu*/
	text_from_div=document.getElementById('menu_'+temp_array[1]).innerHTML;
	document.getElementById('menu_'+temp_array[1]).innerHTML='';
	document.getElementById('menu_'+temp_array[1]).style.width='0px'
	document.getElementById('menu_'+temp_array[1]).style.display='block';
	
	/*ustawianie zmiennych*/
	ab_calendar_content_current_line=0;
	text_from_div=text_from_div.replace(/[\r\n\t]*/g,'');
	
	/*rozwijanie menu*/
	ab_interval_calendar_content=setInterval('showUpCalendarMenuInterval(\''+text_from_div+'\',\''+temp_array[1]+'\')',30);
}
/*---------------------------------------------*/

/*funkcja rowijająca menu
  parameters:
	text_to_show	-	tekst do div'a
	element_to_add	-	id elementu
  -----------------------------------------------  
*/
function showUpCalendarMenuInterval(text_to_show,element_to_add)
{	if(document.getElementById('menu_'+element_to_add).style.width=='120px')
	{	/*jeżeli szerokość jest już dobra*/
		temp_array=text_to_show.match(/(<li[^<]*>(<a[^<]*>)?[^<]*(<\/a>)?<\/li>)/ig);
		
		document.getElementById('menu_'+element_to_add).innerHTML+=temp_array[ab_calendar_content_current_line];
		
		ab_calendar_content_current_line++;
		
		if(ab_calendar_content_current_line==temp_array.length)
		{	/*jeżeli zostały wyświetlone wszystkie linie to kończymy działanie funkcji*/
			clearInterval(ab_interval_calendar_content);
		}
	}
	else
	{	/*zwiększanie szerokości elementu*/
		document.getElementById('menu_'+element_to_add).style.width=parseInt(document.getElementById('menu_'+element_to_add).style.width)+20+'px';
	}
}
/*---------------------------------------------*/

/*-------------------------------------------------------------------
					KALENDARZ - WYŚWIETLANIE MIESIĄCA
  -------------------------------------------------------------------
*/
/*funkcja pobierająca treść na temat danego dnia
  parameters:
	element_with_content_id	-	id elementu z treścią
	content_date			-	data dnia nt., którego funkcja zwraca informacje
	my_host_url				-	url hosta
  -----------------------------------------------
*/
function getCalendarMonthContent(element_with_content_id,content_date,my_host_url)
{	/*adres, na który wysyłamy zapytanie*/
        url='/include/classes/ajax/get-event-content.php?date='+content_date;
	//url=my_host_url+'/zdarzenia/getcontent/'+content_date;
	ab_calendar_url=my_host_url;
	
	/*usuwanie elementu z treścią, jeżeli już taki istnieje*/
	if(document.getElementById('calendar_content_from_ajax'))
	{	document.getElementById('calendar_content_from_ajax').parentNode.removeChild(document.getElementById('calendar_content_from_ajax'));
		clearInterval(ab_interval_calendar_content);
	}
				
	/*tworzenie obiektu i wysyłanie zapytania*/
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	return;
		
	xmlHttp.onreadystatechange = function() 
	{	if (xmlHttp.readyState==4 && xmlHttp.status==200)
		{	/*tworzenie głównego div'a*/
			calendar_content=document.createElement('div');
			calendar_content.id='calendar_content_from_ajax';
	
			calendar_content.style.width='0px';
			
			calendar_content.onclick=function(event)	
			{	cancelEvent(returnEvent(event));
			};
		
			document.getElementById(element_with_content_id).appendChild(calendar_content);
			
			/*wywoływanie funkcji powiększającej div'a z treścią*/
			ab_interval_calendar_content=setInterval('CalendarInterval(\''+xmlHttp.responseText+'\')',30);
		} 
	};
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
/*---------------------------------------------*/

/*---------------------------------------------*/
function CalendarInterval(day_content)
{	if(document.getElementById('calendar_content_from_ajax').style.width=='300px')
	{	/*jeżeli div ma już odpowiednią szerokość*/
		clearInterval(ab_interval_calendar_content);
		
		temp_array=day_content.split(';');
		
		/*ilość lini do wyświetlenia*/
		ab_calendar_content_lines=temp_array.length;
		ab_calendar_content_current_line=1;
		
		/*sprawdzanie typu użytkownika*/
		switch(temp_array[0])
		{	case 'user':	//użytkownik
				ab_interval_calendar_content=setInterval('CalendarContentForUser(\''+day_content+'\')',30);
				break;
			case 'admin':	//administrator
				document.getElementById('calendar_content_from_ajax').innerHTML=xmlHttp.responseText;
				break;
			case 'manager':	//kierownik oddziału
				document.getElementById('calendar_content_from_ajax').innerHTML=xmlHttp.responseText;
				break;				
		}
	}
	else
	{	/*zwiększanie szerokości div'a*/
		document.getElementById('calendar_content_from_ajax').style.width=parseInt(document.getElementById('calendar_content_from_ajax').style.width)+20+'px';
	}
}
/*---------------------------------------------*/

/*funkcja dodająca div'a z treścią nt. danego dnia dla użytkownika
  parameters:
	day_content		-	treść dotycząca danego dnia
	add_to_element	-	id elementu, do którego funkcja dodaje div'a z treścią
  -----------------------------------------------
*/
function CalendarContentForUser(day_content)
{	if(ab_calendar_content_lines==ab_calendar_content_current_line)
	{	/*zostały wyświetlone wszystkie linie*/
			/*przycisk zamknij*/
		temp_img_close_button=document.createElement('a');
		temp_img_close_button.id='ab_calendar_close_button';
		temp_img_close_button.title='zamknij';
		
		temp_img_close_button.onclick=function()	
		{	document.getElementById('calendar_content_from_ajax').parentNode.removeChild(document.getElementById('calendar_content_from_ajax'));
			clearInterval(ab_interval_calendar_content);
		};
			/*przycisk więcej*/
		temp_array=day_content.split(';');
		temp_customer=temp_array[1].split(':');
		
		temp_img_more_button=document.createElement('a');
		temp_img_more_button.id='ab_calendar_more_button';
		temp_img_more_button.title='pokaż wszystkie';
		temp_img_more_button.href='/?modul=zdarzenia&date='+temp_customer[1];
		
		temp_img_more_button.onclick=function()	
		{	window.location=this.href;	};
			
			/*dodawanie przycisków*/
		document.getElementById('calendar_content_from_ajax').appendChild(temp_img_close_button);
		document.getElementById('calendar_content_from_ajax').appendChild(temp_img_more_button);
		
		clearInterval(ab_interval_calendar_content);
	}
	else
	{	/*wyświetlanie kolejnych lini*/
		temp_array=day_content.split(';');
		
		switch(ab_calendar_content_current_line)
		{	case 1:
				/*ilość zadań na dany dzień*/
				temp_customer=temp_array[ab_calendar_content_current_line].split(':');
					/*główny div*/
				temp_div=document.createElement('div');
				temp_div.id='ab_calendar_content_current_task';
					/*data*/
				date_div=document.createElement('div');
				date_div.id='ab_calendar_content_date';
				date_div.innerHTML='<a onclick="window.location=this.href;" href="/?modul=zdarzenia&date='+temp_customer[1]+'" title="pokaż wszystkie">'+temp_customer[1]+'</a>';
					/*ilość*/
				amount_div=document.createElement('div');
				amount_div.id='ab_calendar_content_amount';
				amount_div.innerHTML='zadania: '+temp_customer[0];
				
				temp_div.appendChild(date_div);
				temp_div.appendChild(amount_div);
				
				document.getElementById('calendar_content_from_ajax').appendChild(temp_div);
				break;
			case 2:
				/*ilość zadań zaległych*/
				temp_div=document.createElement('div');
				temp_div.id='ab_calendar_content_past_task';
				temp_div.innerHTML='zaległe: '+temp_array[ab_calendar_content_current_line];
				
				document.getElementById('calendar_content_from_ajax').appendChild(temp_div);
				break;
			default:
				/*klient*/
				temp_customer=temp_array[ab_calendar_content_current_line].split('@');
					/*główny div*/
				temp_div=document.createElement('div');
				temp_div.className='ab_calendar_content_customer';
					/*lp*/
				lp_div=document.createElement('div');
				lp_div.className='ab_calendar_content_customer_lp';
				lp_div.innerHTML=(ab_calendar_content_current_line-2)+'.';
					/*nazwa*/
				name_div=document.createElement('div');
				name_div.className='ab_calendar_content_customer_name';
				name_div.innerHTML=temp_customer[0];
					/*data*/
				date_div=document.createElement('div');
				date_div.className='ab_calendar_content_customer_date';
				date_div.innerHTML=temp_customer[1];
					/*clear*/
				clear_div=document.createElement('div');
				clear_div.className='clear';
				
				temp_div.appendChild(lp_div);
				temp_div.appendChild(name_div);
				temp_div.appendChild(date_div);
				temp_div.appendChild(clear_div);
				
				document.getElementById('calendar_content_from_ajax').appendChild(temp_div);
		}
		
		ab_calendar_content_current_line++;
	}
}
/*---------------------------------------------*/