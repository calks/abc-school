
	
		<h2>
			<a class="back" href="{$back_link}">К списку тестов</a>
			Вопрос {$question_number} из {$questions_count}
		</h2>


		<form>
			<div class="question">{$question->content}</div>
			
			<div class="answers">				
				{foreach item=answer from=$question->answers}
					<div class="option">
						<input type="radio" name="answer" value="{$answer->id}">
						{$answer->content}
					</div>					
				{/foreach}
			</div>	
							
			<input class="submit" type="button" name="submit" value="Ответить">
			<div class="no_answer"></div>
			<input type="hidden" name="task" value="answer">
			

		</form>
	
