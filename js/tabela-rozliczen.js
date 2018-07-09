function CloseAjax(){
   $("#div_ajax").css('display','none');
}

function ShowOptions(element_id, id, type){
    div = $(element_id);
    loadingContainer(div);
    get_html({'params' : {id : id},
                'type'  : 'POST',
                'action': '../include/classes/ajax/akcje.php?type='+type,
                'return_type' : 'html',
                'return_object_id' : "#div_ajax"
        });
}

function ShowTerminy(element_id){
    $("#href_"+element_id).css('display', 'none');
    $("#div_platnosci_"+element_id).css('display','block');
}

function CloseTerminy(element_id){
    $("#href_"+element_id).css('display', '');
    $("#div_platnosci_"+element_id).css('display','none');
}

function PrintInvoices(){
    var invoices_numbers = "";
    $(".CheckInvoice").each(function(){
        if($(this).attr('checked') == true){
            invoices_numbers += (invoices_numbers != "" ? "," : "")+$(this).val();
        }
    })
    $("#invoice_ids").val(invoices_numbers);
    document.print_invoices.submit();
}

function PrintInvoicesNoBg(){
    var invoices_numbers = "";
    $(".CheckInvoice").each(function(){
        if($(this).attr('checked') == true){
            invoices_numbers += (invoices_numbers != "" ? "," : "")+$(this).val();
        }
    })
    $("#invoice_no_bg_ids").val(invoices_numbers);
    document.print_invoices_no_bg.submit();
}

function RaportOrders(){
    GetOrdersNumbers();
    document.orders.action = "?modul=klienci_raporty&akcja=dodawanie";
    document.orders.submit(); 
}

function RaportOrdersMorskie(){
    GetOrdersNumbers();
    document.orders.action = "?modul=klienci_raporty_morskie&akcja=dodawanie";
    document.orders.submit(); 
}

function RaportOrdersLotnicze(){
    GetOrdersNumbers();
    document.orders.action = "?modul=klienci_raporty_lotnicze&akcja=dodawanie";
    document.orders.submit(); 
}

function PotwierdzenieOrders(){
    GetOrdersNumbers();
    document.orders.action = "?modul=klienci_potwierdzenia&akcja=dodawanie";
    document.orders.submit();
}

function FakturaZbiorczaOrders(){
    GetOrdersNumbers();
    //document.orders.action = "?modul=faktury&akcja=dodawanie";
    document.orders.action = "?modul=tabela_rozliczen_nowa&akcja=faktura_zbiorcza";
    document.orders.submit();
}

function GetOrdersNumbers(){
    var orders_numbers = "";
    $(".CheckOrders").each(function(){
        if($(this).attr('checked') == true){
            orders_numbers += (orders_numbers != "" ? "," : "")+$(this).val();
        }
    })
    $("#orders_ids").val(orders_numbers);
}