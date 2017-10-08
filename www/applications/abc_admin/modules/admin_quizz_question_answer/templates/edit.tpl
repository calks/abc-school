
	<h3>Ответы на вопрос "{$question->content|strip_tags:false}"</h3>

        <div class="top_comment">
            {if $action == 'add'}
                Добавление ответа
            {else}
                Редактирование ответа
            {/if}
        </div>
        
        <form action="{$form_action}" method="POST" enctype="multipart/form-data">
        <table summary="" align="center">
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
		        </td>
		    </tr>
	        <tr>
	        	<td>
		            <table summary="" class="edit">			            
			            <tr><th>Текст ответа *:</th><td>{$form->render('content')}</td></tr>			            
			            <tr><th>Это правильный ответ :</th><td>{$form->render('is_right')}</td></tr>
		            </table>	
	            	<br>
	            	* - обязательное поле
	        	</td>
	        </tr>
	        <tr>
	        	<td align="right" class="buttom_form">
		            <input type="button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="&lt;&lt;Назад к списку">
		            <input type="submit" name="save" value="Сохранить">
		            <input type="reset" name="reset" value="Сбросить">
					<input type="hidden" name="action" value="{$action}">
		            {$form->render('id')}
		            {$form->render('seq')}
		            {$form->render('question_id')}
		            <input type="hidden" name="question" value="{$question->id}">
		        </td>
		    </tr>
        </table>
        </form>
        <br>
