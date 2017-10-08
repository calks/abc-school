		
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
	
					
					
					<div class="full_width">
						{if $errors.firstname}<div class="error firstname">{$errors.firstname}</div>{/if}
						<label>Имя *</label>
						{$form->render('firstname')}
					</div>
					
					<div class="full_width">
						{if $errors.lastname}<div class="error lastname">{$errors.lastname}</div>{/if}
						<label>Фамилия *</label>
						{$form->render('lastname')}
					</div>
	
					<div class="full_width">
						{if $errors.email}<div class="error email">{$errors.email}</div>{/if}
						<label>Email *</label>
						{$form->render('email')}
					</div>
	
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
					
					
					<div class="full_width">					
						<label>Доп. инфо</label>
						{$form->render('info')}
					</div>
	
					
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
	
						
					<div class="comment">* - обязательное для заполнения поле</div>
					{$form->render('user_id')}
					{if !$is_ajax}<input type="submit" class="submit" name="submit" value="Сохранить">{/if}
				{/if}	
						
			</form>


	