
	<h3>Вопросы к тесту "{$quizz->name}"</h3>

        <div class="top_comment">
            {if $action == 'add'}
                Добавление вопроса
            {else}
                Редактирование вопроса
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
			            <tr><th>Текст вопроса *:</th><td>{$form->render('content')}</td></tr>			            
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
		            {$form->render('quizz_id')}
		            <input type="hidden" name="quizz" value="{$quizz->id}">
		        </td>
		    </tr>
        </table>
        </form>
        <br>
