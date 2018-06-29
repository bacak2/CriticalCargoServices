<!-- Formularz logowania { -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center" valign="middle">
            <table width="480" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td style="text-align: left; padding: 4px;">
                            <div style="display: block; width: 50%; float: left;"><img src="images/logo.png" alt="Critical CS Plus" /></div>
                        </td>
                    </tr>
                    <tr>
                            <td align="center" valign="middle" class="border-main">
                                    <br>
                                    <form action="./" method="post">
                                    <input type="hidden" name="logowanie" value="1">
                                    <table width="180" border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                    <td align="right" valign="middle"><p class="logowanie"><b>Użytkownik:</b> </td>
                                                    <td colspan="2"><input type="text" name="pp_login" size="20" border="0" class="tabelka"></td>
                                            </tr>
                                            <tr height="1">
                                                    <td colspan="3" height="1"></td>
                                            </tr>
                                            <tr>
                                                    <td align="right" valign="middle"><p class="logowanie"><b>Hasło:</b> </td>
                                                    <td colspan="2"><input type="password" name="pp_haslo" size="20" border="0" class="tabelka"></td>
                                            </tr>
                                            <tr height="1">
                                                    <td colspan="3" height="1"></td>
                                            </tr>
                                            <tr>
                                                    <td align="right" valign="middle"><p class="logowanie"><b>Zaloguj do:</b> </td>
                                                    <td colspan="2">
                                                        <select name="program" class="tabelka">
                                                            <option value="ORDER">  Orderplus  </option>
                                                            <option value="CRM">  CRM  </option>
                                                        </select>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td colspan="3" align="right" valign="middle"><br /><input type="image" src="images/button_logowanie.gif" alt="" border="0"></td>
                                            </tr>
                                    </table>
                                    </form>
                            </td>
                    </tr>
                    <tr>
                             <td><p class="logowanie_dol">powered by <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a> for Critical Cargo and Freight Services</td>
                    </tr>
            </table>
        </td>
    </tr>
</table>
<!-- } Formularz logowania -->


<div id="offtop" onclick='ClosePopup();'></div>
<div id="popup_bg" style='position: fixed; width: 100%; display: block; z-index: 101; visibility: hidden;'><div id="popup"></div></div>

