function SaveKoszty(){
    var error = false;
    $(".check_status").each(function(){
        var koszt_id = $(this).attr("rel");
        var numer_faktury = $("#faktura_check_"+koszt_id).val();
        var status = $(this).val();
        if(status != 1 && numer_faktury == ""){
            alert("Nie można zmienić statusu na inny niż estymowane gdy nie ma wpisanej faktury");
            $(this).css('background-color', "#FFC0C0");
            error = true;
            return false;
        }else if(status == 1 &&  numer_faktury != ""){
            alert("Wprowadzony jest numer faktury, proszę zmienić status");
            $(this).css('background-color', "#FFC0C0");
            error = true;
            return false;
        }else{
            $(this).css('background-color', "transparent");
        }
    });
    if(error == false){
        ValueChange('nowy', 'nowy');
    }
}