
        <div class="top_comment">
			Редактирование фото            
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
		            	<tr><th>Галерея :</th><td>{$form->render('gallery_id')}</td></tr>
			            <tr><th>Комментарий :</th><td>{$form->render('comment')}</td></tr>			            
			            <tr>
			            	<th>Картинка *:</th>
			            	<td>
			            		{$form->render('image')}
			            		<br><br>
			            		{if $object->image}
			            			<img src="{$smarty.const.PHOTOS_URL}/gallery_photo/{$object->id}/thumb/{$object->image}">
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
