function ValueChange(idPola,newValue){
	document.getElementById(idPola).value = newValue;
	document.formularz.submit();
}

function ValueChangeNoSubmit(idPola,newValue){
	document.getElementById(idPola).value = newValue;
}

function NewWindow(mypage,myname,w,h,scroll){
	LeftPosition = (screen.width - w)/2;
	TopPosition = (screen.height - h)/2;
	settings = 'height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+',scrollbars='+scroll+',resizable'
	window.open(mypage,myname,settings);
}

function Close(){
	window.close();
}

function ChangeAction(Form, Pole, Value){
	$("#"+Pole).val(Value);
	$("#"+Form).submit();
}

function Popup(page){
	NewWindow(page, "Popup", 800, 600, "yes");
}

function ShowPopup(){
	$('#offtop').css("visibility", "visible");
	$('#popup_bg').css("visibility", "visible");
	$('#popup').css("visibility", "visible");
}

function ClosePopup(){
	$('#offtop').css("visibility", "hidden");
	$('#popup_bg').css("visibility", "hidden");
	$('#popup').css("visibility", "hidden");
}

function AutomaticClose(ReturnObj){
	$(ReturnObj).css('background-image', '');
	setTimeout(KomunikatyClose, 5000);
}

function KomunikatyClose(){
	$('#Komunikaty').css('display','none');
	$('#Komunikaty2').css('display','none');
}

var url_base = '';
var url_fullPath = '';

function get_html(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });
    ajax_action(setting, params)
    
}

function save_form(setting){
    var params = '';
    params = setting['params'];
    ajax_action(setting, params)
}

function ajax_action(setting, params){
   $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).html(html);
                }
                if(setting['do_after']){
                    eval(setting['do_after']);
                }
             }
          })
}

function PodajPowod(pole){
    if(pole.value == 4){
        document.getElementById("powod_zakazu").style.display = '';
    }else{
        document.getElementById("powod_zakazu").style.display = 'none';
    }
}

function GenerujSpecyfikacje(){
	document.formularz.action = 'specyfikacja_do_faktury.php';
	document.formularz.target = '_blank';
	document.formularz.submit();
}

function PrzeladujForm(){
	document.formularz.action = '';
	document.formularz.target = '';
	ValueChange("OpcjaFormularza", "przeladuj");
}

function ShowLoading(){
    $("#loading").html("<img src='images/ajax-loader.gif' />");
    $("#loading").css("display", "");
}

function CloseLoading(){
    $("#loading").css("display", "none");
}

function get_popup_content(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
             	var width = (setting['width'] ? setting['width'] : 700);
               $('#popup').html(html);
               $("#popup").css('background-color', '#FFF');
				$("#popup").css('width', width+'px');
				pozx = (screen.width/2) - (width/2);
				pozy = $('body').scrollTop()+50;
				ShowPopup(pozx,pozy);
             }
          })
}

function get_popup_content_post(setting){
	var params = '';
	var appers = '';
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
               var width = (setting['width'] ? setting['width'] : 700);
               $('#popup').html(html);
               $("#popup").css('background-color', '#FFF');
				$("#popup").css('width', width+'px');
				pozx = (screen.width/2) - (width/2);
				pozy = $('body').scrollTop()+50;
				ShowPopup(pozx,pozy);
             }
          })
}

function get_content(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).html(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function get_value(setting){
   var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']).val(html);
                }
                ClosePopup();
             }
          })
}

function save_and_add_to_select(setting){
    var params = '';
    $("#popup").html("<img src='images/ajax-loader-big.gif' />");
    $("#popup").css("width", "auto");
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             	{
                	dane = html.split("::");
                	if(dane[0] == "add"){
                            var select = $("#"+setting['add_select_id']);
                            AddOptionToSelect(select, dane[1], dane[2], true);
                            if(setting['function_after']){
                                eval(setting['function_after']);
                            }
                            ClosePopup();
                	}else{
               			$('#popup').html(html);
               			$("#popup").css('background-color', '#FFF');
                                $("#popup").css('width', '900px');
                                pozx = (screen.width/2) - (900/2);
                                pozy = $('body').scrollTop()+50;
                                ShowPopup(pozx,pozy);
                	}
                }
          })
}

function AddOptionToSelect(select, val, text, selected){
    selectOptions = select.attr('options');
    selectOptions[selectOptions.length] = new Option(text, val);
    if(selected){
        select.val(val);
    }
}

