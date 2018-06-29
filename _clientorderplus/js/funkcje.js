function CzyDataIstnieje(Dzien, Miesiac, Rok) {
	// 1582 - rok wprowadzenia aktualnie obowi�zuj�cego kalendarza gregoria�skiego
	if ((Rok > 1582) && (Miesiac >= 1) && (Miesiac <= 12) && (Dzien >= 1) && (Dzien <= 31)) {
		if ((Miesiac == 4) || (Miesiac == 6) || (Miesiac == 9) || (Miesiac == 11)) {
			if (Dzien <= 30) {
				return true;
			}
		}
		else if ((Miesiac == 2) && (Dzien <= 29)) {
			if (!((Rok % 4 == 0) && (Rok % 100 != 0) && (Rok % 400 == 0))) {
				if (Dzien <= 28) {
					return true;
				}
				return false;
			}
			return true;
		}
		else {
			return true;
		}
	}
	return false;
}

function SprawdzDate(Data) {
	FormatDaty = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/;
	if (Data.match(FormatDaty)) {
		Rok = parseInt(Data.substr(0, 4), 10);
		Miesiac = parseInt(Data.substr(5, 2), 10);
		Dzien = parseInt(Data.substr(8, 2), 10);
		return CzyDataIstnieje(Dzien, Miesiac, Rok);
	}
	return false;
}

function ValueChange(pole, NewValue){
	document.getElementById(pole).value = NewValue;
	document.formularz.submit();
}

function PrzeladujForm(){
	document.formularz.action = '';
	document.formularz.target = '';
	document.formularz.submit();
}

function GenerujSpecyfikacje(){
	document.formularz.action = 'specyfikacja_do_faktury.php';
	document.formularz.target = '_blank';
	document.formularz.submit();
}

function CheckZmianaTerminu(){
	if(document.getElementById('ZmianaTerminu').value == 1){
		alert("Przed zapisaniem prosz� pobra� kurs z dnia za�adunku");
		return false;
	}else{
		document.getElementById('nowy').value = 'nowy'; 
		return true;
	}
}

function WyslijZlecenie(){
    akcept = document.getElementById("akceptacja-regulaminu");
    if(akcept.checked == true){
        document.formularz.submit();
    }else{
        alert("Prosz\u0119 zaznaczy\u0107 akceptacj\u0119 regulaminu!");
    }
}

function TypeHours(check){
    if(check == true){
        document.getElementById("raporty_godziny").style.display = "";
    }else{
        document.getElementById("raporty_godziny").style.display = "none";
    }
}

function ShowInfo(mode){
    info = "";
    if(mode == 1){
        info = "standardowo otrzymujecie od nas Państwo codziennie do godziny 10.00  raport z informację o aktualnym statusie przesyłki, jeśeli chcieliby Państwo otrzymać dodatkowe raporty, proszę o wpisanie godzin po przecinku (raporty po godzinie 16.00, wysyłane są sms-em)";
    }
    if(mode == 2){
        info = "w uwagach mogą Państwo zamieścić informacje dodatkowe o zlecenie np. wartość towaru, specjalne wymagania dotyczące zabezpieczenia towaru, numery załadunku itp.";
    }
    ddrivetip(info);
}

var tip;

function Tooltip(klasa_trigger, klasa_cloud){
    $(klasa_trigger).hover(function(){
        tip = $(klasa_cloud);
        tip.show(); //Show tooltip
    }, function() {
        tip.hide(); //Hide tooltip
    }).mousemove(function(e) {
        var mousex = e.pageX + 20; //Get X coodrinates
        var mousey = e.pageY + 20; //Get Y coordinates
        var tipWidth = tip.width(); //Find width of tooltip
        var tipHeight = tip.height(); //Find height of tooltip

        //Distance of element from the right edge of viewport
        var tipVisX = $(window).width() - (mousex + tipWidth);
        //Distance of element from the bottom of viewport
        var tipVisY = $(window).height() - (mousey + tipHeight);

        if ( tipVisX < 0 ) { //If tooltip exceeds the X coordinate of viewport
            mousex = e.pageX - tipWidth - 20;
        } if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
            mousey = e.pageY - tipHeight - 20;
        }
        //Absolute position the tooltip according to mouse position
        tip.css({  top: mousey, left: mousex });
    });
}
$(document).ready(function() {
    //Tooltips
    Tooltip(".tip_trigger", ".tip");
    Tooltip(".tip_trigger2", ".tip2");
});