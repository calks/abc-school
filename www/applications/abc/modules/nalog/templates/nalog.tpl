



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
			
			
			<form action="{$form_action}" method="post" enctype="multipart/form-data">


				<h2>Данные плательщика</h2>
				<div class="full_width">
					{if $errors.parent_name}<div class="error">{$errors.parent_name}</div>{/if}
					<label>ФИО&nbsp;*</label>
					{$form->render('parent_name')}				
				</div>				
				<div class="row">
					{if $errors.parent_inn}<div class="error">{$errors.parent_inn}</div>{/if}
					<label>ИНН&nbsp;*</label>					
					{$form->render('parent_inn')}				
				</div>
				{if $errors.parent_birth_date}<div class="error">{$errors.parent_birth_date}</div>{/if}				
				<div class="row">
					<label>Дата рождения&nbsp;*</label>
					{$form->render('parent_birth_date')}				
				</div>				
				<div class="row">
					{if $errors.parent_document_series}<div class="error">{$errors.parent_document_series}</div>{/if}
					<label>Серия паспорта&nbsp;*</label>
					{$form->render('parent_document_series')}  				
				</div>
				<div class="row">
					{if $errors.parent_document_number}<div class="error">{$errors.parent_document_number}</div>{/if}
					<label>Номер паспорта&nbsp;*</label>
					{$form->render('parent_document_number')}  				
				</div>
				<div class="row">
					{if $errors.parent_document_issue_date}<div class="error">{$errors.parent_document_issue_date}</div>{/if}
					<label>Дата выдачи паспорта&nbsp;*</label>
					{$form->render('parent_document_issue_date')}  				
				</div>
				<div class="row">
					{if $errors.parent_document_issued_by}<div class="error">{$errors.parent_document_issued_by}</div>{/if}
					<label>Код подразделения&nbsp;*</label>
					{$form->render('parent_document_issued_by')}  				
				</div>				
				<div class="row">
					{if $errors.parent_phone}<div class="error">{$errors.parent_phone}</div>{/if}
					<label>Контактный телефон&nbsp;*</label>
					{$form->render('parent_phone')}				
				</div>

				<h2>Данные обучающегося, которому оказаны образовательные услуги</h2>
				<div class="full_width">
					{if $errors.child_name}<div class="error">{$errors.child_name}</div>{/if}
					<label>ФИО&nbsp;*</label>
					{$form->render('child_name')}				
				</div>
				<div class="row">
					{if $errors.child_inn}<div class="error">{$errors.child_inn}</div>{/if}					
					<label>ИНН (при наличии)</label>
					{$form->render('child_inn')}				
				</div>				
				<div class="row">
					{if $errors.child_birth_date}<div class="error">{$errors.child_birth_date}</div>{/if}
					<label>Дата рождения&nbsp;*</label>
					{$form->render('child_birth_date')}				
				</div>
				<div class="row">
					{if $errors.child_document_type}<div class="error">{$errors.child_document_type}</div>{/if}
					<label>Дoкумент&nbsp;*</label>
					{$form->render('child_document_type')}				
				</div>
				<div class="row">
					{if $errors.child_document_series}<div class="error">{$errors.child_document_series}</div>{/if}
					<label>Серия документа&nbsp;*</label>
					{$form->render('child_document_series')}  				
				</div>
				<div class="row">
					{if $errors.child_document_number}<div class="error">{$errors.child_document_number}</div>{/if}
					<label>Номер документа&nbsp;*</label>
					{$form->render('child_document_number')}  				
				</div>
				<div class="row">
					{if $errors.child_document_issue_date}<div class="error">{$errors.child_document_issue_date}</div>{/if}
					<label>Дата выдачи документа&nbsp;*</label>
					{$form->render('child_document_issue_date')}  				
				</div>
				<div class="row child_document_issued_by hidden">
					{if $errors.child_document_issued_by}<div class="error">{$errors.child_document_issued_by}</div>{/if}
					<label>Код подразделения&nbsp;*</label>
					{$form->render('child_document_issued_by')}  				
				</div>				
				
				
				<h2 class="separated">Период и стоимость обучения</h2>
				
				{if $errors.education_period}<div class="error education_period">{$errors.education_period}</div>{/if}
				<div id="education-periods">
					<label>Периоды обучения и стоимость</label>
					<div class="multiple-options">
						<div class="user-inputs">
						
						</div>
						{* <a href="#" class="add-period add-item">Добавить период обучения</a> *}
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
				
				
				<h2 class="separated">Получение справки и согласие на обработку данных</h2>

				<div class="row">
					{if $errors.delivery_type}<div class="error">{$errors.delivery_type}</div>{/if}
					<label>Получить справку&nbsp;*</label>
					{$form->render('delivery_type')}  				
				</div>
				<div class="row delivery_email hidden">
					{if $errors.delivery_email}<div class="error">{$errors.delivery_email}</div>{/if}
					<label>Email для получения справки&nbsp;*</label>
					{$form->render('delivery_email')}  				
				</div>
				{if $errors.data_process_confirmation}<div class="error">{$errors.data_process_confirmation}</div>{/if}
				<div class="row">					
					<label>&nbsp;</label>
					{$form->render('data_process_confirmation')} <span class="confirmation-label">Подписывая настоящее заявление, я даю согласие на обработку персональных данных и подтверждаю, что все персональные данные третьих лиц, указанные мною в данном заявлении, я предоставляю с их добровольного согласия</span>
				</div>
				{if $errors.data_validity_confirmation}<div class="error">{$errors.data_validity_confirmation}</div>{/if}
				<div class="row">					
					<label>&nbsp;</label>
					{$form->render('data_validity_confirmation')} <span class="confirmation-label">Достоверность сведений, указанных в настоящем заявлении подтверждаю</span>
				</div>

				
				
				<div class="comment">* - обязательное для заполнения поле</div>
				<input type="submit" class="submit" name="submit" value="Отправить">
				<input type="hidden" name="form_type" value="preschool">
				
				{include file=$warning_box_template}
						
			</form>		

			

			
							
			
			
		</div>	

        
        
        
        
        
        
    </div>


