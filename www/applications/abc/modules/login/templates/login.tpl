

		<div class="text fields education_form_module">
		
			<h1>Вход на сайт</h1>
			
			<div class="form_switch">
				<a class="active" href="{$login_link}"><span>Форма входа</span></a>
				<a href="{$request_link}"><span>Получить логин и пароль</span></a>
				<a href="{$forgot_link}"><span>Напоминание пароля</span></a>
			</div>

		
			<form class="half-size" action="{$form_action}" method="post">			
				
				
				<div class="full_width">
					{if $errors.login}<div class="error login">{$errors.login}</div>{/if}
					<label>Логин *</label>
					<input type="text" name="login_form[login]" value="{$login_form.login}">
				</div>
				
				<div class="full_width">
					{if $errors.password}<div class="error password">{$errors.password}</div>{/if}
					<label>Пароль *</label>
					<input type="password" name="login_form[pass]" value="{$login_form.pass}">
				</div>
	
					
				<div class="comment">* - обязательное для заполнения поле</div>
				
				<input type="hidden" name="redirect" value="{$redirect}">
				<input type="submit" class="submit" name="submit" value="Войти">
						
			</form>
			
			<div class="warning-box">
				Обращаем особое внимание на то, что с 15 октября каждого нового учебного года 
				регистрацию необходимо пройти повторно. <br>
				С 1 июня по 15 октября информация в личном кабинете не является актуальной. 
			</div>
		</div>				
	

