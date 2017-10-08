
        <div class="top_comment">
            {if $action == 'add'}
                Добавление раздела галереи
            {else}
                Редактирование раздела галереи
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
			            <tr><th>Название *:</th><td>{$form->render('name')}</td></tr>
			            <tr><th>Активен :</th><td>{$form->render('active')}</td></tr>			            
			            <tr>
			            	<th>Картинка :</th>
			            	<td>
			            		{$form->render('image')}
			            		<br><br>
			            		{if $object->image}
			            			<img src="{$smarty.const.PHOTOS_URL}/gallery/{$object->id}/thumb/{$object->image}">
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
		            {$form->render('seq')}
		        </td>
		    </tr>
        </table>
        </form>
        <br>
