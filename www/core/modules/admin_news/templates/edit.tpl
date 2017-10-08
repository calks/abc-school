
        <div class="top_comment">
            {if $action == 'add'}
                Добавление новости
            {else}
                Редактирование новости
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
			            <tr><th>Дата :</th><td>{$form->render('date')}</td></tr>
			            <tr><th>Заголовок *:</th><td>{$form->render('title')}</td></tr>
			            <tr><th>Активна :</th><td>{$form->render('active')}</td></tr>
			            <tr><th>Текст новости *:</th><td>{$form->render('story')}</td></tr>
			            <tr>
			            	<th>Картинка :</th>
			            	<td>
			            		{$form->render('image')}
			            		<br><br>
			            		{if $object->image}
			            			<img src="{$smarty.const.PHOTOS_URL}/news/{$object->id}/small/{$object->image}">
			            			<br><br>
			            			<a href="{$deleteimage_link}&image_field=image">Удалить картинку</a>
			            		{/if}			            	
			            	</td>
			            </tr>
			            
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
