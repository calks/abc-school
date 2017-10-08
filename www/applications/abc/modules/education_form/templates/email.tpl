
	<h1>С сайта abc-school.ru была отправлена заявка на обучение (форма {if $form_type=='kids'}для детей{elseif $form_type=='preschool'}для дошкольников{else}для взрослых{/if})</h1>

	<p>
		<b>Дата и время заполнения анкеты</b>
		{$smarty.now|date_format:"%d.%m.%Y %H:%M"}							
	</p>
	
	
	{if $form_type=='kids'}
	
			<h2>Информация о ребенке</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('kids_name')}							
			</p>
			<p>
				<b>Возраст</b>
				{$form->getValue('age')}							
			</p>
			<p>
				<b>Дата рождения</b>
				{$form->getValue('birth_date')}							
			</p>
			<p>
				<b>Школа</b>
				{$form->getValue('school')}							
			</p>
			<p>
				<b>Класс</b>
				{$form->getValue('grade')}							
			</p>
			<p>
				<b>Смена</b>
				{assign var=shift value=$form->getValue('shift')}
				{$shift_select.$shift}							
			</p>
			<p>
				<b>Продленка?</b>
				{if $form->getValue('prolonged')}Да{else}Нет{/if}
			</p>
			
			
			<h2>Информация о родителях</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('parents_name')}							
			</p>
			<p>
				<b>Место работы</b>
				{$form->getValue('parents_job')}							
			</p>
			
			
			<h2>Контактные данные</h2>
			<p>
				<b>Телефон</b>
				{$form->getValue('phone')}							
			</p>
			<p>
				<b>Адрес</b>
				{$form->getValue('address')}							
			</p>
			<p>
				<b>Email</b>
				{$form->getValue('email')}							
			</p>
			
			
			<h2>Дополнительная информация</h2>
			<p>
				<b>Изучали язык раньше?</b>
				{$form->getValue('learned_earlier')}							
			</p>
			<p>
				<b>Где и как долго?</b>
				{$form->getValue('learned_earlier_detail')}							
			</p>
			<p>
				<b>Заметки, пожелания</b>
				{$form->getValue('comments')}							
			</p>
			
			
	{elseif $form_type=='preschool'}
	
			<h2>Информация о ребенке</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('kids_name')}							
			</p>
			<p>
				<b>Возраст</b>
				{$form->getValue('age')}							
			</p>
			<p>
				<b>Дата рождения</b>
				{$form->getValue('birth_date')}							
			</p>
			<p>
				<b>Дет. сад</b>
				{$form->getValue('school')}							
			</p>
			<p>
				<b>Группа</b>
				{$form->getValue('grade')}							
			</p>

			
			<h2>Информация о родителях</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('parents_name')}							
			</p>
			<p>
				<b>Место работы</b>
				{$form->getValue('parents_job')}							
			</p>
			
			
			<h2>Контактные данные</h2>
			<p>
				<b>Телефон</b>
				{$form->getValue('phone')}							
			</p>
			<p>
				<b>Адрес</b>
				{$form->getValue('address')}							
			</p>
			<p>
				<b>Email</b>
				{$form->getValue('email')}							
			</p>
			
			
			<h2>Дополнительная информация</h2>
			{*<p>
				<b>Изучали язык раньше?</b>
				{$form->getValue('learned_earlier')}							
			</p>*}
			<p>
				<b>Во сколько обычно забираете ребенка из д/сада?</b>
				{$form->getValue('kidergarden_end_time')}							
			</p>

			<p>
				<b>Заметки</b>
				{$form->getValue('comments')}							
			</p>


	
	{else}



			<h2>Общая информация</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('name')}							
			</p>
			<p>
				<b>Возраст</b>
				{$form->getValue('age')}							
			</p>
			<p>
				<b>Дата рождения</b>
				{$form->getValue('birth_date')}							
			</p>
			<p>
				<b>Место работы</b>
				{$form->getValue('parents_job')}							
			</p>
			
			
			
			<h2>Контактные данные</h2>
			<p>
				<b>Телефон</b>
				{$form->getValue('phone')}							
			</p>
			<p>
				<b>Адрес</b>
				{$form->getValue('address')}							
			</p>
			<p>
				<b>Email</b>
				{$form->getValue('email')}							
			</p>
			
			
			<h2>Дополнительная информация</h2>
			<p>
				<b>Изучали язык раньше?</b>
				{$form->getValue('learned_earlier')}							
			</p>
			<p>
				<b>Где и как долго?</b>
				{$form->getValue('learned_earlier_detail')}							
			</p>
			<p>
				<b>Заметки, пожелания</b>
				{$form->getValue('comments')}							
			</p>
	
	
	{/if}
	
	