
function zmienColor(co,jaki)
{
co.style.backgroundColor=jaki;
}

function przeskocz(gdzies)
{
parent.location.replace(gdzies);
}

function potwierdz(tresc,cel)
{
if (confirm(tresc))
 {
 parent.location.replace(cel);
 }
}