function get_select_options(setting){
    var params = '';
    $("#popup").html("<img src='images/ajax-loader-big.gif' />");
    $("#popup").css("width", "auto");
    params = setting['params'];
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             	{
                	dane = html.split("-$-");
                        var select = $("#"+setting['select_id']);
                        $("#"+setting['select_id']+" option").remove();
                        $.each(dane, function(index, value){
                            valse = value.split("::");
                            AddOptionToSelect(select, valse[0], valse[1], false);
                        });
                }
          })
}

function add_row(setting){
	var params = '';
	var appers = '';

    $.each(setting['params'], function(key, value) {
        params = params + appers + key + '=' + value;
        appers = '&';
    });

    add_row_method(params, setting);
}

function save_form_and_add_row(setting){
    params = setting['params'];
    add_row_method(params, setting);
}

function add_row_method(params, setting){
    $.ajax({ type: setting['type'],
             url: url_base + setting['action'],
             data: params,
             success: function(html)
             {
                CloseLoading();
                if(setting['return_object_id'])
                {
                    $(setting['return_object_id']+" tr:last").after(html);
                }
                if(setting['after_get_content']){
                    eval(setting['after_get_content']);
                }
             }
          })
}

function AddContainer(){
    next = parseInt($("#Containers").val());
    nextto = next+1;

    $("#Containers").val(nextto);
    add_row({'params': {next : next},
                'type'  : 'POST',
                'action': '../include/classes/ajax/fcl-row.php',
                'return_type' : 'html',
                'return_object_id' : "#FCL-Table"
        });
}

function AddContainerAir(){
    next = parseInt($("#Containers").val());
    nextto = next+1;

    $("#Containers").val(nextto);
    add_row({'params': {next : next},
                'type'  : 'POST',
                'action': '../include/classes/ajax/fcl-air-row.php',
                'return_type' : 'html',
                'return_object_id' : "#FCL-Table"
        });
}

function AddContainerAirZlec(){
    next = parseInt($("#Containers").val());
    nextto = next+1;

    $("#Containers").val(nextto);
    add_row({'params': {next : next, name : "AirZlec"},
                'type'  : 'POST',
                'action': '../include/classes/ajax/fcl-air-row.php',
                'return_type' : 'html',
                'return_object_id' : "#FCL-Table"
        });
}

function AddContainerZlec(){
    next = parseInt($("#Containers").val());
    nextto = next+1;

    $("#Containers").val(nextto);
    add_row({'params': {next : next, name : "SeaZlec"},
                'type'  : 'POST',
                'action': '../include/classes/ajax/fcl-row.php',
                'return_type' : 'html',
                'return_object_id' : "#FCL-Table"
        });
}

function ChangeSpecification(pole){
    if(pole.value == "FCL"){
        $("#LCL-spec").css('display', 'none');
        $("#FCL-spec").css('display', '');
    }else{
        $("#FCL-spec").css('display', 'none');
        $("#LCL-spec").css('display', '');
    }
}

function RemoveContainer(Idx){
    $("#fcl-row-"+Idx).remove();
}

function ChangeDGR(pole, Type, Idx){
    if(pole.value == "Yes"){
        $("#"+Type+"-class-"+Idx).removeAttr('disabled');
        $("#"+Type+"-un-"+Idx).removeAttr('disabled');
    }else{
        $("#"+Type+"-class-"+Idx).attr('disabled', 'disabled');
        $("#"+Type+"-un-"+Idx).attr('disabled', 'disabled');
    }
}

function OpenText(pole, defvalue, element_id){
    if(pole.value == defvalue){
       $("#"+element_id).css('display', '');
    }else{
        $("#"+element_id).css('display', 'none');
    }
}

function AddPosition(){
    next = parseInt($("#Positions").val());
    nextto = next+1;

    $("#Positions").val(nextto);
    add_row({'params': {next : next},
                'type'  : 'POST',
                'action': '../include/classes/ajax/faktura-morska-pozycja.php',
                'return_type' : 'html',
                'return_object_id' : "#Positions-Table"
        });
}

function AddKoszt(){
    next = parseInt($("#Positions").val());
    nextto = next+1;
    ShowLoading();
    $("#Positions").val(nextto);
    add_row({'params': {next : next},
                'type'  : 'POST',
                'action': '../include/classes/ajax/koszt-row.php',
                'return_type' : 'html',
                'return_object_id' : "#Positions-Table"
        });
}

function RemovePosition(Idx){
    $("#position-row-"+Idx).remove();
}

function RemoveAttach(ID){
    get_content({'params': {ID : ID},
                'type'  : 'POST',
                'action': 'js/remove-attach.php',
                'return_type' : 'html',
                'return_object_id' : "#bl-attach"
        });
}

function SessionTime(){
   get_content({'params': {},
                'type'  : 'POST',
                'action': 'js/session-time.php',
                'return_type' : 'html',
                'return_object_id' : "#session_time"
        });
}

