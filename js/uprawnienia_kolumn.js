function CheckWidokAll(pole, param){
    if(pole.checked == true){
        $("."+param).attr('checked',true);
    }else{
        $("."+param).attr('checked',false);
       
    }
}

function CheckWidok(param){
    $("."+param).each(function(){
        if($(this).attr('checked') == true){
            $(".widok_"+param).attr('checked', true);
        }
    })
}

function ShowColumns(pole, param){
    if(pole.checked == true){
        $("."+param).css('display', "");
        check = 1;
    }else{
        $("."+param).css('display', "none");
         check = 0;
    }
    get_html({'params' : {param : param, check : check},
            'type'  : 'POST',
            'action': '../include/classes/ajax/save_view.php'
    });
}

$().ready(function(){
    $(".ChangeView").each(function(){
        if($(this).attr('checked') == false){
            param = $(this).val();
            $("."+param).css('display', "none");
        }
    })
})