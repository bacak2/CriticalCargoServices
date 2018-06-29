<!--

var ie4, ns4, ns6;
ie = document.all;
ns4 = document.layers;
ns6 = document.getElementById && !document.all;

var ZrodlowyRok;
var ZrodlowyMiesiac;
var ZrodlowyDzien;
var Rok;
var Miesiac;
var Dzien;
var PoleFormularza;

// ilosc dni w roku
var dni = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
// nazwy miesiecy
var miesiac = new Array('Styczeñ','Luty','Marzec','Kwiecieñ', 'Maj','Czerwiec','Lipiec','Sierpieñ','Wrzesieñ','Pa¼dziernik','Listopad','Grudzieñ');
var CzyZmianaTerminu = 0;

// dane kolorow
var kol = new Array(5)
kol[0] = '#FFFFFF'; // kolor tla kalendarza, kolor tekstu wybranego dnia, nazw dni tyg...
kol[1] = '#E1EDFF'; // kolor pol kalendarza - dni zwykle
kol[2] = '#FFDBDB'; // kolor pol kalendarza - niedziele
kol[3] = '#0A74E0'; // kolor pola oznaczajacego aktualny dzien, kolor ramki, przycisku zamykajacego, tekstu
kol[4] = '#AAAAAA'; // kolor pol okreslajacych dni tygodnia (pn,wt...)

// ile lat pokazywane w kalendarzu od aktualnej daty
var wstecz = 1;
var wprzod = 2;

// ilosc dni w Lutym - przeliczane po zmianie miesiaca lub roku
function dniMies()
{
	dni[1] = (Rok % 4 == 0) ? 29 : 28;
}

// pobieranie pozycji myszy
function mysz(e)
{
/*	if(ns4 || ns6)
	{
		x = e.pageX;
		y = e.pageY;
	}
	if(ie)
	{
		x = -event.clientX;
		y = -event.clientY;
	}*/
	x = 0;
	y = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) 	{
		x = e.pageX;
		y = e.pageY;
	}
	else if (e.clientX || e.clientY) 	{
		x = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		y = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
}

// funkcja pokazujaca kalendarz pod kursorem myszy
function showKal(fp, Zmiana)
{
	CzyZmianaTerminu = Zmiana;
	PoleFormularza = fp;
	AktualnaData = PoleFormularza.value;
	FormatDaty = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
	Rok = parseInt(AktualnaData.substr(0, 4), 10);
	Miesiac = parseInt(AktualnaData.substr(5, 2), 10)-1;
	Dzien = parseInt(AktualnaData.substr(8, 2), 10);
	if (AktualnaData.match(FormatDaty) && Rok > 0 && Miesiac >= 0 && Dzien > 0) {
		data = new Date(Rok, Miesiac, Dzien);
	}
	else {
		data = new Date();
	}
	ZrodlowyRok = data.getFullYear();
	ZrodlowyMiesiac = data.getMonth();
	ZrodlowyDzien = data.getDate();
	Rok = ZrodlowyRok;
	Miesiac = ZrodlowyMiesiac;
	Dzien = ZrodlowyDzien;
	
	dniMies();

	pozx = x;
	pozy = y;

	rysujKal();		
	
	if(ns6 || ie)
	{
		document.getElementById('kalendarz').style.left = pozx+'px';
		document.getElementById('kalendarz').style.top = (pozy+10)+'px';
		document.getElementById('kalendarz').style.visibility = 'visible';
	}
}

// funkcja ukrywajaca kalendarz i wstawiajaca wybrana date do pola formularza
function hideKal()
{
	if(ns6 || ie) {
		document.getElementById('kalendarz').style.visibility = 'hidden';
	}
	Miesiac1 = parseInt(Miesiac) + 1;
	PoleFormularza.value = Rok+'-'+(Miesiac1 < 10 ? '0' : '')+(Miesiac1)+'-'+(Dzien < 10 ? '0' : '')+Dzien;
	if(CzyZmianaTerminu == 1 && document.getElementById("ZmianaTerminu")){
		document.getElementById("ZmianaTerminu").value = '1';
	}	
}

