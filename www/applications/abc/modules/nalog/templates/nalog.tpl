



    <div class="text fields">

        <h1>{$page->meta_title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>


        {$page->content}
        
        
        
        
        
        
		<script type="text/javascript">
			var education_periods_options = {$education_periods_options|@json_encode};
			var education_periods = {$education_periods|@json_encode};
			var files_count = {$files_count};
		</script>
	
	
		<div class="text fields education_form_module tax_return_form">
		
			{if $smarty.get.sent}
				<p class="success">
					Данные были успешно отправлены
				</p>
			{/if}
		
			{if $errors.mail}
				<p class="error">
					{$errors.mail}
				</p>
			{/if}
			
			
			<form class="" action="{$form_action}" method="post" enctype="multipart/form-data">
				
				<h2>Информация о ребенке</h2>
				<div class="full_width">
					{if $errors.child_name}<div class="error kids_name">{$errors.child_name}</div>{/if}
					<label>ФИО *</label>
					{$form->render('child_name')}
				</div>				
				{if $errors.child_age}<div class="error age">{$errors.child_age}</div>{/if}
				<div class="row">			
					<label>Дата рождения</label>
					{$form->render('child_birth_date')}				
				</div>
				
				
				<h2 class="separated">Информация о родителях (на кого оформляется вычет)</h2>
				<div class="full_width">
					{if $errors.parent_name}<div class="error parents_name">{$errors.parent_name}</div>{/if}
					<label>ФИО *</label>
					{$form->render('parent_name')}				
				</div>
				{if $errors.parent_birth_date}<div class="error age">{$errors.parent_birth_date}</div>{/if}
				<div class="row">			
					<label>Дата рождения</label>
					{$form->render('parent_birth_date')}				
				</div>
				
				<h2 class="separated">Период и стоимость обучения</h2>
				
				{if $errors.education_period}<div class="error education_period">{$errors.education_period}</div>{/if}
				<div id="education-periods">
					<label>Периоды обучения и стоимость</label>
					<div class="multiple-options">
						<div class="user-inputs">
						
						</div>
						<a href="#" class="add-period add-item">Добавить период обучения</a>
					</div>
				</div>
	
				
				{if $errors.attachment}<div class="error attachment">{$errors.attachment}</div>{/if}
				
				
				{if $errors.contracts_available_yn}<div class="error learned_earlier">{$errors.contracts_available_yn}</div>{/if}
				<label>У меня есть договоры на указанные периоды обучения</label>					
				<div class="input_wrap">{$form->render('contracts_available_yn')}</div>
				
				<div class="row" id="files">
					<label>Прикрепить квитанции/чеки (PNG, JPEG, PDF. Максимум {$max_upload_size})</label>
					<div class="multiple-options">
						<div class="user-inputs">
						</div>
						<a href="#" class="add-period add-item">Добавить файл</a>
					</div>
				</div>
				
				
				
				
				
				<div class="comment">* - обязательное для заполнения поле</div>
				<input type="submit" class="submit" name="submit" value="Отправить">
				<input type="hidden" name="form_type" value="preschool">
				
				{include file=$warning_box_template}
						
			</form>		
			
			
							
			
			
		</div>	

        
        
        
        
        
        
    </div>


