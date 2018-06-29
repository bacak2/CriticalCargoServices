/*----------------------------------------------------------------------------------------------
							FUNKCJE DOTYCZĄCE ELEMENTÓW
  ----------------------------------------------------------------------------------------------
*/
/*funkcja zwracająca tablicę z rozmiarem obrazka
  parametry:
	ab_image_src	-	element, którego rozmiar chcemy pobrać
  return:
	array(x_size,y_size)
  -----------------------------------------------
  UWAGA:	Funkcja do obsługi zdjęć już załodowanych przez przeglądarkę. W wypadku konieczności pobrania rozmiaru zdjęcia z serwera należy wykorzystać własność onload() elementu Image.
  -----------------------------------------------
*/
function returnImageSize(ab_image_src)
{	/*tworzenie obiektu Image*/
	ab_image_element=new Image();
	ab_image_element.src=ab_image_src;
	
	/*zwracanie rozmiaru elementu*/
	obiect_size = new Array(2);
	
	obiect_size[0]=ab_image_element.width;
	obiect_size[1]=ab_image_element.height;
	return obiect_size;
}
/*---------------------------------------------*/

/*----------------------------------------------------------------------------------------------
							FUNKCJE DOTYCZĄCE EKRANU
  ----------------------------------------------------------------------------------------------
*/
/*funkcja zwracająca tablicę z rozmiarem ekranu
  return:
	array(x_size,y_size)
  -----------------------------------------------
*/
function returnScreenSize()
{	/*wstępny rozmiar ekranu*/
	x_size = 0;
    y_size = 0;
	
	/*pobieranie rozmiaru okna w zależności od przeglądarki*/
	if (typeof window.innerWidth!='undefined')
	{	y_size = window.innerHeight;
		x_size = window.innerWidth;
	}
	else
	{	if (document.documentElement &&
			typeof document.documentElement.clientWidth!='undefined' && 
			document.documentElement.clientWidth!=0)
		{	y_size = document.documentElement.clientHeight;
			x_size = document.documentElement.clientWidth;
		}
		else 
		{	if (document.body && typeof document.body.clientWidth!='undefined')
			{	y_size = document.body.clientHeight;
				x_size = document.body.clientWidth;
			}
		}
	}
	
	/*zwracanie rozmiaru ekranu*/
	screen_size = new Array(2);
	screen_size[0]=x_size;
	screen_size[1]=y_size;
	return screen_size;
}
/*---------------------------------------------*/

/*funkcja zwracająca tablicę z współrzędnymi elementu na stronie
  parametry:
	ab_element	-	element, którego położenie chcemy pobrać
  return:
	array(x_position,y_position)
  -----------------------------------------------
*/
function returnElementPosition(ab_element)
{	/*wstępna pozycja elementu*/
	x_position = 0;
    y_position = 0;
	
	/*jeżeli element posiada rodzica, to aktualizujemy wartości określające współrzędne elementu*/
    if (ab_element.offsetParent) 
	{	x_position = ab_element.offsetLeft
        y_position = ab_element.offsetTop
        while (ab_element = ab_element.offsetParent)
		{	x_position += ab_element.offsetLeft
            y_position += ab_element.offsetTop
        }
    }
	
	/*zwracanie współrzędnych elementu*/
	position = new Array(2);
	position[0]=x_position;
	position[1]=y_position;
	return position;
}
/*---------------------------------------------*/

/*----------------------------------------------------------------------------------------------
							OBSŁUGIWANIE ZDARZEŃ GENEROWANYCH PRZEZ MYSZ
  ----------------------------------------------------------------------------------------------
*/
/*funkcja zwracająca tablicę z współrzędnymi kursora
  parametry:
	event		-	obiekt zdarzenia
  return:
	array(x_position,y_position)
  -----------------------------------------------
*/
function mousePosition(event)
{	position = new Array(2);
	if (event.pageX || event.pageY) 	{
		position[0] = event.pageX;
		position[1] = event.pageY;
	}
	else if (event.clientX || event.clientY) 	{
		position[0] = event.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		position[1] = event.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	return position;
}
/*---------------------------------------------*/

/*----------------------------------------------------------------------------------------------
		USUWANIE PROBLEMÓW NIEZGODNOŚCI W PRZEGLĄDARKACH ZE ZMIENNEJ PRZECHOWUJ EVENT
  ----------------------------------------------------------------------------------------------
*/
/*funkcja dołączająca zdarzenie do obiektu
  parametry:
	eventObj	-	obiekt, do którego ma byc dołączone zdarzenie
	event		-	string, nazwa zdarzenia, które ma być obsłużone (bez początkowego 'on')
	eventHandler-	nazwa funkcji (bez apostrofów), która ma obsłużyć zdarzenie  
  -----------------------------------------------
*/
function manageEvent(eventObj,event,eventHandler)
{	if(eventObj.addEventListener) 
	{	eventObj.addEventListener(event,eventHandler,false);
	}
	else if(eventObj.attachEvent)
	{	event="on"+event;
		eventObj.attachEvent(event,eventHandler);
	}
}
/*---------------------------------------------*/

/*funkcja zwracająca obiekt, na którym zostało wykonane zdarzenie
  parametry:
	event		-	obiekt zdarzenia
  -----------------------------------------------
*/
function returnEventObiect(event)
{	if(window.event && window.event.srcElement)
	return window.event.srcElement;
	if(event && event.target)
	return event.target;
}
/*---------------------------------------------*/

/*funkcja anulująca zdarzenie
  parametry:
	event		-	obiekt zdarzenia
  -----------------------------------------------
*/
function cancelEvent(event)
{	if(event.preventDefault)
	{	event.preventDefault();
		event.stopPropagation();
	}
	else
	{	event.returnValue=false;
		event.cancelBubble=true;
	}
}
/*---------------------------------------------*/

/*funkcja zwracająca zdarzenie
  parametry:
	event		-	obiekt zdarzenia
  -----------------------------------------------
*/
function returnEvent(event)
{	event=event ? event : window.event;
	return event;
}
/*---------------------------------------------*/

/*funkcja zwracająca obiekt do obsługi AJAX'a
  -----------------------------------------------
*/
function GetXmlHttpObject()
{	var xmlHttp=null;
	try		 { xmlHttp=new XMLHttpRequest(); }
	catch (e){ try		{ xmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); 	  }
			  catch (e)	{ xmlHttp=new ActiveXObject("Microsoft.XMLHTTP"); }
			 }
	return xmlHttp;
}
/*---------------------------------------------*/