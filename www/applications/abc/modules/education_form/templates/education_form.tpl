
	<div class="text fields education_form_module">
		
		<h1>Стать учеником</h1>
		
		<div class="form_switch">
			<a class="kids{if $form_type=='kids'} active{/if}" href="#"><span>Анкета школьника</span></a>
			<a class="adults{if $form_type=='adults'} active{/if}" href="#"><span>Анкета взрослого</span></a>
			<a class="preschool{if $form_type=='preschool'} active{/if}" href="#"><span>Анкета дошкольника</span></a>
		</div>
		
		
		{if $errors.send}<div class="error-box">{$errors.send}</div>{/if}
		
		
		<form class="preschool{if $form_type!='preschool'} hidden{/if}" action="{$form_action}" method="post">
			
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
				<label>Дет. сад *</label>
				{$form->render('school')}
			</div>
			<div class="full_width">
				{if $errors.grade}<div class="error grade">{$errors.grade}</div>{/if}
				<label>Группа *</label>
				{$form->render('grade')}
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
				<label>Телефон (дом./моб.) *</label>
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
				{*
					{if $errors.learned_earlier}<div class="error learned_earlier">{$errors.learned_earlier}</div>{/if}
					<label>Изучали язык раньше? *</label>					
					<div class="input_wrap">{$form->render('learned_earlier_yn')}</div>
				*}
				<label>Во сколько обычно забираете ребенка из д/сада?</label>
				<div class="input_wrap">{$form->render('kidergarden_end_time')}</div>
				<label>Заметки, пожелания <small>(особенности Вашего ребенка, которые педагогу необходимо учесть)</small></label>
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
