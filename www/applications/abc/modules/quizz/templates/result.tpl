
		
	<h2>
		<a class="back" href="{$back_link}">К списку тестов</a>
		Спасибо за участие в нашем тесте!
	</h2>	
	
	<p>Вы ответили правильно на <b>{$right_answers}</b> из <b>{$questions_total}</b> вопросов.</p>
	

	<a class="view_detailed_result" href="#">подробные результаты</a>
	
	
	<div class="detailed_result hidden">
		{foreach item=q key=key from=$questions}
			<div class="question">
				<h3>Вопрос {$key+1} из {$questions_total}</h3>
				<div class="q">
					{$q->content}
				</div>
				<table class="answers">
					{assign var=right_answer value=0}
					{foreach item=a key=num from=$q->answers}
						{if $a->is_right}
							{assign var=right_answer value=$num+1}
						{/if}	
						<tr {if $q->answer_id==$a->id}
								class="{if $a->is_right}right{else}wrong{/if}"
							{else}
								class="{cycle values="odd,even"}"
							{/if}>							
							<td class="num">{$num+1}</td>
							<td>{$a->content}</td>
						</tr>
					{/foreach}
				</table>
				<p>Правильный ответ: <b>{$right_answer}</b></p>
			</div>
		{/foreach}
	
	</div>