// ukrywanie kalendarza bez wstawiania daty
function exitKal()
{
	if(ns6 || ie) {
		document.getElementById('kalendarz').style.visibility = 'hidden';
	}
}

// ustawianie nowej daty po zmianie miesiaca lub roku
function setData()
{
	Miesiac = document.forms['sdata'].elements['month'].value;
	Rok = document.forms['sdata'].elements['year'].value;
	
	dniMies();
	rysujKal();
}

// rysowanie kalendarza
function rysujKal()
{
	kaltxt = '<form name="sdata" onSubmit="return false;">';
	kaltxt += '<table border=0 cellpadding=0 cellspacing=2 style="border:'+kol[3]+' 2px solid;background-color:'+kol[0]+';">';
	kaltxt += '<tr class=Kaldzien><td colspan=6 height=25><select name="month" class="Kallista2" onChange="setData()">';		
	for(i=0;i<12;i++)
	{
		if(i==Miesiac)
			kaltxt += '<option value="'+i+'" selected>'+miesiac[i]+'</option>';
		else
			kaltxt += '<option value="'+i+'">'+miesiac[i]+'</option>';
	}
	kaltxt += '</select>&nbsp;<select name="year" class="Kallista2" onChange="setData()">';
	for(i=(ZrodlowyRok-wstecz);i<=(ZrodlowyRok+wprzod);i++)
	{
		if(i==Rok)
			kaltxt += '<option value="'+i+'" selected>'+i+'</option>';
		else
			kaltxt += '<option value="'+i+'">'+i+'</option>';	
	}
	kaltxt += '</select>';
	kaltxt += '</td><td><a href="javascript:exitKal()"><span class="Kalaktday">&nbsp;X&nbsp;</span></a></td></tr>';
	kaltxt += '<tr class=Kaldnityg><td width=30>Nd</td><td width=30>Pn</td><td width=30>Wt</td><td width=30>¦r</td>';
	kaltxt += '<td width=30>Czw</td><td width=30>Pt</td><td width=30>So</td></tr><tr class=Kaldzien>';

	j = 1;

	data1 = new Date(Rok, Miesiac, 1);
	DzienTygodnia = data1.getDay();
	
	for(i=0;i<DzienTygodnia+dni[Miesiac];i++)
	{
		if(i>=DzienTygodnia)
		{
			if(j==ZrodlowyDzien && Rok==ZrodlowyRok && Miesiac==ZrodlowyMiesiac)
				kaltxt += '<td class=Kalaktday><a class=Kalaktday href="javascript:Dzien='+j+';hideKal();" >'+j+'</a></td>';
			else if(i%7==0)
				kaltxt += '<td class=Kalniedz><a class=Kalniedz href="javascript:Dzien='+j+';hideKal();" >'+j+'</a></td>';
			else
				kaltxt += '<td><a class=Kaldzien href="javascript:Dzien='+j+';hideKal();" >'+j+'</a></td>';
			j++;
			if(i%7==6)
				kaltxt += '</tr><tr class=Kaldzien>';
		}
		else
			kaltxt += '<td></td>';
	}

	kaltxt += '</tr></table></form>';
	
	document.getElementById("kalendarz").innerHTML = kaltxt;
}

// style kalendarza i warstwa, na ktorej sie znajduje
document.write('<div id="kalendarz" style="visibility:hidden;position:absolute;z-index:2"></div>');
document.write('<style type="text/css">');
document.write('.Kaldzien{font-family:Verdana;font-size:11px;color:'+kol[3]+';text-align:center;background-color:'+kol[1]+';text-decoration:none}');
document.write('.Kalniedz{font-family:Verdana;font-size:11px;color:'+kol[3]+';text-align:center;background-color:'+kol[2]+';text-decoration:none}');
document.write('.Kalaktday{color:'+kol[0]+';font-weight:bold;text-align:center;background-color:'+kol[3]+';text-decoration:none}');
document.write('.Kaldnityg{font-family:Verdana;font-size:11px;color:'+kol[0]+';text-align:center;background-color:'+kol[4]+';}');
document.write('.Kallista{font-family:Verdana;font-size:11px;color:'+kol[3]+';}</style>');

//-->
