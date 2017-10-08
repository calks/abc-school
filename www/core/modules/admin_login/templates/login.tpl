

	<div class="login_form">
		<form action="{$form_action}" method="POST">
		    <table align="center" class="login" border="0">
			    <tr>
			        <td valign="top">Логин:&nbsp;</td>
			        <td><input type="text" name="login_form[login]" value="{$login_form.login}"></td>
			    </tr>
			    <tr>
			        <td valign="top">Пароль:&nbsp;</td>
			        <td valign="top"><input type="password" name="login_form[pass]" value="{$login_form.pass}"></td>
			    </tr>
			    <tr>
			        <td>&nbsp;</td>
			        <td valign="top" style="text-align:right;" colspan="2"><input type="submit" name="submit" value=" Войти"></td>
			    </tr>
			    {*<tr>
			        <td valign="top"></td>
			        <td><a href="{$forgot_link}"><i>забыли пароль?</i></a></td>
			    </tr>*}
		    </table>
		</form>
	</div>
