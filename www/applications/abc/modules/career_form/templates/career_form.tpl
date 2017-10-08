
	<div class="text fields education_form_module">
		
		<h1>Стать преподавателем</h1>
		
		
		<form action="{$form_action}" method="post">			
			
			
			<div class="full_width">
				{if $errors.name}<div class="error name">{$errors.name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('name')}
				
				<div class="row">
					{if $errors.birth_date_n_place}<div class="error birth_date_n_place">{$errors.birth_date_n_place}</div>{/if}
					<label>Дата и место рождения *</label>
					{$form->render('birth_date_n_place')}
				</div>

				<div class="row">
					{if $errors.family_type}<div class="error family_type">{$errors.family_type}</div>{/if}
					<label>Семейное положение *</label>
					{$form->render('family_type')}
				</div>
				
				<div class="row">
					{if $errors.degree}<div class="error degree">{$errors.degree}</div>{/if}
					<label>Образование (в том числе курсы) *</label>
					{$form->render('degree')}
				</div>

				<div class="row">
					{if $errors.experience}<div class="error experience">{$errors.experience}</div>{/if}
					<label>Трудовой опыт (организация, период работы, должность) *</label>
					{$form->render('experience')}
				</div>

				<div class="row">
					{if $errors.foreign_languages}<div class="error foreign_languages">{$errors.foreign_languages}</div>{/if}
					<label>Уровень владения иностранными языками *</label>
					{$form->render('foreign_languages')}
				</div>

				<label>Проф. навыки</label>
				{$form->render('skills')}

				<label>Личные качества</label>
				{$form->render('personality')}

				<div class="row">
					{if $errors.address}<div class="error address">{$errors.address}</div>{/if}
					<label>Адрес *</label>
					{$form->render('address')}
				</div>

				<div class="row">
					{if $errors.phone}<div class="error phone">{$errors.phone}</div>{/if}
					<label>Телефон *</label>
					{$form->render('phone')}
				</div>

				<div class="row">
					{if $errors.email}<div class="error email">{$errors.email}</div>{/if}
					<label>Email *</label>
					{$form->render('email')}
				</div>

			</div>

					
			{if $errors.captcha}<div class="error captcha">{$errors.captcha}</div>{/if}
			<div class="row">
				<label>Код с рисунка *</label>
				{$form->render('captcha')}
	
				{$captcha->display()}
			</div>
				
			
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Отправить">
			<input type="hidden" name="form_type" value="kids">
					
		</form>
		
		
		
		
		
	</div>	
