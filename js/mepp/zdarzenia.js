
/**
 * funckja zwracająca informacje nt. klienta (okno konkretnego zdarzenia)
 *
 * @param   customer_id -   id klienta
 * @param   my_url      -   url do połączenia
 * @param   the_element -   element, który wywołuje akcje
 */
function showInfoAboutCustomer(customer_id,my_url,the_element)
{
    my_url = '/include/classes/ajax/info-about-customer.php'
    /*tworzenie obiektu i wysyłanie zapytania*/
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null)
    {
        return;
    }

    xmlHttp.onreadystatechange = function()
    {
        if (xmlHttp.readyState==4 && xmlHttp.status==200)
        {
            element_position = returnElementPosition(the_element);
            div_element = document.createElement('div');
            div_element.style.position = 'absolute';
            div_element.style.left = (element_position[0]+350)+'px';
            div_element.style.top = element_position[1]+'px';
            div_element.style.width = '400px';
            div_element.className = 'mepp_ajax_box';
            div_element.id = 'mepp_event_content';
            div_element.innerHTML = xmlHttp.responseText;
            document.getElementById('zaczep').appendChild(div_element);

            /*przycisk zamknij*/
            document.getElementById('info_box_close').onclick=function()
            {
                document.getElementById('mepp_event_content').parentNode.removeChild(document.getElementById('mepp_event_content'));
            };
        }
    };
    xmlHttp.open("POST",my_url,true);

    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", 1);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.send("customer_id="+customer_id);
}

function oskontFormPokaz()
{
    if(document.getElementById('nowa_os_kontaktowa_form').style.display=='none')
    {
        document.getElementById('nowa_os_kontaktowa_form').style.display='block';
    }
    else
    {
        document.getElementById('nowa_os_kontaktowa_form').style.display='none';
    }
}

function addOsobaKontakowaAjax(my_url)
{
    /*tworzenie obiektu i wysyłanie zapytania*/
    my_url = '/include/classes/ajax/add-kontakt.php'
    xmlHttp=GetXmlHttpObject();
    if (xmlHttp==null)
    {
        return;
    }

    formularz=document.forms['nowa_osoba_kontaktowa'];
    if(formularz.os_kontaktowa.value=='')
    {
        alert('Nie wypełniłeś pola z imieniem i nazwiskiem kontaktu.');
        return;
    }

    parameters= 'os_kontaktowa='+formularz.os_kontaktowa.value+'&customer_id='+formularz.customer_id.value+
                '&telefon='+formularz.telefon.value+'&mail='+formularz.mail.value
    
    xmlHttp.onreadystatechange = function()
    {
        if (xmlHttp.readyState==4 && xmlHttp.status==200)
        {
            document.getElementById('mepp_event_content').innerHTML = xmlHttp.responseText;

            /*przycisk zamknij*/
            document.getElementById('info_box_close').onclick=function()
            {
                document.getElementById('mepp_event_content').parentNode.removeChild(document.getElementById('mepp_event_content'));
            };
        }
    };
    xmlHttp.open("POST",my_url,true);

    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", 1);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.send(parameters);
}
