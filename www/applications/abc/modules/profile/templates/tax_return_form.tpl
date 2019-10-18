
	<script type="text/javascript">
		var education_periods_options = {$education_periods_options|@json_encode};
	</script>


	<div class="text fields education_form_module tax_return_form">
		
		<h1>Заявка на налоговый вычет</h1>
		
		
		<form class="" action="{$form_action}" method="post" enctype="multipart/form-data">
			
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
			
			{if $errors.education_periods}<div class="error education_periods">{$errors.education_periods}</div>{/if}
			<div id="education-periods">
				<label>Периоды обучения и стоимость</label>
				<div class="multiple-options">
					<div class="user-inputs">
					
					</div>
					<div class="data-inputs">
						{foreach item=item from=$education_periods}
							<input type="hidden" class="period-start-year" name="education_periods[][start_year]" value="{$item.start_year}">
							<input type="hidden" class="period-comment" name="education_periods[][start_year]" value="{$item.comment}">
						{/foreach}
					</div>
					
					<a href="#" class="add-period add-item">Добавить период обучения</a>
				</div>
			</div>

			
			{if $errors.files}<div class="error files">{$errors.files}</div>{/if}
			
			
			{if $errors.contracts_available_yn}<div class="error learned_earlier">{$errors.contracts_available_yn}</div>{/if}
			<label>У меня есть договоры на указанные периоды обучения</label>					
			<div class="input_wrap">{$form->render('contracts_available_yn')}</div>
			
			<div class="row" id="files">
				<label>Прикрепить квитанции/чеки</label>				
				<div class="user-inputs">
				
				</div>
				<div class="data-inputs">
					{foreach item=item from=$education_periods}
						<input type="hidden" class="period-start-year" name="education_periods[][start_year]" value="{$item.start_year}">						
					{/foreach}
				</div>
				
				<a href="#" class="add-period add-item">Добавить период обучения</a>
			</div>
			
			
			{$form->render('files_count')}
			
			
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Отправить">
			<input type="hidden" name="form_type" value="preschool">
			
			{include file=$warning_box_template}
					
		</form>		
		
		
						
		
		
	</div>	
