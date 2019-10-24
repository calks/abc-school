
	
		<h2>
			<a class="back" href="{$back_link}">К списку тестов</a>
			Введите, пожалуйста, свои данные
		</h2>


		<form>
			{if $form_errors.name}<div class="error">{$form_errors.name}</div>{/if}
			<div class="full_width">
				<label>ФИО *</label>
				<input{if $form_errors.name} class="error"{/if} type="text" name="name" size="100" maxlength="255" value="{$form_data.name}">
			</div>				
			{if $form_errors.age}<div class="error">{$form_errors.age}</div>{/if}
			<div class="row">			
				<label>Возраст *</label>
				<div class="one_line">				
					<input{if $form_errors.age} class="error"{/if} type="text" name="age" size="10" maxlength="20" value="{$form_data.age}">
				</div>
			</div>
			{if $form_errors.school || $form_errors.grade}<div class="error">{if $form_errors.school}{$form_errors.school}{else}{$form_errors.grade}{/if}</div>{/if}
			<div class="row">
				<label>Школа *</label>
				<div class="one_line">
					<input{if $form_errors.school} class="error"{/if} type="text" name="school" size="70" maxlength="255" value="{$form_data.school}">
					<label>Класс *</label>
					<input{if $form_errors.grade} class="error"{/if} type="text" name="grade" size="10" maxlength="20" value="{$form_data.grade}">
				</div>
			</div>
			{if $form_errors.phone}<div class="error">{$form_errors.phone}</div>{/if}
			<div class="row">
				<label>Телефон *</label>
				<input{if $form_errors.phone} class="error"{/if} type="text" name="phone" size="50" maxlength="255" value="{$form_data.phone}">
			</div>
			
								
			<div class="comment">* - обязательное для заполнения поле</div>
			<input type="submit" class="submit" name="submit" value="Отправить">
			<input type="hidden" name="task" value="user_info">
								
		</form>