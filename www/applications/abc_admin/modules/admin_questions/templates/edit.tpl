
        <div class="top_comment">
            {if $action == 'add'}
                Добавление записи
            {else}
                Редактирование записи
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
			            <tr><th>Добавлена :</th><td>{$object->created|date_format:"%d.%m.%Y %H:%M:%S"}</td></tr>
			            <tr><th>Уведомление автору :</th><td>{if $object->author_notified}отправлено{else}не отправлено{/if}</td></tr>
			            <tr><th>Активна :</th><td>{$form->render('active')}</td></tr>
			            <tr><th>Имя автора *:</th><td>{$form->render('author_name')}</td></tr>
			            <tr><th>Email автора *:</th><td>{$form->render('author_email')}</td></tr>
			            
			            <tr><th>Вопрос *:</th><td>{$form->render('question')}</td></tr>
			            <tr><th>Ответ *:</th><td>{$form->render('answer')}</td></tr>
			            
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
		            {$form->render('id')}
		            {$form->render('created')}
		            {$form->render('author_notified')}
		            <input type="hidden" name="action" value="{$action}">
		        </td>
		    </tr>
        </table>
        </form>
        <br>
