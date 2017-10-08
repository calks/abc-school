
	<h1>С сайта abc-school.ru была отправлена заявка на вакансию</h1>
	
			<p>
				<b>Дата и время заполнения анкеты</b>
				{$smarty.now|date_format:"%d.%m.%Y %H:%M"}							
			</p>
	
			<p>
				<b>ФИО</b>
				{$form->getValue('name')}							
			</p>
			<p>
				<b>Дата и место рождения</b>
				{$form->getValue('birth_date_n_place')}							
			</p>
			<p>
				<b>Семейное положение</b>
				{$form->getValue('family_type')}							
			</p>
			<p>
				<b>Образование (в том числе курсы)</b>
				{$form->getValue('degree')}							
			</p>
			<p>
				<b>Трудовой опыт (организация, период работы, должность)</b>
				{$form->getValue('experience')}
			</p>
			<p>
				<b>Уровень владения иностранными языками</b>
				{$form->getValue('foreign_languages')}
			</p>
			
			
			<p>
				<b>Проф. навыки</b>
				{$form->getValue('skills')}							
			</p>
			<p>
				<b>Личные качества</b>
				{$form->getValue('personality')}							
			</p>
			<p>
				<b>Адрес</b>
				{$form->getValue('address')}							
			</p>
			
			<p>
				<b>Телефон</b>
				{$form->getValue('phone')}							
			</p>
			<p>
				<b>Email</b>
				{$form->getValue('email')}							
			</p>
			
	