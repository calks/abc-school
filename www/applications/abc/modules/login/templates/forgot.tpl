




		<div class="text fields education_form_module">
		
			<h1>Вход на сайт</h1>
			
			<div class="form_switch">
				<a href="{$login_link}"><span>Форма входа</span></a>
				<a href="{$request_link}"><span>Получить логин и пароль</span></a>
				<a class="active" href="{$forgot_link}"><span>Напоминание пароля</span></a>
			</div>

			
			{if $password_sent}
			
				<p class="success">
					Письмо с паролем было выслано вам на электронную почту.
					<br><br>
					<a href="{$login_link}">вернуться к форме входа</a>
				</p>
			
			{else}
			
				{$document->content}
				
				<form class="half-size" action="{$form_action}" method="post">			
					<div class="full_width">
						{if $errors.email}<div class="error mail">{$errors.email}</div>{/if}
						<label>Email *</label>
						<input type="text" name="email" value="{$email}">
					</div>
				
					<div class="comment">* - обязательное для заполнения поле</div>
					
					<input type="hidden" name="redirect" value="{$redirect}">
					<input type="submit" class="submit" name="submit" value="Выслать">
							
				</form>

				
			{/if}	
		
		</div>				
	





