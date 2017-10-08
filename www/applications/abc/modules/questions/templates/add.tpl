
	<div class="text fields">
		<h1>Задать вопрос</h1>
		
		<form action="{$form_action}" method="post">
			<div class="full_width">
				{if $errors.author_name}<div class="error author_name">{$errors.author_name}</div>{/if}
				<label>Ваше имя *</label>
				{$form->render('author_name')}
				{if $errors.author_email}<div class="error author_email">{$errors.author_email}</div>{/if}
				<label>Ваш Email *</label>
				{$form->render('author_email')}
				{if $errors.question}<div class="error question">{$errors.question}</div>{/if}
				<label>Вопрос *</label>
				{$form->render('question')}
			</div>	
					
			{if $errors.captcha}<div class="error captcha">{$errors.captcha}</div>{/if}
			<label>Код с рисунка *</label>
			{$form->render('captcha')}
			{$captcha->display()}
			
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Добавить вопрос">
			
					
		</form>		
	
	
	</div>