

        <div class="top_comment">
            {if $action == 'add'}
                Добавление праздника
            {else}
                Редактирование праздника
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
		            	<tr><th>Дата *:</th><td>{$form->render('date')}</td></tr>
			            <tr><th>Название *:</th><td>{$form->render('title')}</td></tr>
			            <tr><th>Показывать :</th><td>{$form->render('visibility')}</td></tr>
			            <tr><th>Повторять ежегодно :</th><td>{$form->render('repeat_annually')}</td></tr>
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
		        </td>
		    </tr>
        </table>
        </form>
        <br>
