/*zmienne przechowujące usunięty obiekt*/
var temp_select_mepp=new Array();

/*funkcja usuwająca element select i wstawiająca w jego miejsce pole input
  parameters:
	select_element	-	obiekt select
  -----------------------------------------------
*/
function customerSelectToInput(select_element)
{	if(select_element.value!='last')
	{	/*jeżeli wybrano inną opcję niż dodawanie nowego elementu*/
		return;
	}
	
	/*zapisywanie selecta do zmiennej*/
	temp_select_mepp[select_element.name]=temp_select_grupa=select_element.parentNode.innerHTML;
	
	/*pobieranie rodzica elementu i danych z selecta*/
	el_parent=select_element.parentNode;
	
	element_input=document.createElement('input');
	element_input.name=select_element.name;
	element_input.id=select_element.name;
	element_input.type='text';
	element_input.style.width='80%';
	
	element_div_with_button=document.createElement('div');
	element_div_with_button.className='button_cols';
	element_div_with_button.style.width='100px';
	element_div_with_button.style.styleFloat = 'right';
	element_div_with_button.style.cssFloat = 'right';
	element_div_with_button.innerHTML='<div class="orange_small_button_left"></div><div class="orange_small_button_middle"><button title="wróć do pola wybieralnego" value="'+select_element.name+'" onclick="customerInputToSelect(this);return false;">wróć</button></div><div class="orange_small_button_right"></div>';
	
	el_parent.removeChild(select_element);
	el_parent.appendChild(element_input);
	el_parent.appendChild(element_div_with_button);
}
/*---------------------------------------------*/

/*funkcja usuwająca element input i wstawiająca w jego miejsce pole select
  parameters:
	button_element	-	obiekt button
  -----------------------------------------------
*/
function customerInputToSelect(button_element)
{	
	select_element=temp_select_mepp[button_element.value];
	
	el_parent=button_element.parentNode.parentNode.parentNode;
	
	el_parent.removeChild(document.getElementById(button_element.value));
	el_parent.removeChild(button_element.parentNode.parentNode);
	
	el_parent.innerHTML=select_element;
}
/*---------------------------------------------*/