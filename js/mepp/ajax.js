/*zmienna do kontrolowania divów z treścią zdarzenia*/
var ab_interval_ajax_content;
/*zmienna z aktualnie wyświetlaną linią*/
var ab_ajax_current_line;

/*funkcja pobierająca dane na temat danego zdarzenia
  parameters:
	my_host_url		-	url
	id_of_event		-	id zdarzenia
  -----------------------------------------------
*/
function getEventDesc(my_host_url,id_of_event)
{	/*url do odebrania informacji na temat zdarzenia*/
	my_ajax_url='/include/classes/ajax/get-event-content.php?ev='+id_of_event

	/*tworzenie obiektu i wysyłanie zapytania*/
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	return;

	xmlHttp.onreadystatechange = function()
	{	if (xmlHttp.readyState==4 && xmlHttp.status==200)
		{	/*dodawanie diva*/
			makeDivForAjaxResult('mepp_event_content',document.getElementById('event_'+id_of_event).parentNode,20,10,'');

			/*przerabianie tekstu*/
			ajax_result_text=createEventContent(xmlHttp.responseText);

			/*wywoływanie funkcji powiększającej div'a z treścią*/
			// alert(ajax_result_text);
			ab_interval_ajax_content=setInterval('showAjaxContentBox(\''+ajax_result_text+'\',"mepp_event_content","400")',30);
		}
	};
	xmlHttp.open("GET",my_ajax_url,true);
	xmlHttp.send(null);

}
/*---------------------------------------------*/

/*funkcja pobierająca dane na temat danego zdarzenia
  parameters:
	my_host_url		-	url
	id_of_event		-	id zdarzenia
  -----------------------------------------------
*/
function getSmallEventDesc(my_host_url,id_of_event)
{	/*url do odebrania informacji na temat zdarzenia*/
        my_ajax_url='/include/classes/ajax/get-small-event-content.php?ev='+id_of_event
	//my_ajax_url=my_host_url+'/zdarzenia/getsmalleventcontent/'+id_of_event
				
	/*tworzenie obiektu i wysyłanie zapytania*/
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	return;
		
	xmlHttp.onreadystatechange = function() 
	{	if (xmlHttp.readyState==4 && xmlHttp.status==200)
		{	/*dodawanie diva*/
			makeDivForAjaxResult('mepp_event_content',document.getElementById('event_'+id_of_event).parentNode,'',10,5);
			
			/*przerabianie tekstu*/
			ajax_result_text=createEventContent(xmlHttp.responseText);
			
			/*wywoływanie funkcji powiększającej div'a z treścią*/
			ab_interval_ajax_content=setInterval('showAjaxContentBox(\''+ajax_result_text+'\',"mepp_event_content","260")',30);			
		} 
	};
	xmlHttp.open("GET",my_ajax_url,true);
	xmlHttp.send(null);

}
/*---------------------------------------------*/

/*funkcja zwracająca dane gotowe do wyświetlenia (treść box'u ze zdarzeniem)
  parameters:
	event_content	-	responseText
  -----------------------------------------------
*/
function createEventContent(event_content)
{	temp_response=event_content.split(/<space>/);
		/*temat*/
	title_event_div='<div class="mepp_ajax_head_name">'+temp_response[1]+'</div>';
		/*data_utworzenia, data początkowa, data końcowa*/
	event_date_div='<div class="mepp_ajax_head_date"><span'+(temp_response[6]==1 ? ' class="important_event"' : '')+'>'+temp_response[5]+'</span>, '+temp_response[2]+'</div>';
		/*treść*/
	if(temp_response[7]=='null')
	{	content_event_div='';	}
	else
	{	/*informacje na temat klienta*/
		customer_part='<a href="'+temp_response[12]+'?modul=klienci&akcja=szczegoly&id='+temp_response[9]+'" title="'+temp_response[8]+'"><b>'+temp_response[8]+'</b></a>';
		tel_part = temp_response[10]!='' ? '<br /><b>tel:</b> '+temp_response[10] : '';
		mail_part = temp_response[11]!='' ? '<br /><b>e-mail:</b> <a href="mailto:'+temp_response[11]+'" title="napisz do">'+temp_response[11]+'</a>' : '';
		
                
                if(temp_response.length==15)
                {
                    osoby_kontakt=temp_response[13];
//                    alert(osoby_kontakt);
                    mail_part+=osoby_kontakt;
                }

                content_event_div='<div class="mepp_ajax_description">'+customer_part+tel_part+mail_part+'</div>';
		
		/*treść zdarzenia*/
		temp_content=makeContentAjax(temp_response[7]);
		content_event_div+='<div class="mepp_ajax_content">'+temp_content+'</div>';	
		content_event_div+='<space><div class="mepp_ajax_button button_cols">'+temp_response[0]+'</div>';
	}
	return title_event_div+'<space>'+event_date_div+'<space>'+content_event_div+'<space>';
}
/*---------------------------------------------*/

