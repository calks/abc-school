
	<div class="text fields education_form_module tax_return_form">
		
		<h1>Заявка на налоговый вычет</h1>
		
		
		<form class="" action="{$form_action}" method="post">
			
			<h2>Информация о ребенке</h2>
			<div class="full_width">
				{if $errors.kids_name}<div class="error kids_name">{$errors.child_name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('child_name')}
			</div>				
			{if $errors.child_age}<div class="error age">{$errors.child_age}</div>{/if}
			<div class="row">			
				<label>Дата рождения</label>
				{$form->render('child_birth_date')}				
			</div>
			
			
			<h2 class="separated">Информация о родителях</h2>
			<div class="full_width">
				{if $errors.parent_name}<div class="error parents_name">{$errors.parent_name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('parent_name')}				
			</div>
			{if $errors.parent_age}<div class="error age">{$errors.parent_age}</div>{/if}
			<div class="row">			
				<label>Дата рождения</label>
				{$form->render('child_birth_date')}				
			</div>
			
			<h2 class="separated">Период и стоимость обучения</h2>
			{if $errors.contracts_available_yn}<div class="error learned_earlier">{$errors.contracts_available_yn}</div>{/if}
			<label>У меня есть договоры на указанные периоды обучения</label>					
			<div class="input_wrap">{$form->render('contracts_available_yn')}</div>
			
			
			
			{$form->render('periods_count')}
			{$form->render('files_count')}
			
			
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Отправить">
			<input type="hidden" name="form_type" value="preschool">
			
			{include file=$warning_box_template}
					
		</form>		
		
		
		<form class="kids{if $form_type!='kids'} hidden{/if}" action="{$form_action}" method="post">
			
			<h2>Информация о ребенке</h2>
			<div class="full_width">
				{if $errors.kids_name}<div class="error kids_name">{$errors.kids_name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('kids_name')}
			</div>				
			{if $errors.age}<div class="error age">{$errors.age}</div>{/if}
			<div class="row">			
				<label>Возраст *</label>
				<div class="one_line">				
					{$form->render('age')}
					<label>Дата рождения</label>
					{$form->render('birth_date')}
				</div>
			</div>
			<div class="full_width">
				{if $errors.school}<div class="error school">{$errors.school}</div>{/if}
				<label>Школа *</label>
				{$form->render('school')}
			</div>					
			{if $errors.grade}<div class="error grade">{$errors.grade}</div>{/if}
			<div class="row">
				<label>Класс *</label>
				<div class="one_line">	
					{$form->render('grade')}
					<label>Смена</label>
					{$form->render('shift')}
					<label>Продленка</label>
					{$form->render('prolonged')}
				</div>
			</div>	
			
			
			<h2 class="separated">Информация о родителях</h2>
			<div class="full_width">
				{if $errors.parents_name}<div class="error parents_name">{$errors.parents_name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('parents_name')}
				<label>Место работы</label>
				{$form->render('parents_job')}
			</div>
			
			
			<h2 class="separated">Контактные данные</h2>
			<div class="full_width">
				{if $errors.phone}<div class="error phone">{$errors.phone}</div>{/if}
				<label>Телефон *</label>
				{$form->render('phone')}
				<div class="row">
					{if $errors.address}<div class="error address">{$errors.address}</div>{/if}					
					<label>Адрес *</label>
					{$form->render('address')}
				</div>
				{if $errors.email}<div class="error email">{$errors.email}</div>{/if}
				<label>Email</label>
				{$form->render('email')}
			</div>
			
			
			<h2 class="separated">Дополнительная информация</h2>
			<div class="full_width">
				{if $errors.learned_earlier}<div class="error learned_earlier">{$errors.learned_earlier}</div>{/if}
				<label>Изучали язык раньше? *</label>
				{$form->render('learned_earlier')}
				<label>Где и как долго?</label>
				{$form->render('learned_earlier_detail')}
				<label>Заметки, пожелания</label>
				{$form->render('comments')}
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
			
			{include file=$warning_box_template}
					
		</form>
		
		
		
		
		
		
		<form class="adults{if $form_type!='adults'} hidden{/if}" action="{$form_action}" method="post">
			
			<h2>Общая информация</h2>
			<div class="full_width">
				{if $errors.name}<div class="error name">{$errors.name}</div>{/if}
				<label>ФИО *</label>
				{$form->render('name')}
			</div>
			{if $errors.age}<div class="error age">{$errors.age}</div>{/if}
			<div class="row">				
				<label>Возраст *</label>
				<div class="one_line">
					{$form->render('age')}
					<label>Дата рождения</label>
					{$form->render('birth_date')}
				</div>
			</div>
			<div class="full_width">
				<label>Место работы</label>
				{$form->render('parents_job')}
			</div>
			
			
			<h2 class="separated">Контактные данные</h2>
			<div class="full_width">
				{if $errors.phone}<div class="error phone">{$errors.phone}</div>{/if}
				<label>Телефон *</label>
				{$form->render('phone')}
				<div class="row">
					{if $errors.address}<div class="error address">{$errors.address}</div>{/if}
					<label>Адрес *</label>
					{$form->render('address')}
				</div>
				{if $errors.email}<div class="error email">{$errors.email}</div>{/if}
				<label>Email</label>
				{$form->render('email')}
			</div>
			
			
			<h2 class="separated">Дополнительная информация</h2>
			<div class="full_width">
				{if $errors.learned_earlier}<div class="error learned_earlier">{$errors.learned_earlier}</div>{/if}
				<label>Изучали язык раньше? *</label>
				{$form->render('learned_earlier')}
				<label>Где и как долго?</label>
				{$form->render('learned_earlier_detail')}
				<label>Заметки, пожелания</label>
				{$form->render('comments')}
			</div>
					
			{if $errors.captcha}<div class="error captcha">{$errors.captcha}</div>{/if}
			<div class="row">
				<label>Код с рисунка *</label>
				{$form->render('captcha')}
			
				{$captcha->display()}
			</div>
			
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Отправить">
			<input type="hidden" name="form_type" value="adults">
			
			{include file=$warning_box_template}
					
		</form>		
		
		
		
		
				
		
		
	</div>	
