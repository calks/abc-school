
		
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Результаты теста "{$quizz->name}", {$user_info.name|escape}</title>
{literal}
<style type="text/css">
	body, div, p, td, th, span, h1, h2, h3 {
		margin: 0;
		padding: 0;
		font-family: Arial, sans-serif;
		font-size: 12px;
		font-weight: normal;
		border: none;
	}
	
	table {
		border: 1px solid black;
		border-collapse: collapse;
	}
	
	th, td {
		border: 1px solid black;
		padding: 3px 10px;
	}
	
	
	th {
		background: #ddd;
		font-weight: bold;
	}

	body {		
		width: 700px;
		padding-bottom: 20px;
	}
	
	
	#content {
		padding: 20px;		
	}
	
	#content h1 {
		font-size: 18px;
		margin-bottom: 20px;
	}
	
	#content h2 {		
		margin: 20px 0 10px;
	}
	
	#content h3 {		
		margin: 10px 0;
	}
	
	#content p {
		margin: 10px 0;
		line-height: 130%; 
	}
		
	
	#footer * {
		color: #828282;
		line-height: 130%;		
	}
	
	#footer p {
		margin-top: 20px;		 
	}

</style>
{/literal}
</head>
<body>
	<div id="content">
		<h1>Кто-то прошел тест "{$quizz->name}" на сайте abc-school.ru</h1>


		<h2>Информация о пользователе</h2>

		<b>ФИО:</b> {$user_info.name}<br>
		<b>Возраст:</b> {$user_info.age}<br>
		<b>Школа:</b> {$user_info.school}<br>
		<b>Класс:</b> {$user_info.grade}<br>
		<b>Телефон:</b> {$user_info.phone}<br>
		
		
		<h2>Результат теста</h2>
		
		<p>Пользователь ответил правильно на <b>{$right_answers}</b> из <b>{$questions_total}</b> вопросов.</p>
		
		{foreach item=q key=key from=$questions}
			<div>
				<h3>Вопрос {$key+1} из {$questions_total}</h3>
				
				<p>{$q->content}</p>
				
				<table>
					{assign var=right_answer value=0}
					{foreach item=a key=num from=$q->answers}
						{if $a->is_right}
							{assign var=right_answer value=$num+1}
						{/if}	
						<tr {if $q->answer_id==$a->id}style="background: {if $a->is_right}#aaffaa{else}#ffaaaa{/if}"{/if}>							
							<td class="num">{$num+1}</td>
							<td>{$a->content}</td>
						</tr>
					{/foreach}
				</table>
				<p>Правильный ответ: <b>{$right_answer}</b></p>
			</div>
		{/foreach}
		
	</div>		
		
</body>
</html>
		