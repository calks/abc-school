


	{assign var="td_style" value='style="border: 1px solid #000; padding: 3px 10px"'}
	{assign var="th_style" value='style="border: 1px solid #000; padding: 3px 10px; background: #eee; text-align: center"'}


	<p style="text-align: right">
		Директору<br>
		ЧУДО Лингвоцентра &laquo;Эй Би Си&raquo;<br>
		М.В. Андрюниной
	</p>
	
	
	<p style="text-align: center">
		Заявление<br> 
		о выдаче справки об оплате образовательных услуг<br> 
		для представления в налоговый орган
	</p>


	<table style="border-collapse: collapse; width: 100%">
		<tr>
			<td {$td_style} width="30%">Отчетный период</td>
			<td {$td_style} width="30%">{$education_periods[0].period_name} год</td>
			<td {$td_style}>{$education_periods[0].comment}</td>
		</tr>
		<tr>
			<td {$th_style} colspan="3">Данные плательщика</th>
		</tr>
		<tr>
			<td {$td_style}>ФИО</td>
			<td {$td_style} colspan="2">{$form->getValue('parent_name')}</td>			
		</tr>
		<tr>
			<td {$td_style}>ИНН</td>
			<td {$td_style} colspan="2">{$form->getValue('parent_inn')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Дата рождения</td>
			<td {$td_style} colspan="2">{$form->getValue('parent_birth_date')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Документ (паспорт)</td>
			<td {$td_style} colspan="2">серия {$form->getValue('parent_document_series')} № {$form->getValue('parent_document_number')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Дата выдачи / код подразделения</td>
			<td {$td_style} colspan="2">{$form->getValue('parent_document_issue_date')} / {$form->getValue('parent_document_issued_by')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Контактный телефон</td>
			<td {$td_style} colspan="2">{$form->getValue('parent_phone')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Форма обучения</td>
			<td {$td_style} colspan="2">Очная</td>			
		</tr>
		<tr>
			<td {$th_style} colspan="3">Данные обучающегося, которому оказаны образовательные услуги</th>
		</tr>
		<tr>
			<td {$td_style}>ФИО</td>
			<td {$td_style} colspan="2">{$form->getValue('child_name')}</td>			
		</tr>
		<tr>
			<td {$td_style}>ИНН (при наличии)</td>
			<td {$td_style} colspan="2">{$form->getValue('child_inn')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Дата рождения</td>
			<td {$td_style} colspan="2">{$form->getValue('child_birth_date')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Документ (паспорт или свидетельство о рождении)</td>
			<td {$td_style} colspan="2">
				{if $form->getValue('child_document_type') == 'passport'}
					паспорт
				{elseif $form->getValue('child_document_type') == 'birth_certificate'}
					свидетельство о рождении
				{/if}
			</td>			
		</tr>
		<tr>
			<td {$td_style}>Серия</td>
			<td {$td_style} colspan="2">{$form->getValue('child_document_series')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Номер</td>
			<td {$td_style} colspan="2">{$form->getValue('child_document_number')}</td>			
		</tr>
		<tr>
			<td {$td_style}>Дата выдачи / код подразделения</td>
			<td {$td_style} colspan="2">
				{$form->getValue('child_document_issue_date')}
				{if $form->getValue('child_document_type') == 'passport'}
					/ {$form->getValue('child_document_issued_by')}
				{/if}
			</td>			
		</tr>
		<tr>
			<td {$th_style} colspan="3">Вариант получения справки (заполнить одну из строк)</th>
		</tr>
		<tr>
			<td {$td_style}>На адрес электронной почты</td>
			<td {$td_style} colspan="2">
				{if $form->getValue('delivery_type') == 'by_email'}
					{$form->getValue('delivery_email')}
				{/if}			
			</td>			
		</tr>
		<tr>
			<td {$td_style}>Лично</td>
			<td {$td_style} colspan="2">
				{if $form->getValue('delivery_type') == 'in_person'}
					лично
				{/if}			
			</td>			
		</tr>
	</table>
	
	
	<ul>
		<li>Подписывая настоящее заявление, я даю согласие на обработку персональных данных и подтверждаю, что все персональные данные третьих лиц, указанные мною в данном заявлении, я предоставляю с их добровольного согласия</li>
		<li>Достоверность сведений, указанных в настоящем заявлении подтверждаю</li>
		{if $form->getValue('contracts_available_yn') == 'y'}
			<li>У меня есть договоры на указанные периоды обучения</li>
		{/if}
	</ul>
	<br><br>
	
	<table style="border-collapse: collapse; width: 100%">
		<tr>
			<td>Дата ____________</td>
			<td>Подпись ______________</td>		
		</tr>
	</table>
	

	