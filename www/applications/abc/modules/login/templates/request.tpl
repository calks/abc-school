




		<div class="text fields education_form_module">
		
			<h1>Вход на сайт</h1>
			
			<div class="form_switch">
				<a href="{$login_link}"><span>Форма входа</span></a>
				<a class="active" href="{$request_link}"><span>Получить логин и пароль</span></a>
				<a href="{$forgot_link}"><span>Напоминание пароля</span></a>
			</div>

			{if $errors.general}
				<p class="error">
					{$errors.general}
				</p>
			
			{/if}

			{if $password_sent}
			
				<p class="success">
					Письмо с логином и паролем было выслано на указанную электронную почту.
					<br><br>
					<a href="{$login_link}">вернуться к форме входа</a>
				</p>
			
			{else}
			
				{$document->content}
				
				<form class="two-thirds-size" action="{$form_action}" method="post">		
					{foreach item=field key=fieldname from=$request_form->fields}
						<div class="full_width">
							{if $errors.$fieldname}<div class="error {$fieldname}">{$errors.$fieldname}</div>{/if}
							<label>{$captions.$fieldname} *</label>
							{$request_form->render($fieldname)}
						</div>
					{/foreach}
					
				
					<div class="comment">* - обязательное для заполнения поле</div>
					
					<input type="hidden" name="redirect" value="{$redirect}">
					<input type="submit" class="submit" name="submit" value="Выслать">
							
				</form>

				
			{/if}	
		
		</div>				
	