/*funcja przerabiająca komentarze do wyświetlenia
  -----------------------------------------------
*/
function makeContentAjax(content_to_change)
{	
	temp_response_comment=content_to_change.split(/<comment>/);
	comment_to_return='';
	for(part_of_array in temp_response_comment)
	{
		if(temp_response_comment[part_of_array]=='')
		{	continue;	}
		
		part_of_array_second=temp_response_comment[part_of_array].split(/<date>/);
		comment_to_return+='<div class="ajax_box_comment"><div class="ajax_box_comment_date">'+part_of_array_second[1]+'</div><div class="ajax_box_comment_content">'+part_of_array_second[0]+'</div></div>';
		
	}	
	 // alert(temp_response_comment[0]);
	 // alert(content_to_change);
	return comment_to_return;
}
/*---------------------------------------------*/

/*-----------------------------------------------------------------------------
					ROZWIJANIE BOXU I DODAWANIE GO
  -----------------------------------------------------------------------------
*/

/*funkcja tworząca div'a wyświetlającego rezultat zapytania
  parameters:
	make_div_id		-	id elementu tworzonego
	make_div_parent	-	element, do którego jest dodawany div
	position_left	-	odległość od lewej
	position_top	-	odległość od góry
	position_right	-	odległość od prawej
  ---------------------------------------------
*/
function makeDivForAjaxResult(make_div_id,make_div_parent,position_left,position_top,position_right)
{	/*usuwanie elementu z treścią, jeżeli już taki istnieje*/
	if(document.getElementById(make_div_id))
	{	document.getElementById(make_div_id).parentNode.removeChild(document.getElementById(make_div_id));
		clearInterval(ab_interval_ajax_content);
	}
	
	/*tworzenie głównego div'a*/
	temp_ajax_div=document.createElement('div');
	temp_ajax_div.className='mepp_ajax_box';
	temp_ajax_div.id=make_div_id;
		/*pozycja elementu*/
	if(position_left!='')
	{	temp_ajax_div.style.left=position_left+'px';	}
	if(position_right!='')
	{	temp_ajax_div.style.right=position_right+'px';	}
	temp_ajax_div.style.top=position_top+'px';
		/*szerokość*/
	temp_ajax_div.style.width='0px';
	
	/*dodawanie do elementu rodzica*/
	make_div_parent.appendChild(temp_ajax_div);
	
	/*ustawienie aktualnej lini tekstu do dodania*/
	ab_ajax_current_line=0;
}
/*---------------------------------------------*/

/*funkcja rozszerzająca okno
  parameters:
	ajax_result		-	responseText gotowy do wyświetlenia
	ajax_div_id		-	div, do którego ma zostać załadowany tekst
	element_width	-	szerokość div'a
  -----------------------------------------------
*/
function showAjaxContentBox(ajax_result,ajax_div_id,element_width)
{	if(document.getElementById(ajax_div_id).style.width==element_width+'px')
	{	/*div ma już odpowiednią szerokość*/
		temp_response=ajax_result.split(/<space>/);
		
		if(ab_ajax_current_line==temp_response.length)
		{	/*zostało wyświetlone wszystko*/
			clearInterval(ab_interval_ajax_content);
			
			/*dodawanie buttona zamykającego*/
			addCloseButtonToAjaxBox(ajax_div_id);
		}
		else
		{	/*dodawanie kolejnej lini*/
			document.getElementById(ajax_div_id).innerHTML+=temp_response[ab_ajax_current_line];
			ab_ajax_current_line++;			
		}		
	}
	else
	{	/*zwiększanie szerokości div'a*/
		document.getElementById(ajax_div_id).style.width=parseInt(document.getElementById(ajax_div_id).style.width)+20+'px';
	}
}
/*---------------------------------------------*/

/*funkcja dodająca przycisk zamykający do okna
  parameters:
	ajax_div_id		-	div, do którego ma zostać załadowany tekst
  -----------------------------------------------
*/
function addCloseButtonToAjaxBox(ajax_div_id)
{	temp_img_close_button=document.createElement('a');
	temp_img_close_button.className='mepp_close_ajax_box_button';
	temp_img_close_button.title='zamknij';
	
	temp_img_close_button.onclick=function()	
	{	document.getElementById(ajax_div_id).parentNode.removeChild(document.getElementById(ajax_div_id));
	};
	
	/*dodawanie przycisku*/
	document.getElementById(ajax_div_id).appendChild(temp_img_close_button);
}
/*---------------------------------------------*/