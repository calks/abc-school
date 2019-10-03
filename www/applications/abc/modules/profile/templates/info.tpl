		
			{if $message && !$is_ajax}
				<p>{$message}</p>
			{/if}
			
			
			<form class="half-size" action="{$form_action}" method="post">
			
			
			{if $viewing_teacher}
					<div class="full_width">						
						<label>Имя</label>
						<b>{$info_user->user_name}</b>
					</div>

					<div class="full_width">						
						<label>Email</label>
						<b><a href="mailto:{$info_user->email}">{$info_user->email}</a></b>
					</div>
			{else}

					<div class="full_width">					
						<label>Логин</label>
						<b>{$info_user->login}</b>
					</div>
					
					{if $group_name}
						<div class="full_width">					
							<label>Группа</label>
							<b>{$group_name}</b>
						</div>
					{/if}	
	
					
					{if $form->fields.firstname}
						<div class="full_width">
							{if $errors.firstname}<div class="error firstname">{$errors.firstname}</div>{/if}
							<label>Имя *</label>
							{$form->render('firstname')}
						</div>
					{elseif $info_user->firstname}
						<div class="full_width">					
							<label>Имя</label>
							<b>{$info_user->firstname}</b>						
						</div>
					{/if}	


					{if $form->fields.lastname}
						<div class="full_width">
							{if $errors.lastname}<div class="error lastname">{$errors.lastname}</div>{/if}
							<label>Фамилия *</label>
							{$form->render('lastname')}
						</div>
					{elseif $info_user->lastname}
						<div class="full_width">					
							<label>Фамилия</label>
							<b>{$info_user->lastname}</b>						
						</div>
					{/if}	
					
					{if $form->fields.email}		
						<div class="full_width">
							{if $errors.email}<div class="error email">{$errors.email}</div>{/if}
							<label>Email *</label>
							{$form->render('email')}
						</div>
					{elseif $info_user->email}
						<div class="full_width">					
							<label>Email</label>
							<b>{$info_user->email}</b>						
						</div>
					{/if}	
						
	
					{if $form->fields.parents}
						<div class="full_width">					
							<label>Родители</label>
							{$form->render('parents')}						
						</div>
					{elseif $info_user->parents}	
						<div class="full_width">					
							<label>Родители</label>
							<b>{$info_user->parents}</b>						
						</div>
					{/if}
	
	
					{if $form->fields.phone}
						<div class="full_width">					
							<label>Дом. телефон</label>
							{$form->render('phone')}						
						</div>
					{elseif $info_user->phone}	
						<div class="full_width">					
							<label>Дом. телефон</label>
							<b>{$info_user->phone}</b>						
						</div>
					{/if}	
	
					
					{if $form->fields.cell_phone}
						<div class="full_width">					
							<label>Моб. телефон</label>
							{$form->render('cell_phone')}						
						</div>
					{elseif $info_user->cell_phone}	
						<div class="full_width">					
							<label>Моб. телефон</label>
							<b>{$info_user->cell_phone}</b>						
						</div>
					{/if}	
					
					
					{if $form->fields.info}
						<div class="full_width">					
							<label>Доп. инфо</label>
							{$form->render('info')}
						</div>
					{elseif $info_user->info}
						<div class="full_width">					
							<label>Доп. инфо</label>
							<b>{$info_user->info}</b>						
						</div>
					{/if}	

	
					
					{if $form->fields.new_pass}
						<div class="full_width">
							{if $errors.new_pass}<div class="error new_pass">{$errors.new_pass}</div>{/if}
							<label>Новый пароль<br /><span>(только если хотите изменить)</span></label>
							{$form->render('new_pass')}
						</div>
		
						<div class="full_width">
							{if $errors.new_pass_confirmation}<div class="error new_pass_confirmation">{$errors.new_pass_confirmation}</div>{/if}
							<label>Подтверждение пароля *</label>
							{$form->render('new_pass_confirmation')}
						</div>
					{/if}	
	
						
					{if $can_edit_profile}<div class="comment">* - обязательное для заполнения поле</div>{/if}
					{$form->render('user_id')}
					{if !$is_ajax && $can_edit_profile}<input type="submit" class="submit" name="submit" value="Сохранить">{/if}
				{/if}	
						
			</form>


	