function myRound(number, decimalplaces ){
    if(decimalplaces > 0){
        var multiply1 = Math.pow(10,(decimalplaces + 4));
        var divide1 = Math.pow(10, decimalplaces);
        return Math.round( Math.round(number * multiply1)/10000 )/divide1 ;
    }
    if(decimalplaces < 0){
        var divide2 = Math.pow(10, Math.abs(decimalplaces));
        var multiply2 = Math.pow(10, Math.abs(decimalplaces));
        return Math.round( Math.round(number / divide2) * multiply2 );
    }
    return Math.round(number);
}

function Oblicz(id1, id2, id3)
{
   war1 = document.getElementById(id1).value;
   war2 = document.getElementById(id2).value;
   war1 = war1.replace(',', '.');
   war2 = war2.replace(',', '.');
   wynik = myRound(war1 * war2, 2);

   document.getElementById(id1).value = war1;
   document.getElementById(id2).value = war2;
   document.getElementById(id3).value = wynik;
}

function Oblicz2(id1, id2, id3, id4, id5, id6)
{
   war1 = document.getElementById(id1).value;
   war2 = document.getElementById(id2).value;
   war4 = document.getElementById(id4).value;
   if(war1 == '' || war1 == "np" || war1 == "Np" || war1 == "zw" || war1 == "Zw")
   {
      war1 = 0;
   }
   else
   {
      war1 = war1.replace(',', '.');
      war1 = parseFloat(war1);
   }
   if(war2 == '')
   {
      war2 = 0;
   }
   else
   {
      war2 = war2.replace(',', '.');
      war2 = parseFloat(war2);
   }
   if(war4 == '')
   {
      war4 = 0;
   }
   else
   {
      war4 = war4.replace(',', '.');
      war4 = parseFloat(war4);
   }

   wynik = war2 + ((war1 / 100) * war2);
   wynik = myRound(wynik, 2);
   wynik4 = war4 + ((war1 / 100) * war4);
   wynik4 = myRound(wynik4, 2);
   wynik6 = ((war1 / 100) * war2);
   wynik6 = myRound(wynik6, 2);
   vatval = document.getElementById(id1).value
   if(vatval != "np" && vatval != "Np" && vatval != "Zw" && vatval != "zw"){
   	document.getElementById(id1).value = war1;
   }
   document.getElementById(id2).value = war2;
   document.getElementById(id3).value = wynik;
   document.getElementById(id5).value = wynik4;
   document.getElementById(id6).value = wynik6;
}

function ChangeSelectToInput(name){
    dodaj = $("#"+name+"-select").val();
    if(dodaj == "last"){
        $("#"+name+"-select-box").css('display', 'none');
        $("#"+name+"-input").css('display', 'block');
    }
}

function ChangeInputToSelect(name){
    $("#"+name+"-select").val("0");
    $("#"+name+"-select-box").css('display', 'block');
    $("#"+name+"-input").css('display', 'none');
}

function ChcePrzypomnienie(pole){
    if(pole.checked == true){
        $("#godzina_przyp").css('visibility', 'visible');
    }else{
        $("#godzina_przyp").css('visibility', 'hidden');
    }
}

function ObliczTerminPrzewoznika(){
    data_wplywu = $("#data_wplywu").val();
    ilosc_dni = parseInt($("#termin_platnosci_dni").val());
    FormatDaty = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
    Rok = parseInt(data_wplywu.substr(0, 4), 10);
    Miesiac = parseInt(data_wplywu.substr(5, 2), 10)-1;
    Dzien = parseInt(data_wplywu.substr(8, 2), 10);
    if (data_wplywu.match(FormatDaty) && Rok > 0 && Miesiac >= 0 && Dzien > 0) {
        data = new Date(Rok, Miesiac, Dzien+ilosc_dni);
        NewRok = data.getFullYear();
	NewMiesiac = data.getMonth();
	NewDzien = data.getDate();
        Miesiac1 = parseInt(NewMiesiac) + 1
        data_wstaw = NewRok+'-'+(Miesiac1 < 10 ? '0' : '')+(Miesiac1)+'-'+(NewDzien < 10 ? '0' : '')+NewDzien
        $("#termin_przewoznika").val(data_wstaw);
    }
}

function loadingContainer(div_task)
{
        var offset = div_task.offset();
        top_corner = offset.top+20;
        left_corner = offset.left+50;
	$("#div_ajax").css("top", top_corner+'px');
        $("#div_ajax").css("left", left_corner+'px');
        $("#div_ajax").html('<div class="in_container" id="loading">loading...</div>');
        $("#div_ajax").css("display", 'block');
}