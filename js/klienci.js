function ChangeClientStatus(){
    var status_klienta = $("#klient_status").val();
    var zmien_display = (status_klienta == 1 ? "" : "none");
    $(".only_active_client").css('display', zmien_display);
}

$().ready(function(){
    ChangeClientStatus();
})

function DodajOsKontakowa(cli){
    add_row({'params': {client : cli},
                'type'  : 'POST',
                'action': '../include/classes/ajax/osoba-kontaktowa.php?action=add-os-kontakowa',
                'return_type' : 'html',
                'return_object_id' : "#osoby_kontaktowe_tabelka"
        });
}

function ZapiszOsKontakowa(cli){
    $("#os-kontakt-zapis").html("<img src='/images/ajax-loader.gif' />");
    add_row({'params' : {id_klient : cli, imie_nazwisko : $("#os-kontakt-imie-nazwisko").val(), stanowisko : $("#os-kontakt-stanowisko").val(),
                            telefon : $("#os-kontakt-telefon").val(), mail : $("#os-kontakt-mail").val()},
                'type'  : 'POST',
                'action': '../include/classes/ajax/osoba-kontaktowa.php?action=save-os-kontakowa',
                'return_type' : 'html',
                'return_object_id' : "#osoby_kontaktowe_tabelka",
                'after_get_content' : "AnulujOsKontakowa()"
        });
}

function UsunOsKontaktowa(os, cli){
    $("#Usun_"+os).html("<img src='/images/ajax-loader.gif' />");
    add_row({'params' : {id : os, cli : cli},
                'type'  : 'POST',
                'action': '../include/classes/ajax/osoba-kontaktowa.php?action=del-os-kontaktowa',
                'return_type' : 'html',
                'return_object_id' : "#osoby_kontaktowe_tabelka",
                'after_get_content' : '$("#os_kontaktowa_'+os+'").remove()'
        });
}

function AnulujOsKontakowa(){
    $("#os_kontaktowa_new").remove();
}

function Notice(checkbox, div){
    if(checkbox.checked == true){
        $("#"+div).css('display', '');
    }else{
        $("#"+div).css('display', 'none');
    }
}

function NoticeSelect(select, div){
    if(select.value == "-1"){
        $("#"+div).css('display', '');
    }else{
        $("#"+div).css('display', 'none');
    }
}

function DodajDokument(cli){
    add_row({'params': {client : cli},
                'type'  : 'POST',
                'action': '../include/classes/ajax/zalacznik.php?action=add-zalacznik',
                'return_type' : 'html',
                'return_object_id' : "#zalacznik_tabelka"
        });
}

function ZapiszDokument(){
    ValueChange("OpcjaFormularza", "add_zalacznik");
}

function UsunDokument(os, cli){
    $("#Usun_Zalacznik_"+os).html("<img src='/images/ajax-loader.gif' />");
    add_row({'params' : {id : os, cli : cli},
                'type'  : 'POST',
                'action': '../include/classes/ajax/zalacznik.php?action=del-zalacznik',
                'return_type' : 'html',
                'return_object_id' : "#zalacznik_tabelka",
                'after_get_content' : '$("#zalacznik_'+os+'").remove()'
        });
}

function AnulujDokument(){
    $("#zalacznik_new").remove();
}