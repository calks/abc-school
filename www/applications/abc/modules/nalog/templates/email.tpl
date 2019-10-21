
	<h1>С сайта abc-school.ru была отправлена заявка на получение надогового вычета</h1>

	<p>
		<b>Дата и время заполнения анкеты</b>
		{$smarty.now|date_format:"%d.%m.%Y %H:%M"}							
	</p>
	
	
			<h2>Информация о ребенке</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('child_name')}							
			</p>
			<p>
				<b>Дата рождения</b>
				{$form->getValue('child_birth_date')}							
			</p>
			
			<h2>Информация о родителях (на кого оформляется вычет)</h2>
			<p>
				<b>ФИО</b>
				{$form->getValue('parent_name')}							
			</p>
			<p>
				<b>Место работы</b>
				{$form->getValue('parent_birth_date')}							
			</p>
			
			
			<h2>Период и стоимость обучения</h2>
			<p>
				{foreach item=period from=$education_periods}
					<b>{$period.period_name}</b>
					{$period.comment}<br>
				{/foreach}
											
			</p>
			<p>
				<b>У меня есть договоры на указанные периоды обучения</b>
				{$form->getValue('contracts_available_yn')}							
			</p>
			
			
		
	